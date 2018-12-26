<?php

namespace Feugene\Files\Http;

use Feugene\Files\Exceptions\NotAllowFileTypeToUploadException;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\File\UploadedFile;

trait VerifyTrait
{
    /**
     * @param \Illuminate\Support\Collection $files
     */
    protected function verifyExtensions(Collection $files): void
    {
        $files->map(function (UploadedFile $file) {
            if (!static::allowFileType($ext = $file->getExtension())) {
                throw new NotAllowFileTypeToUploadException($ext);
            }
        });
    }

    /**
     * @param string $ext
     *
     * @return bool
     */
    protected static function allowFileType(string $ext): bool
    {
        return in_array($ext, static::disallowFileType());
    }

    /**
     * @return array
     */
    protected static function disallowFileType(): array
    {
        return ['exe'];
    }
}
