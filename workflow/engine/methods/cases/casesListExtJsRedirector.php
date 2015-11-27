<script>
if (typeof window.parent != 'undefined') {
<?php
$enablePMGmail = false;
require_once (PATH_HOME . "engine" . PATH_SEP . "classes" . PATH_SEP . "class.pmDrive.php");
$pmDrive = new PMDrive();
$enablePMGmail = $pmDrive->getStatusService();
if (isset( $_GET['ux'] )) {
    switch ($_GET['ux']) {
        case 'SIMPLIFIED':
        case 'SWITCHABLE':
        case 'SINGLE':
            $url = '../home';
            break;
        default:
            $url = 'casesListExtJs';
    }
} else if( isset( $_GET['gmail']) && !empty($enablePMGmail) && $enablePMGmail==1 ){
    $url = 'derivatedGmail';
} else {
    $url = 'casesListExtJs';
}
if (isset( $_GET['ux'] )) {
    echo 'if (typeof window.parent.ux_env != \'undefined\') {';
}
echo "  window.parent.location.href = '$url';";
if (isset( $_GET['ux'] )) {
    if(PMLicensedFeatures::getSingleton()->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09') && !empty($enablePMGmail) && $enablePMGmail==1){
	echo '} else { window.parent.location.href = \'derivatedGmail\'; }';
    } else {
        echo '} else { window.parent.location.href = \'casesListExtJs\'; }';
    }   
}
?>
}
</script>