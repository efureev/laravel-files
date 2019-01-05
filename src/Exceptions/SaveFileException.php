<?php

namespace Feugene\Files\Exceptions;

/**
 * Class SaveFileException
 *
 * @package Feugene\Files\Exceptions
 */
class SaveFileException extends \Exception
{

    /**
     * SaveFileException constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        parent::__construct('Can not save file: ' . $path);
    }
}
