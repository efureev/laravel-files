<?php

namespace Feugene\Files\Services\Actions;

use Feugene\Files\Contracts\AfterUploadAction;
use Feugene\Files\Models\File;
use Feugene\Files\Types\BaseFile;

/**
 * Class AfterModelListAction
 *
 * @package Feugene\Files\Services
 */
class AfterModelAction implements AfterUploadAction
{
    /**
     * @param \Feugene\Files\Types\BaseFile $baseFile
     *
     * @return \Feugene\Files\Models\File|\Feugene\Files\Types\BaseFile|\Illuminate\Database\Eloquent\Model|null
     */
    public function handle(BaseFile $baseFile)
    {
        if (($model = (new File)->setBaseFile($baseFile))->save()) {
            return $model;
        }

        return null;
    }
}
