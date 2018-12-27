<?php

namespace Feugene\Files\Http\Controllers;

use Feugene\Files\Contracts\UploadService;
use Feugene\Files\Http\AuthorizeTrait;
use Feugene\Files\Http\UploadTrait;
use Feugene\Files\Http\VerifyTrait;
use Feugene\Files\Models\File;
use Feugene\Files\Services\AfterModelAction;
use Feugene\Files\Services\BeforeBaseAction;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

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

        $list = app(UploadService::class)
            ->setAction(BeforeBaseAction::class, 'before')
            ->setAfterAction(AfterModelAction::class)
            ->upload();

        return $this->getResponse($list);
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
