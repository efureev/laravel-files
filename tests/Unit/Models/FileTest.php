<?php

namespace Feugene\Files\Tests\Unit\Models;

use Feugene\Files\Entities\FileParams;
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

    public function testLoadFromDatabaseWithParams(): void
    {
        $file = File::find($this->model->getKey());

        static::assertInstanceOf(FileParams::class, $file->params);

        $file->params->width = 21;
        $file->params->height = 100;

        static::assertEquals(21, $file->params->width);
        static::assertEquals(100, $file->params->height);
        static::assertIsArray($file->params->toArray());
        static::assertEquals(["width" => 21, "height" => 100], $file->params->toArray());
        static::assertEquals('{"width":21,"height":100}', $file->params->toJson());
        static::assertJson($file->params->toJson());
        static::assertJsonStringEqualsJsonString('{"width":21,"height":100}', $file->params->toJson());

        $file->save();

        $file = File::find($this->model->getKey());
        static::assertJsonStringEqualsJsonString('{"width":21,"height":100}', $file->getOriginal('params'));

        static::assertEquals(21, $file->params->width);
        static::assertEquals(100, $file->params->height);
        static::assertIsArray($file->params->toArray());
        static::assertEquals(["width" => 21, "height" => 100], $file->params->toArray());
        static::assertEquals('{"width":21,"height":100}', $file->params->toJson());
        static::assertJson($file->params->toJson());
        static::assertJsonStringEqualsJsonString('{"width":21,"height":100}', $file->params->toJson());

        $file->params->clear();
        $file->save();

        $file = File::find($this->model->getKey());
        static::assertJsonStringEqualsJsonString('[]', $file->getOriginal('params'));

        static::assertIsArray($file->params->toArray());
        static::assertEquals([], $file->params->toArray());
        static::assertEquals('[]', $file->params->toJson());
        static::assertJson($file->params->toJson());
        static::assertJsonStringEqualsJsonString('[]', $file->params->toJson());

        $file->params->size = 3000;
        $file->params->status = true;
        $file->params->message = 'success';

        $file->save();

        $file = File::find($this->model->getKey());
        static::assertJson($file->params->toJson());
        static::assertIsArray($file->params->toArray());

        static::assertEquals(["size" => 3000, "status" => true, "message" => "success"], $file->params->toArray());
        static::assertEquals('{"size":3000,"status":true,"message":"success"}', $file->params->toJson());
        static::assertCount(3, $file->params);

        foreach ($file->params as $name => $value) {
            switch ($name) {
                case 'size':
                    static::assertEquals(3000, $value);
                    break;
                case 'status':
                    static::assertEquals(true, $value);
                    break;
                case 'message':
                    static::assertEquals('success', $value);
                    break;
            }
        }

        unset($file->params->size);

        static::assertEquals(["status" => true, "message" => "success"], $file->params->toArray());
        static::assertEquals('{"status":true,"message":"success"}', $file->params->toJson());

        $file->params->offsetUnset('status');
        static::assertEquals(["message" => "success"], $file->params->toArray());
        static::assertEquals('{"message":"success"}', $file->params->toJson());

        static::assertTrue(isset($file->params->message));
    }

    /**
     * @expectedException \Php\Support\Exceptions\UnknownPropertyException
     */
    public function testLoadFromDatabaseWithParamsFail(): void
    {
        $file = File::find($this->model->getKey());
        $file->params->width;
    }

    /**
     * @expectedException \Php\Support\Exceptions\InvalidParamException
     */
    public function testLoadFromDatabaseWithParamsFail_2(): void
    {
        $file = File::find($this->model->getKey());
        $file->params->offsetSet(null, 12);
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
