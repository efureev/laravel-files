<?php

namespace Feugene\Files\Traits;

/**
 * Trait Dimensions
 *
 * @package Feugene\Files\Traits
 */
trait Dimensions
{
    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        if ($this->width === null) {
            $this->initWidthAndHeight();
        }

        return $this->width;
    }

    /**
     * @return int|null
     */
    public function getHeight(): ?int
    {
        if ($this->height === null) {
            $this->initWidthAndHeight();
        }

        return $this->height;
    }

    /**
     *
     */
    protected function initWidthAndHeight(): void
    {
        $this->width = null;
        $this->height = null;
        $this->innerMime = null;

        if ($this->isImage() && ($size = @getimagesize($this->getAbsolutePath()))) {
            $this->width = $size[0];
            $this->height = $size[1];
            $this->innerMime = $size['mime'] ?? null;
        }
    }

    /**
     *
     */
    public function updateAttributes(): void
    {
        parent::updateAttributes();

        $this->initWidthAndHeight();
    }
}
