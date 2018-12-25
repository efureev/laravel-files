<?php

namespace Feugene\Files\Tests\Unit;

use Feugene\Files\Tests\AbstractTestCase;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Storage;

/**
 * Class AbstractUnitTestCase
 *
 * @package Fureev\Trees\Tests\Unit
 */
abstract class AbstractUnitTestCase extends AbstractTestCase
{
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

        $driver->getAdapter()->setPathPrefix(realpath(__DIR__ . '/../../storage'));

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
