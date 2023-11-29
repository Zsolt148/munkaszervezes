<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    /**
     * web or admin
     */
    protected string $guard = 'web';

    public function setUp(): void
    {
        parent::setUp();

        $this->seed();

        // Must need this because a bug:
        //Illuminate\Database\Eloquent\MissingAttributeException : The attribute [profile_photo_path] either does not exist or was not retrieved for model [App\Models\User]
        Model::preventAccessingMissingAttributes(false);
    }

    /**
     * Set the currently logged in user for the application.
     *
     * @param  string|null  $guard
     * @return $this
     */
    public function actingAs(UserContract $user, $guard = null)
    {
        return $this->be($user, $guard ?: $this->guard);
    }
}
