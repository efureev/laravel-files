<?php

namespace Feugene\Files\Tests\Unit\Models;

use Feugene\Files\Entities\FileParams;
use Feugene\Files\Models\File;
use Feugene\Files\Models\ImageFile;
use Feugene\Files\Support\Store;

/**
 * Class ImageTest
 *
 * @package Feugene\Files\Tests\Unit\Models
 */
class ImageTest extends AbstractModelTestCase
{
    /**
     * @var \Feugene\Files\Models\ImageFile
     */
    protected $model;

    protected function tearDown(): void
    {
        $baseFle = $this->model->getBaseFile();

        parent::tearDown();

        static::assertFalse($baseFle->isExists());
    }

    /**
     * {@inheritdoc}
     */
    protected function modelFactory(array $attributes = []): File
    {
        /** @var ImageFile $file */
        $file = new ImageFile;

        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage/model'));

        $fileBase = $this->faker->file('tests/mocks/images', $toStorage);

        $baseFile = new \Feugene\Files\Types\BaseFile($fileBase);

        $file->setBaseFile($baseFile);

        $file->save();

        return $file;
    }

    public function testLoadImageFromBase()
    {
        $file = ImageFile::find($this->model->getKey());

        static::assertInstanceOf(ImageFile::class, $file);

        static::assertInstanceOf(FileParams::class, $file->params);

        static::assertEquals($this->model->params->width, $file->params->width);
        static::assertEquals($this->model->width, $file->params->width);
        static::assertEquals($this->model->params->height, $file->params->height);
        static::assertEquals($this->model->height, $file->params->height);
        static::assertEquals($this->model->params->innerMime, $file->params->innerMime);
        static::assertEquals($this->model->innerMime, $file->params->innerMime);

        static::assertTrue($this->model->isImage());
        static::assertFalse($this->model->isDocument());
        static::assertFalse($this->model->isVideo());
        static::assertFalse($this->model->isAudio());
    }

}
