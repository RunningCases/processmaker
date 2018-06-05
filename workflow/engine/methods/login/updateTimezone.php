<?php

// Update the selected timezone for the user
if (PMLicensedFeatures::getSingleton()->verifyfeature('oq3S29xemxEZXJpZEIzN01qenJUaStSekY4cTdJVm5vbWtVM0d4S2lJSS9qUT0=')) {
    // Update User Time Zone
    if (isset($_POST['form']['BROWSER_TIME_ZONE'])) {
        $user = new Users();
        $user->update(['USR_UID' => $_SESSION['USER_LOGGED'], 'USR_TIME_ZONE' => $_POST['form']['BROWSER_TIME_ZONE']]);
        $_SESSION['USR_TIME_ZONE'] = $_POST['form']['BROWSER_TIME_ZONE'];
        unset($_SESSION['__TIME_ZONE_FAILED__'], $_SESSION['BROWSER_TIME_ZONE']);
    }

    // Redirect to origin page
    G::header('Location: ' . $_SERVER['HTTP_REFERER']);
}
