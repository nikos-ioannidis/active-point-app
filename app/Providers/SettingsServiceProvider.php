<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register a singleton for settings manager
        $this->app->singleton('settings', function ($app) {
            return new class {
                protected $config;

                public function __construct()
                {
                    $this->loadConfig();
                }

                protected function loadConfig()
                {
                    $path = config_path('settings.json');
                    if (File::exists($path)) {
                        $this->config = json_decode(File::get($path), true);
                    } else {
                        $this->config = [];
                    }
                }

                public function all()
                {
                    return $this->config;
                }

                public function get($key, $default = null)
                {
                    return Setting::get($key, $this->getDefaultValue($key, $default));
                }

                public function set($key, $value)
                {
                    return Setting::set($key, $value);
                }

                public function getConfig($key = null)
                {
                    if ($key === null) {
                        return $this->config;
                    }

                    foreach ($this->config as $item) {
                        if ($item['key'] === $key) {
                            return $item;
                        }
                    }

                    return null;
                }

                public function getDefaultValue($key, $default = null)
                {
                    $config = $this->getConfig($key);
                    return $config ? $config['default'] : $default;
                }

                public function getByGroup($group)
                {
                    return array_filter($this->config, function ($item) use ($group) {
                        return $item['group'] === $group;
                    });
                }

                public function getGroups()
                {
                    $groups = [];
                    foreach ($this->config as $item) {
                        if (!in_array($item['group'], $groups)) {
                            $groups[] = $item['group'];
                        }
                    }
                    return $groups;
                }

                public function getValidationRules()
                {
                    $rules = [];
                    foreach ($this->config as $item) {
                        $rules[$item['key']] = $item['validation'] ?? '';
                    }
                    return $rules;
                }
            };
        });

        // Register a facade
        $this->app->bind('settings.facade', function ($app) {
            return $app['settings'];
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
