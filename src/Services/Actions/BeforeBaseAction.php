<?php

namespace Feugene\Files\Services\Actions;

use Feugene\Files\Contracts\BeforeUploadAction;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class BeforeBaseAction
 *
 * @package Feugene\Files\Services
 */
class BeforeBaseAction implements BeforeUploadAction
{
    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return \Symfony\Component\HttpFoundation\File\UploadedFile|null
     */
    public function handle(UploadedFile $file): ?UploadedFile
    {
        return $file;
    }
}
