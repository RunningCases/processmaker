<?php
/**
 * authSources_Ajax.php
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.23
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

use ProcessMaker\Plugins\PluginRegistry;

try {
    global $RBAC;
    if ($RBAC->userCanAccess( 'PM_SETUP_ADVANCE' ) != 1) {
        G::SendTemporalMessage( 'ID_USER_HAVENT_RIGHTS_PAGE', 'error', 'labels' );
        G::header( 'location: ../login/login' );
        die();
    }

    switch ($_REQUEST['action']) {
        case 'searchUsers':
            $criteria = new Criteria( 'workflow' );
            $criteria->addSelectColumn( UsersPeer::USR_USERNAME );
            $criteria->add( UsersPeer::USR_STATUS, array ('CLOSED'
            ), Criteria::NOT_IN );
            $dataset = UsersPeer::DoSelectRs( $criteria );
            $dataset->setFetchmode( ResultSet::FETCHMODE_ASSOC );
            $dataset->next();
            $pmUsers = array ();
            while ($row = $dataset->getRow()) {
                $pmUsers[] = $row['USR_USERNAME'];
                $dataset->next();
            }

            $aFields = $RBAC->getAuthSource( $_POST['sUID'] );

            //$oJSON = new Services_JSON();
            $i = 0;
            $oUser = new Users();
            $aAux = $RBAC->searchUsers( $_POST['sUID'], $_POST['sKeyword'] );
            $aUsers = array ();
            // note added by gustavo cruz gustavo-at-colosa.com
            // changed the user data showed to accept FirstName and LastName variables
            $aUsers[] = array ('Checkbox' => 'char','Username' => 'char','FullName' => 'char','FirstName' => 'char','LastName' => 'char','Email' => 'char','DistinguishedName' => 'char'
            );
            foreach ($aAux as $aUser) {
                if (! in_array( $aUser['sUsername'], $pmUsers )) {
                    // add replace to change D'Souza to D*Souza by krlos
                    $sCheckbox = '<div align="center"><input type="checkbox" name="aUsers[' . $i . ']" id="aUsers[' . $i . ']" value=\'' . str_replace( "\'", "*", addslashes( Bootstrap::json_encode( $aUser ) ) ) . '\' /></div>';
                    $i ++;
                } else {
                    $sCheckbox = G::LoadTranslation( 'ID_USER_REGISTERED' ) . ':<br />(' . $aUser['sUsername'] . ')';
                }
                // note added by gustavo cruz gustavo-at-colosa.com
                // assign the user data to the DBArray variable.
                $aUsers[] = array ('Checkbox' => $sCheckbox,'Username' => $aUser['sUsername'],'FullName' => $aUser['sFullname'],'FirstName' => $aUser['sFirstname'],'LastName' => $aUser['sLastname'],'Email' => $aUser['sEmail'],'DistinguishedName' => $aUser['sDN']
                );
            }
            global $_DBArray;
            $_DBArray['users'] = $aUsers;
            $_SESSION['_DBArray'] = $_DBArray;
            $oCriteria = new Criteria( 'dbarray' );
            $oCriteria->setDBArrayTable( 'users' );
            $aData = Array ('Checkbox' => '0','FullName' => '0'
            );

            global $G_PUBLISH;
            $G_PUBLISH = new Publisher();
            if ($aFields['AUTH_SOURCE_PROVIDER'] != 'ldap') {
                $G_PUBLISH->AddContent( 'propeltable', 'pagedTableLdap', 'authSources/ldapSearchResults', $oCriteria, ' ', array ('Checkbox' => G::LoadTranslation( 'ID_MSG_CONFIRM_DELETE_CASE_SCHEDULER' )
                ) );
            } else {
                if (file_exists( PATH_XMLFORM . 'authSources/' . $aFields['AUTH_SOURCE_PROVIDER'] . 'Edit.xml' )) {
                    $G_PUBLISH->AddContent( 'propeltable', 'pagedTableLdap', 'authSources/' . $aFields['AUTH_SOURCE_PROVIDER'] . 'SearchResults', $oCriteria, ' ', array ('Checkbox' => G::LoadTranslation( 'ID_MSG_CONFIRM_DELETE_CASE_SCHEDULER' )
                    ) );
                } else {
                    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', array ('MESSAGE' => 'File: ' . $aFields['AUTH_SOURCE_PROVIDER'] . 'SearchResults.xml' . ' doesn\'t exist.'
                    ) );
                }
            }
            G::RenderPage( 'publish', 'raw' );
            break;
        case 'authSourcesList':

            global $RBAC;

            $co = new Configurations();
            $config = $co->getConfiguration('authSourcesList', 'pageSize', '', $_SESSION['USER_LOGGED']);
            $limit_size = isset($config['pageSize']) ? $config['pageSize'] : 20;

            $start = isset($_REQUEST['start']) ? $_REQUEST['start'] : 0;
            $limit = isset($_REQUEST['limit']) ? $_REQUEST['limit'] : $limit_size;
            $filter = isset($_REQUEST['textFilter']) ? $_REQUEST['textFilter'] : '';

            $criterias = $RBAC->getAuthenticationSources($start, $limit, $filter);

            $dataSourceAuthentication = AuthenticationSourcePeer::doSelectRS($criterias['COUNTER']);
            $dataSourceAuthentication->setFetchmode(ResultSet::FETCHMODE_ASSOC);
            $dataSourceAuthentication->next();
            $row = $dataSourceAuthentication->getRow();
            $total_sources = $row['CNT'];

            if (!empty($_REQUEST['orderBy']) && isset($_REQUEST['ascending']) && defined("AuthenticationSourcePeer::" . $_REQUEST['orderBy'])) {
                if ($_REQUEST['ascending'] === '1') {
                    $criterias['LIST']->addAscendingOrderByColumn(constant("AuthenticationSourcePeer::" . $_REQUEST['orderBy']));
                }
                if ($_REQUEST['ascending'] === '0') {
                    $criterias['LIST']->addDescendingOrderByColumn(constant("AuthenticationSourcePeer::" . $_REQUEST['orderBy']));
                }
            } else {
                $criterias['LIST']->addAscendingOrderByColumn(AuthenticationSourcePeer::AUTH_SOURCE_NAME);
            }
            $dataset = AuthenticationSourcePeer::doSelectRS($criterias['LIST']);
            $dataset->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            global $RBAC;
            $auth = $RBAC->getAllUsersByAuthSource();

            $sources = [];
            while ($dataset->next()) {
                $row = $dataset->getRow();
                $values = explode("_", $row["AUTH_SOURCE_PASSWORD"]);
                foreach ($values as $value) {
                    if ($value == "2NnV3ujj3w") {
                        $row["AUTH_SOURCE_PASSWORD"] = G::decrypt($values[0], $row["AUTH_SOURCE_SERVER_NAME"]);
                    }
                }
                $label = G::LoadTranslation('ID_DISABLE');
                if ($row['AUTH_SOURCE_ENABLED_TLS'] === "1") {
                    $label = G::LoadTranslation('ID_ENABLE');
                }
                $row['AUTH_SOURCE_ENABLED_TLS_LABEL'] = $label;
                //additional information
                $authSourceData = unserialize($row['AUTH_SOURCE_DATA']);
                if (is_array($authSourceData)) {
                    $row = array_merge($row, $authSourceData);
                }
                $sources[] = $row;
                $index = sizeof($sources) - 1;
                $sources[$index]['CURRENT_USERS'] = isset($auth[$sources[$index]['AUTH_SOURCE_UID']]) ? $auth[$sources[$index]['AUTH_SOURCE_UID']] : 0;
            }
            $response = [
                'sources' => $sources,
                'total_sources' => $total_sources
            ];
            echo G::json_encode($response);
            break;
        case 'canDeleteAuthSource':
            try {
                $authUID = $_POST['auth_uid'];
                global $RBAC;
                $aAuth = $RBAC->getAllUsersByAuthSource();
                $response = isset( $aAuth[$authUID] ) ? 'false' : 'true';
                echo '{success: ' . $response . '}';
            } catch (Exception $ex) {
                $token = strtotime("now");
                PMException::registerErrorLog($ex, $token);
                $varRes = '{success: false, error: ' . G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) . '}';
                G::outRes( $varRes );
            }
            break;
        case 'deleteAuthSource':
            try {
                global $RBAC;
                $RBAC->removeAuthSource( $_POST['auth_uid'] );
                echo '{success: true}';
            } catch (Exception $ex) {
                $token = strtotime("now");
                PMException::registerErrorLog($ex, $token);
                $varRes = '{success: false, error: ' . G::LoadTranslation("ID_EXCEPTION_LOG_INTERFAZ", array($token)) . '}';
                G::outRes( $varRes );
            }
            break;
        case 'authSourcesNew':
            $pluginRegistry = PluginRegistry::loadSingleton();

            $arr = Array ();
            $oDirectory = dir( PATH_RBAC . 'plugins' . PATH_SEP );

            while ($sObject = $oDirectory->read()) {
                if (($sObject != '.') && ($sObject != '..') && ($sObject != '.svn') && ($sObject != 'ldap')) {
                    if (is_file( PATH_RBAC . 'plugins' . PATH_SEP . $sObject )) {
                        $sType = trim(str_replace(array("class.", ".php"), "", $sObject));

                        // Filter Authentication Sources added by plugins, because these will be configured from another place
                        if ($sType != "ldapAdvanced" && $sType != "Gauth") {
                            $arr[] = array("sType" => $sType, "sLabel" => $sType);
                        }
                    }
                }
            }

            /*----------------------------------********---------------------------------*/
            if (PMLicensedFeatures::getSingleton()->verifyfeature("sywN09PSzh1MVdOajZBdnhMbFhCSnpNT1lLTEFwVklmOTE=")) {
                $arr[] = array("sType" => "ldapAdvanced", "sLabel" => "ldapAdvanced");
            }
            /*----------------------------------********---------------------------------*/

            echo '{sources: ' . G::json_encode( $arr ) . '}';
            break;
        case 'loadauthSourceData':
            global $RBAC;

            $fields = $RBAC->getAuthSource( $_POST['sUID'] );
            if (is_array( $fields['AUTH_SOURCE_DATA'] )) {
                foreach ($fields['AUTH_SOURCE_DATA'] as $field => $value) {
                    $fields[$field] = $value;
                }
            }
            unset( $fields['AUTH_SOURCE_DATA'] );
            $result = new stdclass();
            $result->success = true;
            $result->sources = $fields;
            print (G::json_encode( $result )) ;
            break;
    }
} catch (Exception $e) {
    $fields = array ('MESSAGE' => $e->getMessage()
    );
    global $G_PUBLISH;
    $G_PUBLISH = new Publisher();
    $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $fields );
    G::RenderPage( 'publish', 'blank' );
}

