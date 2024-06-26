<?php

namespace Tests\Browser;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;

trait CreatesApplication
{
    /**
     * Creates the application.
     */
    public function createApplication(): Application
    {
        $app = require 'bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
