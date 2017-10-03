<?php

$function = $_REQUEST['functionAccion'];

switch ($function) {
    case "ldapVerifyIfExistsRecordsInDb":
        $response = array();

        try {
            $authenticationSourceUid = $_POST["authenticationSourceUid"];

            $arrayAuthenticationSourceData = $RBAC->getAuthSource($authenticationSourceUid);

            $flagUser = false;
            $flagDepartment = false;
            $flagGroup = false;

            //Users
            $criteria = new Criteria("rbac");

            $criteria->addSelectColumn(RbacUsersPeer::USR_UID);
            $criteria->add(RbacUsersPeer::USR_AUTH_USER_DN, "%" . $arrayAuthenticationSourceData["AUTH_SOURCE_BASE_DN"], Criteria::LIKE);
            $criteria->setOffset(0); //Start
            $criteria->setLimit(1);  //Limit

            $rsCriteria = RbacUsersPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $flagUser = true;
            }

            //Departments
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(DepartmentPeer::DEP_UID);
            $criteria->add(DepartmentPeer::DEP_LDAP_DN, "%" . $arrayAuthenticationSourceData["AUTH_SOURCE_BASE_DN"], Criteria::LIKE);
            $criteria->setOffset(0); //Start
            $criteria->setLimit(1);  //Limit

            $rsCriteria = DepartmentPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $flagDepartment = true;
            }

            //Groups
            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(GroupwfPeer::GRP_UID);
            $criteria->add(GroupwfPeer::GRP_LDAP_DN, "%" . $arrayAuthenticationSourceData["AUTH_SOURCE_BASE_DN"], Criteria::LIKE);
            $criteria->setOffset(0); //Start
            $criteria->setLimit(1);  //Limit

            $rsCriteria = GroupwfPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            if ($rsCriteria->next()) {
                $flagGroup = true;
            }

            //Response
            $response["status"] = "OK";
            $response["existsRecords"] = ($flagUser || $flagDepartment || $flagGroup)? 1 : 0;
        } catch (Exception $e) {
            //Response
            $response["status"]  = "ERROR";
            $response["message"] = $e->getMessage();
        }

        echo G::json_encode($response);
        break;
    case 'ldapGrid':
        $data = array();
        switch ($_REQUEST['tipo']) {
            case 'crear':
                $data = array('ID' => G::generateUniqueID());
                break;
            case 'read':
                if (isset($_REQUEST['data']) && $_REQUEST['data'] != '') {
                    $dataValue = G::json_decode($_REQUEST['data']);
                    $data = array();
                    foreach ($dataValue as $value) {
                        $data[] = array(
                            'ID' => G::generateUniqueID(),
                            'ATTRIBUTE_LDAP' => $value->attributeLdap,
                            'ATTRIBUTE_USER' => $value->attributeUser
                        );
                    }
                }
                break;
            default:
                break;
        }
        echo G::json_encode(array('success'=> true, 'data' => $data, 'message'=>'Created Quote', 'total' => count($data)));
        break;
    case 'ldapSave':
        if (isset($_POST['AUTH_SOURCE_SHOWGRID-checkbox'])) {
            if ($_POST['AUTH_SOURCE_SHOWGRID-checkbox'] == 'on') {
                $_POST['AUTH_SOURCE_SHOWGRID'] = 'on';
                $attributes = G::json_decode($_POST['AUTH_SOURCE_GRID_TEXT']);
                $con = 1;
                foreach ($attributes as $value) {
                    $_POST['AUTH_SOURCE_GRID_ATTRIBUTE'][$con] = (array)$value;
                    $con++;
                }
            }
            unset($_POST['AUTH_SOURCE_SHOWGRID-checkbox']);
        }

        if ($_POST['AUTH_ANONYMOUS'] == '1') {
            $_POST['AUTH_SOURCE_SEARCH_USER'] = '';
            $_POST['AUTH_SOURCE_PASSWORD'] = '';
        }

        if (isset($_POST['AUTH_SOURCE_GRID_TEXT'])) {
            unset($_POST['AUTH_SOURCE_GRID_TEXT']);
        }
        if (isset($_POST['DELETE1'])) {
            unset($_POST['DELETE1']);
        }
        if (isset($_POST['DELETE2'])) {
            unset($_POST['DELETE2']);
        }
        if (isset($_POST['AUTH_SOURCE_ATTRIBUTE_IDS'])) {
            unset($_POST['AUTH_SOURCE_ATTRIBUTE_IDS']);
        }
        if (isset($_POST['AUTH_SOURCE_SHOWGRID_FLAG'])) {
            unset($_POST['AUTH_SOURCE_SHOWGRID_FLAG']);
        }
        if (isset($_POST['AUTH_SOURCE_GRID_TEXT'])) {
            unset($_POST['AUTH_SOURCE_GRID_TEXT']);
        }

        $aCommonFields = array ('AUTH_SOURCE_UID','AUTH_SOURCE_NAME','AUTH_SOURCE_PROVIDER','AUTH_SOURCE_SERVER_NAME','AUTH_SOURCE_PORT','AUTH_SOURCE_ENABLED_TLS','AUTH_ANONYMOUS','AUTH_SOURCE_SEARCH_USER','AUTH_SOURCE_PASSWORD','AUTH_SOURCE_VERSION','AUTH_SOURCE_BASE_DN','AUTH_SOURCE_OBJECT_CLASSES','AUTH_SOURCE_ATTRIBUTES');

        $aFields = $aData = array ();
        foreach ($_POST as $sField => $sValue) {
            if (in_array( $sField, $aCommonFields )) {
                $aFields[$sField] = $sValue;
            } else {
                $aData[$sField] = $sValue;
            }
        }

        if (!isset($aData['AUTH_SOURCE_SHOWGRID']) || $aData['AUTH_SOURCE_SHOWGRID'] == 'off') {
            unset($aData['AUTH_SOURCE_GRID_ATTRIBUTE']);
            unset($aData['AUTH_SOURCE_SHOWGRID']);
        }

        $aFields['AUTH_SOURCE_DATA'] = $aData;

        //LDAP_PAGE_SIZE_LIMIT
        $ldapAdvanced = new LdapAdvanced();

        try {
            $arrayAuthenticationSourceData = $aFields;
            $arrayAuthenticationSourceData['AUTH_SOURCE_VERSION'] = 3;

            $aFields['AUTH_SOURCE_DATA']['LDAP_PAGE_SIZE_LIMIT'] = $ldapAdvanced->getPageSizeLimit(
                $ldapAdvanced->ldapConnection($arrayAuthenticationSourceData),
                $arrayAuthenticationSourceData['AUTH_SOURCE_BASE_DN']
            );
        } catch (Exception $e) {
            $aFields['AUTH_SOURCE_DATA']['LDAP_PAGE_SIZE_LIMIT'] = $ldapAdvanced->getPageSizeLimit(false);
        }

        //Save
        if ($aFields['AUTH_SOURCE_UID'] == '') {
            $RBAC->createAuthSource( $aFields );
        } else {
            $RBAC->updateAuthSource( $aFields );
        }
        echo G::json_encode(array('success'=> true));
        break;
    case "searchUsers":
        $response = array();

        try {
            $pageSize = $_POST["pageSize"];

            $authenticationSourceUid = $_POST["sUID"];
            $keyword = $_POST["sKeyword"];
            $start = (isset($_POST["start"]))? $_POST["start"]: 0;
            $limit = (isset($_POST["limit"]))? $_POST["limit"]: $pageSize;

            //Get Users from Database
            $arrayUser = array();

            $criteria = new Criteria("workflow");

            $criteria->addSelectColumn(UsersPeer::USR_USERNAME);
            $criteria->addSelectColumn(RbacUsersPeer::UID_AUTH_SOURCE);
            $criteria->addJoin(UsersPeer::USR_UID, RbacUsersPeer::USR_UID);
            $criteria->add(UsersPeer::USR_STATUS, "CLOSED", Criteria::NOT_EQUAL);

            $rsCriteria = UsersPeer::doSelectRS($criteria);
            $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

            while ($rsCriteria->next()) {
                $row = $rsCriteria->getRow();
                $arrayUser[strtolower($row["USR_USERNAME"])] = $row['UID_AUTH_SOURCE'];
            }

            //Get data
            $arrayData = array();

            $ldapAdvanced = new LdapAdvanced();
            $ldapAdvanced->sAuthSource = $authenticationSourceUid;

            $result = $ldapAdvanced->searchUsers($keyword, $start, $limit);

            foreach ($result["data"] as $value) {
                $arrayUserData = $value;

                if (!isset($arrayUser[strtolower($arrayUserData["sUsername"])])) {
                    $arrayUserData["STATUS"] = G::LoadTranslation("ID_NOT_IMPORTED");
                    $arrayUserData["IMPORT"] = 1;
                } elseif($authenticationSourceUid === $arrayUser[strtolower($arrayUserData["sUsername"])]) {
                    $arrayUserData["STATUS"] = G::LoadTranslation("ID_IMPORTED");
                    $arrayUserData["IMPORT"] = 0;
                } else {
                    $arrayUserData["STATUS"] = G::LoadTranslation("ID_CANNOT_IMPORT");
                    $arrayUserData["IMPORT"] = 0;
                }

                $arrayData[] = $arrayUserData;
            }

            //Response
            $response["status"]  = "OK";
            $response["success"] = true;
            $response["resultTotal"] = $result["numRecTotal"];
            $response["resultRoot"]  = $arrayData;
        } catch (Exception $e) {
            //Response
            $response["status"]  = "ERROR";
            $response["message"] = $e->getMessage();
        }

        echo G::json_encode($response);
        break;
    case 'importUsers':
        $usersImport   = $_REQUEST['UsersImport'];
        $authSourceUid = $_REQUEST['AUTH_SOURCE_UID'];

        $aUsers = G::json_decode($usersImport);
        global $RBAC;
        $aFields = $RBAC->getAuthSource( $authSourceUid );
        $aAttributes = array();

        if (isset($aFields['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'])) {
            $aAttributes = $aFields['AUTH_SOURCE_DATA']['AUTH_SOURCE_GRID_ATTRIBUTE'];
        }

        $usersCreated = '';
        $countUsers = 0;
        //$usersImport
        foreach ($aUsers as $sUser) {
            $aUser   = (array)$sUser;
            $matches = array();
            $aData   = array();
            $aData['USR_USERNAME'] = str_replace( "*", "'", $aUser['sUsername'] );
            $aData["USR_PASSWORD"] = "00000000000000000000000000000000";
            // note added by gustavo gustavo-at-colosa.com
            // asign the FirstName and LastName variables
            // add replace to change D*Souza to D'Souza by krlos
            $aData['USR_FIRSTNAME'] = str_replace( "*", "'", $aUser['sFirstname'] );
            $aData['USR_LASTNAME'] = str_replace( "*", "'", $aUser['sLastname'] );
            $aData['USR_EMAIL'] = $aUser['sEmail'];
            $aData['USR_DUE_DATE'] = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm' ), date( 'd' ), date( 'Y' ) + 2 ) );
            $aData['USR_CREATE_DATE'] = date( 'Y-m-d H:i:s' );
            $aData['USR_UPDATE_DATE'] = date( 'Y-m-d H:i:s' );
            $aData['USR_BIRTHDAY'] = date( 'Y-m-d' );
            $aData['USR_STATUS'] = (isset($aUser['USR_STATUS'])) ? (($aUser['USR_STATUS'] == 'ACTIVE') ? 1 : 0) : 1;
            $aData['USR_AUTH_TYPE'] = strtolower( $aFields['AUTH_SOURCE_PROVIDER'] );
            $aData['UID_AUTH_SOURCE'] = $aFields['AUTH_SOURCE_UID'];
            // validating with regexp if there are some missing * inside the DN string
            // if it's so the is changed to the ' character
            preg_match( '/[a-zA-Z]\*[a-zA-Z]/', $aUser['sDN'], $matches );

            foreach ($matches as $key => $match) {
                $newMatch = str_replace( '*', '\'', $match );
                $aUser['sDN'] = str_replace( $match, $newMatch, $aUser['sDN'] );
            }
            $aData['USR_AUTH_USER_DN'] = $aUser['sDN'];

            try {
                $sUserUID = $RBAC->createUser( $aData, 'PROCESSMAKER_OPERATOR', $aFields['AUTH_SOURCE_NAME']);
                $usersCreated .= $aData['USR_USERNAME'].' ';
                $countUsers ++;
            } catch (Exception $oError) {
                $G_PUBLISH = new Publisher();
                $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', array ('MESSAGE' => $oError->getMessage()) );
                G::RenderPage("publish", "blank");
                die();
            }

            $aData['USR_STATUS'] = (isset($aUser['USR_STATUS'])) ? $aUser['USR_STATUS'] :'ACTIVE';
            $aData['USR_UID'] = $sUserUID;
            $aData['USR_ROLE'] = 'PROCESSMAKER_OPERATOR';

            $calendarObj = new Calendar();
            $calendarObj->assignCalendarTo($sUserUID, '00000000000000000000000000000001', 'USER');

            if (count($aAttributes)) {
                foreach ($aAttributes as $value) {
                    if (isset($aUser[$value['attributeUser']])) {
                        $aData[$value['attributeUser']] = str_replace( "*", "'", $aUser[$value['attributeUser']] );
                        if ($value['attributeUser'] == 'USR_STATUS') {
                            $evalValue = $aData[$value['attributeUser']];
                            $statusValue = $aData['USR_STATUS'];
                            $aData[$value['attributeUser']] = $statusValue;
                        }
                    }
                }
            }
            $oUser = new Users();
            $oUser->create( $aData );
        }

        $sClassName = strtolower($aFields['AUTH_SOURCE_PROVIDER']);

        $plugin = G::factory($sClassName);

        $aAuthSource = $RBAC->authSourcesObj->load($authSourceUid);

        if (is_null($plugin->ldapcnn)) {
            $plugin->ldapcnn = $plugin->ldapConnection($aAuthSource);
        }

        $ldapcnn = $plugin->ldapcnn;

        $plugin->log($ldapcnn, "Users imported $countUsers: " . $usersCreated);

        echo G::json_encode(array('success'=> true));
        break;
    case "ldapTestConnection":
        $response = array();

        try {
            if ($_POST["AUTH_ANONYMOUS"] == "1") {
                $_POST["AUTH_SOURCE_SEARCH_USER"] = "";
                $_POST["AUTH_SOURCE_PASSWORD"] = "";
            }

            $arrayAuthenticationSourceData = $_POST;
            $arrayAuthenticationSourceData['AUTH_SOURCE_VERSION'] = 3;

            //Test connection
            $ldapAdvanced = new LdapAdvanced();

            $ldapcnn = $ldapAdvanced->ldapConnection($arrayAuthenticationSourceData);

            //Response
            $response["status"] = "OK";
        } catch (Exception $e) {
            //Response
            $response["status"]  = "ERROR";
            $response["message"] = $e->getMessage();
        }

        echo G::json_encode($response);
        break;
    default:
        break;
}

