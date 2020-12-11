<?php

global $G_PUBLISH;
$G_PUBLISH = new Publisher();
try {
    echo file_get_contents(PATH_HOME . "public_html/lib/authenticationSources/index.html");
} catch (Exception $e) {
    $message = [
        'MESSAGE' => $e->getMessage()
    ];
    $G_PUBLISH->AddContent('xmlform', 'xmlform', 'login/showMessage', '', $message);
    G::RenderPage('publish', 'blank');
}