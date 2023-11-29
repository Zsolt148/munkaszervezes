<?php

use App\Helpers\AdminFeatures;
use App\Helpers\Features;
use App\Helpers\WebsiteFeatures;

return [

    /*
    |--------------------------------------------------------------------------
    | Website and Admin features
    |--------------------------------------------------------------------------
    |
    | Some of the features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features, or you can even remove all of these if you need to.
    |
    */

    'website-admin' => [
        //        Features::gallery(),
        //        Features::menus(),
        //        Features::pages(),
    ],

    /*
    |--------------------------------------------------------------------------
    | WebsiteFeatures
    |--------------------------------------------------------------------------
    |
    | Some of the features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features, or you can even remove all of these if you need to.
    |
    */

    'website' => [
        //        WebsiteFeatures::login(),
        //        WebsiteFeatures::registration(),
        //        WebsiteFeatures::resetPasswords(),
        //        WebsiteFeatures::emailVerification(), // Must implement Illuminate\Contracts\Auth\MustVerifyEmail to Website model
        //        WebsiteFeatures::updateProfileInformation(),
        //        WebsiteFeatures::updatePasswords(),
        //        WebsiteFeatures::deleteAccount(),
    ],

    /*
    |--------------------------------------------------------------------------
    | AdminFeatures
    |--------------------------------------------------------------------------
    |
    | Some of the features are optional. You may disable the features
    | by removing them from this array. You're free to only remove some of
    | these features, or you can even remove all of these if you need to.
    |
    */

    'admin' => [
        //        AdminFeatures::registration(),
        AdminFeatures::resetPasswords(),
        AdminFeatures::emailVerification(), // Must implement Illuminate\Contracts\Auth\MustVerifyEmail to Admin model
        AdminFeatures::updateProfileInformation(),
        AdminFeatures::updatePasswords(),
        AdminFeatures::twoFactorAuthentication(),
        AdminFeatures::deleteAccount(),

        AdminFeatures::logs(),
    ],
];
