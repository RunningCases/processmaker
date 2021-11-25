#!/usr/bin/env php
<?php

/*
 * Script o build vendors that requires make some builds and copy some files to a determined path
 *
 * @license Colosa Inc.
 * @author Erik Amaru Ortiz
 */

$rootPath = __DIR__;

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

$hashVendors = '';
$hashes = array();
foreach ($projects as $project) {
    echo PHP_EOL;
    out("=> Building project: ", 'info', false);
    $output = array();
    echo $project.' '.PHP_EOL;
    chdir($vendorDir.DS.$project);
    exec ('git rev-parse --short HEAD', $hashes);

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
//get the hash for all vendor projects
$hashVendors = implode ('-', $hashes );

//the script is completed if the option is Debug = 1
if ($debug) {
    echo PHP_EOL;
    die;
}

out("=> compresing and combining js files", 'info');

$jsFiles = array (
    "workflow/public_html/lib/js/wz_jsgraphics.js",
    "workflow/public_html/lib/js/jquery-1.10.2.min.js",
    "workflow/public_html/lib/js/underscore-min.js",
    "workflow/public_html/lib/js/jquery-ui.min.js",
    "workflow/public_html/lib/js/jquery.layout.min.js",
    "workflow/public_html/lib/js/modernizr.js",
    "workflow/public_html/lib/js/restclient.min.js",
    "workflow/public_html/lib/pmUI/pmui.min.js",
    "workflow/public_html/lib/mafe/mafe.min.js",
    "workflow/public_html/lib/mafe/designer.min.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/tiny_mce.js",

    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pmGrids/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pmSimpleUploader/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pmVariablePicker/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/visualchars/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/xhtmlxtras/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/wordcount/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/visualblocks/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/table/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/template/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/visualblocks/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/preview/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/print/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/style/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/save/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/tabfocus/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/searchreplace/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/paste/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/media/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/lists/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/insertdatetime/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/example/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/pagebreak/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/example_dependency/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/noneditable/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/fullpage/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/layer/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/legacyoutput/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/fullscreen/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/iespell/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/inlinepopups/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/autoresize/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/contextmenu/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advlist/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/autolink/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/directionality/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/emotions/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/themes/advanced/editor_template.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advhr/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advlink/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/advimage/editor_plugin.js",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/nonbreaking/editor_plugin.js",

    "gulliver/js/codemirror/lib/codemirror.js",
    "gulliver/js/codemirror/mode/javascript/javascript.js",
    "gulliver/js/codemirror/addon/edit/matchbrackets.js",
    "gulliver/js/codemirror/mode/htmlmixed/htmlmixed.js",
    "gulliver/js/codemirror/mode/xml/xml.js",
    "gulliver/js/codemirror/mode/css/css.js",
    "gulliver/js/codemirror/mode/clike/clike.js",
    "gulliver/js/codemirror/mode/php/php.js",
);


$bigHandler = fopen ("{$rootPath}/workflow/public_html/lib/js/mafe-{$hashVendors}.js", "w");
foreach ($jsFiles as $jsFile) {
    $fileContent = file_get_contents("{$rootPath}/$jsFile");
    fprintf($bigHandler, "%s\n\n", $fileContent);
    printf (" - File %s added to big.js\n", basename($jsFile));
}
fclose ($bigHandler);
printf ( "mafe-{$hashVendors}.js file has %d bytes\n", filesize("{$rootPath}/workflow/public_html/lib/js/mafe-{$hashVendors}.js"));



out("=> compresing and combining css files", 'info');

$cssFiles = array (
    "gulliver/js/codemirror/lib/codemirror.css",

    "gulliver/js/tinymce/jscripts/tiny_mce/themes/advanced/skins/o2k7/ui.css",
    "gulliver/js/tinymce/jscripts/tiny_mce/themes/advanced/skins/o2k7/ui_silver.css",
    "gulliver/js/tinymce/jscripts/tiny_mce/plugins/inlinepopups/skins/clearlooks2/window.css",
    "gulliver/js/tinymce/jscripts/tiny_mce/themes/advanced/skins/o2k7/content.css",

    "workflow/public_html/lib/pmUI/pmui.min.css",
    "workflow/public_html/lib/mafe/mafe.min.css"

);
$bigHandler = fopen ("{$rootPath}/workflow/public_html/lib/css/mafe-{$hashVendors}.css", "w");
foreach ($cssFiles as $cssFile) {
    $fileContent = file_get_contents("{$rootPath}/$cssFile");
    fprintf($bigHandler, "%s\n\n", $fileContent);
    printf (" - File %s added to big.css\n", basename($cssFile));
}
fclose ($bigHandler);
printf ( "mafe-{$hashVendors}.css file has %d bytes\n", filesize("{$rootPath}/workflow/public_html/lib/css/mafe-{$hashVendors}.css"));

file_put_contents("{$rootPath}/workflow/public_html/lib/buildhash", $hashVendors);
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
