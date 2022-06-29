<?php

/**
 * @file
 * Convenience file that registers autoload handler for HTML Purifier.
 * It also does some sanity checks.
 */

spl_autoload_register(function($class)
{
    return HTMLPurifier_Bootstrap::autoload($class);
});

if (ini_get('zend.ze1_compatibility_mode')) {
    trigger_error("HTML Purifier is not compatible with zend.ze1_compatibility_mode; please turn it off", E_USER_ERROR);
}

// vim: et sw=4 sts=4
