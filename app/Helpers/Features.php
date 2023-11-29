<?php

namespace App\Helpers;

class Features
{
    /**
     * Determine if the given feature is enabled.
     *
     * @return bool
     */
    public static function enabled(string $feature)
    {
        return in_array($feature, config('features.website-admin', []));
    }

    /**
     * @return bool
     */
    public static function hasGallery()
    {
        return static::enabled(static::gallery());
    }

    /**
     * @return bool
     */
    public static function hasMenus()
    {
        return static::enabled(static::menus());
    }

    /**
     * @return bool
     */
    public static function hasPages()
    {
        return static::enabled(static::pages());
    }

    /**
     * @return string
     */
    public static function gallery()
    {
        return 'gallery';
    }

    /**
     * @return string
     */
    public static function menus()
    {
        return 'menus';
    }

    /**
     * @return string
     */
    public static function pages()
    {
        return 'pages';
    }
}
