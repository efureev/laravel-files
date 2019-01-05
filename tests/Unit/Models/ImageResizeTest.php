<?php

namespace Feugene\Files\Tests\Unit\Models;

use Feugene\Files\Entities\Modificators\ResizeModificator;
use Feugene\Files\Entities\Modificators\ScaleModificator;
use Feugene\Files\Models\File;
use Feugene\Files\Models\ImageFile;
use Feugene\Files\Support\Store;

/**
 * Class ImageResizeTest
 *
 * @package Feugene\Files\Tests\Unit\Models
 */
class ImageResizeTest extends AbstractModelTestCase
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

    /**
     * @throws \Feugene\Files\Exceptions\SaveFileException
     * @throws \Gumlet\ImageResizeException
     */
    public function testScale()
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        $child = $file->scale(new ScaleModificator(50));

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals($file->params->width / 2, $child->params->width);
        static::assertEquals($file->params->height / 2, $child->params->height);

        static::assertNull($child->parent);
        static::assertNull($child->parent_id);
    }

    /**
     * @throws \Gumlet\ImageResizeException
     */
    public function testResizeByWidth()
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        $child = $file->resize(new ResizeModificator(50));

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals(50, $child->params->width);
        static::assertEquals(50, $child->width);

        $ratio = $file->width / $child->width;
        $heightChild = (int)floor($file->height / $ratio);

        static::assertEquals($heightChild, $child->params->height);
        static::assertEquals($heightChild, $child->height);

        static::assertEquals($file->innerMime, $child->innerMime);
        static::assertEquals($file->mime, $child->mime);

        static::assertNull($child->parent);
        static::assertNull($child->parent_id);
    }

    /**
     * @throws \Gumlet\ImageResizeException
     */
    public function testResizeByHeight()
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        $child = $file->resize(new ResizeModificator(null, 50));

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals(50, $child->params->height);
        static::assertEquals(50, $child->height);

        $ratio = $file->height / $child->height;
        $widthChild = (int)floor($file->width / $ratio);

        static::assertEquals($widthChild, $child->params->width);
        static::assertEquals($widthChild, $child->width);

        static::assertEquals($file->innerMime, $child->innerMime);
        static::assertEquals($file->mime, $child->mime);

        static::assertNull($child->parent);
        static::assertNull($child->parent_id);
    }

    /**
     * @throws \Gumlet\ImageResizeException
     */
    public function testResizeByWidthAndHeightBestFit()
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        $child = $file->resize(new ResizeModificator(100, 80));

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals($file->innerMime, $child->innerMime);
        static::assertEquals($file->mime, $child->mime);

        static::assertNull($child->parent);
        static::assertNull($child->parent_id);
    }

    /**
     * @throws \Gumlet\ImageResizeException
     */
    public function testResizeByWidthAndHeightImportant()
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        $child = $file->resize(new ResizeModificator(100, 50, false), true);

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals(100, $child->params->width);
        static::assertEquals(100, $child->width);

        static::assertEquals(50, $child->params->height);
        static::assertEquals(50, $child->height);

        static::assertEquals($file->innerMime, $child->innerMime);
        static::assertEquals($file->mime, $child->mime);

        static::assertNull($child->parent);
        static::assertNull($child->parent_id);
    }

    /**
     * @throws \Gumlet\ImageResizeException
     */
    public function testCreateChildScale(): void
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        $child = $file->createChild(new ScaleModificator(50));

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertTrue($child->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals($file->params->width / 2, $child->params->width);
        static::assertEquals($file->params->height / 2, $child->params->height);

        static::assertEquals($file->getKey(), $child->parent->getKey());

        static::assertEquals(1, $file->children()->count());
        static::assertEquals($child->getKey(), $file->children()->first()->getKey());
    }

    /**
     * @throws \Gumlet\ImageResizeException
     */
    public function testCreateChildResizeByWidth(): void
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        /** @var ImageFile $child */
        $child = $file->createChild(new ResizeModificator(50));

        static::assertEquals(50, $child->params->width);
        static::assertEquals(50, $child->width);

        $ratio = $file->width / $child->width;
        $heightChild = (int)floor($file->height / $ratio);

        static::assertEquals($heightChild, $child->params->height);
        static::assertEquals($heightChild, $child->height);

        static::assertEquals($file->innerMime, $child->innerMime);
        static::assertEquals($file->mime, $child->mime);

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertTrue($child->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals($file->getKey(), $child->parent->getKey());

        static::assertEquals(1, $file->children()->count());
        static::assertEquals($child->getKey(), $file->children()->first()->getKey());
    }

    /**
     * @throws \Gumlet\ImageResizeException
     */
    public function testCreateChildResizeByHeight(): void
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        /** @var ImageFile $child */
        $child = $file->createChild(new ResizeModificator(null, 50));

        static::assertEquals(50, $child->params->height);
        static::assertEquals(50, $child->height);

        $ratio = $file->height / $child->height;
        $widthChild = (int)floor($file->width / $ratio);

        static::assertEquals($widthChild, $child->params->width);
        static::assertEquals($widthChild, $child->width);

        static::assertEquals($file->innerMime, $child->innerMime);
        static::assertEquals($file->mime, $child->mime);

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertTrue($child->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals($file->getKey(), $child->parent->getKey());

        static::assertEquals(1, $file->children()->count());
        static::assertEquals($child->getKey(), $file->children()->first()->getKey());
    }

    /**
     * @throws \Gumlet\ImageResizeException
     */
    public function testCreateChildResizeByWidthAndHeightBestFit(): void
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        /** @var ImageFile $child */
        $child = $file->createChild(new ResizeModificator(100, 50));

        static::assertEquals($file->innerMime, $child->innerMime);
        static::assertEquals($file->mime, $child->mime);

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertTrue($child->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals($file->getKey(), $child->parent->getKey());

        static::assertEquals(1, $file->children()->count());
        static::assertEquals($child->getKey(), $file->children()->first()->getKey());
    }

}
