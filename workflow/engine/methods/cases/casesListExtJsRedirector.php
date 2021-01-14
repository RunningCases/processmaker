<script>
if (typeof window.parent != 'undefined') {
<?php
/*----------------------------------********---------------------------------*/
$pathDerivateGmail = 'derivatedGmail';
$statusPMGmail = false;
$licensedFeatures = PMLicensedFeatures::getSingleton();
if ($licensedFeatures->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')) {
    $pmGoogle = new PmGoogleApi();
    $statusPMGmail = $pmGoogle->getServiceGmailStatus();
}
/*----------------------------------********---------------------------------*/
if (isset($_GET['ux'])) {
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
} elseif ($statusPMGmail) {
    $url = $pathDerivateGmail;
    /*----------------------------------********---------------------------------*/
} else {
    $url = 'casesListExtJs';
}
if (isset($_GET['ux'])) {
    echo 'if (typeof window.parent.ux_env != \'undefined\') {';
}
echo '  parent.parent.postMessage("redirect=todo","*");';
if (isset($_GET['ux'])) {
    /*----------------------------------********---------------------------------*/
    if (PMLicensedFeatures::getSingleton()->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09') && $statusPMGmail) {
        echo '} else { window.parent.location.href = \''.$pathDerivateGmail.'\'; }';
    } else {
        /*----------------------------------********---------------------------------*/
        echo '} else { 
                if (parent.parent.postMessage) {
                    parent.parent.postMessage("redirect=todo","*");
                } else {
                    window.parent.location.href = \'casesListExtJs\';
                }
            }';
        /*----------------------------------********---------------------------------*/
    }
    /*----------------------------------********---------------------------------*/
}
echo "try {parent.parent.updateCasesTree();parent.parent.highlightCasesTree();} catch(e) {}";
?>
}
</script>