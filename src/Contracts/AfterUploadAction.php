<?php

namespace Feugene\Files\Contracts;

use Feugene\Files\Types\BaseFile;

/**
 * Interface AfterUploadAction
 *
 * @package Feugene\Files\Contracts
 */
interface AfterUploadAction
{
    /**
     * @param \Feugene\Files\Types\BaseFile $file
     *
     * @return \Feugene\Files\Types\BaseFile|\Illuminate\Database\Eloquent\Model:null
     */
    public function handle(BaseFile $file);
}
