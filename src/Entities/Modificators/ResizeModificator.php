<?php

namespace Feugene\Files\Entities\Modificators;

use Feugene\Files\Entities\AbstractModificator;

/**
 * Class ResizeModificator
 *
 * @package Feugene\Files\Entities
 */
class ResizeModificator extends AbstractModificator
{
    /**
     * @var int
     */
    public $height;

    /**
     * @var int
     */
    public $width;

    /**
     * @var bool
     */
    public $bestFit = true;

    /**
     * ResizeModificator constructor.
     *
     * @param int|null $width
     * @param int|null $height
     * @param bool     $bestFit
     */
    public function __construct(?int $width = null, ?int $height = null, bool $bestFit = true)
    {
        $this->value = [$width, $height];
        $this->width = $width;
        $this->height = $height;
        $this->bestFit = $bestFit;
    }

    /**
     * @return string
     */
    public function getValueString(): string
    {
        return $this->width . 'x' . $this->height;
    }

    /**
     * @param \Feugene\Files\Models\ImageFile $model
     * @param mixed                           ...$options
     *  -  boolean $allow_enlarge All resize functions have $allow_enlarge option which is set to false by default. You
     *  can enable by passing true to any resize function
     *
     * @throws \Gumlet\ImageResizeException
     */
    public function handle($model, ... $options)
    {
        if ($this->width !== null && $this->height !== null) {
            if ($this->bestFit) {
                $model->getImageProcessor()->resizeToBestFit($this->width, $this->height, ...$options);
            } else {
                $model->getImageProcessor()->resize($this->width, $this->height, ...$options);
            }
        } elseif ($this->width !== null && $this->height === null) {
            $model->getImageProcessor()->resizeToWidth($this->width, ...$options);
        } elseif ($this->width === null && $this->height !== null) {
            $model->getImageProcessor()->resizeToHeight($this->height, ...$options);
        }


    }
}
