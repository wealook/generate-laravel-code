<?php

namespace Tests;

use Tests\TestCase;

class TestModelSR extends TestCase
{

    public function test()
    {
//        $this->artisan('produce:model_sr Admin\\\Config --table=admin_config   --route=web');
//        $controller = $this->app->make('App\Http\Controllers\Admin\ConfigController');
//        $this->get('/admin/config')->assertOk();

        $this->artisan('produce:model_sr Admin\\\Config --table=admin_config   --route=web --view=config');
        $controller = $this->app->make('App\Http\Controllers\Admin\ConfigController');
        $this->get('/admin/config')->assertOk();
    }

}