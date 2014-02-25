#!/usr/bin/env php
<?php

/*
 * Script o build vendors that requires make some builds and copy some files to a determined path
 *
 * @license Colosa Inc.
 * @author Erik Amaru Ortiz
 */

$config = @parse_ini_file("workflow/engine/config/env.ini");

$debug = !empty($config) && isset($config['debug']) ? $config['debug'] : 0;

define('DS', DIRECTORY_SEPARATOR);

// --no-ansi wins over --ansi
if (in_array('--no-ansi', $argv)) {
    define('USE_ANSI', false);
} elseif (in_array('--ansi', $argv)) {
    define('USE_ANSI', true);
} else {
    // On Windows, default to no ANSI, except in ANSICON and ConEmu.
    // Everywhere else, default to ANSI if stdout is a terminal.
    define('USE_ANSI',
    (DIRECTORY_SEPARATOR == '\\')
        ? (false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI'))
        : (function_exists('posix_isatty') && posix_isatty(1))
    );
}


$vendorDir = dirname(__FILE__) . DS . 'vendor';

if (! is_dir($vendorDir )) {
    echo "Vendor directory is missing!" . PHP_EOL;
    exit();
}

$projects = array(
    'colosa/MichelangeloFE',
    'colosa/pmUI'
);

out("build-vendor.php", 'purple');

out("generating files for ", 'purple', false);
out( $debug ? 'debug' : 'production', 'success', false);
out(" mode", 'purple');

foreach ($projects as $project) {
    echo PHP_EOL;
    out("=> Building project: ", 'info', false);
    echo $project.' '.PHP_EOL;
    chdir($vendorDir.DS.$project);
    if ($debug) {
        exec ('rake pmBuildDebug', $output, $exitCode );
    } else {
        exec ('rake pmBuild', $output, $exitCode );
    }

    if ($exitCode) {
        out("$project executed with errors!", 'error');
        foreach ($output as $line) {
            print "$line\n";
        }
        echo PHP_EOL;
        die;
    } else {
        foreach ($output as $line) {
            print "$line\n";
        }
        out("$project completed", 'success');
        echo PHP_EOL;
    }

}

echo PHP_EOL;




/////////////////////


/**
 * colorize output
 */
function out($text, $color = null, $newLine = true)
{
    $styles = array(
        'success' => "\033[0;35;32m%s\033[0m",
        'error'   => "\033[0;35;31m%s\033[0m",
        'purple'  => "\033[0;35;35m%s\033[0m",
        'info'    => "\033[1;33;34m%s\033[0m"
    );

    $format = '%s';

    if (isset($styles[$color])) {
        $format = $styles[$color];
    }

    if ($newLine) {
        $format .= PHP_EOL;
    }

    printf($format, $text);
}
