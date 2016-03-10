<script>
if (typeof window.parent != 'undefined') {
<?php
/*----------------------------------********---------------------------------*/
$pathDerivateGmail = 'derivatedGmail?tasks=';
$statusPMGmail = false;
$licensedFeatures = &PMLicensedFeatures::getSingleton();
if ($licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
    G::LoadClass( "pmGoogleApi" );
    $pmGoogle = new PMGoogleApi();
    $statusPMGmail = $pmGoogle->getServiceGmailStatus();
}
/*----------------------------------********---------------------------------*/
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
/*----------------------------------********---------------------------------*/
} else if( $statusPMGmail ){
    $url = $pathDerivateGmail . $_GET['tasks'].'&arrayData='.$_GET['arrayData'];
/*----------------------------------********---------------------------------*/
} else {
    $url = 'casesListExtJs';
}
if (isset( $_GET['ux'] )) {
    echo 'if (typeof window.parent.ux_env != \'undefined\') {';
}
echo "  window.parent.location.href = '$url';";
if (isset( $_GET['ux'] )) {
    /*----------------------------------********---------------------------------*/
    if(PMLicensedFeatures::getSingleton()->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09') && $statusPMGmail){
        echo '} else { window.parent.location.href = \''.$pathDerivateGmail.$_GET['tasks'].'&arrayData='.$_GET['arrayData'].'\'; }';
    } else {
    /*----------------------------------********---------------------------------*/
        echo '} else { window.parent.location.href = \'casesListExtJs\'; }';
    /*----------------------------------********---------------------------------*/
    }
    /*----------------------------------********---------------------------------*/
}
?>
}
</script>