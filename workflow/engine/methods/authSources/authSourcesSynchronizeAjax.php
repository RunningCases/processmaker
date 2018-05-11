<?php
/**
 * authSourcesSynchronizeAjax.php
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 *
 **/

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
    $json = new Services_JSON();
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
            die($json->encode($nodes));
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
                        $arrayAux = custom_ldap_explode_dn($departmentDn);
                        $departmentCurrent = array_shift($arrayAux);
                        $parentDn = implode(',', $arrayAux);

                        $arrayAux = explode('=', $departmentCurrent);
                        $departmentTitle = trim($arrayAux[1]);
                        $parentUid = $ldapAdvanced->getDepUidIfExistsDN($parentDn);

                        if (str_ireplace($authenticationSource['AUTH_SOURCE_BASE_DN'], '', $parentDn) != '' &&
                            $parentUid == ''
                        ) {
                            $response = new stdClass();
                            $response->status = 'ERROR';
                            $response->message = G::LoadTranslation(
                                'ID_DEPARTMENT_CHECK_PARENT_DEPARTMENT',
                                [$parentDn, $departmentTitle]
                            );

                            echo $json->encode($response);
                            exit(0);
                        }
                    }

                    $department = new Department();

                    $departmentUid = $department->create([
                        'DEP_TITLE' => stripslashes($departmentTitle),
                        'DEP_PARENT' => $parentUid,
                        'DEP_LDAP_DN' => $departmentDn,
                        'DEP_REF_CODE' => ''
                    ]);

                    if ($departmentUid === false) {
                        $response = new stdClass();
                        $response->status = 'ERROR';
                        $response->message = G::LoadTranslation('ID_DEPARTMENT_ERROR_CREATE');

                        echo $json->encode($response);
                        exit(0);
                    }
                }
            }

            if (!empty($depsToUncheck)) {
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

                        $arrayDepartmentData = $department->Load($departmentUid);
                        $arrayDepartmentData['DEP_LDAP_DN'] = '';

                        $result = $department->update($arrayDepartmentData);

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
            die($json->encode($response));
            break;
        case "loadGroups":
            global $ldapAdvanced;
            global $groups;

            $ldapAdvanced = getLDAPAdvanceInstance($_REQUEST["authUid"]);
            $groups = $ldapAdvanced->searchGroups();
            $nodes = lookForChildrenGroups();
            die($json->encode($nodes));
            break;
        case "saveGroups":
            $groupsToCheck = explode("|", $_REQUEST["groupsDN"]);
            $groupsToCheck = array_map("urldecode", $groupsToCheck);
            $groupsToUncheck = getGroupsToUncheck($groupsToCheck);
            $RBAC = RBAC::getSingleton();
            $authenticationSource = $RBAC->authSourcesObj->load($_REQUEST["authUid"]);
            $ldapAdvanced = getLDAPAdvanceInstance($_REQUEST["authUid"]);

            foreach ($groupsToCheck as $groupDN) {
                //$baseDN = str_replace($authenticationSource["AUTH_SOURCE_BASE_DN"], "", $groupDN);
                $ous = custom_ldap_explode_dn($groupDN);
                $currentGroup = array_shift($ous);
                //$parentDN = implode(",", $ous);
                //$ous = custom_ldap_explode_dn($baseDN);
                //$currentGroup = array_shift($ous);

                foreach ($ous as $key => $val) {
                    $aux = explode("=", $val);

                    if (isset($aux[0]) && strtolower(trim($aux[0]) != "ou")) {
                        unset($ous[$key]);
                    }
                }

                $groupAux = explode("=", $currentGroup);
                $groupTitle = isset($groupAux[1]) ? trim($groupAux[1]) : "";
                $groupUID = $ldapAdvanced->getGrpUidIfExistsDN($groupDN);

                if ($groupUID == "") {
                    $group = new Groupwf();
                    $row["GRP_TITLE"] = stripslashes($groupTitle);
                    $row["GRP_LDAP_DN"] = $groupDN;
                    $groupUID = $group->create($row);

                    if ($groupUID == false) {
                        $response = new stdclass();
                        $response->status = "ERROR";
                        $response->message = "Error creating group";
                        die($json->encode($response));
                    }
                }
            }

            if (count($groupsToUncheck) > 0) {
                foreach ($groupsToUncheck as $groupDN) {
                    $groupUID = $ldapAdvanced->getGrpUidIfExistsDN($groupDN);

                    if ($groupUID != "") {
                        $group = new Groupwf();
                        $groupInfo = $group->Load($groupUID);
                        $groupInfo["GRP_LDAP_DN"] = "";
                        $group->update($groupInfo);

                        if (!isset($authenticationSource["AUTH_SOURCE_DATA"]["GROUPS_TO_UNASSIGN"])) {
                            $authenticationSource["AUTH_SOURCE_DATA"]["GROUPS_TO_UNASSIGN"] = array();
                        }

                        $authenticationSource["AUTH_SOURCE_DATA"]["GROUPS_TO_UNASSIGN"][] = $groupUID;
                    }
                }

                $RBAC->authSourcesObj->update($authenticationSource);
            }

            $response = new stdclass();
            $response->status = "OK";
            die($json->encode($response));
            break;
    }
} catch (Exception $error) {
    $response = new stdclass();
    $response->status = "ERROR";
    $response->message = $error->getMessage();

    die($json->encode($response));
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
