<?php

namespace Feugene\Files\Types;

/**
 * Class ImageFile
 *
 * @package Feugene\Files\Types
 */
class ImageFile extends BaseFile
{

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return 0;
    }

}
