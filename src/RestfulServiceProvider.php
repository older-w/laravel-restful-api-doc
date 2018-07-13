<?php
/**
 * Created by PhpStorm.
 * User: white
 * Date: 7/13/18
 * Time: 10:02 AM
 */

namespace OlderW\RestfulDoc;

use Illuminate\Support\ServiceProvider;

class RestfulServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        'OlderW\RestfulDoc\Console\ApiCommand',
    ];

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__.'/../config' => config_path()], 'laravel-restfuldoc-config');
        }
    }
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->loadAdminAuthConfig();

        $this->commands($this->commands);
    }
    /**
     * Setup auth configuration.
     *
     * @return void
     */
    protected function loadAdminAuthConfig()
    {
        config(array_dot(config('restfulapi.auth', []), 'auth.'));
    }

}