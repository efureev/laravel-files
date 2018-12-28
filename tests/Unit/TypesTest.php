<?php

namespace Feugene\Files\Tests\Unit;

use Feugene\Files\Support\Types;

/**
 * Class TypesTest
 *
 * @package Feugene\Files\Tests\Unit
 */
class TypesTest extends AbstractUnitTestCase
{

    public function testMimeIs(): void
    {
        static::assertTrue(Types::mimeIs('images/jpg', 'images/jpg'));
        static::assertFalse(Types::mimeIs('images/jpg', ''));
        static::assertFalse(Types::mimeIs('', ''));
        static::assertTrue(Types::mimeIs('images/jpg', '*'));
        static::assertTrue(Types::mimeIs('images/jpg', 'images/*'));
        static::assertTrue(Types::mimeIs('images/jpg', 'images'));
        static::assertTrue(Types::mimeIs('images/jpg', '*'));

        static::assertFalse(Types::mimeIs('images/jpg', 'images/png'));
        static::assertFalse(Types::mimeIs('images/jpg', 'png'));

        static::assertFalse(Types::mimeIs('images/jpg', '*/png'));
        static::assertTrue(Types::mimeIs('images/jpg', '*/jpg'));
        static::assertFalse(Types::mimeIs('images/jpg', '/'));
        static::assertFalse(Types::mimeIs('', 'video'));
        static::assertFalse(Types::mimeIs('', 'images/jpg'));
        static::assertFalse(Types::mimeIs('', 'images/*'));
        static::assertFalse(Types::mimeIs('', '*'));
    }

    public function testExtensionIs(): void
    {
        static::assertTrue(Types::extensionIs('jpg', 'jpg'));
        static::assertFalse(Types::extensionIs('jpg', ''));
        static::assertFalse(Types::extensionIs('', ''));
        static::assertTrue(Types::extensionIs('', '*'));
        static::assertFalse(Types::extensionIs('', 'jpg'));
        static::assertTrue(Types::extensionIs('jpg', '*'));
        static::assertFalse(Types::extensionIs('jpg', 'png'));
    }

    public function testExtensionIn(): void
    {
        static::assertTrue(Types::extensionIn('jpg', ['jpg']));
        static::assertTrue(Types::extensionIn('jpg', ['png', 'jpg']));
        static::assertTrue(Types::extensionIn('jpg', ['svg', 'png', 'jpg']));
        static::assertTrue(Types::extensionIn('jpg', ['', 'png', 'jpg']));
        static::assertTrue(Types::extensionIn('jpg', ['', 'jpg']));
        static::assertTrue(Types::extensionIn('jpg', ['jpg', 'png', '']));
        static::assertTrue(Types::extensionIn('jpg', ['*']));

        static::assertFalse(Types::extensionIn('jpg', ['']));
        static::assertFalse(Types::extensionIn('jpg', []));
        static::assertFalse(Types::extensionIn('jpg', ['png']));
        static::assertFalse(Types::extensionIn('jpg', ['jpeg']));

        static::assertFalse(Types::extensionIn('', ['']));
        static::assertFalse(Types::extensionIn('', ['jpg']));
        static::assertTrue(Types::extensionIn('', ['*']));
    }

}
