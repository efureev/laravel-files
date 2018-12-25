<?php

namespace Feugene\Files\Exceptions;

/**
 * Class MissingPathException
 *
 * @package Feugene\Files\Exceptions
 */
class MissingFilePathException extends \Exception
{

    /**
     * MissingPathException constructor.
     *
     * @param string $message
     */
    public function __construct($message = 'Missing path value for File')
    {
        parent::__construct($message);
    }
}
