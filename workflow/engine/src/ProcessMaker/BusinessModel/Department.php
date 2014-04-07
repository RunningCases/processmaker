<?php
namespace ProcessMaker\BusinessModel;

use \G;
use \UsersPeer;
use \DepartmentPeer;

/**
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 */
class Department
{
    /**
     * Get list for Departments
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getDepartments()
    {
        $oDepartment = new \Department();
        $aDepts = $oDepartment->getDepartments('');
        foreach ($aDepts as &$depData) {
            $depData['DEP_CHILDREN'] = $this->getChildren($depData);
            $depData = array_change_key_case($depData, CASE_LOWER);
        }
        return $aDepts;
    }

    /**
     * Get list for Departments
     * @var string $dep_uid. Uid for Department
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function getDepartment($dep_uid)
    {
        $dep_uid = Validator::depUid($dep_uid);
        $criteria = new \Criteria( 'workflow' );
        $criteria->add( DepartmentPeer::DEP_UID, $dep_uid, \Criteria::EQUAL );
        $con = \Propel::getConnection( DepartmentPeer::DATABASE_NAME );
        $objects = DepartmentPeer::doSelect( $criteria, $con );
        $oUsers = new \Users();

        $node = array ();
        foreach ($objects as $oDepartment) {
            $node['DEP_UID'] = $oDepartment->getDepUid();
            $node['DEP_PARENT'] = $oDepartment->getDepParent();
            $node['DEP_TITLE'] = $oDepartment->getDepTitle();
            $node['DEP_STATUS'] = $oDepartment->getDepStatus();
            $node['DEP_MANAGER'] = $oDepartment->getDepManager();
            $node['DEP_LDAP_DN'] = $oDepartment->getDepLdapDn();
            $node['DEP_LAST'] = 0;

            $manager = $oDepartment->getDepManager();
            if ($manager != '') {
                $UserUID = $oUsers->load($manager);
                $node['DEP_MANAGER_USERNAME'] = isset( $UserUID['USR_USERNAME'] ) ? $UserUID['USR_USERNAME'] : '';
                $node['DEP_MANAGER_FIRSTNAME'] = isset( $UserUID['USR_FIRSTNAME'] ) ? $UserUID['USR_FIRSTNAME'] : '';
                $node['DEP_MANAGER_LASTNAME'] = isset( $UserUID['USR_LASTNAME'] ) ? $UserUID['USR_LASTNAME'] : '';
            } else {
                $node['DEP_MANAGER_USERNAME'] = '';
                $node['DEP_MANAGER_FIRSTNAME'] = '';
                $node['DEP_MANAGER_LASTNAME'] = '';
            }

            $criteriaCount = new \Criteria( 'workflow' );
            $criteriaCount->clearSelectColumns();
            $criteriaCount->addSelectColumn( 'COUNT(*)' );
            $criteriaCount->add( DepartmentPeer::DEP_PARENT, $oDepartment->getDepUid(), \Criteria::EQUAL );
            $rs = DepartmentPeer::doSelectRS( $criteriaCount );
            $rs->next();
            $row = $rs->getRow();
            $node['HAS_CHILDREN'] = $row[0];
        }
        $node = array_change_key_case($node, CASE_LOWER);
        return $node;
    }

    /**
     * Save Department
     * @var string $dep_data. Data for Process
     * @var string $create. Flag for create or update
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function saveDepartment($dep_data, $create = true)
    {
        Validator::isArray($dep_data, '$dep_data');
        Validator::isNotEmpty($dep_data, '$dep_data');
        Validator::isBoolean($create, '$create');

        $dep_data = array_change_key_case($dep_data, CASE_UPPER);
        $oDepartment = new \Department();
        if (isset($dep_data['DEP_UID']) && $dep_data['DEP_UID'] != '') {
            Validator::depUid($dep_data['DEP_UID']);
        }
        if (isset($dep_data['DEP_PARENT']) && $dep_data['DEP_PARENT'] != '') {
            Validator::depUid($dep_data['DEP_PARENT'], 'dep_parent');
        }
        if (isset($dep_data['DEP_MANAGER']) && $dep_data['DEP_MANAGER'] != '') {
            Validator::usrUid($dep_data['DEP_MANAGER'], 'dep_manager');
        }
        if (isset($dep_data['DEP_STATUS'])) {
            Validator::depStatus($dep_data['DEP_STATUS']);
        }

        if (!$create) {
            $dep_data['DEPO_TITLE'] = $dep_data['DEP_TITLE'];
            if (isset($dep_data['DEP_TITLE'])) {
                Validator::depTitle($dep_data['DEP_TITLE'], $dep_data['DEP_UID']);
            }
            $oDepartment->update($dep_data);
            $oDepartment->updateDepartmentManager($dep_data['DEP_UID']);
        } else {
            if (isset($dep_data['DEP_TITLE'])) {
                Validator::depTitle($dep_data['DEP_TITLE']);
            } else {
                throw (new \Exception("The field dep_title is required."));
            }
            $dep_uid = $oDepartment->create($dep_data);
            $response = $this->getDepartment($dep_uid);
            return $response;
        }
    }

    /**
     * Delete department
     * @var string $dep_uid. Uid for department
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    public function deleteDepartment($dep_uid)
    {
        $dep_uid = Validator::depUid($dep_uid);
        $dep_data = $this->getDepartment($dep_uid);
        if ($dep_data['has_children'] != 0) {
            throw (new \Exception("Can not delete the department, it has a children department."));
        }
        $oDepartment = new \Department();
        $oDepartment->remove($dep_uid);
    }

    /**
     * Look for Children for department
     * @var array $dataDep. Data for child department
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     */
    protected function getChildren ($dataDep)
    {
        $children = array();
        if ((int)$dataDep['HAS_CHILDREN'] > 0) {
            $oDepartment = new \Department();
            $aDepts = $oDepartment->getDepartments($dataDep['DEP_UID']);
            foreach ($aDepts as &$depData) {
                $depData['DEP_CHILDREN'] = $this->getChildren($depData);
                $depData = array_change_key_case($depData, CASE_LOWER);
                $children[] = $depData;
            }
        }
        return $children;
    }
}

