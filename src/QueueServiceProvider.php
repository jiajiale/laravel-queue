<?php
namespace Jiajiale\LaravelQueue;

use Illuminate\Support\ServiceProvider;

class QueueServiceProvider extends ServiceProvider
{
    /**
     * If is defer.
     * @var bool
     */
    protected $defer = true;

    /**
     *  Boot the service.
     */
    public function boot()
    {
        $this->publishes([
            dirname(__DIR__).'/config/queue.php' => config_path('laravel-queue.php'), ],
            'laravel-queue'
        );
    }

    /**
     * Register the service.
     */
    public function register()
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/config/queue.php', 'laravel.queue');

        $this->app->singleton('laravel-queue', function () {
            return new Queue(config('laravel.queue'));
        });
    }
}