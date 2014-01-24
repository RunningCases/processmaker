#!/usr/bin/env php
<?php

/*
 * Script o build vendors that requires make some builds and copy some files to a determined path
 *
 * @license Colosa Inc.
 * @author Erik Amaru Ortiz
 */

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

echo PHP_EOL;
out("Building JS Projects ", 'info');
out("--------------------", 'info');

foreach ($projects as $project) {
    echo PHP_EOL;
    out("=> Building project: ", 'info', false);
    echo $project.' '.PHP_EOL;
    chdir($vendorDir.DS.$project);
    echo `rake`;
    out("Completed!", 'success');
}

echo PHP_EOL;


out("Copying project files to its destination", 'info', true);
out("----------------------------------------", 'info', true);

$destinationDir = dirname(__FILE__) . DS . 'workflow/public_html/lib';

if (! is_dir($destinationDir)) {
    mkdir($destinationDir, 0777);
}
if (! is_dir($destinationDir.'/js')) {
    mkdir($destinationDir.'/js', 0777);
}
if (! is_dir($destinationDir.'/css')) {
    mkdir($destinationDir.'/css', 0777);
}
if (! is_dir($destinationDir.'/img')) {
    mkdir($destinationDir.'/img', 0777);
}
if (! is_dir($destinationDir.'/mafe')) {
    mkdir($destinationDir.'/mafe', 0777);
}
if (! is_dir($destinationDir.'/pmUI')) {
    mkdir($destinationDir.'/pmUI', 0777);
}


$filesCollection = array(
    // Libs
    "jquery/jquery/jquery-1.10.2.min.js" => "js/jquery-1.10.2.min.js",
    "underscore/underscore/underscore-min.js" => "js/underscore-min.js",
    "colosa/pmUI/libraries/restclient/restclient-min.js" => "js/restclient-min.js",

    // MichelangeloFE
    "colosa/MichelangeloFE/lib/wz_jsgraphics/wz_jsgraphics.js" => "js/wz_jsgraphics.js",
    "colosa/MichelangeloFE/build/js/designer.js" => "mafe/designer.js",
    "colosa/MichelangeloFE/build/js/mafe.min.js" => "mafe/mafe.min.js",
    "colosa/MichelangeloFE/build/css/mafe.css" => "mafe/mafe.css",
    "colosa/MichelangeloFE/build/img/*" => "img/",
    // pmUI
    "colosa/pmUI/libraries/jquery.layout/LayoutPanel.css" => "css/jquery.layout.css",
    "colosa/pmUI/libraries/jquery-ui/css/css-customized/jquery-ui-1.10.3.custom.css" => "css/jquery-ui-1.10.3.custom.min.css",
    "colosa/pmUI/libraries/dataTables/css/jquery.dataTables.css" => "css/jquery.dataTables.css",
    "colosa/pmUI/libraries/jquery.layout/jquery.layout.min.js" => "js/jquery.layout.min.js",
    "colosa/pmUI/libraries/jquery-ui/js/jquery-ui-1.10.3.custom.min.js" => "js/jquery-ui-1.10.3.custom.min.js",
    "colosa/pmUI/libraries/dataTables/js/jquery.dataTables.min.js" => "js/jquery.dataTables.min.js",

    array(
        "try_files" => array("colosa/pmUI/build/js/min/pmui-1.0.0.min.js", "colosa/pmUI/build/js/pmui-1.0.0.js"),
        "to_file" => "pmUI/pmui-1.0.0.js"
    ),
    "colosa/pmUI/build/css/pmui-1.0.0.css" => "pmUI/pmui-1.0.0.css",
    "colosa/pmUI/build/img/*" => "img/",

);

out("* Destination dir: ", 'info', false);
echo $destinationDir . PHP_EOL.PHP_EOL;

$successCount = 0;

foreach ($filesCollection as $source => $target) {
    if (! is_array($target)) {
        if (strpos($source, '*') !== false) {
            out("Create dir: ", 'info', false);
            echo $target;
            out(" from source: ", 'info', false);
            echo $source;
            out(" [DONE]", "success", true) . PHP_EOL;
            echo `cp -Rf $vendorDir/$source $destinationDir/$target`;
            $successCount++;
        } else {
            out("Create file: ", 'info', false);
            echo $target;
            out(" from source: ", 'info', false);
            echo $source;

            if (file_exists("$vendorDir/$source")) {
                out(" [DONE]", "success", true) . PHP_EOL;
                echo `cp -Rf $vendorDir/$source $destinationDir/$target`;
                $successCount++;
            } else {
                out(" [FAILED]", "error", true) . PHP_EOL;
            }
        }
    } else {
        out("Create file: ", 'info', false);
        echo $target['to_file'];
        out(" from source: ", 'info', false);

        $sw = true;
        $files = $target['try_files'];
        $target = $target['to_file'];

        foreach ($files as $file) {
            if (file_exists("$vendorDir/$file")) {
                echo $file;
                out(" [DONE]", "success", true) . PHP_EOL;
                echo `cp -Rf $vendorDir/$file $destinationDir/$target`;
                $successCount++;
                $sw = false;
                break;
            }
        }

        if ($sw) {
            echo '('.implode(', ', $files).')';
            out(" [FAILED]", "error", true) . PHP_EOL;
        }
    }
}

$n = count($filesCollection);
echo PHP_EOL;
echo sprintf("- Finished, Copied [%s/%s] files.", $successCount, $n).PHP_EOL;

if ($successCount == count($filesCollection)) {
    out(sprintf("- All files copied successfully!", $successCount, $n), "success", true);
} else {
    out("- Finished but with errors while copying!", "error", true);
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
        'error' => "\033[0;35;31m%s\033[0m",
        'info' => "\033[1;33;34m%s\033[0m"
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