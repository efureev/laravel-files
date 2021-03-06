<?php

namespace Feugene\Files\Models;

use Feugene\Files\Entities\AbstractModificator;
use Php\Support\Exceptions\MethodNotAllowedException;

/**
 * Class AudioFile
 *
 * @package Feugene\Files\Models
 */
class AudioFile extends AbstractRelationFile
{
    /**
     * @param \Feugene\Files\Entities\AbstractModificator ...$options
     *
     * @return \Feugene\Files\Models\AbstractRelationFile
     * @throws \Php\Support\Exceptions\MethodNotAllowedException
     */
    public function createChild(AbstractModificator ... $options): AbstractRelationFile
    {
        throw new MethodNotAllowedException('This method not allowed');
    }
}
