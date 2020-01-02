<?php

namespace Tests;

use Tests\TestCase;

class TestModelSR extends TestCase
{

    public function test()
    {
        $this->artisan('produce:model_sr AdminConfig --table=admin_config   --route=web');
        $controller = $this->app->make('App\Http\Controllers\Admin\ConfigController');
        $this->get('/admin/config')->assertOk();
    }

}