<?php

namespace Wealook\Generate;

//use Wealook\Console\Commands\MakeCode;
//use Wealook\Console\Commands\MakeModel;
use Illuminate\Support\ServiceProvider;

class GenerateServiceProvider extends  ServiceProvider {
    /**
     * @var array
     */
    protected $commands = [
//       MakeCode::class,
        Console\Commands\ProduceModel::class,
    ];


    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands($this->commands);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }
}


