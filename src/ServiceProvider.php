<?php

namespace Feugene\Files;

use Feugene\Files\Contracts\UploadService;
use Feugene\Files\Models\File;
use Feugene\Files\Observers\FileObserver;
use Illuminate\Database\Eloquent\Factory;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Class ServiceProvider
 *
 * @package Feugene\Files
 */
class ServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(UploadService::class, \Feugene\Files\Services\UploadService::class);
    }

    public function boot(): void
    {
        $this->mergeConfigFrom(self::path('config/files.php'), 'files');

        $this->loadMigrationsFrom(self::path('database/migrations'));

        if ($this->app->environment() !== 'production') {
            $this->app->make(Factory::class)->load(self::path('database/factories'));
        }

        File::observe(FileObserver::class);

        $this->registerPolicies();
    }

    private static function path($path)
    {
        return __DIR__ . '/../' . ltrim($path, '/');
    }

    public function registerPolicies()
    {
        $policies = config('files.policy', []);
        if (is_string($policies)) {
            $policies = [$policies];
        }

        foreach ($policies as $value) {
            Gate::policy(File::class, $value);
        }
    }
}
