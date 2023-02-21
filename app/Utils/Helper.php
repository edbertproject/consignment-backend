<?php
namespace App\Utils;

class Helper {

    /**
     * The helper for strict value must be array
     *
     * @param $value
     * @return array
     */
    public static function arrayStrict($value): array
    {
        return is_array($value) ? $value : [$value];
    }
}
