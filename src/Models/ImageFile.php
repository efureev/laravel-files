<?php

namespace Feugene\Files\Models;

use Feugene\Files\Traits\Dimensions;

/**
 * Class ImageFile
 *
 * @package Feugene\Files\Models
 * @property int    $height
 * @property int    $width
 * @property string $innerMime
 */
class ImageFile extends File
{
    use Dimensions;

    /**
     * @param int|null $value
     */
    public function setWidthAttribute(?int $value)
    {
        $this->params->width = $value;
    }

    /**
     * @return int
     */
    public function getWidthAttribute()
    {
        return $this->params->width;
    }

    /**
     * @param int|null $value
     */
    public function setHeightAttribute(?int $value)
    {
        $this->params->height = $value;
    }

    /**
     * @return int
     */
    public function getHeightAttribute()
    {
        return $this->params->height;
    }

    /**
     * @param string|null $value
     */
    public function setInnerMimeAttribute(?string $value)
    {
        $this->params->innerMime = $value;
    }

    /**
     * @return string
     */
    public function getInnerMimeAttribute()
    {
        return $this->params->innerMime;
    }

}
