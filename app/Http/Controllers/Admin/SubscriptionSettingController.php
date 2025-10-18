<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSubscriptionSettingsRequest;
use App\Models\Setting;
use App\Models\SubscriptionLog;
use App\Models\UserAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class SubscriptionSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:manage-subscription-settings');
    }

    public function edit(): View
    {
        $limit = Setting::getValue(Setting::FREE_MCQ_LIMIT_KEY, 10);
        $featureMatrix = Setting::getSubscriptionFeatureMatrix();
        $limits = Setting::getSubscriptionLimits();

        return view('admin.settings.subscription', [
            'limit' => $limit,
            'featureMatrix' => $featureMatrix,
            'featureFields' => UserAccess::FEATURE_FIELDS,
            'limits' => $limits,
        ]);
    }

    public function update(UpdateSubscriptionSettingsRequest $request): RedirectResponse
    {
        $limit = (int) $request->input('free_mcq_limit_per_set');
        $freeSelection = $request->input('free_features', []);
        $subscribedSelection = $request->input('subscribed_features', []);

        $featureMatrix = [
            'free' => $this->mapSelectedFeatures($freeSelection),
            'subscribed' => $this->mapSelectedFeatures($subscribedSelection, true),
        ];

        $limits = Setting::getSubscriptionLimits();
        $limits['mcq_preview_per_set'] = $limit;

        Setting::setValue(Setting::FREE_MCQ_LIMIT_KEY, $limit, 'integer');
        Setting::setValue(Setting::SUBSCRIPTION_FEATURE_MATRIX_KEY, $featureMatrix, 'json');
        Setting::setValue(Setting::SUBSCRIPTION_LIMITS_KEY, $limits, 'json');

        SubscriptionLog::create([
            'action' => 'settings_updated',
            'description' => 'free_mcq_limit_per_set updated by admin',
            'meta' => [
                'admin_id' => $request->user()->id,
                'value' => $limit,
                'features' => $featureMatrix,
                'limits' => $limits,
            ],
        ]);

        return redirect()
            ->route('admin.subscription-settings.edit')
            ->with('success', 'Subscription settings updated.');
    }

    private function mapSelectedFeatures(array $selected, bool $forSubscribed = false): array
    {
        $map = array_fill_keys(UserAccess::FEATURE_FIELDS, false);

        foreach ($selected as $feature) {
            if (in_array($feature, UserAccess::FEATURE_FIELDS, true)) {
                $map[$feature] = true;
            }
        }

        if ($forSubscribed && !$map['mcq_management']) {
            // Keep management access reserved for admins unless explicitly enabled.
            $map['mcq_management'] = false;
        }

        return $map;
    }
}
