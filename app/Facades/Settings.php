<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static mixed get(string $key, mixed $default = null)
 * @method static void set(string $key, mixed $value)
 * @method static array all()
 * @method static array|null getConfig(string $key = null)
 * @method static mixed getDefaultValue(string $key, mixed $default = null)
 * @method static array getByGroup(string $group)
 * @method static array getGroups()
 * @method static array getValidationRules()
 *
 * @see \App\Providers\SettingsServiceProvider
 */
class Settings extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'settings.facade';
    }
}
