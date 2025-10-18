<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionActivated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public UserSubscription $userSubscription)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $subscription = $this->userSubscription->subscription;

        return (new MailMessage)
            ->subject('Your subscription is active')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your subscription has been activated successfully.')
            ->line('Plan: ' . ($subscription?->name ?? 'Custom Plan'))
            ->line('Starts at: ' . optional($this->userSubscription->starts_at)->toDayDateTimeString())
            ->line('Expires at: ' . optional($this->userSubscription->expires_at)->toDayDateTimeString())
            ->line('You now have unlimited access to all MCQ sets.')
            ->line('Thank you for supporting our platform!');
    }
}
