<?php

namespace Feugene\Files\Tests;

use AvtoDev\DevTools\Tests\PHPUnit\AbstractLaravelTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class AbstractTestCase extends AbstractLaravelTestCase
{
    use WithFaker;
}
