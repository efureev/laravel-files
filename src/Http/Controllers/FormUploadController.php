<?php

namespace Feugene\Files\Http\Controllers;

use Feugene\Files\Exceptions\MissingFilesToUploadException;
use Feugene\Files\Http\AuthorizeTrait;
use Feugene\Files\Http\UploadTrait;
use Feugene\Files\Http\VerifyTrait;
use Feugene\Files\Models\File;
use Feugene\Files\Types\BaseFile;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Collection;

class FormUploadController extends BaseController
{
    use AuthorizeTrait, UploadTrait, VerifyTrait, ValidatesRequests;

    /**
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     */
    public function upload()
    {
        $this->authorizeAction(__METHOD__);

        if (($uploadedFiles = $this->getUploadedFiles())->isEmpty()) {
            throw new MissingFilesToUploadException;
        }

        $this->verifyExtensions($uploadedFiles);

        return $this->getResponse($this->generateList($uploadedFiles));

    }

    /**
     * @param \Illuminate\Support\Collection $uploadedFiles
     *
     * @return \Illuminate\Support\Collection
     */
    protected function generateList(Collection $uploadedFiles)
    {
        return $uploadedFiles->map(function ($uploadFile) {
            $baseFile = $this->uploadFileToFolder($uploadFile);

            $list[] = $this->afterUpload($baseFile);
        })->filter();
    }

    /**
     * @param \Illuminate\Support\Collection|array $data
     * @param bool                                 $status
     *
     * @return array
     */
    protected function getResponse($data = [], bool $status = true)
    {
        return [
            'success' => $status,
            'data'    => $data,
        ];
    }

    /**
     * @param \Feugene\Files\Types\BaseFile $baseFile
     *
     * @return \Feugene\Files\Models\File|null
     */
    protected function afterUpload(BaseFile $baseFile)
    {
        if (($model = (new File)->setBaseFile($baseFile))->save()) {
            return $model;
        }

        return null;
    }


    /**
     * @param string $id
     *
     * @return array|\Illuminate\Http\JsonResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function delete(string $id)
    {
        $this->authorizeAction(__METHOD__);

        /** @var File $file */
        if ($file = File::find($id)) {

            return $this->getResponse($file, $file->delete());
        }

        return $this->getResponse([], false);
    }
}
