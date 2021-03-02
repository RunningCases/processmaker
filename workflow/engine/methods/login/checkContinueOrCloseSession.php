<?php

if (!empty($_POST['form'])) {
    if (!empty($_POST['form']['buttonContinue'])) {
        $_SESSION['__WEBENTRYCONTINUE__'] = true;
    }
    if (!empty($_POST['form']['buttonLogout'])) {
        $_SESSION = [];
    }
    G::header('Location: ' . $_SERVER['HTTP_REFERER']);
}