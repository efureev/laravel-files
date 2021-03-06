<?php

namespace Feugene\Files\Http;

use Feugene\Files\Exceptions\NotAllowFileTypeToUploadException;
use Illuminate\Support\Collection;
use Php\Support\Exceptions\InvalidParamException;
use Symfony\Component\HttpFoundation\File\File;

/**
 * Trait VerifyTrait
 *
 * @package Feugene\Files\Http
 */
trait VerifyTrait
{
    /**
     * @var array
     */
    protected $disallowFileType = [
        'exe'
    ];

    /**
     * @param \Illuminate\Support\Collection $files
     */
    public function verifyExtensions(Collection $files): void
    {
        $files->map(function (File $file) {

            if ($file instanceof \Illuminate\Http\Testing\File) {
                $ext = pathinfo($file->name, PATHINFO_EXTENSION);
            } else {
                $ext = $file->getExtension();
            }

            if (!$this->allowFileType($ext)) {
                throw new NotAllowFileTypeToUploadException($ext);
            }
        });
    }

    /**
     * @param string $ext
     *
     * @return bool
     */
    protected function allowFileType(string $ext): bool
    {
        return !in_array($ext, $this->disallowFileType);
    }

    /**
     * @param string|array $types
     *
     * @return $this
     */
    public function setDisallowFileTypes($types)
    {
        if (is_string($types)) {
            $types = [$types];
        } elseif (!is_array($types)) {
            throw new InvalidParamException('Invalid params $types: must have be "string" or "array"');
        }

        $this->disallowFileType = array_flip(array_flip(array_merge($this->disallowFileType, $types)));

        return $this;
    }
}
