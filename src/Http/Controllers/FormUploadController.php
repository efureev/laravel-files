<?php

namespace Feugene\Files\Http\Controllers;

use Feugene\Files\Contracts\UploadService;
use Feugene\Files\Http\AuthorizeTrait;
use Feugene\Files\Models\File;
use Feugene\Files\Services\Actions\AfterModelAction;
use Illuminate\Routing\Controller as BaseController;

/**
 * Class FormUploadController
 *
 * @package Feugene\Files\Http\Controllers
 */
class FormUploadController extends BaseController
{
    use AuthorizeTrait;

    /**
     * @return array
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Exception
     * @codeCoverageIgnore
     */
    public function upload()
    {
        $this->authorizeAction('upload');

        $list = app(UploadService::class)
            ->setAfterAction(AfterModelAction::class)
            ->upload();

        return $this->getResponse($list);
    }


    /**
     * @param \Illuminate\Support\Collection|array $data
     * @param bool                                 $status
     *
     * @return array
     * @codeCoverageIgnore
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
     * @codeCoverageIgnore
     */
    public function delete(string $id)
    {
        $this->authorizeAction('delete');

        /** @var File $file */
        if ($file = File::find($id)) {
            return $this->getResponse($file, $file->delete());
        }

        return $this->getResponse([], false);
    }
}
