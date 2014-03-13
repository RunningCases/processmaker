<?php
namespace BusinessModel;

/**
 * Validator fields
 *
 * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
 * @copyright Colosa - Bolivia
 *
 * @protected
 */
class Validator{
    /**
     * Validate dep_uid
     * @var string $dep_uid. Uid for Departament
     * @var string $nameField. Name of field for message
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    static public function depUid($dep_uid, $nameField = 'dep_uid')
    {
        $dep_uid = trim($dep_uid);
        if ($dep_uid == '') {
            throw (new \Exception("The departament with $nameField: '' does not exist."));
        }
        $oDepartment = new \Department();
        if (!($oDepartment->existsDepartment($dep_uid))) {
            throw (new \Exception("The departament with $nameField: '$dep_uid' does not exist."));
        }
        return $dep_uid;
    }

    /**
     * Validate dep_title
     * @var string $dep_title. Name or Title for Departament
     * @var string $dep_uid. Uid for Departament
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return array
     *
     * @url GET
     */
    static public function depTitle($dep_title, $dep_uid = '')
    {
        $dep_title = trim($dep_title);
        if ($dep_title == '') {
            throw (new \Exception("The departament with dep_title: '' is incorrect."));
        }

        $oCriteria = new \Criteria( 'workflow' );
        $oCriteria->clearSelectColumns();
        $oCriteria->addSelectColumn( \ContentPeer::CON_CATEGORY );
        $oCriteria->addSelectColumn( \ContentPeer::CON_VALUE );
        $oCriteria->addSelectColumn( \DepartmentPeer::DEP_PARENT );
        $oCriteria->add( \ContentPeer::CON_CATEGORY, 'DEPO_TITLE' );
        $oCriteria->addJoin( \ContentPeer::CON_ID, \DepartmentPeer::DEP_UID, \Criteria::LEFT_JOIN );
        $oCriteria->add( \ContentPeer::CON_VALUE, $dep_title );
        $oCriteria->add( \ContentPeer::CON_LANG, SYS_LANG );
        if ($dep_uid != '') {
            $oCriteria->add( \ContentPeer::CON_ID, $dep_uid, \Criteria::NOT_EQUAL );
        }

        $oDataset = \DepartmentPeer::doSelectRS( $oCriteria );
        $oDataset->setFetchmode( \ResultSet::FETCHMODE_ASSOC );
        if ($oDataset->next()) {
            throw (new \Exception("The departament with dep_title: '$dep_title' already exists."));
        }
        return $dep_title;
    }

    /**
     * Validate dep_status
     * @var string $dep_uid. Uid for Departament
     * @var string $nameField. Name of field for message
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    static public function depStatus($dep_status)
    {
        $dep_status = trim($dep_status);
        $values = array('ACTIVE', 'INACTIVE');
        if (!in_array($dep_status, $values)) {
            throw (new \Exception("The departament with dep_status: '$dep_status' is incorrect."));
        }
        return $dep_status;
    }

    /**
     * Validate usr_uid
     * @var string $dep_uid. Uid for Departament
     * @var string $nameField. Name of field for message
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return string
     */
    static public function usrUid($usr_uid, $nameField = 'usr_uid')
    {
        $usr_uid = trim($usr_uid);
        if ($usr_uid == '') {
            throw (new \Exception("The user with $nameField: '' does not exist."));
        }
        $oUsers = new \Users();
        if (!($oUsers->userExists($usr_uid))) {
            throw (new \Exception("The user with $nameField: '$usr_uid' does not exist."));
        }
        return $usr_uid;
    }

    /**
     * Validate is array
     * @var array $field. Field type array
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    static public function isArray($field, $nameField)
    {
        if (!is_array($field)) {
            throw (new \Exception("Invalid value for '$nameField' it must be an array."));
        }
    }

    /**
     * Validate is boolean
     * @var boolean $field. Field type boolean
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    static public function isBoolean($field, $nameField)
    {
        if (!is_bool($field)) {
            throw (new \Exception("Invalid value for '$nameField' it must be a boolean."));
        }
    }

    /**
     * Validate is boolean
     * @var boolean $field. Field type boolean
     *
     * @access public
     * @author Brayan Pereyra (Cochalo) <brayan@colosa.com>
     * @copyright Colosa - Bolivia
     *
     * @return void
     */
    static public function isNotEmpty($field, $nameField)
    {
        if (empty($field)) {
            throw (new \Exception("The field '$nameField' is empty."));
        }
    }
}


