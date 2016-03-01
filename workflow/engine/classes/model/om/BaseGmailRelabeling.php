<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/GmailRelabelingPeer.php';

/**
 * Base class that represents a row from the 'GMAIL_RELABELING' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseGmailRelabeling extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        GmailRelabelingPeer
    */
    protected static $peer;

    /**
     * The value for the labeling_uid field.
     * @var        string
     */
    protected $labeling_uid;

    /**
     * The value for the create_date field.
     * @var        int
     */
    protected $create_date;

    /**
     * The value for the app_uid field.
     * @var        string
     */
    protected $app_uid = '';

    /**
     * The value for the del_index field.
     * @var        int
     */
    protected $del_index = 0;

    /**
     * The value for the current_last_index field.
     * @var        int
     */
    protected $current_last_index = 0;

    /**
     * The value for the unassigned field.
     * @var        int
     */
    protected $unassigned = 0;

    /**
     * The value for the status field.
     * @var        string
     */
    protected $status = 'pending';

    /**
     * The value for the msg_error field.
     * @var        string
     */
    protected $msg_error;

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
     * Get the [labeling_uid] column value.
     * 
     * @return     string
     */
    public function getLabelingUid()
    {

        return $this->labeling_uid;
    }

    /**
     * Get the [optionally formatted] [create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->create_date === null || $this->create_date === '') {
            return null;
        } elseif (!is_int($this->create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [create_date] as date/time value: " .
                    var_export($this->create_date, true));
            }
        } else {
            $ts = $this->create_date;
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
     * Get the [app_uid] column value.
     * 
     * @return     string
     */
    public function getAppUid()
    {

        return $this->app_uid;
    }

    /**
     * Get the [del_index] column value.
     * 
     * @return     int
     */
    public function getDelIndex()
    {

        return $this->del_index;
    }

    /**
     * Get the [current_last_index] column value.
     * 
     * @return     int
     */
    public function getCurrentLastIndex()
    {

        return $this->current_last_index;
    }

    /**
     * Get the [unassigned] column value.
     * 
     * @return     int
     */
    public function getUnassigned()
    {

        return $this->unassigned;
    }

    /**
     * Get the [status] column value.
     * 
     * @return     string
     */
    public function getStatus()
    {

        return $this->status;
    }

    /**
     * Get the [msg_error] column value.
     * 
     * @return     string
     */
    public function getMsgError()
    {

        return $this->msg_error;
    }

    /**
     * Set the value of [labeling_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setLabelingUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->labeling_uid !== $v) {
            $this->labeling_uid = $v;
            $this->modifiedColumns[] = GmailRelabelingPeer::LABELING_UID;
        }

    } // setLabelingUid()

    /**
     * Set the value of [create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->create_date !== $ts) {
            $this->create_date = $ts;
            $this->modifiedColumns[] = GmailRelabelingPeer::CREATE_DATE;
        }

    } // setCreateDate()

    /**
     * Set the value of [app_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setAppUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->app_uid !== $v || $v === '') {
            $this->app_uid = $v;
            $this->modifiedColumns[] = GmailRelabelingPeer::APP_UID;
        }

    } // setAppUid()

    /**
     * Set the value of [del_index] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDelIndex($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->del_index !== $v || $v === 0) {
            $this->del_index = $v;
            $this->modifiedColumns[] = GmailRelabelingPeer::DEL_INDEX;
        }

    } // setDelIndex()

    /**
     * Set the value of [current_last_index] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setCurrentLastIndex($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->current_last_index !== $v || $v === 0) {
            $this->current_last_index = $v;
            $this->modifiedColumns[] = GmailRelabelingPeer::CURRENT_LAST_INDEX;
        }

    } // setCurrentLastIndex()

    /**
     * Set the value of [unassigned] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setUnassigned($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->unassigned !== $v || $v === 0) {
            $this->unassigned = $v;
            $this->modifiedColumns[] = GmailRelabelingPeer::UNASSIGNED;
        }

    } // setUnassigned()

    /**
     * Set the value of [status] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setStatus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->status !== $v || $v === 'pending') {
            $this->status = $v;
            $this->modifiedColumns[] = GmailRelabelingPeer::STATUS;
        }

    } // setStatus()

    /**
     * Set the value of [msg_error] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setMsgError($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->msg_error !== $v) {
            $this->msg_error = $v;
            $this->modifiedColumns[] = GmailRelabelingPeer::MSG_ERROR;
        }

    } // setMsgError()

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

            $this->labeling_uid = $rs->getString($startcol + 0);

            $this->create_date = $rs->getTimestamp($startcol + 1, null);

            $this->app_uid = $rs->getString($startcol + 2);

            $this->del_index = $rs->getInt($startcol + 3);

            $this->current_last_index = $rs->getInt($startcol + 4);

            $this->unassigned = $rs->getInt($startcol + 5);

            $this->status = $rs->getString($startcol + 6);

            $this->msg_error = $rs->getString($startcol + 7);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 8; // 8 = GmailRelabelingPeer::NUM_COLUMNS - GmailRelabelingPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating GmailRelabeling object", $e);
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
            $con = Propel::getConnection(GmailRelabelingPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            GmailRelabelingPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(GmailRelabelingPeer::DATABASE_NAME);
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
                    $pk = GmailRelabelingPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += GmailRelabelingPeer::doUpdate($this, $con);
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


            if (($retval = GmailRelabelingPeer::doValidate($this, $columns)) !== true) {
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
        $pos = GmailRelabelingPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getLabelingUid();
                break;
            case 1:
                return $this->getCreateDate();
                break;
            case 2:
                return $this->getAppUid();
                break;
            case 3:
                return $this->getDelIndex();
                break;
            case 4:
                return $this->getCurrentLastIndex();
                break;
            case 5:
                return $this->getUnassigned();
                break;
            case 6:
                return $this->getStatus();
                break;
            case 7:
                return $this->getMsgError();
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
        $keys = GmailRelabelingPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getLabelingUid(),
            $keys[1] => $this->getCreateDate(),
            $keys[2] => $this->getAppUid(),
            $keys[3] => $this->getDelIndex(),
            $keys[4] => $this->getCurrentLastIndex(),
            $keys[5] => $this->getUnassigned(),
            $keys[6] => $this->getStatus(),
            $keys[7] => $this->getMsgError(),
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
        $pos = GmailRelabelingPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setLabelingUid($value);
                break;
            case 1:
                $this->setCreateDate($value);
                break;
            case 2:
                $this->setAppUid($value);
                break;
            case 3:
                $this->setDelIndex($value);
                break;
            case 4:
                $this->setCurrentLastIndex($value);
                break;
            case 5:
                $this->setUnassigned($value);
                break;
            case 6:
                $this->setStatus($value);
                break;
            case 7:
                $this->setMsgError($value);
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
        $keys = GmailRelabelingPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setLabelingUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setCreateDate($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setAppUid($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setDelIndex($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setCurrentLastIndex($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setUnassigned($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setStatus($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setMsgError($arr[$keys[7]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(GmailRelabelingPeer::DATABASE_NAME);

        if ($this->isColumnModified(GmailRelabelingPeer::LABELING_UID)) {
            $criteria->add(GmailRelabelingPeer::LABELING_UID, $this->labeling_uid);
        }

        if ($this->isColumnModified(GmailRelabelingPeer::CREATE_DATE)) {
            $criteria->add(GmailRelabelingPeer::CREATE_DATE, $this->create_date);
        }

        if ($this->isColumnModified(GmailRelabelingPeer::APP_UID)) {
            $criteria->add(GmailRelabelingPeer::APP_UID, $this->app_uid);
        }

        if ($this->isColumnModified(GmailRelabelingPeer::DEL_INDEX)) {
            $criteria->add(GmailRelabelingPeer::DEL_INDEX, $this->del_index);
        }

        if ($this->isColumnModified(GmailRelabelingPeer::CURRENT_LAST_INDEX)) {
            $criteria->add(GmailRelabelingPeer::CURRENT_LAST_INDEX, $this->current_last_index);
        }

        if ($this->isColumnModified(GmailRelabelingPeer::UNASSIGNED)) {
            $criteria->add(GmailRelabelingPeer::UNASSIGNED, $this->unassigned);
        }

        if ($this->isColumnModified(GmailRelabelingPeer::STATUS)) {
            $criteria->add(GmailRelabelingPeer::STATUS, $this->status);
        }

        if ($this->isColumnModified(GmailRelabelingPeer::MSG_ERROR)) {
            $criteria->add(GmailRelabelingPeer::MSG_ERROR, $this->msg_error);
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
        $criteria = new Criteria(GmailRelabelingPeer::DATABASE_NAME);

        $criteria->add(GmailRelabelingPeer::LABELING_UID, $this->labeling_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getLabelingUid();
    }

    /**
     * Generic method to set the primary key (labeling_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setLabelingUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of GmailRelabeling (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setCreateDate($this->create_date);

        $copyObj->setAppUid($this->app_uid);

        $copyObj->setDelIndex($this->del_index);

        $copyObj->setCurrentLastIndex($this->current_last_index);

        $copyObj->setUnassigned($this->unassigned);

        $copyObj->setStatus($this->status);

        $copyObj->setMsgError($this->msg_error);


        $copyObj->setNew(true);

        $copyObj->setLabelingUid(NULL); // this is a pkey column, so set to default value

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
     * @return     GmailRelabeling Clone of current object.
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
     * @return     GmailRelabelingPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new GmailRelabelingPeer();
        }
        return self::$peer;
    }
}

