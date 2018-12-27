<?php

namespace Feugene\Files\Tests;

use AvtoDev\DevTools\Tests\PHPUnit\AbstractLaravelTestCase;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Storage;

abstract class AbstractTestCase extends AbstractLaravelTestCase
{
    use WithFaker;

    use DatabaseMigrations;

    protected function afterApplicationBootstrapped(Application $app)
    {
        $app->register(\Feugene\Files\ServiceProvider::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $driver = Storage::disk(config('filesystems.default'));

        $driver->getAdapter()->setPathPrefix(realpath(__DIR__ . '/../storage'));

        $this->app->useStoragePath(Storage::disk(config('filesystems.default'))->path(''));
    }

    /**
     * @param string $path
     * @param int    $mode
     *
     * @return string|null
     */
    protected static function getOrCreateFilePath(string $path, $mode = 0777): ?string
    {
        if (!is_dir($path)) {
            if (!mkdir($path, $mode, true)) {
                return null;
            }
        }

        return $path;
    }
}
