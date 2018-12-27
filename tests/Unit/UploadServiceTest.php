<?php

namespace Feugene\Files\Tests\Unit;

use Feugene\Files\Contracts\UploadService;
use Feugene\Files\Services\AfterModelAction;
use Feugene\Files\Services\BeforeBaseAction;
use Feugene\Files\Support\Store;
use Feugene\Files\Types\BaseFile;

/**
 * Class UploadServiceTest
 *
 * @package Feugene\Files\Tests\Unit
 */
class UploadServiceTest extends AbstractUnitTestCase
{
    public function testInstance(): void
    {
        app(UploadService::class);
        static::assertInstanceOf(UploadService::class, app(UploadService::class));
    }

    public function testSetPath(): void
    {
        $service = app(UploadService::class);
        static::assertEquals('', $service->getPath());

        $service->setPath('test/path');
        static::assertEquals('test/path', $service->getPath());

        $service->setPath('storage');
        static::assertEquals('storage', $service->getPath());
    }

    public function testGetDriver(): void
    {
        $service = app(UploadService::class);
        static::assertEquals('local', $service->getDriver());

        $service->setDriver('s3');
        static::assertEquals('s3', $service->getDriver());
    }

    /**
     * @expectedException \Feugene\Files\Exceptions\NotAllowFileTypeToUploadException
     */
    public function testVerify(): void
    {
        $service = app(UploadService::class);

        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage/app/test'));

        $fileBase = $this->faker->file('tests/mocks', $toStorage);
        /** @var BaseFile $file */
        $file = new BaseFile($fileBase);

        $fileExe = $file->move($file->getPath(), $file->getBasename($file->getExtension()) . 'exe');

        $service->setDisallowFileTypes(['rar', 'exe'])->verifyExtensions(collect([$fileExe]));
    }

    /**
     * @expectedException \Php\Support\Exceptions\InvalidParamException
     */
    public function testSetInvalidBeforeActions(): void
    {
        $service = app(UploadService::class);

        $service->setAction(BeforeBaseAction::class, 'before');
        $service->setBeforeAction(BeforeBaseAction::class);
        $service->setBeforeAction([BeforeBaseAction::class]);
        $service->setBeforeAction(function ($file) {
            return $file;
        });

        $service->setBeforeAction(new BeforeBaseAction);
    }

    /**
     * @expectedException \Php\Support\Exceptions\InvalidParamException
     */
    public function testSetInvalidAfterActions(): void
    {
        $service = app(UploadService::class);

        $service->setAction(AfterModelAction::class, 'after');
        $service->setAfterAction(AfterModelAction::class);
        $service->setAfterAction([AfterModelAction::class]);
        $service->setAfterAction(function ($file) {
            return $file;
        });

        $service->setBeforeAction(new BeforeBaseAction);
    }

    /**
     * @expectedException \Feugene\Files\Exceptions\MissingFilesToUploadException
     */
    public function testEmptyUpload(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);

        $service->upload();
    }
}
