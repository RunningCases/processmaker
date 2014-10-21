<?php
try {
    $response = new stdClass;
    if (isset( $_POST['javascriptCache'] ) || isset( $_POST['metadataCache'] ) || isset( $_POST['htmlCache'] )) {

        $msgLog = '';
        if (isset( $_POST['javascriptCache'] )) {
            G::rm_dir( PATH_C . 'ExtJs' );
            $response->javascript = true;
            $msgLog .= 'Javascript cache ';
        }

        if (isset( $_POST['metadataCache'] )) {
            G::rm_dir( PATH_C . 'xmlform' );
            $response->xmlform = true;
            $msgLog .= 'Forms Metadata cache ';
        }

        if (isset( $_POST['htmlCache'] )) {
            G::rm_dir( PATH_C . 'smarty' );
            $response->smarty = true;
            $msgLog .= 'Forms Html Templates cache ';
        }

        $response->success = true;

        G::auditLog("ClearCache", $msgLog);
    } else {
        $response->success = false;
    }
} catch (Exception $e) {
    $response->success = false;
    $response->message = $e->getMessage();
}
echo G::json_encode( $response );

