<?php

namespace Tests;

class AdminTestCase extends TestCase
{
    protected string $guard = 'admin';

    public function setUp(): void
    {
        parent::setUp();
    }
}
