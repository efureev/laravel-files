<?php

namespace Feugene\Files\Tests\Unit\Models;

use Feugene\Files\Entities\Modificators\ScaleImageModificator;
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
    }

    /**
     * @throws \Gumlet\ImageResizeException
     */
    public function testCreateChild(): void
    {
        /** @var ImageFile $file */
        $file = ImageFile::find($this->model->getKey());

        $child = $file->createChild(new ScaleModificator(50));

        static::assertEquals(get_class($file), get_class($child));
        static::assertTrue($file->getBaseFile()->isExists());
        static::assertNotEquals($file->getBaseFile()->getBasename(), $child->getBaseFile()->getBasename());

        static::assertEquals($file->params->width / 2, $child->params->width);
        static::assertEquals($file->params->height / 2, $child->params->height);
    }

}
