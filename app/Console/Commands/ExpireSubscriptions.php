<?php

namespace App\Console\Commands;

use App\Models\SubscriptionLog;
use App\Models\UserSubscription;
use App\Notifications\SubscriptionExpired;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Expire user subscriptions that are past their end date and notify users.';

    public function handle(): int
    {
        $expired = UserSubscription::query()
            ->where('status', UserSubscription::STATUS_ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->get();

        if ($expired->isEmpty()) {
            $this->info('No subscriptions to expire today.');
            return self::SUCCESS;
        }

        foreach ($expired as $userSubscription) {
            $userSubscription->markExpired();

            SubscriptionLog::create([
                'user_id' => $userSubscription->user_id,
                'user_subscription_id' => $userSubscription->id,
                'action' => 'subscription_expired',
                'description' => 'Subscription automatically expired by scheduler',
            ]);

            // Notify the user so they can renew if they wish.
            Notification::send($userSubscription->user, new SubscriptionExpired($userSubscription));
        }

        $this->info(sprintf('Expired %d subscription(s).', $expired->count()));

        return self::SUCCESS;
    }
}
