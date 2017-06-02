<html>
    <head>
        <script>
<?php
/**
 * This page is redirected from the login page.
 */
G::LoadClass('pmFunctions');
$userUid = $_SESSION['USER_LOGGED'];
$userInfo = PMFInformationUser($userUid);
$result = [
    'user_logged' => $userUid,
    'userName'    => $userInfo['username'],
    'firstName'   => $userInfo['firstname'],
    'lastName'    => $userInfo['lastname'],
    'mail'        => $userInfo['mail'],
    'image'       => '../users/users_ViewPhoto?t='.microtime(true),
];
?>
            parent.fullfill(<?= G::json_encode($result) ?>);
        </script>
    </head>
</html>