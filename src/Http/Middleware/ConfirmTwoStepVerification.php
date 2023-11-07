<?php

namespace Kohaku1907\Laravel2step\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kohaku1907\Laravel2step\Contracts\TwoStepAuthenticatable;

class ConfirmTwoStepVerification
{
    public function handle(Request $request, Closure $next, string $route = '2step.confirm'): mixed
    {
        $user = $request->user();
        if (! $user instanceof TwoStepAuthenticatable || ! $user->hasTwoStepEnabled() || $this->recentlyConfirmed($request)) {
            return $next($request);
        }

        return $request->expectsJson()
            ? response()->json(['message' => 'Step 2 verification is required.'], 403)
            : response()->redirectGuest(url()->route($route));
    }

    /**
     * Determine if the confirmation timeout has expired.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function recentlyConfirmed(Request $request): bool
    {
        $key = config('2step.confirm_key');

        return $request->session()->get("$key.expires_at") >= now()->getTimestamp();
    }
}