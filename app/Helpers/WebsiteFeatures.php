<?php

namespace App\Helpers;

class WebsiteFeatures
{
    /**
     * Determine if the given feature is enabled.
     *
     * @return bool
     */
    public static function enabled(string $feature)
    {
        return in_array($feature, config('features.website', []));
    }

    /**
     * Determine if the feature is enabled and has a given option enabled.
     *
     * @return bool
     */
    public static function optionEnabled(string $feature, string $option)
    {
        return static::enabled($feature) &&
            config("fortify-options.{$feature}.{$option}") === true;
    }

    /**
     * Determine if the application is using any features that require "profile" management.
     *
     * @return bool
     */
    public static function hasProfileFeatures()
    {
        return static::enabled(static::updateProfileInformation()) ||
            static::enabled(static::updatePasswords());
    }

    /**
     * Determine if the application can update a user's profile information.
     *
     * @return bool
     */
    public static function canUpdateProfileInformation()
    {
        return static::enabled(static::updateProfileInformation());
    }

    /**
     * Determine if the application is using any security profile features.
     *
     * @return bool
     */
    public static function hasSecurityFeatures()
    {
        return static::enabled(static::updatePasswords());
    }

    /**
     * @return bool
     */
    public static function canLogin()
    {
        return static::enabled(static::login());
    }

    /**
     * @return bool
     */
    public static function canRegister()
    {
        return static::enabled(static::registration());
    }

    /**
     * @return bool
     */
    public static function canUpdatePasswords()
    {
        return static::enabled(static::updatePasswords());
    }

    /**
     * @return bool
     */
    public static function canResetPasswords()
    {
        return static::enabled(static::resetPasswords());
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
    public static function canDeleteAccount()
    {
        return static::enabled(static::deleteAccount());
    }

    /**
     * Enable the registration feature.
     *
     * @return string
     */
    public static function login()
    {
        return 'login';
    }

    /**
     * Enable the registration feature.
     *
     * @return string
     */
    public static function registration()
    {
        return 'registration';
    }

    /**
     * Enable the password reset feature.
     *
     * @return string
     */
    public static function resetPasswords()
    {
        return 'reset-passwords';
    }

    /**
     * Enable the email verification feature.
     *
     * @return string
     */
    public static function emailVerification()
    {
        return 'email-verification';
    }

    /**
     * Enable the update profile information feature.
     *
     * @return string
     */
    public static function updateProfileInformation()
    {
        return 'update-profile-information';
    }

    /**
     * Enable the update password feature.
     *
     * @return string
     */
    public static function updatePasswords()
    {
        return 'update-passwords';
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
}
