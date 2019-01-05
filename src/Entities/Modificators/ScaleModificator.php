<?php

namespace Feugene\Files\Entities\Modificators;

use Feugene\Files\Entities\AbstractModificator;

/**
 * Class ScaleImageModificator
 *
 * @package Feugene\Files\Entities
 */
class ScaleModificator extends AbstractModificator
{
    /**
     * ScaleImageModificator constructor.
     *
     * @param $value
     */
    public function __construct(int $value)
    {
        $this->value = $value;
    }

    /**
     * @param \Feugene\Files\Models\ImageFile $model
     * @param mixed                           ...$options
     *
     * @throws \Gumlet\ImageResizeException
     */
    public function handle($model, ... $options)
    {
        $model->getImageProcessor()->scale($this->value);
    }
}
