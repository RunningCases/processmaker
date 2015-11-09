<script>
if (typeof window.parent != 'undefined') {
<?php
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
} else if( isset( $_GET['gmail'] ) ){
    $url = 'derivatedGmail';
} else {
    $url = 'casesListExtJs';
}
if (isset( $_GET['ux'] )) {
    echo 'if (typeof window.parent.ux_env != \'undefined\') {';
}
echo "  window.parent.location.href = '$url';";
if (isset( $_GET['ux'] )) {
    if(PMLicensedFeatures::getSingleton()->verifyfeature('7qhYmF1eDJWcEdwcUZpT0k4S0xTRStvdz09')){
	echo '} else { window.parent.location.href = \'derivatedGmail\'; }';
    } else {
        echo '} else { window.parent.location.href = \'casesListExtJs\'; }';
    }   
}
?>
}
</script>