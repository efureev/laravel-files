<?php

namespace Feugene\Files\Contracts;

/**
 * Interface UploadService
 *
 * @package Feugene\Files\Contracts
 * @mixin \Feugene\Files\Services\UploadService
 */
interface UploadService
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function upload();

    /**
     * @param string|array $actions
     * @param string       $type
     *
     * @return \Feugene\Files\Contracts\UploadService
     * @throws \Php\Support\Exceptions\InvalidParamException
     */
    public function setAction($actions, string $type): self;
}
