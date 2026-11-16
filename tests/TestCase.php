<?php

declare(strict_types=1);

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function createApplication(): Application
    {
        // Rector's scoped autoloader can register ahead of the app's, breaking Laravel's base-path inference.
        $_ENV['APP_BASE_PATH'] = dirname(__DIR__);

        return parent::createApplication();
    }
}
