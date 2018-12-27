<?php

namespace Feugene\Files\Services;

use Feugene\Files\Contracts\AfterUploadAction;
use Feugene\Files\Contracts\BeforeUploadAction;
use Feugene\Files\Contracts\UploadService as UploadServiceContract;
use Feugene\Files\Exceptions\MissingFilesToUploadException;
use Feugene\Files\Http\UploadTrait;
use Feugene\Files\Http\VerifyTrait;
use Feugene\Files\Types\BaseFile;
use Illuminate\Support\Collection;
use Php\Support\Exceptions\InvalidParamException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadService
 *
 * @package Feugene\Files\Services
 */
class UploadService implements UploadServiceContract
{
    use UploadTrait, VerifyTrait;

    /**
     * @var array
     */
    protected $beforeUploadActions = [];
    /**
     * @var array
     */
    protected $afterUploadActions = [];

    /**
     * @return \Illuminate\Support\Collection
     */
    public function upload()
    {
        if (($uploadedFiles = $this->getUploadedFiles())->isEmpty()) {
            throw new MissingFilesToUploadException;
        }

        $this->verifyExtensions($uploadedFiles);

        return $this->generateFileList($uploadedFiles);
    }

    /**
     * @param \Illuminate\Support\Collection $uploadedFiles
     *
     * @return \Illuminate\Support\Collection
     */
    protected function generateFileList(Collection $uploadedFiles)
    {
        return $uploadedFiles->map(function ($uploadFile) {
            $uploadFile = $this->beforeUploadFile($uploadFile);

            $baseFile = $this->uploadFileToFolder($uploadFile);

            return $this->afterUploadFile($baseFile);
        })->filter();
    }

    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile
     */
    private function beforeUploadFile(UploadedFile $file): UploadedFile
    {
        if (!$this->beforeUploadActions || !is_array($this->beforeUploadActions)) {
            return $file;
        }
        foreach ($this->beforeUploadActions as $action) {
            if ($action instanceof \Closure) {
                $file = $action($file);
            } elseif (is_string($action) && class_exists($action)) {
                $cls = new $action;

                if (!$cls instanceof BeforeUploadAction) {
                    throw new InvalidParamException('Invalid instance! Must have ' . BeforeUploadAction::class);
                }

                $file = $cls->handle($file);
            }
        }

        return $file;
    }

    /**
     * @param \Feugene\Files\Types\BaseFile $baseFile
     *
     * @return \Feugene\Files\Models\File|\Feugene\Files\Types\BaseFile|null
     */
    private function afterUploadFile(BaseFile $baseFile)
    {
        if (!$this->afterUploadActions || !is_array($this->afterUploadActions)) {
            return $baseFile;
        }

        foreach ($this->afterUploadActions as $action) {
            if ($action instanceof \Closure) {
                $baseFile = $action($baseFile);
            } elseif (is_string($action) && class_exists($action)) {
                $cls = new $action;

                if (!$cls instanceof AfterUploadAction) {
                    throw new InvalidParamException('Invalid instance! Must have ' . AfterUploadAction::class);
                }

                $baseFile = $cls->handle($baseFile);
            }
        }

        return $baseFile;
    }

    /**
     * @param string|array $actions
     * @param string       $type
     *
     * @return \Feugene\Files\Contracts\UploadService
     * @throws \Php\Support\Exceptions\InvalidParamException
     */
    public function setAction($actions, string $type): UploadServiceContract
    {
        if (is_string($actions) || $actions instanceof \Closure) {
            $actions = [$actions];
        } elseif (!is_array($actions)) {
            throw new InvalidParamException('Invalid params $type: must have be "after" or "before"');
        }

        foreach ($actions as $action) {
            switch ($type) {
                case 'before':
                    $this->beforeUploadActions[] = $action;
                    break;
                case 'after':
                    $this->afterUploadActions[] = $action;
                    break;
            }
        }

        return $this;
    }

    /**
     * @param string|array $actions
     *
     * @return \Feugene\Files\Contracts\UploadService
     */
    public function setBeforeAction($actions): UploadServiceContract
    {
        return $this->setAction($actions, 'before');
    }

    /**
     * @param string|array $actions
     *
     * @return \Feugene\Files\Contracts\UploadService
     */
    public function setAfterAction($actions): UploadServiceContract
    {
        return $this->setAction($actions, 'after');
    }

}
