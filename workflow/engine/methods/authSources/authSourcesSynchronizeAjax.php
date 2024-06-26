<?php

class treeNode extends stdclass
{
    public $text = "";
    public $cls = "";
    public $leaf = false;
    public $checked = false;
    public $children = array();
    public $id = "";
}

try {
    header("Content-Type: application/json;");

    switch ($_REQUEST["m"]) {
        case "loadDepartments":
            global $ldapAdvanced;
            global $departments;
            global $terminatedOu;
            global $baseDN;

            $ldapAdvanced = getLDAPAdvanceInstance($_REQUEST["authUid"]);
            $RBAC = RBAC::getSingleton();
            $authenticationSource = $RBAC->authSourcesObj->load($_REQUEST["authUid"]);
            $baseDN = $authenticationSource["AUTH_SOURCE_BASE_DN"];
            $departments = $ldapAdvanced->searchDepartments();
            $terminatedOu = $ldapAdvanced->getTerminatedOu();
            $nodes = lookForChildrenDeps("");
            die(json_encode($nodes));
            break;
        case "saveDepartments":
            $depsToCheck = ($_REQUEST['departmentsDN'] != '') ? explode('|', $_REQUEST['departmentsDN']) : [];
            $depsToCheck = array_map("urldecode", $depsToCheck);
            $depsToUncheck = getDepartmentsToUncheck($depsToCheck);
            $RBAC = RBAC::getSingleton();
            $authenticationSource = $RBAC->authSourcesObj->load($_REQUEST["authUid"]);
            $ldapAdvanced = getLDAPAdvanceInstance($_REQUEST["authUid"]);

            foreach ($depsToCheck as $departmentDn) {
                $departmentUid = $ldapAdvanced->getDepUidIfExistsDN($departmentDn);
                if ($departmentUid == '') {
                    if (strcasecmp($departmentDn, $authenticationSource['AUTH_SOURCE_BASE_DN']) == 0) {
                        $departmentTitle = 'ROOT (' . $authenticationSource['AUTH_SOURCE_BASE_DN'] . ')';
                        $parentUid = '';
                    } else {
                        $ous = custom_ldap_explode_dn($departmentDn);
                        $departmentCurrent = array_shift($ous);
                        $parentDn = implode(',', $ous);
                        $ous = explode('=', $departmentCurrent);
                        $departmentTitle = trim($ous[1]);
                        $parentUid = $ldapAdvanced->getDepUidIfExistsDN($parentDn);
                        if (str_ireplace($authenticationSource['AUTH_SOURCE_BASE_DN'], '', $parentDn) != '' && $parentUid == '') {
                            $response = new stdClass();
                            $response->status = 'ERROR';
                            $response->message = G::LoadTranslation(
                                'ID_DEPARTMENT_CHECK_PARENT_DEPARTMENT',
                                [$parentDn, $departmentTitle]
                            );
                            echo json_encode($response);
                            exit(0);
                        }
                    }

                    $departmentUid = $ldapAdvanced->getDepartmentUidByTitle($departmentTitle);
                    $department = new Department();
                    if ($departmentUid === '') {
                        $data = [
                            'DEP_TITLE' => stripslashes($departmentTitle),
                            'DEP_PARENT' => $parentUid,
                            'DEP_LDAP_DN' => $departmentDn,
                            'DEP_REF_CODE' => ''
                        ];
                        $departmentUid = $department->create($data);
                        if ($departmentUid === false) {
                            $response = new stdClass();
                            $response->status = 'ERROR';
                            $response->message = G::LoadTranslation('ID_DEPARTMENT_ERROR_CREATE');
                            echo json_encode($response);
                            exit(0);
                        }
                    } else {
                        $data = $department->Load($departmentUid);
                        $data['DEP_LDAP_DN'] = $departmentDn;
                        $department->update($data);
                    }
                }
            }

            if (count($depsToUncheck) > 0) {
                $baseDnLength = strlen($authenticationSource['AUTH_SOURCE_BASE_DN']);
                foreach ($depsToUncheck as $departmentDn) {
                    $departmentUid = $ldapAdvanced->getDepUidIfExistsDN($departmentDn);
                    if ($departmentUid != '' && 
                        strcasecmp(
                            substr($departmentDn, strlen($departmentDn) - $baseDnLength), 
                            $authenticationSource['AUTH_SOURCE_BASE_DN']
                        ) == 0
                    ) {
                        $department = new Department();
                        $data = $department->Load($departmentUid);
                        $data['DEP_LDAP_DN'] = '';
                        $department->update($data);
                        if (!isset($authenticationSource['AUTH_SOURCE_DATA']['DEPARTMENTS_TO_UNASSIGN'])) {
                            $authenticationSource['AUTH_SOURCE_DATA']['DEPARTMENTS_TO_UNASSIGN'] = [];
                        }
                        $authenticationSource['AUTH_SOURCE_DATA']['DEPARTMENTS_TO_UNASSIGN'][] = $departmentUid;
                    }
                }
                $RBAC->authSourcesObj->update($authenticationSource);
            }

            $response = new stdclass();
            $response->status = "OK";
            if ($ldapAdvanced->checkDuplicateDepartmentTitles()) {
                $response->warning = G::LoadTranslation("ID_IT_WAS_IDENTIFIED_DUPLICATED_DEPARTMENTS_PLEASE_REMOVE_THESE_DEPARTMENTS");
            }
            die(json_encode($response));
            break;
        case "loadGroups":
            global $ldapAdvanced;
            global $groups;

            $ldapAdvanced = getLDAPAdvanceInstance($_REQUEST["authUid"]);
            $groups = $ldapAdvanced->searchGroups();
            $nodes = lookForChildrenGroups();
            die(json_encode($nodes));
            break;
        case "saveGroups":
            $groupsToCheck = explode("|", $_REQUEST["groupsDN"]);
            $groupsToCheck = array_map("urldecode", $groupsToCheck);
            $groupsToUncheck = getGroupsToUncheck($groupsToCheck);
            $RBAC = RBAC::getSingleton();
            $authenticationSource = $RBAC->authSourcesObj->load($_REQUEST["authUid"]);
            $ldapAdvanced = getLDAPAdvanceInstance($_REQUEST["authUid"]);

            foreach ($groupsToCheck as $groupDN) {
                $ous = custom_ldap_explode_dn($groupDN);
                $currentGroup = array_shift($ous);
                $groupAux = explode("=", $currentGroup);
                $groupTitle = isset($groupAux[1]) ? trim($groupAux[1]) : "";
                $groupTitle = stripslashes($groupTitle);
                if (empty($groupTitle)) {
                    continue;
                }
                $groupUid = $ldapAdvanced->getGroupUidByTitle($groupTitle);
                $groupwf = new Groupwf();
                if ($groupUid === "") {
                    $group = [
                        "GRP_TITLE" => $groupTitle,
                        "GRP_LDAP_DN" => $groupDN
                    ];
                    $groupwf->create($group);
                } else {
                    $group = $groupwf->Load($groupUid);
                    $group["GRP_LDAP_DN"] = $groupDN;
                    $groupwf->update($group);
                }
            }

            if (count($groupsToUncheck) > 0) {
                foreach ($groupsToUncheck as $groupDN) {
                    $ous = custom_ldap_explode_dn($groupDN);
                    $currentGroup = array_shift($ous);
                    $groupAux = explode("=", $currentGroup);
                    $groupTitle = isset($groupAux[1]) ? trim($groupAux[1]) : "";
                    $groupTitle = stripslashes($groupTitle);
                    if (empty($groupTitle)) {
                        continue;
                    }
                    $groupUid = $ldapAdvanced->getGroupUidByTitle($groupTitle);
                    if ($groupUid != "") {
                        $groupwf = new Groupwf();
                        $group = $groupwf->Load($groupUid);
                        $group["GRP_LDAP_DN"] = "";
                        $groupwf->update($group);
                        if (!isset($authenticationSource["AUTH_SOURCE_DATA"]["GROUPS_TO_UNASSIGN"])) {
                            $authenticationSource["AUTH_SOURCE_DATA"]["GROUPS_TO_UNASSIGN"] = [];
                        }
                        $authenticationSource["AUTH_SOURCE_DATA"]["GROUPS_TO_UNASSIGN"][] = $groupUid;
                    }
                }
                $RBAC->authSourcesObj->update($authenticationSource);
            }
            $response = new stdclass();
            $response->status = "OK";
            if ($ldapAdvanced->checkDuplicateTitles()) {
                $response->warning = G::LoadTranslation("ID_IT_WAS_IDENTIFIED_DUPLICATED_GROUPS_PLEASE_REMOVE_THESE_GROUPS");
            }
            die(json_encode($response));
            break;
    }
} catch (Exception $error) {
    $response = new stdclass();
    $response->status = "ERROR";
    $response->message = $error->getMessage();

    die(json_encode($response));
}

function getLDAPAdvanceInstance($authUid)
{
    $RBAC = RBAC::getSingleton();
    $ldapAdvanced = new LdapAdvanced();
    $ldapAdvanced->sAuthSource = $authUid;
    $ldapAdvanced->sSystem = $RBAC->sSystem;

    return $ldapAdvanced;
}

function getDepartments($parent)
{
    global $departments;
    global $terminatedOu;
    global $baseDN;

    $parentDepartments = $departments;
    $childDepartments = $departments;
    $currentDepartments = array();

    foreach ($parentDepartments as $key => $val) {
        if (strtolower($val["dn"]) != strtolower($parent)) {
            if ((strtolower($val["parent"]) == strtolower($parent)) && (strtolower($val["ou"]) != strtolower($terminatedOu))) {
                $node = array();
                $node["DEP_UID"] = $val["ou"];
                $node["DEP_TITLE"] = $val["ou"];
                $node["DEP_USERS"] = $val["users"];
                $node["DEP_DN"] = $val["dn"];
                $node["HAS_CHILDREN"] = false;
                $departments[$key]["hasChildren"] = false;

                foreach ($childDepartments as $key2 => $val2) {
                    if (strtolower($val2["parent"]) == strtolower($val["dn"])) {
                        $node["HAS_CHILDREN"] = true;
                        $departments[$key]["hasChildren"] = true;
                        break;
                    }
                }

                $node["DEP_LAST"] = false;
                $currentDepartments[] = $node;
            }
        }
    }

    if (isset($currentDepartments[count($currentDepartments) - 1])) {
        $currentDepartments[count($currentDepartments) - 1]["DEP_LAST"] = true;
    }

    return $currentDepartments;
}

function lookForChildrenDeps($parent)
{
    global $ldapAdvanced;
    global $departments;

    $allDepartments = getDepartments($parent);
    $departmentsObjects = array();

    $arrayDepartmentNumberOfUsersFromDb = $ldapAdvanced->departmentsGetNumberOfUsersFromDb();

    foreach ($allDepartments as $department) {
        $departmentObject = new treeNode();
        $departmentObject->text = htmlentities($department["DEP_TITLE"], ENT_QUOTES, "UTF-8");
        $departmentUid = $ldapAdvanced->getDepUidIfExistsDN($department["DEP_DN"]);

        if ($departmentUid != "") {
            $departmentObject->text .= " (" . ((isset($arrayDepartmentNumberOfUsersFromDb[$departmentUid])) ? $arrayDepartmentNumberOfUsersFromDb[$departmentUid] : 0) . ")";
            $departmentObject->checked = true;
        } else {
            $departmentObject->checked = false;
        }

        if ($department["HAS_CHILDREN"] == 1) {
            $departmentObject->children = lookForChildrenDeps($department["DEP_DN"]);
        }

        $departmentObject->id = urlencode($department["DEP_DN"]);
        $departmentsObjects[] = $departmentObject;
    }
    return $departmentsObjects;
}

function getDepartmentsWithDN()
{
    $arrayDepartmentLdapDn = array();

    $criteria = new Criteria("workflow");

    $criteria->addSelectColumn(DepartmentPeer::DEP_LDAP_DN);
    $criteria->add(DepartmentPeer::DEP_LDAP_DN, "", Criteria::NOT_EQUAL);
    $criteria->add(DepartmentPeer::DEP_LDAP_DN, null, Criteria::ISNOTNULL);

    $rsCriteria = DepartmentPeer::doSelectRS($criteria);
    $rsCriteria->setFetchmode(ResultSet::FETCHMODE_ASSOC);

    while ($rsCriteria->next()) {
        $row = $rsCriteria->getRow();

        $arrayDepartmentLdapDn[] = $row;
    }

    return $arrayDepartmentLdapDn;
}

function getDepartmentsToUncheck($depsToCheck)
{
    $departmentsWithDN = getDepartmentsWithDN();
    $depsToUncheck = array();

    foreach ($departmentsWithDN as $departmentWithDN) {
        $found = false;

        foreach ($depsToCheck as $depToCheck) {
            if ($departmentWithDN["DEP_LDAP_DN"] == $depToCheck) {
                $found = true;
            }
        }

        if (!$found) {
            $depsToUncheck[] = $departmentWithDN["DEP_LDAP_DN"];
        }
    }

    return $depsToUncheck;
}

function getGroups()
{
    global $groups;

    $currentGroups = array();

    foreach ($groups as $key => $val) {
        $node = array();
        $node["GRP_UID"] = $val["cn"];
        $node["GRP_TITLE"] = $val["cn"];
        $node["GRP_USERS"] = $val["users"];
        $node["GRP_DN"] = $val["dn"];
        $currentGroups[] = $node;
    }

    return $currentGroups;
}

function lookForChildrenGroups()
{
    global $ldapAdvanced;
    global $groups;

    $allGroups = getGroups();
    $groupsObjects = array();

    $arrayGroupNumberOfUsersFromDb = $ldapAdvanced->groupsGetNumberOfUsersFromDb();

    foreach ($allGroups as $group) {
        $groupObject = new treeNode();
        $groupObject->text = htmlentities($group["GRP_TITLE"], ENT_QUOTES, "UTF-8");
        $groupUid = $ldapAdvanced->getGrpUidIfExistsDN($group["GRP_DN"]);

        if ($groupUid != "") {
            $groupObject->text .= " (" . ((isset($arrayGroupNumberOfUsersFromDb[$groupUid])) ? $arrayGroupNumberOfUsersFromDb[$groupUid] : 0) . ")";
            $groupObject->checked = true;
        } else {
            $groupObject->checked = false;
        }

        $groupObject->id = urlencode($group["GRP_DN"]);
        $groupsObjects[] = $groupObject;
    }

    return $groupsObjects;
}

function getGroupsWithDN()
{
    $groupInstance = new Groupwf();
    $allGroups = $groupInstance->getAll()->data;
    $groupsWithDN = array();

    foreach ($allGroups as $group) {
        if ($group["GRP_LDAP_DN"] != "") {
            $groupsWithDN[] = $group;
        }
    }

    return $groupsWithDN;
}

function getGroupsToUncheck($groupsToCheck)
{
    $groupsWithDN = getGroupsWithDN();
    $groupsToUncheck = array();

    foreach ($groupsWithDN as $groupWithDN) {
        $found = false;

        foreach ($groupsToCheck as $groupToCheck) {
            if ($groupWithDN["GRP_LDAP_DN"] == $groupToCheck) {
                $found = true;
            }
        }

        if (!$found) {
            $groupsToUncheck[] = $groupWithDN["GRP_LDAP_DN"];
        }
    }

    return $groupsToUncheck;
}

function custom_ldap_explode_dn($dn)
{
    $result = ldap_explode_dn($dn, 0);
    unset($result["count"]);

    foreach ($result as $key => $value) {
        $result[$key] = addcslashes(preg_replace_callback("/\\\([0-9A-Fa-f]{2})/", function ($m) {
            return chr(hexdec($m[1]));
        }, $value), '<>,"');
    }

    return $result;
}
