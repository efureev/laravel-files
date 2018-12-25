<?php

namespace Feugene\Files\Tests\Unit;

use Feugene\Files\Support\Store;

/**
 * Class BaseFileTest
 *
 * @package Feugene\Files\Tests\Unit\Models
 */
class StoreTest extends AbstractUnitTestCase
{
    public function testIsAbsolutePath(): void
    {
        static::assertTrue(Store::isAbsolutePath(Store::pathToStorage('')));
        static::assertTrue(Store::isAbsolutePath(Store::pathToStorage()));
        static::assertTrue(Store::isAbsolutePath('/tmp'));
        static::assertfalse(Store::isAbsolutePath('./tmp'));
    }

}
