<?php

// Get the Home Directory, snippet adapted from sysGeneric.php
$documentRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$sections = explode('/', $documentRoot);
array_pop($sections);
$pathHome = implode('/', $sections) . '/';

// Include the "paths_installed.php" file
require_once $pathHome . 'engine/config/paths_installed.php';

// Set the fonts styles file
$fileName = 'fonts.css';

// Check if the requested css file exists and if is accessible
if (!file_exists(PATH_DATA . 'fonts/tcpdf/' . $fileName)) {
    // Redirect to error page 404
    header('Location: /errors/error404.php');
    die();
} else {
    // Stream the font file
    header('Content-Type: text/css');
    readfile(PATH_DATA . 'fonts/tcpdf/' . $fileName);
}
