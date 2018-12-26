<?php

namespace Feugene\Files\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class MissingFilesToUploadException
 *
 * @package Feugene\Files\Exceptions
 */
class MissingFilesToUploadException extends HttpException
{
    /**
     * MissingFilesToUploadException constructor.
     *
     * @param string $message
     */
    public function __construct($message = 'Missing files to upload')
    {
        parent::__construct(418, $message);
    }
}
