<?php

namespace App\Helpers;

use Laravel\Fortify\Features as BaseFeatures;

class AdminFeatures extends BaseFeatures
{
    /**
     * Determine if the given feature is enabled.
     *
     * @return bool
     */
    public static function enabled(string $feature)
    {
        return in_array($feature, config('features.admin', []));
    }

    /**
     * Determine if the application is using any features that require "profile" management.
     *
     * @return bool
     */
    public static function hasProfileFeatures()
    {
        return static::enabled(static::updateProfileInformation()) ||
            static::enabled(static::updatePasswords()) ||
            static::enabled(static::twoFactorAuthentication());
    }

    /**
     * Determine if the application is using any security profile features.
     *
     * @return bool
     */
    public static function hasSecurityFeatures()
    {
        return static::enabled(static::updatePasswords()) ||
            static::canManageTwoFactorAuthentication() ||
            static::canDeleteAccount();
    }

    /**
     * @return bool
     */
    public static function hasEmailVerification()
    {
        return static::enabled(static::emailVerification());
    }

    /**
     * @return bool
     */
    public static function canRegister()
    {
        return static::enabled(static::registration());
    }

    /**
     * Determine if the application can manage two factor authentication.
     *
     * @return bool
     */
    public static function canUpdatePasswords()
    {
        return static::enabled(static::updatePasswords());
    }

    /**
     * @return bool
     */
    public static function canDeleteAccount()
    {
        return static::enabled(static::deleteAccount());
    }

    /**
     * @return bool
     */
    public static function hasLogs()
    {
        return static::enabled(static::logs());
    }

    /**
     * Enable the delete account feature.
     *
     * @return string
     */
    public static function deleteAccount()
    {
        return 'delete-account';
    }

    /**
     * @return string
     */
    public static function logs()
    {
        return 'logs';
    }
}
