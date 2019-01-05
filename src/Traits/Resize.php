<?php

namespace Feugene\Files\Traits;

use Feugene\Files\Entities\AbstractModificator;
use Feugene\Files\Entities\Modificators\ResizeModificator;
use Feugene\Files\Entities\Modificators\ScaleModificator;
use Feugene\Files\Models\ImageFile;
use Gumlet\ImageResize;
use Ramsey\Uuid\Uuid;

/**
 * Trait Resize
 *
 * @package Feugene\Files\Traits
 */
trait Resize
{
    /** @var \Gumlet\ImageResize */
    protected $processor;

    /**
     * @return \Gumlet\ImageResize
     * @throws \Gumlet\ImageResizeException
     */
    public function getImageProcessor(): ImageResize
    {
        if (!$this->processor) {
            $this->processor = new ImageResize($this->getAbsolutePath());
        }

        return $this->processor;
    }

    /**
     * @param \Feugene\Files\Entities\Modificators\ScaleModificator $mod
     *
     * @return \Feugene\Files\Models\ImageFile
     * @throws \Gumlet\ImageResizeException
     */
    public function scale(ScaleModificator $mod): ImageFile
    {
        $extPostfix = '.' . $this->getBaseFile()->getExtension();
        $newName = dirname($this->getAbsolutePath()) . '/' . $this->getBaseFile()->getBasename($extPostfix) . '_scale_' . $mod->getValueString() . $extPostfix;

        $mod->handle($this);
        $this->getImageProcessor()->save($newName);

        /** @var \Feugene\Files\Models\ImageFile $file */
        $file = ImageFile::fromAbsolutePath($newName);

        $file->key = $file->width . 'x' . $file->height;

        $file->save();

        return $file;
    }

    /**
     * @param \Feugene\Files\Entities\Modificators\ResizeModificator $mod
     * @param mixed                                                  ...$options
     *
     * @return \Feugene\Files\Models\ImageFile
     * @throws \Gumlet\ImageResizeException
     */
    public function resize(ResizeModificator $mod, ... $options): ImageFile
    {
        $extPostfix = '.' . $this->getBaseFile()->getExtension();
        $newName = dirname($this->getAbsolutePath()) . '/' . $this->getBaseFile()->getBasename($extPostfix) . '_resize_' . $mod->getValueString() . $extPostfix;

        $mod->handle($this, ...$options);
        $this->getImageProcessor()->save($newName);

        /** @var \Feugene\Files\Models\ImageFile $file */
        $file = ImageFile::fromAbsolutePath($newName);

        $file->key = $mod->getValueString();

        $file->save();

        return $file;
    }

    /**
     * @param \Feugene\Files\Entities\AbstractModificator ...$options
     *
     * @return \Feugene\Files\Models\ImageFile
     * @throws \Gumlet\ImageResizeException
     */
    public function modify(AbstractModificator ... $options): ImageFile
    {
        $extPostfix = '.' . $this->getBaseFile()->getExtension();
        $newName = dirname($this->getAbsolutePath()) . '/' . ((string)Uuid::uuid4()) . $extPostfix;

        $keys = [];
        foreach ($options as $option) {
            $option->handle($this);
            $keys[] = ((int)floor($this->getImageProcessor()->getDestWidth())) . 'x' . ((int)floor($this->getImageProcessor()->getDestHeight()));
        }

        $this->getImageProcessor()->save($newName);

        /** @var \Feugene\Files\Models\ImageFile $file */
        $file = ImageFile::fromAbsolutePath($newName);

        if ($keys) {
            $file->key = implode('|', $keys);
        }

        return $file;
    }

}
