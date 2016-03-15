<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/ProReportingPeer.php';

/**
 * Base class that represents a row from the 'PRO_REPORTING' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseProReporting extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ProReportingPeer
    */
    protected static $peer;

    /**
     * The value for the pro_uid field.
     * @var        string
     */
    protected $pro_uid;

    /**
     * The value for the month field.
     * @var        int
     */
    protected $month = 0;

    /**
     * The value for the year field.
     * @var        int
     */
    protected $year = 0;

    /**
     * The value for the avg_time field.
     * @var        double
     */
    protected $avg_time = 0;

    /**
     * The value for the sdv_time field.
     * @var        double
     */
    protected $sdv_time = 0;

    /**
     * The value for the total_cases_in field.
     * @var        double
     */
    protected $total_cases_in = 0;

    /**
     * The value for the total_cases_out field.
     * @var        double
     */
    protected $total_cases_out = 0;

    /**
     * The value for the configured_process_time field.
     * @var        double
     */
    protected $configured_process_time = 0;

    /**
     * The value for the configured_process_cost field.
     * @var        double
     */
    protected $configured_process_cost = 0;

    /**
     * The value for the total_cases_open field.
     * @var        double
     */
    protected $total_cases_open = 0;

    /**
     * The value for the total_cases_overdue field.
     * @var        double
     */
    protected $total_cases_overdue = 0;

    /**
     * The value for the total_cases_on_time field.
     * @var        double
     */
    protected $total_cases_on_time = 0;

    /**
     * The value for the pro_cost field.
     * @var        double
     */
    protected $pro_cost = 0;

    /**
     * The value for the pro_unit_cost field.
     * @var        string
     */
    protected $pro_unit_cost = '';

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
     * Get the [pro_uid] column value.
     * 
     * @return     string
     */
    public function getProUid()
    {

        return $this->pro_uid;
    }

    /**
     * Get the [month] column value.
     * 
     * @return     int
     */
    public function getMonth()
    {

        return $this->month;
    }

    /**
     * Get the [year] column value.
     * 
     * @return     int
     */
    public function getYear()
    {

        return $this->year;
    }

    /**
     * Get the [avg_time] column value.
     * 
     * @return     double
     */
    public function getAvgTime()
    {

        return $this->avg_time;
    }

    /**
     * Get the [sdv_time] column value.
     * 
     * @return     double
     */
    public function getSdvTime()
    {

        return $this->sdv_time;
    }

    /**
     * Get the [total_cases_in] column value.
     * 
     * @return     double
     */
    public function getTotalCasesIn()
    {

        return $this->total_cases_in;
    }

    /**
     * Get the [total_cases_out] column value.
     * 
     * @return     double
     */
    public function getTotalCasesOut()
    {

        return $this->total_cases_out;
    }

    /**
     * Get the [configured_process_time] column value.
     * 
     * @return     double
     */
    public function getConfiguredProcessTime()
    {

        return $this->configured_process_time;
    }

    /**
     * Get the [configured_process_cost] column value.
     * 
     * @return     double
     */
    public function getConfiguredProcessCost()
    {

        return $this->configured_process_cost;
    }

    /**
     * Get the [total_cases_open] column value.
     * 
     * @return     double
     */
    public function getTotalCasesOpen()
    {

        return $this->total_cases_open;
    }

    /**
     * Get the [total_cases_overdue] column value.
     * 
     * @return     double
     */
    public function getTotalCasesOverdue()
    {

        return $this->total_cases_overdue;
    }

    /**
     * Get the [total_cases_on_time] column value.
     * 
     * @return     double
     */
    public function getTotalCasesOnTime()
    {

        return $this->total_cases_on_time;
    }

    /**
     * Get the [pro_cost] column value.
     * 
     * @return     double
     */
    public function getProCost()
    {

        return $this->pro_cost;
    }

    /**
     * Get the [pro_unit_cost] column value.
     * 
     * @return     string
     */
    public function getProUnitCost()
    {

        return $this->pro_unit_cost;
    }

    /**
     * Set the value of [pro_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setProUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->pro_uid !== $v) {
            $this->pro_uid = $v;
            $this->modifiedColumns[] = ProReportingPeer::PRO_UID;
        }

    } // setProUid()

    /**
     * Set the value of [month] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setMonth($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->month !== $v || $v === 0) {
            $this->month = $v;
            $this->modifiedColumns[] = ProReportingPeer::MONTH;
        }

    } // setMonth()

    /**
     * Set the value of [year] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setYear($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->year !== $v || $v === 0) {
            $this->year = $v;
            $this->modifiedColumns[] = ProReportingPeer::YEAR;
        }

    } // setYear()

    /**
     * Set the value of [avg_time] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setAvgTime($v)
    {

        if ($this->avg_time !== $v || $v === 0) {
            $this->avg_time = $v;
            $this->modifiedColumns[] = ProReportingPeer::AVG_TIME;
        }

    } // setAvgTime()

    /**
     * Set the value of [sdv_time] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setSdvTime($v)
    {

        if ($this->sdv_time !== $v || $v === 0) {
            $this->sdv_time = $v;
            $this->modifiedColumns[] = ProReportingPeer::SDV_TIME;
        }

    } // setSdvTime()

    /**
     * Set the value of [total_cases_in] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setTotalCasesIn($v)
    {

        if ($this->total_cases_in !== $v || $v === 0) {
            $this->total_cases_in = $v;
            $this->modifiedColumns[] = ProReportingPeer::TOTAL_CASES_IN;
        }

    } // setTotalCasesIn()

    /**
     * Set the value of [total_cases_out] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setTotalCasesOut($v)
    {

        if ($this->total_cases_out !== $v || $v === 0) {
            $this->total_cases_out = $v;
            $this->modifiedColumns[] = ProReportingPeer::TOTAL_CASES_OUT;
        }

    } // setTotalCasesOut()

    /**
     * Set the value of [configured_process_time] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setConfiguredProcessTime($v)
    {

        if ($this->configured_process_time !== $v || $v === 0) {
            $this->configured_process_time = $v;
            $this->modifiedColumns[] = ProReportingPeer::CONFIGURED_PROCESS_TIME;
        }

    } // setConfiguredProcessTime()

    /**
     * Set the value of [configured_process_cost] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setConfiguredProcessCost($v)
    {

        if ($this->configured_process_cost !== $v || $v === 0) {
            $this->configured_process_cost = $v;
            $this->modifiedColumns[] = ProReportingPeer::CONFIGURED_PROCESS_COST;
        }

    } // setConfiguredProcessCost()

    /**
     * Set the value of [total_cases_open] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setTotalCasesOpen($v)
    {

        if ($this->total_cases_open !== $v || $v === 0) {
            $this->total_cases_open = $v;
            $this->modifiedColumns[] = ProReportingPeer::TOTAL_CASES_OPEN;
        }

    } // setTotalCasesOpen()

    /**
     * Set the value of [total_cases_overdue] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setTotalCasesOverdue($v)
    {

        if ($this->total_cases_overdue !== $v || $v === 0) {
            $this->total_cases_overdue = $v;
            $this->modifiedColumns[] = ProReportingPeer::TOTAL_CASES_OVERDUE;
        }

    } // setTotalCasesOverdue()

    /**
     * Set the value of [total_cases_on_time] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setTotalCasesOnTime($v)
    {

        if ($this->total_cases_on_time !== $v || $v === 0) {
            $this->total_cases_on_time = $v;
            $this->modifiedColumns[] = ProReportingPeer::TOTAL_CASES_ON_TIME;
        }

    } // setTotalCasesOnTime()

    /**
     * Set the value of [pro_cost] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setProCost($v)
    {

        if ($this->pro_cost !== $v || $v === 0) {
            $this->pro_cost = $v;
            $this->modifiedColumns[] = ProReportingPeer::PRO_COST;
        }

    } // setProCost()

    /**
     * Set the value of [pro_unit_cost] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setProUnitCost($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->pro_unit_cost !== $v || $v === '') {
            $this->pro_unit_cost = $v;
            $this->modifiedColumns[] = ProReportingPeer::PRO_UNIT_COST;
        }

    } // setProUnitCost()

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

            $this->pro_uid = $rs->getString($startcol + 0);

            $this->month = $rs->getInt($startcol + 1);

            $this->year = $rs->getInt($startcol + 2);

            $this->avg_time = $rs->getFloat($startcol + 3);

            $this->sdv_time = $rs->getFloat($startcol + 4);

            $this->total_cases_in = $rs->getFloat($startcol + 5);

            $this->total_cases_out = $rs->getFloat($startcol + 6);

            $this->configured_process_time = $rs->getFloat($startcol + 7);

            $this->configured_process_cost = $rs->getFloat($startcol + 8);

            $this->total_cases_open = $rs->getFloat($startcol + 9);

            $this->total_cases_overdue = $rs->getFloat($startcol + 10);

            $this->total_cases_on_time = $rs->getFloat($startcol + 11);

            $this->pro_cost = $rs->getFloat($startcol + 12);

            $this->pro_unit_cost = $rs->getString($startcol + 13);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 14; // 14 = ProReportingPeer::NUM_COLUMNS - ProReportingPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating ProReporting object", $e);
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
            $con = Propel::getConnection(ProReportingPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            ProReportingPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(ProReportingPeer::DATABASE_NAME);
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
                    $pk = ProReportingPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += ProReportingPeer::doUpdate($this, $con);
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


            if (($retval = ProReportingPeer::doValidate($this, $columns)) !== true) {
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
        $pos = ProReportingPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getProUid();
                break;
            case 1:
                return $this->getMonth();
                break;
            case 2:
                return $this->getYear();
                break;
            case 3:
                return $this->getAvgTime();
                break;
            case 4:
                return $this->getSdvTime();
                break;
            case 5:
                return $this->getTotalCasesIn();
                break;
            case 6:
                return $this->getTotalCasesOut();
                break;
            case 7:
                return $this->getConfiguredProcessTime();
                break;
            case 8:
                return $this->getConfiguredProcessCost();
                break;
            case 9:
                return $this->getTotalCasesOpen();
                break;
            case 10:
                return $this->getTotalCasesOverdue();
                break;
            case 11:
                return $this->getTotalCasesOnTime();
                break;
            case 12:
                return $this->getProCost();
                break;
            case 13:
                return $this->getProUnitCost();
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
        $keys = ProReportingPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getProUid(),
            $keys[1] => $this->getMonth(),
            $keys[2] => $this->getYear(),
            $keys[3] => $this->getAvgTime(),
            $keys[4] => $this->getSdvTime(),
            $keys[5] => $this->getTotalCasesIn(),
            $keys[6] => $this->getTotalCasesOut(),
            $keys[7] => $this->getConfiguredProcessTime(),
            $keys[8] => $this->getConfiguredProcessCost(),
            $keys[9] => $this->getTotalCasesOpen(),
            $keys[10] => $this->getTotalCasesOverdue(),
            $keys[11] => $this->getTotalCasesOnTime(),
            $keys[12] => $this->getProCost(),
            $keys[13] => $this->getProUnitCost(),
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
        $pos = ProReportingPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setProUid($value);
                break;
            case 1:
                $this->setMonth($value);
                break;
            case 2:
                $this->setYear($value);
                break;
            case 3:
                $this->setAvgTime($value);
                break;
            case 4:
                $this->setSdvTime($value);
                break;
            case 5:
                $this->setTotalCasesIn($value);
                break;
            case 6:
                $this->setTotalCasesOut($value);
                break;
            case 7:
                $this->setConfiguredProcessTime($value);
                break;
            case 8:
                $this->setConfiguredProcessCost($value);
                break;
            case 9:
                $this->setTotalCasesOpen($value);
                break;
            case 10:
                $this->setTotalCasesOverdue($value);
                break;
            case 11:
                $this->setTotalCasesOnTime($value);
                break;
            case 12:
                $this->setProCost($value);
                break;
            case 13:
                $this->setProUnitCost($value);
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
        $keys = ProReportingPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setProUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setMonth($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setYear($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setAvgTime($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setSdvTime($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setTotalCasesIn($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setTotalCasesOut($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setConfiguredProcessTime($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setConfiguredProcessCost($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setTotalCasesOpen($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setTotalCasesOverdue($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setTotalCasesOnTime($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setProCost($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setProUnitCost($arr[$keys[13]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ProReportingPeer::DATABASE_NAME);

        if ($this->isColumnModified(ProReportingPeer::PRO_UID)) {
            $criteria->add(ProReportingPeer::PRO_UID, $this->pro_uid);
        }

        if ($this->isColumnModified(ProReportingPeer::MONTH)) {
            $criteria->add(ProReportingPeer::MONTH, $this->month);
        }

        if ($this->isColumnModified(ProReportingPeer::YEAR)) {
            $criteria->add(ProReportingPeer::YEAR, $this->year);
        }

        if ($this->isColumnModified(ProReportingPeer::AVG_TIME)) {
            $criteria->add(ProReportingPeer::AVG_TIME, $this->avg_time);
        }

        if ($this->isColumnModified(ProReportingPeer::SDV_TIME)) {
            $criteria->add(ProReportingPeer::SDV_TIME, $this->sdv_time);
        }

        if ($this->isColumnModified(ProReportingPeer::TOTAL_CASES_IN)) {
            $criteria->add(ProReportingPeer::TOTAL_CASES_IN, $this->total_cases_in);
        }

        if ($this->isColumnModified(ProReportingPeer::TOTAL_CASES_OUT)) {
            $criteria->add(ProReportingPeer::TOTAL_CASES_OUT, $this->total_cases_out);
        }

        if ($this->isColumnModified(ProReportingPeer::CONFIGURED_PROCESS_TIME)) {
            $criteria->add(ProReportingPeer::CONFIGURED_PROCESS_TIME, $this->configured_process_time);
        }

        if ($this->isColumnModified(ProReportingPeer::CONFIGURED_PROCESS_COST)) {
            $criteria->add(ProReportingPeer::CONFIGURED_PROCESS_COST, $this->configured_process_cost);
        }

        if ($this->isColumnModified(ProReportingPeer::TOTAL_CASES_OPEN)) {
            $criteria->add(ProReportingPeer::TOTAL_CASES_OPEN, $this->total_cases_open);
        }

        if ($this->isColumnModified(ProReportingPeer::TOTAL_CASES_OVERDUE)) {
            $criteria->add(ProReportingPeer::TOTAL_CASES_OVERDUE, $this->total_cases_overdue);
        }

        if ($this->isColumnModified(ProReportingPeer::TOTAL_CASES_ON_TIME)) {
            $criteria->add(ProReportingPeer::TOTAL_CASES_ON_TIME, $this->total_cases_on_time);
        }

        if ($this->isColumnModified(ProReportingPeer::PRO_COST)) {
            $criteria->add(ProReportingPeer::PRO_COST, $this->pro_cost);
        }

        if ($this->isColumnModified(ProReportingPeer::PRO_UNIT_COST)) {
            $criteria->add(ProReportingPeer::PRO_UNIT_COST, $this->pro_unit_cost);
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
        $criteria = new Criteria(ProReportingPeer::DATABASE_NAME);

        $criteria->add(ProReportingPeer::PRO_UID, $this->pro_uid);
        $criteria->add(ProReportingPeer::MONTH, $this->month);
        $criteria->add(ProReportingPeer::YEAR, $this->year);

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

        $pks[0] = $this->getProUid();

        $pks[1] = $this->getMonth();

        $pks[2] = $this->getYear();

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

        $this->setProUid($keys[0]);

        $this->setMonth($keys[1]);

        $this->setYear($keys[2]);

    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of ProReporting (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setAvgTime($this->avg_time);

        $copyObj->setSdvTime($this->sdv_time);

        $copyObj->setTotalCasesIn($this->total_cases_in);

        $copyObj->setTotalCasesOut($this->total_cases_out);

        $copyObj->setConfiguredProcessTime($this->configured_process_time);

        $copyObj->setConfiguredProcessCost($this->configured_process_cost);

        $copyObj->setTotalCasesOpen($this->total_cases_open);

        $copyObj->setTotalCasesOverdue($this->total_cases_overdue);

        $copyObj->setTotalCasesOnTime($this->total_cases_on_time);

        $copyObj->setProCost($this->pro_cost);

        $copyObj->setProUnitCost($this->pro_unit_cost);


        $copyObj->setNew(true);

        $copyObj->setProUid(NULL); // this is a pkey column, so set to default value

        $copyObj->setMonth('0'); // this is a pkey column, so set to default value

        $copyObj->setYear('0'); // this is a pkey column, so set to default value

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
     * @return     ProReporting Clone of current object.
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
     * @return     ProReportingPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ProReportingPeer();
        }
        return self::$peer;
    }
}

