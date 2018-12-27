<?php

namespace Feugene\Files\Http;

use Feugene\Files\Support\Store;
use Feugene\Files\Types\BaseFile;
use Illuminate\Support\Collection;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\File\UploadedFile;

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
     * @var bool
     */
    public $uniqueFileName = true;

    /**
     * @return \Illuminate\Support\Collection|UploadedFile[]
     */
    protected static function getUploadedFiles(): Collection
    {
        $files = request()->file('file');

        if ($files instanceof UploadedFile) {
            $files = [$files];
        }

        return collect($files);
    }

    /**
     * @return string
     */
    protected function getUploadFolder(): string
    {
        if ($this->path === null) {
            $this->path = request('path', '');
        }

        return $this->path;
    }

    /**
     * @param string $path
     *
     * @return \Feugene\Files\Http\UploadTrait
     */
    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getDriver(): string
    {
        return request('driver', $this->driver);
    }

    /**
     * @param string $name
     *
     * @return \Feugene\Files\Http\UploadTrait
     */
    public function setDriver(string $name): self
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
            return $uploadFile->getClientOriginalName();
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

        return new BaseFile($file->getRealPath());
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
