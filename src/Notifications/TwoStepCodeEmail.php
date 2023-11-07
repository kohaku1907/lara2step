<?php

namespace Kohaku1907\Laravel2step\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class TwoStepCodeEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $code;

    /**
     * Create a new notification instance.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Determine which queues should be used for each notification channel.
     *
     * @return array
     */
    public function viaQueues()
    {
        return [
            'mail' => config('2step.queue'),
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $user = $notifiable;
        $message = new MailMessage();
        $message->subject(__('2step::email.subject'));
        $message->greeting(__('2step::email.greeting', ['name' => $user->name]));
        $message->line(__('2step::email.message', ['code' => $this->code]));
        $message->line(__('2step::email.line1', ['minutes' => config('2step.resend_timeout')]));
        $message->salutation(__('2step::email.salutation'));
        $message->line(__('2step::email.footer'));

        return $message;
    }

    
}