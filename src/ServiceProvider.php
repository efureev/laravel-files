<?php

namespace Feugene\Files;

use Feugene\Files\Models\File;
use Feugene\Files\Observers\FileObserver;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

    public function register(): void
    {

    }

    public function boot(): void
    {

        $this->app->config->set('files', require self::path('config/files.php'));


        $this->loadMigrationsFrom(self::path('database/migrations'));

        if ($this->app->environment() !== 'production') {
            $this->app->make(Factory::class)->load(self::path('database/factories'));
        }

        File::observe(FileObserver::class);

    }

    private static function path($path)
    {
        return __DIR__ . '/../' . ltrim($path, '/');
    }
}
