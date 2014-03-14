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

    /**
     * Function that sorts an associative array by given array keys
     *
     * @author Erik Amaru Ortiz <erik@colosa.com, aortiz.erik@gmail.com>
     * @param array $data associative contains array data to sort by key.
     * @param array $columns contains the key to sort by those them.
     * @param mixed $direction this param by default is SORT_ASC, it can contains a value from [SORT_ASC|SORT_DESC]
     *                         php constants, or it can be a array(SORT_ASC,SORT_DESC,..), it must have the same length
     *                         of $columns array.
     * @return array
     * @throws \Exception
     *
     * Example:
     *   $data = array();
     *   $data[] = array('volume' => 67, 'edition' => 2);
     *   $data[] = array('volume' => 86, 'edition' => 1);
     *   $data[] = array('volume' => 85, 'edition' => 6);
     *   $data[] = array('volume' => 98, 'edition' => 2);
     *   $data[] = array('volume' => 86, 'edition' => 6);
     *   $data[] = array('volume' => 67, 'edition' => 7);
     *
     *   $r = ArrayUtil::sort($data, array("volume", "edition"), array(SORT_DESC, SORT_ASC));
     *   print_r($r);
     *
     * Example Output:
     * Array
     * (
     *     [0] => Array (
     *         [volume] => 98
     *         [edition] => 2
     *     )
     *     [1] => Array (
     *         [volume] => 86
     *         [edition] => 1
     *     )
     *     [2] => Array (
     *         [volume] => 86
     *         [edition] => 6
     *     )
     *     [3] => Array (
     *         [volume] => 85
     *         [edition] => 6
     *     )
     *     [4] => Array (
     *         [volume] => 67
     *         [edition] => 2
     *     )
     *     [5] => Array (
     *         [volume] => 67
     *         [edition] => 7
     *     )
     * )
     */
    public static function sort($data, $columns, $direction = SORT_ASC)
    {
        $composedData = array();

        if (is_array($direction)) {
            if (count($direction) !== count($columns)) {
                throw new \Exception("Error, \$columns length and \$direction length must be equals.");
            }
        }

        foreach ($data as $row) {
            $j = 0;
            foreach ($columns as $i => $col) {
                $composedData[$j++][] = $row[$col];
                $composedData[$j++] = is_array($direction) ? $direction[$i] : $direction;
            }
        }

        $composedData[] = & $data;

        call_user_func_array("array_multisort", $composedData);

        return $data;
    }
}

