<?php

namespace Feugene\Files\Tests\Unit\Models;

use Feugene\Files\Entities\ImageFileOptions;
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

    /*public function testCreateChild()
    {
        /** @var ImageFile $file * /
        $file = ImageFile::find($this->model->getKey());

        static::assertInstanceOf(ImageFile::class, $file);

        $child1 = $file->createChild(new ImageFileOptions([
            'width' => '20%',
        ]));

        $child2 = $file->createChild(new ImageFileOptions([
            'width' => 200,
        ]));

    }*/

}
