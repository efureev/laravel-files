<?php

namespace Feugene\Files\Tests\Feature;

use Feugene\Files\Contracts\UploadService;
use Feugene\Files\Models\File;
use Feugene\Files\Services\Actions\AfterModelAction;
use Feugene\Files\Services\Actions\BeforeBaseAction;
use Feugene\Files\Tests\AbstractTestCase;
use Feugene\Files\Types\BaseFile;
use Illuminate\Http\UploadedFile;

/**
 * Class UploadServiceTest
 *
 * @package Feugene\Files\Tests\Feature
 */
class UploadServiceTest extends AbstractTestCase
{

    public function testSimpleUpload(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);

        $file = UploadedFile::fake()->create('document.pdf', 200);
        $service->setUploadedFiles($file);
        $list = $service->setPath('storage/test')->upload();
        static::assertInstanceOf(\Illuminate\Support\Collection::class, $list);
        static::assertInstanceOf(BaseFile::class, $list->first());
        static::assertCount(1, $list);
    }

    public function testSimpleUpload_cyrillic(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);

        $file = UploadedFile::fake()->create('тест.pdf', 200);
        $service->setUploadedFiles($file);
        $service->uniqueFileName = false;
        $list = $service->setPath('storage/test')->upload();

        static::assertInstanceOf(\Illuminate\Support\Collection::class, $list);
        static::assertInstanceOf(BaseFile::class, $list->first());
        static::assertCount(1, $list);

        $file = UploadedFile::fake()->create('тест.pdf', 3200);
        $service->setUploadedFiles($file);
        $list = $service->upload();
        
        static::assertInstanceOf(\Illuminate\Support\Collection::class, $list);
        static::assertInstanceOf(BaseFile::class, $list->first());
        static::assertCount(1, $list);
    }

    /**
     * @expectedException \Feugene\Files\Exceptions\NotAllowFileTypeToUploadException
     */
    public function testSimpleUploadFailByDisallowExt(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);
        $service->setDisallowFileTypes(['exe', 'bin', 'php']);

        $fileExe = UploadedFile::fake()->create('main.exe', 2000);
        $filePhp = UploadedFile::fake()->create('index.php', 100);

        $service->setUploadedFiles([$fileExe, $filePhp]);

        $service->upload();
    }


    /**
     * @expectedException \Feugene\Files\Exceptions\NotAllowFileTypeToUploadException
     */
    public function testSimpleUploadFailByDisallowExt_2(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);
        $service->setDisallowFileTypes('php');

        $filePhp = UploadedFile::fake()->create('index.php', 100);

        $service->setUploadedFiles($filePhp);

        $service->upload();
    }

    /**
     * @expectedException \Php\Support\Exceptions\InvalidParamException
     */
    public function testSimpleUploadFailByDisallowExt_fail(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);
        $service->setDisallowFileTypes(function () {
        });

        $filePhp = UploadedFile::fake()->create('index.php', 100);

        $service->setUploadedFiles($filePhp);

        $service->upload();
    }

    public function testUploadWithActions(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);

        $file1 = UploadedFile::fake()->create('image.jpg', 200);
        $file2 = UploadedFile::fake()->create('document.pdf', 1200);
        $service->setUploadedFiles([$file1, $file2]);
        $service->uniqueFileName = false;

        $list = $service
            ->setPath('storage/test')
            ->setAction(BeforeBaseAction::class, 'before')
            ->setAction(AfterModelAction::class, 'after')
            ->setBeforeAction(BeforeBaseAction::class)
            ->setBeforeAction(function ($file) {
                return $file;
            })
            ->setAfterAction(function ($file) {
                return $file;
            })
            ->upload();

        static::assertInstanceOf(\Illuminate\Support\Collection::class, $list);
        static::assertInstanceOf(File::class, $list->first());
        static::assertCount(2, $list);

        $file = UploadedFile::fake()->create('document.pdf', 300);
        $service->setUploadedFiles($file);

        $list = $service
            ->setPath('storage/test')
            ->upload();

        static::assertInstanceOf(\Illuminate\Support\Collection::class, $list);
        static::assertInstanceOf(File::class, $list->first());
        static::assertCount(1, $list);
    }

    /**
     * @expectedException \Php\Support\Exceptions\InvalidParamException
     */
    public function testUploadWithActionsFail_1(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);

        $file1 = UploadedFile::fake()->create('image.jpg', 200);
        $service->setUploadedFiles($file1);

        $service
            ->setPath('storage/test')
            ->setAction(AfterModelAction::class, 'before')
            ->upload();
    }

    /**
     * @expectedException \Php\Support\Exceptions\InvalidParamException
     */
    public function testUploadWithActionsFail_2(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);

        $file1 = UploadedFile::fake()->create('image.jpg', 200);
        $service->setUploadedFiles($file1);

        $service
            ->setPath('storage/test')
            ->setAction(BeforeBaseAction::class, 'after')
            ->upload();
    }

    /**
     * @expectedException \Php\Support\Exceptions\InvalidParamException
     */
    public function testUploadWithActionsFail_3(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);

        $file1 = UploadedFile::fake()->create('image.jpg', 200);
        $service->setUploadedFiles($file1);

        $service
            ->setPath('storage/test')
            ->setAction(new AfterModelAction, 'after')
            ->upload();
    }

    /**
     * @expectedException \Feugene\Files\Exceptions\MissingFilesToUploadException
     */
    public function testUploadWithActionsFail_4(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);
        $service->upload();
    }
}
