<?php

namespace Tests;

use Database\Seeders\BaseRoles;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Seed the base roles whenever the test database is refreshed.
     */
    protected bool $seed = true;

    /**
     * @var class-string
     */
    protected string $seeder = BaseRoles::class;
}
