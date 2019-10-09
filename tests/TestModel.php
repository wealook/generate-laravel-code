<?php

namespace Tests;

use Tests\TestCase;

class TestModel extends TestCase
{

    public function test()
    {
        $this->artisan('produce:model Admin/Config --table=admin_config  --force')->expectsOutput('Model created successfully.');
        $menu = $this->app->make('App\Models\Admin\Config');
        $menu->get();
    }

}