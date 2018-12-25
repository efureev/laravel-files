<?php

namespace Feugene\Files\Tests\Unit;

use Feugene\Files\Support\Store;
use Feugene\Files\Types\BaseFile;

/**
 * Class BaseFileTest
 *
 * @package Feugene\Files\Tests\Unit\Models
 */
class BaseFileTest extends AbstractUnitTestCase
{
    public function testLoadFromString(): void
    {
        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('tmp'));

        $fileBase = $this->faker->file('tests/mocks', $toStorage);

        /** @var BaseFile $file */
        $file = new BaseFile($fileBase);

        static::assertInstanceOf(BaseFile::class, $file);
        static::assertTrue($file->isExists());

        $file->remove();
        static::assertFalse($file->isExists());
    }

    public function testLoadFromStringToCustomPath(): void
    {
        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage/app/public'));

        $fileBase = $this->faker->file('tests/mocks', $toStorage);

        /** @var BaseFile $file */
        $file = new BaseFile($fileBase);

        static::assertInstanceOf(BaseFile::class, $file);
        static::assertTrue($file->isExists());

        $file->remove();
        static::assertFalse($file->isExists());
    }

    public function testCopyFile(): void
    {
        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage'));

        $fileBase = $this->faker->file('tests/mocks', $toStorage);

        /** @var BaseFile $file */
        $file = new BaseFile($fileBase);

        static::assertInstanceOf(BaseFile::class, $file);
        static::assertTrue($file->isExists());

        $filePathFrom = $file->getRealPath();

        $file2 = $file->copy(Store::pathToStorage('storage/app/public/to'));

        static::assertTrue($file->isExists());
        static::assertTrue(file_exists($filePathFrom));
        static::assertEquals($file->getRealPath(), $filePathFrom);

        static::assertTrue($file2->isExists());
        static::assertEquals($file2->getPath(), Store::pathToStorage('storage/app/public/to'));
        static::assertEquals($file2->getSize(), $file->getSize());
        static::assertEquals($file2->getMimeType(), $file->getMimeType());
        static::assertEquals($file2->getExtension(), $file->getExtension());

        $file->remove();
        $file2->remove();

        static::assertFalse($file->isExists());
        static::assertFalse($file2->isExists());
    }

    /**
     * @expectedException  \Symfony\Component\HttpFoundation\File\Exception\FileException
     */
    public function testCopyFileFail(): void
    {
        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage'));

        $fileBase = $this->faker->file('tests/mocks', $toStorage);

        /** @var BaseFile $file */
        $file = new BaseFile($fileBase);

        static::assertInstanceOf(BaseFile::class, $file);
        static::assertTrue($file->isExists());

        $path = static::getOrCreateFilePath(Store::pathToStorage('storage/app/fail'), 0611);

        $file2 = $file->copy($path);

        static::assertFalse($file2->isExists());
        $file->remove();

        static::assertFalse($file->isExists());
    }

    public function testMoveFile(): void
    {
        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage'));

        $fileBase = $this->faker->file('tests/mocks', $toStorage);

        /** @var BaseFile $file */
        $file = new BaseFile($fileBase);

        static::assertInstanceOf(BaseFile::class, $file);
        static::assertTrue($file->isExists());

        $filePathFrom = $file->getRealPath();
        $fileSizeFrom = $file->getSize();
        $fileMimeFrom = $file->getMimeType();

        /** @var BaseFile $fileTo
         */
        $fileTo = $file->move(Store::pathToStorage('storage/app/public/to'));

        static::assertFalse($file->isExists());
        static::assertFalse($file->getRealPath());
        static::assertFalse(file_exists($filePathFrom));

        static::assertTrue($fileTo->isExists());
        static::assertEquals($fileTo->getPath(), Store::pathToStorage('storage/app/public/to'));

        static::assertEquals($fileTo->getSize(), $fileSizeFrom);
        static::assertEquals($fileTo->getMimeType(), $fileMimeFrom);
        static::assertEquals($fileTo->getExtension(), $file->getExtension());

        $fileTo->remove();

        static::assertFalse($file->isExists());
    }

    public function testCopy_2(): void
    {
        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage'));

        $fileBase = $this->faker->file('tests/mocks', $toStorage);

        /** @var BaseFile $file */
        $file = new BaseFile($fileBase);

        static::assertInstanceOf(BaseFile::class, $file);
        static::assertTrue($file->isExists());

        $path = Store::pathToStorage('storage/app/fail2');

        $file2 = $file->copy($path);

        static::assertTrue($file2->isExists());
        $file->remove();
        $file2->remove();

        static::assertFalse($file->isExists());
        static::assertFalse($file2->isExists());
    }


}
