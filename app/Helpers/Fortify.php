<?php

namespace App\Helpers;

use Laravel\Fortify\Fortify as BaseFortify;

class Fortify extends BaseFortify
{
    /**
     * Indicates if Fortify routes will be registered.
     *
     * @var bool
     */
    public static $registersRoutes = false;
}
