<?php

namespace Feugene\Files\Traits;

use Feugene\Files\Models\AudioFile;
use Feugene\Files\Models\DocumentFile;
use Feugene\Files\Models\File;
use Feugene\Files\Models\ImageFile;
use Feugene\Files\Models\VideoFile;
use Feugene\Files\Support\Types;

/**
 * Trait FileTypes
 *
 * @package Feugene\Files\Traits
 */
trait FileTypes
{
    /**
     * @param string $format
     *
     * @return bool
     */
    public function isMime(string $format): bool
    {
        return Types::mimeIs($this->mime, $format);
    }

    /**
     * @param string $extension
     *
     * @return bool
     */
    public function isExtension(string $extension): bool
    {
        return Types::extensionIs($this->ext ?? $this->getBaseFile()->getExtension(), $extension);
    }

    /**
     * @param array $list
     *
     * @return bool
     */
    public function inExtensionList(array $list): bool
    {
        return Types::extensionIn($this->ext ?? $this->getBaseFile()->getExtension(), $list);
    }

    /**
     * @param array $list
     *
     * @return bool
     */
    public function inMimeList(array $list): bool
    {
        return Types::inMime($this->mime ?? $this->getBaseFile()->getMimeType()(), $list);
    }

    /**
     * @return bool
     */
    public function isImage()
    {
        $type = config('files.types.image', []);

        return $this->inMimeList($type['formats']) || $this->inExtensionList($type['extensions']);
    }

    /**
     * @return bool
     */
    public function isSvg()
    {
        return $this->isMime('image/svg+xml');
    }

    /**
     * @return bool
     */
    public function isVideo()
    {
        if ($this->isMime('video')) {
            return true;
        }

        if ($this->inMimeList(['audio', 'image'])) {
            return false;
        }

        $type = config('files.types.video', []);

        return $this->inMimeList($type['formats']) || $this->inExtensionList($type['extensions']);
    }

    /**
     * @return bool
     */
    public function isAudio()
    {
        if ($this->isMime('audio')) {
            return true;
        }

        if ($this->inMimeList(['video', 'image'])) {
            return false;
        }

        $type = config('files.types.audio', []);

        return $this->inMimeList($type['formats']) || $this->inExtensionList($type['extensions']);
    }

    /**
     * @return bool
     */
    public function isDocument()
    {
        if ($this->inMimeList(['audio', 'video', 'image'])) {
            return false;
        }

        $type = config('files.types.document', []);

        return $this->inMimeList($type['formats']) || $this->inExtensionList($type['extensions']);
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        if ($this->isImage()) {
            return ImageFile::class;
        }

        if ($this->isDocument()) {
            return DocumentFile::class;
        }

        if ($this->isAudio()) {
            return AudioFile::class;
        }

        if ($this->isAudio()) {
            return VideoFile::class;
        }

        return File::class;
    }

    /**
     * @return $this
     */
    public function toType()
    {
        if (static::class === ($type = $this->getType())) {
            return $this;
        }

        $instance = new $type($this->toArray());
        $instance->id = $this->id;
        $instance->parent_id = $this->parent_id;
        $instance->updateAttributes();

        return $instance;
    }

}
