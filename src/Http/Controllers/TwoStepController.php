<?php

namespace Kohaku1907\Laravel2step\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Kohaku1907\Laravel2step\Models\TwoStepAuth;

class TwoStepController extends Controller
{
    private $_user;

    private TwoStepAuth $_twoStepAuth;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('throttle:60,1')->only('confirm');
        $this->middleware(
            function ($request, $next) {
                $this->_user = Auth::user();
                $this->_twoStepAuth = $this->_user->twoStepAuth;

                return $next($request);
            }
        );
    }

    /**
     * Show the 2step form.
     *
     * @return \Illuminate\Http\Response
     */
    public function form()
    {
        $twoStepAuth = $this->_twoStepAuth;
        $user = $this->_user;

        if($twoStepAuth->checkExceededTime()) {
            $twoStepAuth->resetAttempt();
        }

        if($twoStepAuth->isExceededMaxAttempts()) {
            $data = $twoStepAuth->getExceededData();
            return view('2step::exceeded', $data);
        }
        
        if(!$twoStepAuth->code || $user->codeFormatChanged()) {
            $user->generateTwoStepCode();
        }

        if(!$twoStepAuth->request_at) {
            $twoStepAuth->sendVerificationCodeNotification();
        } else {
            $allowedTime = $twoStepAuth->request_at->addMinutes(config('2step.resend_timeout'));
            if($allowedTime->isPast()) {
                $twoStepAuth->sendVerificationCodeNotification();
            }
        }

        $remainingAttempts = $twoStepAuth->getRemainingAttempts();
        $data = [
            'remainingAttempts' => $remainingAttempts,
            'user' => $this->_user,
        ];
        
        return response()->view('2step::confirm', $data);
    }

    /**
     * Confirm the validation code.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function confirm(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            ['code' => 'required|array']
        );
        if($validator->fails()) {
            return $request->wantsJson() 
                ? response()->json(['message' => __('2step::messages.invalid_code')], 422)
                : redirect()->back()->withErrors($validator);
        }
        
        $inputCode = implode('', $request->code);
        if(!$this->_twoStepAuth->validateCode($inputCode)) {
            return $request->wantsJson() 
                ? response()->json(['message' => __('2step::messages.invalid_code')], 422)
                : redirect()->back()->withErrors(['code' => __('2step::messages.invalid_code')]);
        }

        $this->extendConfirmationTimeout($request);

        return $request->wantsJson()
            ? response()->json(['message' => 'Success'], 200)
            : redirect()->intended();
    }

   /**
     * Resend the validation code triggered by user.
     *
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $this->_twoStepAuth->sendVerificationCodeNotification();

        //TODO: define for custom channel such as sms
        $recipient = $this->_twoStepAuth->channel === 'email' ? $this->_user->email : 'your phone number';
        if($this->_twoStepAuth->channel === 'email') {
            $recipient = explode('@', $recipient);
            $recipient = substr($recipient[0], 0, 2) . '**@' . $recipient[1];
        } else {
            $recipient = substr($recipient, 0, 3) . '**' . substr($recipient, -2);
        }
       
        return response()->json([
            'title' => 'Success',
            'message' => __('2step::messages.sent_success', ['recipient' => $recipient]),
        ], 200);
    }

    protected function extendConfirmationTimeout(Request $request): void
    {
        $key = config('2step.confirm_key');
        $time = config('2step.timeout');

        // This will let the developer remember the confirmation indefinitely.
        if ($time !== INF) {
            $time = now()->addMinutes($time)->getTimestamp();
        }

        $request->session()->put("$key.expires_at", $time);
    }
}