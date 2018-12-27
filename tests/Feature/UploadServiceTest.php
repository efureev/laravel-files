<?php

namespace Feugene\Files\Tests\Feature;

use Feugene\Files\Contracts\UploadService;
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

    /**
     * @expectedException \Feugene\Files\Exceptions\NotAllowFileTypeToUploadException
     */
    public function t2estSimpleUploadFailByDisallowExt(): void
    {
        /** @var \Feugene\Files\Services\UploadService $service */
        $service = app(UploadService::class);
        $service->setDisallowFileTypes(['exe', 'bin', 'php']);

        $fileExe = UploadedFile::fake()->create('main.exe', 2000);
        $filePhp = UploadedFile::fake()->create('index.php', 100);
        $service->setUploadedFiles([$fileExe, $filePhp]);

        $service->upload();
    }

    public function t2estUploadWithActions(): void
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

}
