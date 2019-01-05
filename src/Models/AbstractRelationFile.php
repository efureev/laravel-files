<?php

namespace Feugene\Files\Models;

use Feugene\Files\Entities\AbstractModificator;
use Feugene\Files\Traits\Relation;

/**
 * Class AbstractRelationFile
 *
 * @package Feugene\Files\Models
 */
abstract class AbstractRelationFile extends File
{
    use Relation;

    /**
     * @param \Feugene\Files\Entities\AbstractModificator ...$options
     *
     * @return \Feugene\Files\Models\AbstractRelationFile
     */
    abstract public function createChild(AbstractModificator ... $options): AbstractRelationFile;
}
