<?php

namespace Tests;

use Tests\TestCase;

class TestModel extends TestCase
{

    public function test()
    {
//        $this->mockConsoleOutput = false;
        $this->artisan('make:model sdf --table=sd')->expectsOutput('DB table must input  --table=table_name');
//        $this->assertFileExists(('routes.php'));

    }

}