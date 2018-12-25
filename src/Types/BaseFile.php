<?php

namespace Feugene\Files\Types;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Class BaseFile
 *
 * @package Feugene\Files\Types
 */
class BaseFile extends File
{
    /**
     * @return bool
     */
    public function isExists(): bool
    {
        return file_exists($this->getPathname());
    }

    /**
     * Copy the file to a new location.
     *
     * @param string      $directory
     * @param string|null $name
     *
     * @return self A File object representing the new file
     */
    public function copy(string $directory, string $name = null): BaseFile
    {
        $target = $this->getTargetFile($directory, $name);

        set_error_handler(function ($type, $msg) use (&$error) {
            $error = $msg;
        });
        $renamed = copy($this->getPathname(), $target);
        restore_error_handler();
        if (!$renamed) {
            throw new FileException(sprintf('Could not copy the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error)));
        }

        @chmod($target, 0666 & ~umask());

        return $target;
    }

    /**
     * @param string $directory
     * @param null   $name
     *
     * @return self
     */
    protected function getTargetFile($directory, $name = null)
    {
        if (!is_dir($directory)) {
            if (false === @mkdir($directory, 0777, true) && !is_dir($directory)) {
                throw new FileException(sprintf('Unable to create the "%s" directory', $directory));
            }
        } elseif (!is_writable($directory)) {
            throw new FileException(sprintf('Unable to write in the "%s" directory', $directory));
        }

        $target = rtrim($directory, '/\\') . \DIRECTORY_SEPARATOR . (null === $name ? $this->getBasename() : $this->getName($name));

        return new static($target, false);
    }

    /**
     * @return bool
     */
    public function remove(): bool
    {
        return !@unlink($this);
    }

}
