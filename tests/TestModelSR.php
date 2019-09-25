<?php

namespace Tests;

use Tests\TestCase;

class TestModelSR extends TestCase
{

    public function test()
    {
        $this->artisan('produce:model_sr Admin\\\Config --table=admin_config ');
        $menu = $this->app->make('App\Models\Admin\Config');
    }

}