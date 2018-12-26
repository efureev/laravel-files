<?php

namespace Feugene\Files\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class NotAllowFileTypeToUploadException
 *
 * @package Feugene\Files\Exceptions
 */
class NotAllowFileTypeToUploadException extends HttpException
{
    protected $extension;

    /**
     * NotAllowFileTypeToUploadException constructor.
     *
     * @param string $extension
     * @param string $message
     */
    public function __construct(string $extension = null, $message = 'This file type don\'t allow to upload')
    {
        $this->extension = $extension;
        parent::__construct(403, $extension ? ($message . ': ' . $extension) : $message);
    }
}
