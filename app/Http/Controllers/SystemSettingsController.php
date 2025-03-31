<?php

namespace App\Http\Controllers;

use App\Facades\Settings;
use App\Models\Setting;
use App\Models\WorkCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SystemSettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        // Get all settings groups
        $groups = Settings::getGroups();

        // Get all settings configurations
        $settingsConfig = Settings::all();

        // Get current values from database
        $settingsValues = [];
        foreach ($settingsConfig as $config) {
            $settingsValues[$config['key']] = Settings::get($config['key']);
        }

        // Load dynamic data for select_dynamic fields
        $dynamicData = [];
        foreach ($settingsConfig as $config) {
            if ($config['type'] === 'select_dynamic' && isset($config['source'])) {
                $source = $config['source'];

                // Handle different model sources
                switch ($source['model']) {
                    case 'WorkCategory':
                        $items = WorkCategory::orderBy($source['label_field'])->get();
                        $dynamicData[$config['key']] = $items->map(function ($item) use ($source) {
                            return [
                                'value' => $item[$source['value_field']],
                                'label' => $item[$source['label_field']],
                            ];
                        });
                        break;
                    // Add more cases for other models as needed
                }
            }
        }

        return Inertia::render('SystemSettings/Index', [
            'groups' => $groups,
            'settingsConfig' => $settingsConfig,
            'settingsValues' => $settingsValues,
            'dynamicData' => $dynamicData,
        ]);
    }

    /**
     * Update the settings.
     */
    public function update(Request $request)
    {
        // Get validation rules from settings configuration
        $rules = Settings::getValidationRules();

        // Validate the request
        $validated = $request->validate($rules);

        // Update each setting
        foreach ($validated as $key => $value) {
            Settings::set($key, $value);
        }

        // Clear settings cache
        Setting::clearCache();

        return redirect()->route('system-settings.index')
            ->with('success', 'System Settings updated successfully.');
    }
}
