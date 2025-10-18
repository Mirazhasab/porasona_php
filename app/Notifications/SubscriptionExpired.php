<?php

namespace App\Notifications;

use App\Models\UserSubscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpired extends Notification implements ShouldQueue
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
            ->subject('Your subscription has expired')
            ->greeting('Hello ' . $notifiable->name)
            ->line('Your subscription has expired as of ' . optional($this->userSubscription->expires_at)->toDayDateTimeString() . '.')
            ->line('Plan: ' . ($subscription?->name ?? 'Subscription'))
            ->line('Renew now to regain full access to all MCQ sets.')
            ->action('Renew Subscription', route('subscriptions.index'))
            ->line('Thank you for being with us.');
    }
}
