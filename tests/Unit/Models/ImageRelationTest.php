<?php

namespace Feugene\Files\Tests\Unit\Models;

use Feugene\Files\Entities\Modificators\ResizeModificator;
use Feugene\Files\Entities\Modificators\ScaleModificator;
use Feugene\Files\Models\File;
use Feugene\Files\Models\ImageFile;
use Feugene\Files\Support\Store;

/**
 * Class ImageRelationTest
 *
 * @package Feugene\Files\Tests\Unit\Models
 */
class ImageRelationTest extends AbstractModelTestCase
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
     * @throws \Gumlet\ImageResizeException
     */
    public function t1estCreateChild(): void
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        $child = $file->createChild();

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertTrue($child->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals($file->params->width, $child->params->width);
        static::assertEquals($file->params->height, $child->params->height);
        static::assertEquals($file->mime, $child->mime);
        static::assertEquals($file->ext, $child->ext);
        static::assertEquals($file->driver, $child->driver);
        static::assertEquals($file->key, $child->key);
        static::assertJsonStringEqualsJsonString($file->params->toJson(), $child->params->toJson());

        static::assertEquals($file->getKey(), $child->parent->getKey());
        static::assertEquals($file->getKey(), $child->parent_id);

        static::assertEquals(1, $file->children()->count());
        static::assertEquals($child->getKey(), $file->children()->first()->getKey());
    }

    /**
     * @throws \Gumlet\ImageResizeException
     */
    public function testRemoveModelWithChildren(): void
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        /** @var ImageFile $child1 */
        $child1 = $file->createChild(new ResizeModificator(50));
        /** @var ImageFile $child2 */
        $child2 = $file->createChild(new ScaleModificator(50));

        static::assertEquals($file->getKey(), $child1->parent->getKey());
        static::assertEquals($file->getKey(), $child2->parent->getKey());
        static::assertEquals($file->getKey(), $child1->parent_id);
        static::assertEquals($file->getKey(), $child2->parent_id);

        static::assertEquals(2, $file->children()->count());

        static::assertTrue($file->delete());

        static::assertNull(ImageFile::find($file->getKey()));
        static::assertFalse($file->getBaseFile()->isExists());

        static::assertNull(ImageFile::find($child1->getKey()));
        static::assertFalse($child1->getBaseFile()->isExists());

        static::assertNull(ImageFile::find($child2->getKey()));
        static::assertFalse($child2->getBaseFile()->isExists());
    }
}
