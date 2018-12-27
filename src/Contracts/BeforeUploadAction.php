<?php

namespace Feugene\Files\Contracts;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Interface BeforeUploadAction
 *
 * @package Feugene\Files\Contracts
 */
interface BeforeUploadAction
{
    /**
     * @param \Symfony\Component\HttpFoundation\File\UploadedFile $file
     *
     * @return UploadedFile|null
     */
    public function handle(UploadedFile $file): ?UploadedFile;
}
