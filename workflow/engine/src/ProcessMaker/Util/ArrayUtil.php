<?php
namespace ProcessMaker\Util;

class ArrayUtil
{
    public static function boolToIntValues($array)
    {
        array_walk($array, function (&$v) {
            if ($v === false) $v = 0;
            elseif ($v === true) $v = 1;
            elseif ($v === null) $v = 0;
        });

        return $array;
    }
}