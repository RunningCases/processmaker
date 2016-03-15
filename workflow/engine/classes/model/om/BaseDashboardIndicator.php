<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/DashboardIndicatorPeer.php';

/**
 * Base class that represents a row from the 'DASHBOARD_INDICATOR' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseDashboardIndicator extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        DashboardIndicatorPeer
    */
    protected static $peer;

    /**
     * The value for the das_ind_uid field.
     * @var        string
     */
    protected $das_ind_uid = '';

    /**
     * The value for the das_uid field.
     * @var        string
     */
    protected $das_uid = '';

    /**
     * The value for the das_ind_type field.
     * @var        string
     */
    protected $das_ind_type = '';

    /**
     * The value for the das_ind_title field.
     * @var        string
     */
    protected $das_ind_title = '';

    /**
     * The value for the das_ind_goal field.
     * @var        double
     */
    protected $das_ind_goal = 0;

    /**
     * The value for the das_ind_direction field.
     * @var        int
     */
    protected $das_ind_direction = 2;

    /**
     * The value for the das_uid_process field.
     * @var        string
     */
    protected $das_uid_process = '';

    /**
     * The value for the das_ind_first_figure field.
     * @var        string
     */
    protected $das_ind_first_figure = '';

    /**
     * The value for the das_ind_first_frequency field.
     * @var        string
     */
    protected $das_ind_first_frequency = '';

    /**
     * The value for the das_ind_second_figure field.
     * @var        string
     */
    protected $das_ind_second_figure = '';

    /**
     * The value for the das_ind_second_frequency field.
     * @var        string
     */
    protected $das_ind_second_frequency = '';

    /**
     * The value for the das_ind_create_date field.
     * @var        int
     */
    protected $das_ind_create_date;

    /**
     * The value for the das_ind_update_date field.
     * @var        int
     */
    protected $das_ind_update_date;

    /**
     * The value for the das_ind_status field.
     * @var        int
     */
    protected $das_ind_status = 1;

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
     * Get the [das_ind_uid] column value.
     * 
     * @return     string
     */
    public function getDasIndUid()
    {

        return $this->das_ind_uid;
    }

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
     * Get the [das_ind_type] column value.
     * 
     * @return     string
     */
    public function getDasIndType()
    {

        return $this->das_ind_type;
    }

    /**
     * Get the [das_ind_title] column value.
     * 
     * @return     string
     */
    public function getDasIndTitle()
    {

        return $this->das_ind_title;
    }

    /**
     * Get the [das_ind_goal] column value.
     * 
     * @return     double
     */
    public function getDasIndGoal()
    {

        return $this->das_ind_goal;
    }

    /**
     * Get the [das_ind_direction] column value.
     * 
     * @return     int
     */
    public function getDasIndDirection()
    {

        return $this->das_ind_direction;
    }

    /**
     * Get the [das_uid_process] column value.
     * 
     * @return     string
     */
    public function getDasUidProcess()
    {

        return $this->das_uid_process;
    }

    /**
     * Get the [das_ind_first_figure] column value.
     * 
     * @return     string
     */
    public function getDasIndFirstFigure()
    {

        return $this->das_ind_first_figure;
    }

    /**
     * Get the [das_ind_first_frequency] column value.
     * 
     * @return     string
     */
    public function getDasIndFirstFrequency()
    {

        return $this->das_ind_first_frequency;
    }

    /**
     * Get the [das_ind_second_figure] column value.
     * 
     * @return     string
     */
    public function getDasIndSecondFigure()
    {

        return $this->das_ind_second_figure;
    }

    /**
     * Get the [das_ind_second_frequency] column value.
     * 
     * @return     string
     */
    public function getDasIndSecondFrequency()
    {

        return $this->das_ind_second_frequency;
    }

    /**
     * Get the [optionally formatted] [das_ind_create_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDasIndCreateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->das_ind_create_date === null || $this->das_ind_create_date === '') {
            return null;
        } elseif (!is_int($this->das_ind_create_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->das_ind_create_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [das_ind_create_date] as date/time value: " .
                    var_export($this->das_ind_create_date, true));
            }
        } else {
            $ts = $this->das_ind_create_date;
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
     * Get the [optionally formatted] [das_ind_update_date] column value.
     * 
     * @param      string $format The date/time format string (either date()-style or strftime()-style).
     *                          If format is NULL, then the integer unix timestamp will be returned.
     * @return     mixed Formatted date/time value as string or integer unix timestamp (if format is NULL).
     * @throws     PropelException - if unable to convert the date/time to timestamp.
     */
    public function getDasIndUpdateDate($format = 'Y-m-d H:i:s')
    {

        if ($this->das_ind_update_date === null || $this->das_ind_update_date === '') {
            return null;
        } elseif (!is_int($this->das_ind_update_date)) {
            // a non-timestamp value was set externally, so we convert it
            $ts = strtotime($this->das_ind_update_date);
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse value of [das_ind_update_date] as date/time value: " .
                    var_export($this->das_ind_update_date, true));
            }
        } else {
            $ts = $this->das_ind_update_date;
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
     * Get the [das_ind_status] column value.
     * 
     * @return     int
     */
    public function getDasIndStatus()
    {

        return $this->das_ind_status;
    }

    /**
     * Set the value of [das_ind_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasIndUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ind_uid !== $v || $v === '') {
            $this->das_ind_uid = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_UID;
        }

    } // setDasIndUid()

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
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_UID;
        }

        if ($this->aDashboard !== null && $this->aDashboard->getDasUid() !== $v) {
            $this->aDashboard = null;
        }

    } // setDasUid()

    /**
     * Set the value of [das_ind_type] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasIndType($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ind_type !== $v || $v === '') {
            $this->das_ind_type = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_TYPE;
        }

    } // setDasIndType()

    /**
     * Set the value of [das_ind_title] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasIndTitle($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ind_title !== $v || $v === '') {
            $this->das_ind_title = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_TITLE;
        }

    } // setDasIndTitle()

    /**
     * Set the value of [das_ind_goal] column.
     * 
     * @param      double $v new value
     * @return     void
     */
    public function setDasIndGoal($v)
    {

        if ($this->das_ind_goal !== $v || $v === 0) {
            $this->das_ind_goal = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_GOAL;
        }

    } // setDasIndGoal()

    /**
     * Set the value of [das_ind_direction] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDasIndDirection($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->das_ind_direction !== $v || $v === 2) {
            $this->das_ind_direction = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_DIRECTION;
        }

    } // setDasIndDirection()

    /**
     * Set the value of [das_uid_process] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasUidProcess($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_uid_process !== $v || $v === '') {
            $this->das_uid_process = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_UID_PROCESS;
        }

    } // setDasUidProcess()

    /**
     * Set the value of [das_ind_first_figure] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasIndFirstFigure($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ind_first_figure !== $v || $v === '') {
            $this->das_ind_first_figure = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_FIRST_FIGURE;
        }

    } // setDasIndFirstFigure()

    /**
     * Set the value of [das_ind_first_frequency] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasIndFirstFrequency($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ind_first_frequency !== $v || $v === '') {
            $this->das_ind_first_frequency = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_FIRST_FREQUENCY;
        }

    } // setDasIndFirstFrequency()

    /**
     * Set the value of [das_ind_second_figure] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasIndSecondFigure($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ind_second_figure !== $v || $v === '') {
            $this->das_ind_second_figure = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_SECOND_FIGURE;
        }

    } // setDasIndSecondFigure()

    /**
     * Set the value of [das_ind_second_frequency] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setDasIndSecondFrequency($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->das_ind_second_frequency !== $v || $v === '') {
            $this->das_ind_second_frequency = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_SECOND_FREQUENCY;
        }

    } // setDasIndSecondFrequency()

    /**
     * Set the value of [das_ind_create_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDasIndCreateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [das_ind_create_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->das_ind_create_date !== $ts) {
            $this->das_ind_create_date = $ts;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_CREATE_DATE;
        }

    } // setDasIndCreateDate()

    /**
     * Set the value of [das_ind_update_date] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDasIndUpdateDate($v)
    {

        if ($v !== null && !is_int($v)) {
            $ts = strtotime($v);
            //Date/time accepts null values
            if ($v == '') {
                $ts = null;
            }
            if ($ts === -1 || $ts === false) {
                throw new PropelException("Unable to parse date/time value for [das_ind_update_date] from input: " .
                    var_export($v, true));
            }
        } else {
            $ts = $v;
        }
        if ($this->das_ind_update_date !== $ts) {
            $this->das_ind_update_date = $ts;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_UPDATE_DATE;
        }

    } // setDasIndUpdateDate()

    /**
     * Set the value of [das_ind_status] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setDasIndStatus($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->das_ind_status !== $v || $v === 1) {
            $this->das_ind_status = $v;
            $this->modifiedColumns[] = DashboardIndicatorPeer::DAS_IND_STATUS;
        }

    } // setDasIndStatus()

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

            $this->das_ind_uid = $rs->getString($startcol + 0);

            $this->das_uid = $rs->getString($startcol + 1);

            $this->das_ind_type = $rs->getString($startcol + 2);

            $this->das_ind_title = $rs->getString($startcol + 3);

            $this->das_ind_goal = $rs->getFloat($startcol + 4);

            $this->das_ind_direction = $rs->getInt($startcol + 5);

            $this->das_uid_process = $rs->getString($startcol + 6);

            $this->das_ind_first_figure = $rs->getString($startcol + 7);

            $this->das_ind_first_frequency = $rs->getString($startcol + 8);

            $this->das_ind_second_figure = $rs->getString($startcol + 9);

            $this->das_ind_second_frequency = $rs->getString($startcol + 10);

            $this->das_ind_create_date = $rs->getTimestamp($startcol + 11, null);

            $this->das_ind_update_date = $rs->getTimestamp($startcol + 12, null);

            $this->das_ind_status = $rs->getInt($startcol + 13);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 14; // 14 = DashboardIndicatorPeer::NUM_COLUMNS - DashboardIndicatorPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating DashboardIndicator object", $e);
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
            $con = Propel::getConnection(DashboardIndicatorPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            DashboardIndicatorPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(DashboardIndicatorPeer::DATABASE_NAME);
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
                    $pk = DashboardIndicatorPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += DashboardIndicatorPeer::doUpdate($this, $con);
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


            if (($retval = DashboardIndicatorPeer::doValidate($this, $columns)) !== true) {
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
        $pos = DashboardIndicatorPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getDasIndUid();
                break;
            case 1:
                return $this->getDasUid();
                break;
            case 2:
                return $this->getDasIndType();
                break;
            case 3:
                return $this->getDasIndTitle();
                break;
            case 4:
                return $this->getDasIndGoal();
                break;
            case 5:
                return $this->getDasIndDirection();
                break;
            case 6:
                return $this->getDasUidProcess();
                break;
            case 7:
                return $this->getDasIndFirstFigure();
                break;
            case 8:
                return $this->getDasIndFirstFrequency();
                break;
            case 9:
                return $this->getDasIndSecondFigure();
                break;
            case 10:
                return $this->getDasIndSecondFrequency();
                break;
            case 11:
                return $this->getDasIndCreateDate();
                break;
            case 12:
                return $this->getDasIndUpdateDate();
                break;
            case 13:
                return $this->getDasIndStatus();
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
        $keys = DashboardIndicatorPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getDasIndUid(),
            $keys[1] => $this->getDasUid(),
            $keys[2] => $this->getDasIndType(),
            $keys[3] => $this->getDasIndTitle(),
            $keys[4] => $this->getDasIndGoal(),
            $keys[5] => $this->getDasIndDirection(),
            $keys[6] => $this->getDasUidProcess(),
            $keys[7] => $this->getDasIndFirstFigure(),
            $keys[8] => $this->getDasIndFirstFrequency(),
            $keys[9] => $this->getDasIndSecondFigure(),
            $keys[10] => $this->getDasIndSecondFrequency(),
            $keys[11] => $this->getDasIndCreateDate(),
            $keys[12] => $this->getDasIndUpdateDate(),
            $keys[13] => $this->getDasIndStatus(),
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
        $pos = DashboardIndicatorPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setDasIndUid($value);
                break;
            case 1:
                $this->setDasUid($value);
                break;
            case 2:
                $this->setDasIndType($value);
                break;
            case 3:
                $this->setDasIndTitle($value);
                break;
            case 4:
                $this->setDasIndGoal($value);
                break;
            case 5:
                $this->setDasIndDirection($value);
                break;
            case 6:
                $this->setDasUidProcess($value);
                break;
            case 7:
                $this->setDasIndFirstFigure($value);
                break;
            case 8:
                $this->setDasIndFirstFrequency($value);
                break;
            case 9:
                $this->setDasIndSecondFigure($value);
                break;
            case 10:
                $this->setDasIndSecondFrequency($value);
                break;
            case 11:
                $this->setDasIndCreateDate($value);
                break;
            case 12:
                $this->setDasIndUpdateDate($value);
                break;
            case 13:
                $this->setDasIndStatus($value);
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
        $keys = DashboardIndicatorPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setDasIndUid($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setDasUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setDasIndType($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setDasIndTitle($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setDasIndGoal($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setDasIndDirection($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setDasUidProcess($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setDasIndFirstFigure($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setDasIndFirstFrequency($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setDasIndSecondFigure($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setDasIndSecondFrequency($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setDasIndCreateDate($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setDasIndUpdateDate($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setDasIndStatus($arr[$keys[13]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(DashboardIndicatorPeer::DATABASE_NAME);

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_UID)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_UID, $this->das_ind_uid);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_UID)) {
            $criteria->add(DashboardIndicatorPeer::DAS_UID, $this->das_uid);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_TYPE)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_TYPE, $this->das_ind_type);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_TITLE)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_TITLE, $this->das_ind_title);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_GOAL)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_GOAL, $this->das_ind_goal);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_DIRECTION)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_DIRECTION, $this->das_ind_direction);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_UID_PROCESS)) {
            $criteria->add(DashboardIndicatorPeer::DAS_UID_PROCESS, $this->das_uid_process);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_FIRST_FIGURE)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_FIRST_FIGURE, $this->das_ind_first_figure);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_FIRST_FREQUENCY)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_FIRST_FREQUENCY, $this->das_ind_first_frequency);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_SECOND_FIGURE)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_SECOND_FIGURE, $this->das_ind_second_figure);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_SECOND_FREQUENCY)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_SECOND_FREQUENCY, $this->das_ind_second_frequency);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_CREATE_DATE)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_CREATE_DATE, $this->das_ind_create_date);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_UPDATE_DATE)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_UPDATE_DATE, $this->das_ind_update_date);
        }

        if ($this->isColumnModified(DashboardIndicatorPeer::DAS_IND_STATUS)) {
            $criteria->add(DashboardIndicatorPeer::DAS_IND_STATUS, $this->das_ind_status);
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
        $criteria = new Criteria(DashboardIndicatorPeer::DATABASE_NAME);

        $criteria->add(DashboardIndicatorPeer::DAS_IND_UID, $this->das_ind_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getDasIndUid();
    }

    /**
     * Generic method to set the primary key (das_ind_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setDasIndUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of DashboardIndicator (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setDasUid($this->das_uid);

        $copyObj->setDasIndType($this->das_ind_type);

        $copyObj->setDasIndTitle($this->das_ind_title);

        $copyObj->setDasIndGoal($this->das_ind_goal);

        $copyObj->setDasIndDirection($this->das_ind_direction);

        $copyObj->setDasUidProcess($this->das_uid_process);

        $copyObj->setDasIndFirstFigure($this->das_ind_first_figure);

        $copyObj->setDasIndFirstFrequency($this->das_ind_first_frequency);

        $copyObj->setDasIndSecondFigure($this->das_ind_second_figure);

        $copyObj->setDasIndSecondFrequency($this->das_ind_second_frequency);

        $copyObj->setDasIndCreateDate($this->das_ind_create_date);

        $copyObj->setDasIndUpdateDate($this->das_ind_update_date);

        $copyObj->setDasIndStatus($this->das_ind_status);


        $copyObj->setNew(true);

        $copyObj->setDasIndUid(''); // this is a pkey column, so set to default value

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
     * @return     DashboardIndicator Clone of current object.
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
     * @return     DashboardIndicatorPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new DashboardIndicatorPeer();
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

