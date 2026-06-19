<?php

namespace Ajangsupardi\PostcodeId;

use Ajangsupardi\PostcodeId\Console\Commands\DownloadPostcode;
use Ajangsupardi\PostcodeId\Services\PostcodeParser;
use Illuminate\Support\ServiceProvider;

class PostcodeIdServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/postcode.php', 'postcode');

        $this->publishes([
            __DIR__.'/../config/postcode.php' => config_path('postcode.php'),
        ], 'postcode-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations'),
        ], 'postcode-migrations');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                DownloadPostcode::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->app->bind(PostcodeParser::class, function () {
            return new PostcodeParser();
        });
    }
}
