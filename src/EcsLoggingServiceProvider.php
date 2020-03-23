<?php

namespace AviationCode\EcsLogging;

use AviationCode\EcsLogging\Commands\LogOutputDecorator;
use AviationCode\EcsLogging\Types\Process;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\OutputStyle;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Log\LogManager;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EcsLoggingServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /** @var LogManager $log */
        $log = $this->app['log'];
        $log->extend('ecs', function (Application $app, array $config) {
            return new EcsLogger($config);
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('ecs-logging.php'),
            ], 'config');

            Event::listen(CommandStarting::class, Process::onCommandStarting());
            Event::listen(CommandFinished::class, Process::onCommandFinished());

            if (Config::get('ecs-logging.formatter.commands.redirect_output', false)) {
                $this->app->bind(OutputStyle::class, LogOutputDecorator::class);
            }
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'ecs-logging');
    }
}
