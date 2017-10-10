<?php
global $RBAC;
$RBAC->allows(basename(__FILE__), $_GET['MAIN_DIRECTORY']);

$mainDirectory = !empty($_GET['MAIN_DIRECTORY']) ? $_GET['MAIN_DIRECTORY'] : '';
$proUid = !empty($_GET['PRO_UID']) ? $_GET['PRO_UID'] : '';
$currentDirectory = !empty($_GET['CURRENT_DIRECTORY']) ? $_GET['CURRENT_DIRECTORY'] . PATH_SEP : '';
$file = !empty($_GET['FILE']) ? $_GET['FILE'] : '';
$extension = (!empty($_GET['sFilextension']) && $_GET['sFilextension'] === 'javascript') ? '.js' : '';

//validated process exists, return throw if not exists.
$process = new Process();
$process->load($proUid);

switch ($mainDirectory) {
    case 'mailTemplates':
        $directory = PATH_DATA_MAILTEMPLATES;
        break;
    case 'public':
        $directory = PATH_DATA_PUBLIC;
        break;
    default:
        die();
        break;
}

$directory .= $proUid . PATH_SEP;

//Delete return directory because path exists in PATH_DATA_MAILTEMPLATES or PATH_DATA_PUBLIC
foreach (pathinfo($currentDirectory) as $value) {
    if ($value !== '..') {
        $directory .= $value . PATH_SEP;
    }
}
$file .= $extension;

if (file_exists($directory . $file)) {
    G::streamFile($directory . $file, true);
}
