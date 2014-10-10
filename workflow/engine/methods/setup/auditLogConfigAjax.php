<?php
global $G_TMP_MENU;

switch ($_GET['action']) {
    case 'saveOption':
        try {
            G::LoadClass( 'serverConfiguration' );
            $oServerConf = & serverConf::getSingleton();

            /*you can use SYS_TEMP or SYS_SYS ON AUDIT_LOG_CONF to save for each workspace*/
            $oServerConf->unsetAuditLogProperty( 'AL_TYPE', SYS_SYS );
            if (isset( $_POST['acceptAL'] )) {
                $oServerConf->setAuditLogProperty( 'AL_OPTION', 1, SYS_SYS );
                $oServerConf->unsetAuditLogProperty( 'AL_NEXT_DATE', SYS_SYS );
                $response->enable = true;
                G::auditLog("EnableAuditLog");
            } else {
                $oServerConf->setAuditLogProperty( 'AL_OPTION', 0, SYS_SYS );
                $oServerConf->unsetAuditLogProperty( 'AL_NEXT_DATE', SYS_SYS );
                $oServerConf->setAuditLogProperty( 'AL_TYPE', 'endaudit', SYS_SYS );
                $response->enable = false;
                G::auditLog("DisableAuditLog");
            }
            $response->success = true;
            
        } catch (Exception $e) {
            $response->success = false;
            $response->msg = $e->getMessage();
        }
        echo G::json_encode( $response );
        break;
}

