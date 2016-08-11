<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/CatalogPeer.php';

/**
 * Base class that represents a row from the 'CATALOG' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseCatalog extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        CatalogPeer
    */
    protected static $peer;

    /**
     * The value for the cat_uid field.
     * @var        string
     */
    protected $cat_uid = '';

    /**
     * The value for the cat_label_id field.
     * @var        string
     */
    protected $cat_label_id = '';

    /**
     * The value for the cat_type field.
     * @var        string
     */
    protected $cat_type = '';

    /**
     * The value for the cat_flag field.
     * @var        string
     */
    protected $cat_flag = '';

    /**
     * The value for the cat_observation field.
     * @var        string
     */
    protected $cat_observation;

    /**
     * The value for the cat_create_date field.
     * @var        int
     */
    protected $cat_create_date;

    /**
     * The value for the cat_update_date field.
     * @var        int
     */
    protected $cat_update_date;

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
     * Get the [cat_uid] column value.
     * 
     * @return     string
     */
    public function getCatUid()
    {

        return $this->cat_uid;
    }

    /**
     * Get the [cat_label_id] column value.
     * 
     * @return     string
     */
    public function getCatLabelId()
    {

        return $this->cat_label_id;
    }

    /**
     * Get the [cat_type] column value.
     * 
     * @return     string
     */
    public function getCatType()
    {

        return $this->cat_type;
    }

    /**
     * Get the [cat_flag] column value.
     * 
     * @return     string
     */
    public function getCatFlag()
    {

        return $this->cat_flag;
    }

    /**
     * Get the [cat_observation] column value.
     * 
     * @return     string
     */
    public function getCatObservation()
    {

        return $this->cat_observation;
    }

    /**
     * Get the [optionally formatted] [cat_create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getCatCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->cat_create_date === null || $this->cat_create_date === '') {
            return null;
        } elseif (!is_int($this->cat_create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->cat_create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [cat_create_date] as date/time value: " .
                    var_export($this->cat_create_date, true));
            }
        } else {
            $ts = $this->cat_create_date;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Get the [optionally formatted] [cat_update_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getCatUpdateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->cat_update_date === null || $this->cat_update_date === '') {
            return null;
        } elseif (!is_int($this->cat_update_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->cat_update_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [cat_update_date] as date/time value: " .
                    var_export($this->cat_update_date, true));
            }
        } else {
            $ts = $this->cat_update_date;
        }
        if ($format === null) {
            return $ts;
        } elseif (strpos($format, '%') !== false) {
            return strftime($format, $ts);
        } else {
            return date($format, $ts);
        }
    }

    /**
     * Set the value of [cat_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCatUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->cat_uid !== $v || $v === '') {
            $this->cat_uid = $v;
            $this->modifiedColumns[] = CatalogPeer::CAT_UID;
        }

    } // setCatUid()

    /**
     * Set the value of [cat_label_id] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCatLabelId($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->cat_label_id !== $v || $v === '') {
            $this->cat_label_id = $v;
            $this->modifiedColumns[] = CatalogPeer::CAT_LABEL_ID;
        }

    } // setCatLabelId()

    /**
     * Set the value of [cat_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCatType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->cat_type !== $v || $v === '') {
            $this->cat_type = $v;
            $this->modifiedColumns[] = CatalogPeer::CAT_TYPE;
        }

    } // setCatType()

    /**
     * Set the value of [cat_flag] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCatFlag($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->cat_flag !== $v || $v === '') {
            $this->cat_flag = $v;
            $this->modifiedColumns[] = CatalogPeer::CAT_FLAG;
        }

    } // setCatFlag()

    /**
     * Set the value of [cat_observation] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setCatObservation($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->cat_observation !== $v) {
            $this->cat_observation = $v;
            $this->modifiedColumns[] = CatalogPeer::CAT_OBSERVATION;
        }

    } // setCatObservation()

    /**
     * Set the value of [cat_create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setCatCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [cat_create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->cat_create_date !== $ts) {
            $this->cat_create_date = $ts;
            $this->modifiedColumns[] = CatalogPeer::CAT_CREATE_DATE;
        }

    } // setCatCreateDate()

    /**
     * Set the value of [cat_update_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setCatUpdateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [cat_update_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->cat_update_date !== $ts) {
            $this->cat_update_date = $ts;
            $this->modifiedColumns[] = CatalogPeer::CAT_UPDATE_DATE;
        }

    } // setCatUpdateDate()

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

            $this->cat_uid = $rs->getString($startcol + 0);

            $this->cat_label_id = $rs->getString($startcol + 1);

            $this->cat_type = $rs->getString($startcol + 2);

            $this->cat_flag = $rs->getString($startcol + 3);

            $this->cat_observation = $rs->getString($startcol + 4);

            $this->cat_create_date = $rs->getTimestamp($startcol + 5, null);

            $this->cat_update_date = $rs->getTimestamp($startcol + 6, null);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 7; // 7 = CatalogPeer::NUM_COLUMNS - CatalogPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating Catalog object", $e);
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
            $con = Propel::getConnection(CatalogPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            CatalogPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(CatalogPeer::DATABASE_NAME);
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


            // If this object has been modified, then save it to the database.
            if ($this->isModified()) {
                if ($this->isNew()) {
                    $pk = CatalogPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += CatalogPeer::doUpdate($this, $con);
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


            if (($retval = CatalogPeer::doValidate($this, $columns)) !== true) {
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
        $pos = CatalogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getCatUid();
                break;
            case 1:
                return $this->getCatLabelId();
                break;
            case 2:
                return $this->getCatType();
                break;
            case 3:
                return $this->getCatFlag();
                break;
            case 4:
                return $this->getCatObservation();
                break;
            case 5:
                return $this->getCatCreateDate();
                break;
            case 6:
                return $this->getCatUpdateDate();
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
        $keys = CatalogPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getCatUid(),
            $keys[1] => $this->getCatLabelId(),
            $keys[2] => $this->getCatType(),
            $keys[3] => $this->getCatFlag(),
            $keys[4] => $this->getCatObservation(),
            $keys[5] => $this->getCatCreateDate(),
            $keys[6] => $this->getCatUpdateDate(),
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
        $pos = CatalogPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setCatUid($value);
                break;
            case 1:
                $this->setCatLabelId($value);
                break;
            case 2:
                $this->setCatType($value);
                break;
            case 3:
                $this->setCatFlag($value);
                break;
            case 4:
                $this->setCatObservation($value);
                break;
            case 5:
                $this->setCatCreateDate($value);
                break;
            case 6:
                $this->setCatUpdateDate($value);
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
        $keys = CatalogPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setCatUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setCatLabelId($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setCatType($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setCatFlag($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setCatObservation($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setCatCreateDate($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setCatUpdateDate($arr[$keys[6]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(CatalogPeer::DATABASE_NAME);

        if ($this->isColumnModified(CatalogPeer::CAT_UID)) {
            $criteria->add(CatalogPeer::CAT_UID, $this->cat_uid);
        }

        if ($this->isColumnModified(CatalogPeer::CAT_LABEL_ID)) {
            $criteria->add(CatalogPeer::CAT_LABEL_ID, $this->cat_label_id);
        }

        if ($this->isColumnModified(CatalogPeer::CAT_TYPE)) {
            $criteria->add(CatalogPeer::CAT_TYPE, $this->cat_type);
        }

        if ($this->isColumnModified(CatalogPeer::CAT_FLAG)) {
            $criteria->add(CatalogPeer::CAT_FLAG, $this->cat_flag);
        }

        if ($this->isColumnModified(CatalogPeer::CAT_OBSERVATION)) {
            $criteria->add(CatalogPeer::CAT_OBSERVATION, $this->cat_observation);
        }

        if ($this->isColumnModified(CatalogPeer::CAT_CREATE_DATE)) {
            $criteria->add(CatalogPeer::CAT_CREATE_DATE, $this->cat_create_date);
        }

        if ($this->isColumnModified(CatalogPeer::CAT_UPDATE_DATE)) {
            $criteria->add(CatalogPeer::CAT_UPDATE_DATE, $this->cat_update_date);
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
        $criteria = new Criteria(CatalogPeer::DATABASE_NAME);

        $criteria->add(CatalogPeer::CAT_UID, $this->cat_uid);
        $criteria->add(CatalogPeer::CAT_TYPE, $this->cat_type);

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

        $pks[0] = $this->getCatUid();

        $pks[1] = $this->getCatType();

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

        $this->setCatUid($keys[0]);

        $this->setCatType($keys[1]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of Catalog (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setCatLabelId($this->cat_label_id);

        $copyObj->setCatFlag($this->cat_flag);

        $copyObj->setCatObservation($this->cat_observation);

        $copyObj->setCatCreateDate($this->cat_create_date);

        $copyObj->setCatUpdateDate($this->cat_update_date);


        $copyObj->setNew(true);

        $copyObj->setCatUid(''); // this is a pkey column, so set to default value

        $copyObj->setCatType(''); // this is a pkey column, so set to default value

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
     * @return     Catalog Clone of current object.
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
     * @return     CatalogPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new CatalogPeer();
        }
        return self::$peer;
    }
}

