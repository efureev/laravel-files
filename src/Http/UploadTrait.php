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
     * @return string
     */
    protected function getDriver(): string
    {
        return request('driver', $this->driver);
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

    /* protected $directory;
     protected $fileType;

     abstract public function upload();

     protected function initDirectory(): void
     {
         $this->directory = request()->input('directory');
         upload()->makeDirectoryIfNotExists($this->directory);
     }

     protected function getDirectory()
     {
         if ($this->directory === null) {
             $this->initDirectory();
         }

         return $this->directory;
     }

     protected function initFileType(): void
     {
         $this->fileType = request()->input('fileType') ?? 'default';
     }

     protected function getFileType()
     {
         if ($this->fileType === null) {
             $this->initFileType();
         }

         return $this->fileType;
     }

     protected function getUploadedFiles()
     {
         $files = request()->file('file');

         if ($files instanceof UploadedFile) {
             $files = [$files];
         }

         return collect($files)->map(function ($uploadedFile) {
             $file = new File;
             $file->setBaseFile($uploadedFile);

             return $file;
         });
     }

     protected function uploadFileToDirectory($file)
     {
         $uploadPath = upload()->path($this->getDirectory());

         $fileBasename = $file->getBaseFile() instanceof UploadedFile ? $file->getClientOriginalName() : $file->getBasename();

         $this->renameIfExists($uploadPath, $fileBasename);

         $file->move($uploadPath . '/' . $fileBasename);

         chmod($file->getPath(), 0644);

         return $file;
     }

     protected function getValidExtensions()
     {
         $fileType = $this->getFileType();

         $types = config('files.types');

         $validExtensions = [];

         if (isset($types[$fileType]['extensions'])) {
             $validExtensions = $types[$fileType]['extensions'];
         } else {
             foreach ($types as $key => $type) {
                 $validExtensions = array_merge($validExtensions, $type['extensions']);
             }
         }

         return $validExtensions;
     }

     protected function validateFilesExt($files, $extensions): bool
     {
         if ($extensions) {
             foreach ($files as $file) {
                 if (! in_array($file->getClientOriginalExtension(), $extensions)) {
                     return false;
                 }
             }
         }

         return true;
     }

     protected function renameIfExists($uploadPath, &$fileBasename): void
     {
         // без задания локали pathinfo() обрезает начало в имени файла на латинице
         $oldLocale = setlocale(LC_CTYPE, 0);
         setlocale(LC_CTYPE, 'ru_RU.utf8');

         $fileName = pathinfo($fileBasename, PATHINFO_FILENAME);
         $fileExt  = pathinfo($fileBasename, PATHINFO_EXTENSION);
         $counter  = 0;

         setlocale(LC_CTYPE, $oldLocale);

         while (file_exists($uploadPath . '/' . $fileBasename)) {
             $fileBasename = $fileName . '(' . (++$counter) . ')';

             if ($fileExt) {
                 $fileBasename .= '.' . $fileExt;
             }
         }
     }*/
}
