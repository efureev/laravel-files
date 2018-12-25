<?php

namespace Feugene\Files\Tests\Unit\Models;

use Feugene\Files\Models\File;
use Feugene\Files\Support\Store;
use Feugene\Files\Traits\BaseFileApply;

/**
 * Class FileTest
 *
 * @package Feugene\Files\Tests\Unit\Models
 */
class FileTest extends AbstractModelTestCase
{
    /**
     * @var \Feugene\Files\Models\File
     */
    protected $model;

    protected function tearDown(): void
    {
        $baseFle = $this->model->getBaseFile();

        parent::tearDown();

        static::assertFalse($baseFle->isExists());
    }

    /**
     * Assert model uses required traits.
     *
     * @return void
     */
    public function testTraits(): void
    {
        static::assertClassUsesTraits(File::class, [
            BaseFileApply::class,
        ]);
    }

    /**
     * Test model fillable attributes.
     *
     * @return void
     */
    public function testFillable(): void
    {
        $this->assertModelHasFillableAttributes([
            'path',
            'driver',
            'ext',
            'size',
            'mime',
            'params',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    protected function modelFactory(array $attributes = []): File
    {

        /** @var File $file */
        $file = factory(File::class)->make($attributes);

        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage/model'));

        $fileBase = $this->faker->file('tests/mocks', $toStorage);

        $baseFile = new \Feugene\Files\Types\BaseFile($fileBase);

        $file->setBaseFile($baseFile);
        $file->save();

        return $file;
    }

    public function testLoadFromDatabase(): void
    {
        $file = File::find($this->model->getKey());

        static::assertInstanceOf(File::class, $file);
    }

    /**
     * @throws \Exception
     */
    public function testSafeDeleteFile(): void
    {
        /** @var File $file */
        $file = File::find($this->model->getKey());

        static::assertTrue($file->delete());

        $file = File::find($this->model->getKey());
        static::assertNull($file);
    }

    public function testLoadFromString(): void
    {
        /** @var File $file */
        $file = File::fromAbsolutePath($this->faker->file('tests/mocks'));

        static::assertNotNull($file->getBaseFile());

        static::assertEquals($file->ext, $file->getBaseFile()->getExtension());
        static::assertEquals($file->path, $file->getBaseFile()->getRealPath());
        static::assertEquals($file->size, $file->getBaseFile()->getSize());
        static::assertEquals($file->mime, $file->getBaseFile()->getMimeType());
    }

    public function testSetBaseFileFromString(): void
    {
        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage/model'));

        $fileBase = $this->faker->file('tests/mocks', $toStorage);

        $file = new File([
            'path' => $fileBase
        ]);

        static::assertEquals($file->ext, $file->getBaseFile()->getExtension());
        static::assertEquals($fileBase, $file->getBaseFile()->getRealPath());
        static::assertEquals($file->path, $file->getRelativePath());
        static::assertEquals($file->getAbsolutePath(), $file->getBaseFile()->getRealPath());
        static::assertEquals($file->size, $file->getBaseFile()->getSize());
        static::assertEquals($file->mime, $file->getBaseFile()->getMimeType());

        $file2 = new File([
            'path' => $file->getRelativePath()
        ]);

        static::assertEquals($file2->ext, $file2->getBaseFile()->getExtension());
        static::assertEquals($file->getBaseFile(), $file2->getBaseFile());
        static::assertEquals($file2->path, $file2->getRelativePath());
        static::assertEquals($file2->getAbsolutePath(), $file2->getBaseFile()->getRealPath());
        static::assertEquals($file2->size, $file2->getBaseFile()->getSize());
        static::assertEquals($file2->mime, $file2->getBaseFile()->getMimeType());
    }

    /**
     * @expectedException \Feugene\Files\Exceptions\MissingFilePathException
     */
    public function testEmptyPath(): void
    {
        $file = new File([
            'path' => ''
        ]);
    }



    /**
     * @expectedException \Feugene\Files\Exceptions\MissingFilePathException
     */
    public function testEmptyPath_2(): void
    {
        $file = new File([
            'path' => null
        ]);
    }

    /**
     * @expectedException \Feugene\Files\Exceptions\MissingFilePathException
     */
    public function testEmptyPath_3(): void
    {
        $file = new File;
        $file->path = null;
    }

    /**
     * @expectedException \Feugene\Files\Exceptions\MissingFilePathException
     */
    public function testEmptyPath_4(): void
    {
        $file = new File;
        $file->path = '';
    }

}
