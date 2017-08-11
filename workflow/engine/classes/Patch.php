<?php

/**
 * class, helping to set some not desirable settings but necesary
 * @author reav
 *
 */

/**
 * class, helping to set some not desirable settings but necesary
 * @author reav
 *
 */abstract class patch
{
    static protected $isPathchable = false;
    static public $dbAdapter = 'mysql';
    abstract static public function isApplicable();
    abstract static public function execute();
}
