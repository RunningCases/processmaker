<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/DashboardDasIndPeer.php';

/**
 * Base class that represents a row from the 'DASHBOARD_DAS_IND' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseDashboardDasInd extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        DashboardDasIndPeer
    */
    protected static $peer;

    /**
     * The value for the das_uid field.
     * @var        string
     */
    protected $das_uid = '';

    /**
     * The value for the owner_uid field.
     * @var        string
     */
    protected $owner_uid = '';

    /**
     * The value for the owner_type field.
     * @var        string
     */
    protected $owner_type = '';

    /**
     * @var        Dashboard
     */
    protected $aDashboard;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Get the [das_uid] column value.
     * 
     * @return     string
     */
    public function getDasUid()
    {

        return $this->das_uid;
    }

    /**
     * Get the [owner_uid] column value.
     * 
     * @return     string
     */
    public function getOwnerUid()
    {

        return $this->owner_uid;
    }

    /**
     * Get the [owner_type] column value.
     * 
     * @return     string
     */
    public function getOwnerType()
    {

        return $this->owner_type;
    }

    /**
     * Set the value of [das_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_uid !== $v || $v === '') {
            $this->das_uid = $v;
            $this->modifiedColumns[] = DashboardDasIndPeer::DAS_UID;
        }

        if ($this->aDashboard !== null && $this->aDashboard->getDasUid() !== $v) {
            $this->aDashboard = null;
        }

    } // setDasUid()

    /**
     * Set the value of [owner_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setOwnerUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->owner_uid !== $v || $v === '') {
            $this->owner_uid = $v;
            $this->modifiedColumns[] = DashboardDasIndPeer::OWNER_UID;
        }

    } // setOwnerUid()

    /**
     * Set the value of [owner_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setOwnerType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->owner_type !== $v || $v === '') {
            $this->owner_type = $v;
            $this->modifiedColumns[] = DashboardDasIndPeer::OWNER_TYPE;
        }

    } // setOwnerType()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (1-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param      ResultSet $rs The ResultSet class with cursor advanced to desired record pos.
     * @param      int $startcol 1-based offset column which indicates which restultset column to start with.
     * @return     int next starting column
     * @throws     PropelException  - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate(ResultSet $rs, $startcol = 1)
    {
        try {

            $this->das_uid = $rs->getString($startcol + 0);

            $this->owner_uid = $rs->getString($startcol + 1);

            $this->owner_type = $rs->getString($startcol + 2);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 3; // 3 = DashboardDasIndPeer::NUM_COLUMNS - DashboardDasIndPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating DashboardDasInd object", $e);
        }
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param      Connection $con
     * @return     void
     * @throws     PropelException
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete($con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(DashboardDasIndPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            DashboardDasIndPeer::doDelete($this, $con);
            $this->setDeleted(true);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Stores the object in the database.  If the object is new,
     * it inserts it; otherwise an update is performed.  This method
     * wraps the doSave() worker method in a transaction.
     *
     * @param      Connection $con
     * @return     int The number of rows affected by this insert/update
     * @throws     PropelException
     * @see        doSave()
     */
    public function save($con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(DashboardDasIndPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            $affectedRows = $this->doSave($con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Stores the object in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param      Connection $con
     * @return     int The number of rows affected by this insert/update and any referring
     * @throws     PropelException
     * @see        save()
     */
    protected function doSave($con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;


            // We call the save method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aDashboard !== null) {
                if ($this->aDashboard->isModified()) {
                    $affectedRows += $this->aDashboard->save($con);
                }
                $this->setDashboard($this->aDashboard);
            }


            // If this object has been modified, then save it to the database.
            if ($this->isModified()) {
                if ($this->isNew()) {
                    $pk = DashboardDasIndPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += DashboardDasIndPeer::doUpdate($this, $con);
                }
                $this->resetModified(); // [HL] After being saved an object is no longer 'modified'
            }

            $this->alreadyInSave = false;
        }
        return $affectedRows;
    } // doSave()

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return     array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param      mixed $columns Column name or an array of column names.
     * @return     boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();
            return true;
        } else {
            $this->validationFailures = $res;
            return false;
        }
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggreagated array of ValidationFailed objects will be returned.
     *
     * @param      array $columns Array of column names to validate.
     * @return     mixed <code>true</code> if all validations pass; 
                   array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their coresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aDashboard !== null) {
                if (!$this->aDashboard->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aDashboard->getValidationFailures());
                }
            }


            if (($retval = DashboardDasIndPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }



            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param      string $name name
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TYPE_PHPNAME,
     *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = DashboardDasIndPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        return $this->getByPosition($pos);
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @return     mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch($pos) {
            case 0:
                return $this->getDasUid();
                break;
            case 1:
                return $this->getOwnerUid();
                break;
            case 2:
                return $this->getOwnerType();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param      string $keyType One of the class type constants TYPE_PHPNAME,
     *                        TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = DashboardDasIndPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDasUid(),
            $keys[1] => $this->getOwnerUid(),
            $keys[2] => $this->getOwnerType(),
        );
        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param      string $name peer name
     * @param      mixed $value field value
     * @param      string $type The type of fieldname the $name is of:
     *                     one of the class type constants TYPE_PHPNAME,
     *                     TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = DashboardDasIndPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        return $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param      int $pos position in xml schema
     * @param      mixed $value field value
     * @return     void
     */
    public function setByPosition($pos, $value)
    {
        switch($pos) {
            case 0:
                $this->setDasUid($value);
                break;
            case 1:
                $this->setOwnerUid($value);
                break;
            case 2:
                $this->setOwnerType($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME,
     * TYPE_NUM. The default key type is the column's phpname (e.g. 'authorId')
     *
     * @param      array  $arr     An array to populate the object from.
     * @param      string $keyType The type of keys the array uses.
     * @return     void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = DashboardDasIndPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setDasUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setOwnerUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setOwnerType($arr[$keys[2]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DashboardDasIndPeer::DATABASE_NAME);

        if ($this->isColumnModified(DashboardDasIndPeer::DAS_UID)) {
            $criteria->add(DashboardDasIndPeer::DAS_UID, $this->das_uid);
        }

        if ($this->isColumnModified(DashboardDasIndPeer::OWNER_UID)) {
            $criteria->add(DashboardDasIndPeer::OWNER_UID, $this->owner_uid);
        }

        if ($this->isColumnModified(DashboardDasIndPeer::OWNER_TYPE)) {
            $criteria->add(DashboardDasIndPeer::OWNER_TYPE, $this->owner_type);
        }


        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return     Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(DashboardDasIndPeer::DATABASE_NAME);

        $criteria->add(DashboardDasIndPeer::DAS_UID, $this->das_uid);
        $criteria->add(DashboardDasIndPeer::OWNER_UID, $this->owner_uid);

        return $criteria;
    }

    /**
     * Returns the composite primary key for this object.
     * The array elements will be in same order as specified in XML.
     * @return     array
     */
    public function getPrimaryKey()
    {
        $pks = array();

        $pks[0] = $this->getDasUid();

        $pks[1] = $this->getOwnerUid();

        return $pks;
    }

    /**
     * Set the [composite] primary key.
     *
     * @param      array $keys The elements of the composite key (order must match the order in XML file).
     * @return     void
     */
    public function setPrimaryKey($keys)
    {

        $this->setDasUid($keys[0]);

        $this->setOwnerUid($keys[1]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of DashboardDasInd (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setOwnerType($this->owner_type);


        $copyObj->setNew(true);

        $copyObj->setDasUid(''); // this is a pkey column, so set to default value

        $copyObj->setOwnerUid(''); // this is a pkey column, so set to default value

    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return     DashboardDasInd Clone of current object.
     * @throws     PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);
        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return     DashboardDasIndPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new DashboardDasIndPeer();
        }
        return self::$peer;
    }

    /**
     * Declares an association between this object and a Dashboard object.
     *
     * @param      Dashboard $v
     * @return     void
     * @throws     PropelException
     */
    public function setDashboard($v)
    {


        if ($v === null) {
            $this->setDasUid('');
        } else {
            $this->setDasUid($v->getDasUid());
        }


        $this->aDashboard = $v;
    }


    /**
     * Get the associated Dashboard object
     *
     * @param      Connection Optional Connection object.
     * @return     Dashboard The associated Dashboard object.
     * @throws     PropelException
     */
    public function getDashboard($con = null)
    {
        // include the related Peer class
        include_once 'classes/model/om/BaseDashboardPeer.php';

        if ($this->aDashboard === null && (($this->das_uid !== "" && $this->das_uid !== null))) {

            $this->aDashboard = DashboardPeer::retrieveByPK($this->das_uid, $con);

            /* The following can be used instead of the line above to
               guarantee the related object contains a reference
               to this object, but this level of coupling
               may be undesirable in many circumstances.
               As it can lead to a db query with many results that may
               never be used.
               $obj = DashboardPeer::retrieveByPK($this->das_uid, $con);
               $obj->addDashboards($this);
             */
        }
        return $this->aDashboard;
    }
}

