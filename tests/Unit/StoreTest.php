<?php

namespace Feugene\Files\Tests\Unit;

use Feugene\Files\Support\Store;

/**
 * Class StoreTest
 *
 * @package Feugene\Files\Tests\Unit
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
