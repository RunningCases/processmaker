<?php

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
    'colosa/michelangelofe',
    'colosa/pmui',
    'colosa/restclient'
);

echo PHP_EOL;
out(" *** Building js projects ***", 'info', true);

foreach ($projects as $project) {
    echo PHP_EOL;
    for($i=0; $i<40; $i++) echo '-';
    echo PHP_EOL;
    out(" Building: $project ", 'success', true);
    for($i=0; $i<40; $i++) echo '-';
    echo PHP_EOL.PHP_EOL;

    chdir($vendorDir.DS.$project);
    echo `rake`;    
}

echo PHP_EOL;


out(" *** Compying project files to its destination ***", 'info', true);

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
if (! is_dir($destinationDir.'/mafe')) {
    mkdir($destinationDir.'/mafe', 0777);
}
if (! is_dir($destinationDir.'/pmUI')) {
    mkdir($destinationDir.'/pmUI', 0777);
}


$filesCollection = array(
    "jquery/jquery/jquery-1.10.2.min.js" => "js/jquery-1.10.2.min.js",
    "underscore/underscore/underscore-min.js" => "js/underscore-min.js",
    //libs
    /*"colosa/michelangelo-fe-libs/jQueryLayout/jquery.layout.min.js" => "js/jquery.layout.min.js",
    "colosa/michelangelo-fe-libs/jQueryUI/jquery-ui-1.10.3.custom.min.js" => "js/jquery-ui-1.10.3.custom.min.js",
    "colosa/michelangelo-fe-libs/jQueryUI/jquery-ui-1.10.3.custom.min.css" => "js/jquery-ui-1.10.3.custom.min.css",
    "colosa/michelangelo-fe-libs/wz_jsgraphics/wz_jsgraphics.js" => "js/wz_jsgraphics.js",*/    

    "colosa/restclient/restclient-min.js" => "js/restclient-min.js",
    
    // michelangelofe
    "colosa/michelangelofe/lib/wz_jsgraphics/wz_jsgraphics.js" => "js/wz_jsgraphics.js",
    "colosa/michelangelofe/build/js/designer.js" => "mafe/designer.js",
    "colosa/michelangelofe/build/js/mafe.min.js" => "mafe/mafe.min.js",
    "colosa/michelangelofe/build/css/mafe.css" => "mafe/mafe.css",
    "colosa/michelangelofe/build/img/*" => "img/",
    // pmui
    "colosa/pmui/libraries/jquery.layout/jquery.layout.min.js" => "js/jquery.layout.min.js",
    "colosa/pmui/libraries/jquery.layout/LayoutPanel.css" => "css/jquery.layout.css",
    "colosa/pmui/libraries/jquery-ui/js/jquery-ui-1.10.3.custom.min.js" => "js/jquery-ui-1.10.3.custom.min.js",
    "colosa/pmui/libraries/jquery-ui/css/css-customized/jquery-ui-1.10.3.custom.css" => "css/jquery-ui-1.10.3.custom.min.css",

    "colosa/pmui/build/js/min/pmui-1.0.0.min.js" => "pmUI/pmui-1.0.0.js",
    "colosa/pmui/build/css/pmui-1.0.0.css" => "pmUI/pmui-1.0.0.css",
    "colosa/pmui/build/img/*" => "img/",

);

echo "Destination dir: $destinationDir" . PHP_EOL.PHP_EOL;

foreach ($filesCollection as $source => $target) {
    out("Copy: ", 'info', false);
    echo "$source $target" . PHP_EOL;
    echo `cp -Rf $vendorDir/$source $destinationDir/$target`;
}

echo PHP_EOL;





















/////////////////////

/**
 * colorize output
 */
function out($text, $color = null, $newLine = true)
{
    $styles = array(
        'success' => "\033[0;32m%s\033[0m",
        'error' => "\033[31;31m%s\033[0m",
        'info' => "\033[33;33m%s\033[0m"
    );

    $format = '%s';

    if (isset($styles[$color]) && USE_ANSI) {
        $format = $styles[$color];
    }

    if ($newLine) {
        $format .= PHP_EOL;
    }

    printf($format, $text);
}