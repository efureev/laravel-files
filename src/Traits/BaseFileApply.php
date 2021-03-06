<?php

namespace Feugene\Files\Traits;


use Feugene\Files\Support\Store;
use Feugene\Files\Types\BaseFile;
use Illuminate\Support\Facades\Storage;

/**
 * Trait BaseFileApply
 *
 * @package Feugene\Files\Traits
 */
trait BaseFileApply
{
    /**
     * @var \Feugene\Files\Types\BaseFile
     */
    protected $baseFile;

    /**
     * @return string
     */
    public function getAbsolutePath(): string
    {
        return (string)$this->getBaseFile()->getRealPath();
    }

    /**
     * @return string
     */
    public function getRelativePath(): string
    {
        $prefix = $this->pathToModelStorage();
        if (starts_with((string)$this->getBaseFile()->getRealPath(), $prefix)) {
            return preg_replace('#^' . preg_quote($prefix) . '/*#', '', (string)$this->getBaseFile()->getRealPath());
        }

        return (string)$this->getBaseFile()->getRealPath();
    }


    /**
     * @param string $path
     *
     * @return string
     */
    public function pathToModelStorage(string $path = ''): string
    {
        return (string)realpath(Storage::disk($this->getDriver())->path($path));
    }


    /**
     * @return string
     */
    protected function getDriver(): string
    {
        return !empty($this->driver) ? $this->driver : config('filesystems.driverDefault', 'local');
    }

    /**
     * @return \Feugene\Files\Types\BaseFile
     */
    public function getBaseFile(): BaseFile
    {
        if (!$this->baseFile && !empty($this->path)) {
            $this->setBaseFileFromString($this->path, false);
        }

        return $this->baseFile;
    }


    /**
     * @return bool
     */
    public function hasBaseFile(): bool
    {
        return $this->baseFile !== null;
    }

    /**
     * @param \Feugene\Files\Types\BaseFile $file
     * @param bool                          $updateModel
     *
     * @return $this
     */
    public function setBaseFile(BaseFile $file, bool $updateModel = true): self
    {
        $this->baseFile = $file;

        if ($updateModel) {
            $this->updateAttributes();
        }

        return $this;
    }

    /**
     * @param string $path
     * @param bool   $updateModel
     *
     * @return $this
     */
    protected function setBaseFileFromString(string $path, bool $updateModel = true): self
    {
        if (!Store::isAbsolutePath($path)) {
            $path = $this->pathToModelStorage($path);
        }

        $this->setBaseFile(new BaseFile($path), $updateModel);

        return $this;
    }

    /**
     *
     */
    public function updateAttributes(): void
    {
        $this->attributes['path'] = $this->getRelativePath();

        $this->ext = $this->getBaseFile()->getExtension();
        $this->driver = $this->getDriver();

        if ($this->getBaseFile()->isExists()) {
            clearstatcache();
            $this->size = $this->getBaseFile()->getSize();
            $this->mime = $this->getBaseFile()->getMimeType();
        }
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public static function fromAbsolutePath(string $path): self
    {
        return (new static)->setBaseFile(new BaseFile($path));
    }

}
