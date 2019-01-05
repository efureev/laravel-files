<?php

namespace Feugene\Files\Models;

use Feugene\Files\Entities\AbstractModificator;
use Feugene\Files\Traits\Dimensions;
use Feugene\Files\Traits\Resize;

/**
 * Class ImageFile
 *
 * @package Feugene\Files\Models
 * @property int    $height
 * @property int    $width
 * @property string $innerMime
 */
class ImageFile extends AbstractRelationFile
{
    use Dimensions, Resize;

    /**
     * @param int|null $value
     */
    public function setWidthAttribute(?int $value): void
    {
        $this->params->width = $value;
    }

    /**
     * @return int
     */
    public function getWidthAttribute(): int
    {
        return $this->params->width;
    }

    /**
     * @param int|null $value
     */
    public function setHeightAttribute(?int $value): void
    {
        $this->params->height = $value;
    }

    /**
     * @return int
     */
    public function getHeightAttribute(): int
    {
        return $this->params->height;
    }

    /**
     * @param string|null $value
     */
    public function setInnerMimeAttribute(?string $value): void
    {
        $this->params->innerMime = $value;
    }

    /**
     * @return string|null
     */
    public function getInnerMimeAttribute(): string
    {
        return $this->params->innerMime;
    }

    /**
     * @param \Feugene\Files\Entities\AbstractModificator ...$options
     *
     * @return \Feugene\Files\Models\AbstractRelationFile
     * @throws \Gumlet\ImageResizeException
     */
    public function createChild(AbstractModificator ... $options): AbstractRelationFile
    {
        $instance = $this->modify(...$options);
        $instance->setParent($this);
        $instance->save();

        return $instance;
    }

}
