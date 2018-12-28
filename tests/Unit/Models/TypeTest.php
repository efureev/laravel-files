<?php

namespace Feugene\Files\Tests\Unit\Models;

use Feugene\Files\Models\File;
use Feugene\Files\Support\Store;

/**
 * Class TypeTest
 *
 * @package Feugene\Files\Tests\Unit\Models
 */
class TypeTest extends AbstractModelTestCase
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
     * {@inheritdoc}
     */
    protected function modelFactory(array $attributes = []): File
    {
        /** @var File $file */
        $file = factory(File::class)->make($attributes);

        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage/model'));

        $fileBase = $this->faker->file('tests/mocks/images', $toStorage);

        $baseFile = new \Feugene\Files\Types\BaseFile($fileBase);

        $file->setBaseFile($baseFile);
        $file->save();

        return $file;
    }

    public function testIsMime(): void
    {
        $toStorage = static::getOrCreateFilePath(Store::pathToStorage('storage/model'));

        for ($i = 0; $i <= 30; $i++) {
            $fileBase = $this->faker->file('tests/mocks', $toStorage);

            $baseFile = new \Feugene\Files\Types\BaseFile($fileBase);

            /** @var File $file */
            $file = new File;
            $file->setBaseFile($baseFile);

            switch ($file->ext) {
                case 'jpg':
                    static::assertEquals('image/jpeg', $file->mime);
                    static::assertTrue($file->isMime('image/jpeg'));
                    static::assertTrue($file->isMime('image'));
                    static::assertTrue($file->isMime('image/*'));
                    static::assertTrue($file->inMimeList(['image/jpeg', 'image/png']));
                    static::assertTrue($file->inMimeList(['image/png', 'image']));
                    static::assertFalse($file->inMimeList(['image/png', '']));

                    static::assertFalse($file->isMime('image/png'));
                    static::assertFalse($file->isMime('audio/mpeg'));
                    static::assertFalse($file->isMime(''));

                    static::assertTrue($file->isExtension('jpg'));
                    static::assertTrue($file->inExtensionList(['jpg', 'png']));
                    static::assertFalse($file->inExtensionList(['jpeg', 'png']));
                    static::assertFalse($file->isExtension('png'));
                    static::assertFalse($file->isExtension('txt'));
                    static::assertFalse($file->isExtension(''));
                    static::assertTrue($file->isImage());
                    static::assertFalse($file->isVideo());
                    static::assertFalse($file->isAudio());
                    static::assertFalse($file->isDocument());
                    static::assertFalse($file->isSvg());

                    break;
                case 'xlsx':
                    static::assertEquals('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', $file->mime);
                    static::assertTrue($file->isMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));
                    static::assertTrue($file->isMime('application/*'));
                    static::assertTrue($file->isMime('application'));
                    static::assertTrue($file->inMimeList(['application/*', 'image/png']));
                    static::assertTrue($file->inMimeList(['application', 'image']));
                    static::assertFalse($file->inMimeList(['image/png', '']));

                    static::assertFalse($file->isMime('image/png'));
                    static::assertFalse($file->isMime('audio/mpeg'));
                    static::assertFalse($file->isMime(''));

                    static::assertTrue($file->isExtension('xlsx'));
                    static::assertTrue($file->inExtensionList(['jpg', 'xlsx']));
                    static::assertFalse($file->inExtensionList(['jpeg', 'png']));
                    static::assertFalse($file->isExtension('png'));
                    static::assertFalse($file->isExtension('txt'));
                    static::assertFalse($file->isExtension(''));
                    static::assertTrue($file->isDocument());
                    static::assertFalse($file->isImage());
                    static::assertFalse($file->isVideo());
                    static::assertFalse($file->isAudio());
                    static::assertFalse($file->isSvg());

                    break;
                case 'xml':
                    static::assertEquals('inode/x-empty', $file->mime);
                    static::assertTrue($file->isMime('inode/x-empty'));
                    static::assertFalse($file->isMime('image/png'));
                    static::assertFalse($file->isMime(''));
                    static::assertFalse($file->isExtension(''));
                    static::assertTrue($file->isExtension('xml'));
                    static::assertTrue($file->inExtensionList(['xml', 'doc']));
                    static::assertFalse($file->isExtension('doc'));
                    static::assertFalse($file->isExtension('txt'));
                    static::assertTrue($file->isDocument());
                    static::assertFalse($file->isImage());
                    static::assertFalse($file->isVideo());
                    static::assertFalse($file->isAudio());
                    static::assertFalse($file->isSvg());
                    break;
                case 'mp3':
                    static::assertEquals('audio/mpeg', $file->mime);
                    static::assertTrue($file->isMime('audio/mpeg'));
                    static::assertFalse($file->isMime('image/png'));
                    static::assertFalse($file->isMime(''));
                    static::assertFalse($file->isExtension(''));
                    static::assertTrue($file->isExtension('mp3'));
                    static::assertTrue($file->inExtensionList(['xlsx', 'mp3']));
                    static::assertFalse($file->isExtension('doc'));
                    static::assertFalse($file->isExtension('txt'));
                    static::assertTrue($file->isAudio());
                    static::assertFalse($file->isImage());
                    static::assertFalse($file->isVideo());
                    static::assertFalse($file->isDocument());
                    static::assertFalse($file->isSvg());
                    break;
                case 'mp4':
                    static::assertEquals('video/mp4', $file->mime);
                    static::assertTrue($file->isMime('video/mp4'));
                    static::assertFalse($file->isMime('image/png'));
                    static::assertFalse($file->isExtension(''));
                    static::assertTrue($file->isExtension('mp4'));
                    static::assertTrue($file->inExtensionList(['mp4', 'mp4']));
                    static::assertFalse($file->isExtension('doc'));
                    static::assertFalse($file->isExtension('txt'));
                    static::assertTrue($file->isVideo());
                    static::assertFalse($file->isImage());
                    static::assertFalse($file->isDocument());
                    static::assertFalse($file->isAudio());
                    static::assertFalse($file->isSvg());
                    break;
                case 'txt':
                    static::assertEquals('text/plain', $file->mime);
                    static::assertTrue($file->isMime('text/plain'));
                    static::assertFalse($file->isMime('image/png'));
                    static::assertFalse($file->isMime(''));
                    static::assertFalse($file->isExtension(''));
                    static::assertTrue($file->inExtensionList(['xlsx', 'txt']));
                    static::assertFalse($file->isExtension('doc'));
                    static::assertTrue($file->isExtension('txt'));
                    static::assertTrue($file->isDocument());
                    static::assertFalse($file->isImage());
                    static::assertFalse($file->isVideo());
                    static::assertFalse($file->isAudio());
                    static::assertFalse($file->isSvg());
                    break;
            }
        }
    }


    public function testLoadImageFromFile()
    {
        $file = File::find($this->model->getKey());

        $type = $file->getType();
        static::assertInternalType('string', $type);

        /** @var File $toFile */
        $toFile = $file->toType();

        static::assertInstanceOf(File::class, $toFile);
        static::assertInstanceOf($type, $toFile);

        static::assertArraySubset($file->toArray(), $toFile->toArray());
    }
}
