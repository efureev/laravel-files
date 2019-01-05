<?php

namespace Feugene\Files\Traits;

use Feugene\Files\Entities\AbstractModificator;
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

        $this->getImageProcessor()
            ->scale($mod->getValue())
            ->save($newName);

        /** @var \Feugene\Files\Models\ImageFile $file */
        $file = ImageFile::fromAbsolutePath($newName);

        $file->setParent($this);
        $file->key = 'scale:' . $mod->getValueString();

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
            $keys[] = (string)$option;
            $option->handle($this);
        }

        $this->getImageProcessor()->save($newName);

        /** @var \Feugene\Files\Models\ImageFile $file */
        $file = ImageFile::fromAbsolutePath($newName);

        $file->key = implode('|', $keys);
        $file->setParent($this);

        return $file;
    }

    /**
     * @param \Feugene\Files\Entities\ImageFileOptions $options
     *
     * @return \Feugene\Files\Models\ImageFile
     * @throws \Gumlet\ImageResizeException
     */
//    public function resize(ImageFileOptions $options): ImageFile
//    {
//        $image = new ImageResize($this->getAbsolutePath());
//        $image->resize($options->width, $options->height);
//        dd($this->getAbsolutePath(), $image);
    /*
            $image = Image::make($this->getPath());

            if ($size['static']) {
                $image->fit($size['width'], $size['height'], null);
            } else {
                $image->resize($size['width'], $size['height'], function ($constraint) use ($size): void {
                    $constraint->aspectRatio();

                    if (empty($size['enlarge'])) {
                        $constraint->upsize();
                    }
                });
            }

            return $image->save(null, config('upload.images.quality', null));*/
//    }
    /*
        protected function resizeImage($size)
        {
            $image = new ImageResize($this->getAbsolutePath());


            $image = Image::make($this->getPath());

            if ($size['static']) {
                $image->fit($size['width'], $size['height'], null);
            } else {
                $image->resize($size['width'], $size['height'], function ($constraint) use ($size): void {
                    $constraint->aspectRatio();

                    if (empty($size['enlarge'])) {
                        $constraint->upsize();
                    }
                });
            }

            return $image->save(null, config('upload.images.quality', null));
        }*/

}
