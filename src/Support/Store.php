<?php

namespace Feugene\Files\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Class Store
 *
 * @package Feugene\Files\Support
 */
class Store
{
    /**
     * Path to base storage
     *
     * @param string      $path
     * @param string|null $driver
     *
     * @return string
     */
    public static function pathToStorage(string $path = '', string $driver = null): string
    {
        if (empty($driver)) {
            $driver = config('filesystems.default', 'local');
        }

        return Storage::disk($driver)->path($path);
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    public static function isAbsolutePath(string $path): bool
    {
        return mb_substr($path, 0, 1) === \DIRECTORY_SEPARATOR;
    }
}
