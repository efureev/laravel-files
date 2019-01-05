<?php

namespace Feugene\Files\Entities;


use Feugene\Files\Contracts\Modificator;

/**
 * Class AbstractModificator
 *
 * @package Feugene\Files\Entities
 */
abstract class AbstractModificator implements Modificator
{
    /**
     * @var mixed
     */
    protected $value;

    /**
     * @return string
     */
    public function getValueString(): string
    {
        return (string)$this->value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return static::getTypeName() . ':' . $this->getValueString();
    }

    /**
     * @return string
     */
    public static function getTypeName(): string
    {
        return strtolower(str_replace('Modificator', '', static::shortClassName()));
    }

    /**
     * @return string
     */
    protected static function shortClassName(): string
    {
        $array = explode('\\', static::class);

        return array_pop($array);
    }

}
