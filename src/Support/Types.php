<?php

namespace Feugene\Files\Support;

/**
 * Class Types
 *
 * @package Feugene\Files\Support
 */
class Types
{

    /**
     * Compare MIME types
     *
     * @param string $mimeExist
     * @param string $mimeAsk
     *
     * @return bool
     */
    public static function mimeIs(string $mimeExist, string $mimeAsk): bool
    {
        if (empty($mimeExist) || empty($mimeAsk)) {
            return false;
        }

        $mimeAsk = self::normalizeMime($mimeAsk);
        $mimeExist = self::normalizeMime($mimeExist);

        if ($mimeExist === $mimeAsk) {
            return true;
        }

        if (mb_strpos($mimeAsk, '/') !== false) {
            list($typeAsk, $formatAsk) = explode('/', $mimeAsk);
        } else {
            $typeAsk = $mimeAsk;
            $formatAsk = null;
        }

        list($typeExist, $formatExist) = explode('/', $mimeExist);

        if ($typeAsk === '*' && (is_null($formatAsk) || $formatAsk === $formatExist)) {
            return true;
        }

        if ($typeAsk !== $typeExist) {
            return false;
        }

        if ($formatAsk === '*' || is_null($formatAsk)) {
            return true;
        }

        return $formatExist === $formatAsk;
    }

    /**
     * @param string $exist
     * @param string $ask
     *
     * @return bool
     */
    public static function extensionIs(string $exist, string $ask): bool
    {
        if ($ask === '*') {
            return true;
        }

        $exist = self::normalizeExtension($exist);

        if (empty($exist) || empty($ask)) {
            return false;
        }

        $ask = self::normalizeMime($ask);

        return $exist === $ask;
    }

    /**
     * @param string $exist
     * @param array  $list
     *
     * @return bool
     */
    public static function extensionIn(string $exist, array $list): bool
    {
        foreach ($list as $item) {
            if (Types::extensionIs($exist, $item)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $exist
     * @param array  $list
     *
     * @return bool
     */
    public static function inMime(string $exist, array $list): bool
    {
        foreach ($list as $item) {
            if (Types::mimeIs($exist, $item)) {
                return true;
            }
        }

        return false;
    }


    /**
     * @param string $string
     *
     * @return string
     */
    private static function normalizeExtension(string $string): string
    {
        return static::toLower($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private static function normalizeMime(string $string): string
    {
        return static::toLower($string);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    private static function toLower(string $string): string
    {
        return mb_strtolower($string);
    }
}
