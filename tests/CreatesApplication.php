<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

//        $app->booting(function () {
//            $loader = \Illuminate\Foundation\AliasLoader::getInstance();
//            $loader->alias('Admin', \Encore\Admin\Facades\Admin::class);
//        });

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        $app->register('Wealook\Generate\GenerateServiceProvider');

        return $app;
    }
}
