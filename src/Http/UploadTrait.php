<?php

namespace Feugene\Files\Http;

use Feugene\Files\Support\Store;
use Feugene\Files\Types\BaseFile;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Trait UploadTrait
 *
 * @package Feugene\Files\Http
 */
trait UploadTrait
{
    /**
     * Path to upload
     *
     * @var string
     */
    protected $path;

    /** @var string */
    protected $driver = 'local';

    /**
     * @var null|array|\Illuminate\Http\UploadedFile
     */
    protected $uploadFiles;

    /**
     * @var bool
     */
    public $uniqueFileName = true;

    /**
     * @return \Illuminate\Support\Collection|UploadedFile[]
     */
    protected function getUploadedFiles(): Collection
    {
        $files = $this->getUploadedFilesFromSource();

        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        return collect($files);
    }

    /**
     * @return array|\Illuminate\Http\UploadedFile|null
     */
    protected function getUploadedFilesFromSource()
    {
        if ($this->uploadFiles === null) {
            $this->uploadFiles = request()->file('file');
        }

        return $this->uploadFiles;
    }

    /**
     * @param UploadedFile|array $files
     *
     * @return $this
     */
    public function setUploadedFiles($files)
    {
        $this->uploadFiles = $files;

        return $this;
    }

    /**
     * @return string
     */
    protected function getUploadFolder(): string
    {
        if ($this->path === null) {
            $this->path = (string)request('path', '');
        }

        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return $this
     */
    public function setPath(string $path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return (string)$this->path;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return (string)request('driver', $this->driver);
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setDriver(string $name)
    {
        $this->driver = $name;

        return $this;
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadFile
     *
     * @return string
     * @throws \Exception
     */
    protected function getFileName(UploadedFile $uploadFile): string
    {
        if (!$this->uniqueFileName) {
            return (string)$uploadFile->getClientOriginalName();
        }

        return Uuid::uuid4()->toString() . '.' . $uploadFile->getClientOriginalExtension();
    }


    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $uploadFile
     *
     * @return \Feugene\Files\Types\BaseFile
     * @throws \Exception
     */
    protected function uploadFileToFolder(UploadedFile $uploadFile): BaseFile
    {
        $uploadFolder = Store::pathToStorage($this->getUploadFolder(), $this->getDriver());

        $fileName = $this->getFileName($uploadFile);

        $fileName = static::renameIfExists($uploadFolder, $fileName);

        $file = $uploadFile->move($uploadFolder, $fileName);

        return new BaseFile((string)$file->getRealPath());
    }

    /**
     * @param string $uploadPath
     * @param string $fileBasename
     *
     * @return string
     */
    private static function renameIfExists(string $uploadPath, string $fileBasename): string
    {
        if (!file_exists($uploadPath . \DIRECTORY_SEPARATOR . $fileBasename)) {
            return $fileBasename;
        }

        $fileName = pathinfo($fileBasename, PATHINFO_FILENAME);
        $fileExt = pathinfo($fileBasename, PATHINFO_EXTENSION);

        $counter = 0;

        while (file_exists($uploadPath . \DIRECTORY_SEPARATOR . $fileBasename)) {
            $fileBasename = $fileName . '_' . (++$counter);

            if ($fileExt) {
                $fileBasename .= '.' . $fileExt;
            }
        }

        return $fileBasename;
    }
}
