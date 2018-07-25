<?php
global $G_TMP_MENU;

switch ($_GET['action']) {
    case 'saveOption':
        try {
            $oServerConf = ServerConf::getSingleton();
            $response = new $oServerConf;
            $conf = new Configurations();
            /*you can use SYS_TEMP or SYS_SYS ON AUDIT_LOG_CONF to save for each workspace*/
            if (isset($_POST['acceptAL'])) {
                $conf->aConfig = 'true';
                $conf->saveConfig('AUDIT_LOG', 'log');
                $response->enable = true;
                G::auditLog("EnableAuditLog");
            } else {
                G::auditLog("DisableAuditLog");
                $conf->aConfig = 'false';
                $conf->saveConfig('AUDIT_LOG', 'log');
                $response->enable = false;
            }
            $response->success = true;
        } catch (Exception $e) {
            $response->success = false;
            $response->msg = $e->getMessage();
        }
        echo G::json_encode($response);
        break;
}
