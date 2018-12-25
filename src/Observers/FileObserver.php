<?php

namespace Feugene\Files\Observers;

use Feugene\Files\Models\File;

class FileObserver
{
    /**
     * @param \Feugene\Files\Models\File $file
     *
     * @throws \Exception
     */
    public function creating(File $file)
    {
        $file->setAttribute('id', \Ramsey\Uuid\Uuid::uuid4());
    }

    /**
     * @param \Feugene\Files\Models\File $file
     */
    public function deleted(File $file)
    {
        if ($file->getBaseFile()) {
            $file->getBaseFile()->remove();
        }
    }

}
