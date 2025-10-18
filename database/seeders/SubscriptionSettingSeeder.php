<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SubscriptionSettingSeeder extends Seeder
{
    public function run(): void
    {
        Setting::setValue(Setting::FREE_MCQ_LIMIT_KEY, 10, 'integer');
        Setting::setValue(Setting::SUBSCRIPTION_FEATURE_MATRIX_KEY, Setting::defaultSubscriptionFeatureMatrix(), 'json');
        Setting::setValue(Setting::SUBSCRIPTION_LIMITS_KEY, Setting::defaultSubscriptionLimits(), 'json');
    }
}
