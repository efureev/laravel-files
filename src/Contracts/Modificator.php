<?php

namespace Feugene\Files\Contracts;

/**
 * Interface Modificator
 *
 * @package Feugene\Files\Contracts
 */
interface Modificator
{
    public function handle($model, ... $options);
}
