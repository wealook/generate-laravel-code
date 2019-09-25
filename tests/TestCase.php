<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function setUp(): void
    {
        parent::setUp();

//        $adminConfig = require __DIR__.'/config/admin.php';

        $this->app['config']->set('database.default', 'mysql');
        $this->app['config']->set('database.connections.mysql.host', '172.16.10.130');
        $this->app['config']->set('database.connections.mysql.database', 'rasp');
        $this->app['config']->set('database.connections.mysql.port', '6033');
        $this->app['config']->set('database.connections.mysql.username', 'root');
        $this->app['config']->set('database.connections.mysql.password', 'root');
        $this->app['config']->set('database.connections.mysql.charset', 'utf8mb4');
        $this->app['config']->set('database.connections.mysql.collation', 'utf8mb4_unicode_ci');
        $this->app['config']->set('app.key', 'AckfSECXIvnK5r28GVIWUAxmbBSjTsmF');
//        $this->app['config']->set('filesystems', require __DIR__.'/config/filesystems.php');
//        $this->app['config']->set('admin', $adminConfig);

    }

}
