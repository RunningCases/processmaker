<?php

require_once 'propel/om/BaseObject.php';

require_once 'propel/om/Persistent.php';


include_once 'propel/util/Criteria.php';

include_once 'classes/model/PluginsRegistryPeer.php';

/**
 * Base class that represents a row from the 'PLUGINS_REGISTRY' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BasePluginsRegistry extends BaseObject implements Persistent
{

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        PluginsRegistryPeer
    */
    protected static $peer;

    /**
     * The value for the id field.
     * @var        int
     */
    protected $id;

    /**
     * The value for the pr_uid field.
     * @var        string
     */
    protected $pr_uid = '';

    /**
     * The value for the plugin_namespace field.
     * @var        string
     */
    protected $plugin_namespace;

    /**
     * The value for the plugin_description field.
     * @var        string
     */
    protected $plugin_description = '';

    /**
     * The value for the class_name field.
     * @var        string
     */
    protected $class_name;

    /**
     * The value for the friendly_name field.
     * @var        string
     */
    protected $friendly_name = '';

    /**
     * The value for the file_name field.
     * @var        string
     */
    protected $file_name = '';

    /**
     * The value for the plugin_folder field.
     * @var        string
     */
    protected $plugin_folder = '';

    /**
     * The value for the plugin_version field.
     * @var        string
     */
    protected $plugin_version = '';

    /**
     * The value for the plugin_enable field.
     * @var        boolean
     */
    protected $plugin_enable = true;

    /**
     * The value for the plugin_private field.
     * @var        boolean
     */
    protected $plugin_private = true;

    /**
     * The value for the plugin_menus field.
     * @var        string
     */
    protected $plugin_menus;

    /**
     * The value for the plugin_folders field.
     * @var        string
     */
    protected $plugin_folders;

    /**
     * The value for the plugin_triggers field.
     * @var        string
     */
    protected $plugin_triggers;

    /**
     * The value for the plugin_pm_functions field.
     * @var        string
     */
    protected $plugin_pm_functions;

    /**
     * The value for the plugin_redirect_login field.
     * @var        string
     */
    protected $plugin_redirect_login;

    /**
     * The value for the plugin_steps field.
     * @var        string
     */
    protected $plugin_steps;

    /**
     * The value for the plugin_css field.
     * @var        string
     */
    protected $plugin_css;

    /**
     * The value for the plugin_js field.
     * @var        string
     */
    protected $plugin_js;

    /**
     * The value for the plugin_rest_service field.
     * @var        string
     */
    protected $plugin_rest_service;

    /**
     * The value for the plugin_attributes field.
     * @var        string
     */
    protected $plugin_attributes;

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
     * Get the [id] column value.
     * 
     * @return     int
     */
    public function getId()
    {

        return $this->id;
    }

    /**
     * Get the [pr_uid] column value.
     * 
     * @return     string
     */
    public function getPrUid()
    {

        return $this->pr_uid;
    }

    /**
     * Get the [plugin_namespace] column value.
     * 
     * @return     string
     */
    public function getPluginNamespace()
    {

        return $this->plugin_namespace;
    }

    /**
     * Get the [plugin_description] column value.
     * 
     * @return     string
     */
    public function getPluginDescription()
    {

        return $this->plugin_description;
    }

    /**
     * Get the [class_name] column value.
     * 
     * @return     string
     */
    public function getClassName()
    {

        return $this->class_name;
    }

    /**
     * Get the [friendly_name] column value.
     * 
     * @return     string
     */
    public function getFriendlyName()
    {

        return $this->friendly_name;
    }

    /**
     * Get the [file_name] column value.
     * 
     * @return     string
     */
    public function getFileName()
    {

        return $this->file_name;
    }

    /**
     * Get the [plugin_folder] column value.
     * 
     * @return     string
     */
    public function getPluginFolder()
    {

        return $this->plugin_folder;
    }

    /**
     * Get the [plugin_version] column value.
     * 
     * @return     string
     */
    public function getPluginVersion()
    {

        return $this->plugin_version;
    }

    /**
     * Get the [plugin_enable] column value.
     * 
     * @return     boolean
     */
    public function getPluginEnable()
    {

        return $this->plugin_enable;
    }

    /**
     * Get the [plugin_private] column value.
     * 
     * @return     boolean
     */
    public function getPluginPrivate()
    {

        return $this->plugin_private;
    }

    /**
     * Get the [plugin_menus] column value.
     * 
     * @return     string
     */
    public function getPluginMenus()
    {

        return $this->plugin_menus;
    }

    /**
     * Get the [plugin_folders] column value.
     * 
     * @return     string
     */
    public function getPluginFolders()
    {

        return $this->plugin_folders;
    }

    /**
     * Get the [plugin_triggers] column value.
     * 
     * @return     string
     */
    public function getPluginTriggers()
    {

        return $this->plugin_triggers;
    }

    /**
     * Get the [plugin_pm_functions] column value.
     * 
     * @return     string
     */
    public function getPluginPmFunctions()
    {

        return $this->plugin_pm_functions;
    }

    /**
     * Get the [plugin_redirect_login] column value.
     * 
     * @return     string
     */
    public function getPluginRedirectLogin()
    {

        return $this->plugin_redirect_login;
    }

    /**
     * Get the [plugin_steps] column value.
     * 
     * @return     string
     */
    public function getPluginSteps()
    {

        return $this->plugin_steps;
    }

    /**
     * Get the [plugin_css] column value.
     * 
     * @return     string
     */
    public function getPluginCss()
    {

        return $this->plugin_css;
    }

    /**
     * Get the [plugin_js] column value.
     * 
     * @return     string
     */
    public function getPluginJs()
    {

        return $this->plugin_js;
    }

    /**
     * Get the [plugin_rest_service] column value.
     * 
     * @return     string
     */
    public function getPluginRestService()
    {

        return $this->plugin_rest_service;
    }

    /**
     * Get the [plugin_attributes] column value.
     * 
     * @return     string
     */
    public function getPluginAttributes()
    {

        return $this->plugin_attributes;
    }

    /**
     * Set the value of [id] column.
     * 
     * @param      int $v new value
     * @return     void
     */
    public function setId($v)
    {

        // Since the native PHP type for this column is integer,
        // we will cast the input value to an int (if it is not).
        if ($v !== null && !is_int($v) && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::ID;
        }

    } // setId()

    /**
     * Set the value of [pr_uid] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPrUid($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->pr_uid !== $v || $v === '') {
            $this->pr_uid = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PR_UID;
        }

    } // setPrUid()

    /**
     * Set the value of [plugin_namespace] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginNamespace($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_namespace !== $v) {
            $this->plugin_namespace = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_NAMESPACE;
        }

    } // setPluginNamespace()

    /**
     * Set the value of [plugin_description] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginDescription($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_description !== $v || $v === '') {
            $this->plugin_description = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_DESCRIPTION;
        }

    } // setPluginDescription()

    /**
     * Set the value of [class_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setClassName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->class_name !== $v) {
            $this->class_name = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::CLASS_NAME;
        }

    } // setClassName()

    /**
     * Set the value of [friendly_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFriendlyName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->friendly_name !== $v || $v === '') {
            $this->friendly_name = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::FRIENDLY_NAME;
        }

    } // setFriendlyName()

    /**
     * Set the value of [file_name] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setFileName($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->file_name !== $v || $v === '') {
            $this->file_name = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::FILE_NAME;
        }

    } // setFileName()

    /**
     * Set the value of [plugin_folder] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginFolder($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_folder !== $v || $v === '') {
            $this->plugin_folder = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_FOLDER;
        }

    } // setPluginFolder()

    /**
     * Set the value of [plugin_version] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginVersion($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_version !== $v || $v === '') {
            $this->plugin_version = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_VERSION;
        }

    } // setPluginVersion()

    /**
     * Set the value of [plugin_enable] column.
     * 
     * @param      boolean $v new value
     * @return     void
     */
    public function setPluginEnable($v)
    {

        if ($this->plugin_enable !== $v || $v === true) {
            $this->plugin_enable = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_ENABLE;
        }

    } // setPluginEnable()

    /**
     * Set the value of [plugin_private] column.
     * 
     * @param      boolean $v new value
     * @return     void
     */
    public function setPluginPrivate($v)
    {

        if ($this->plugin_private !== $v || $v === true) {
            $this->plugin_private = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_PRIVATE;
        }

    } // setPluginPrivate()

    /**
     * Set the value of [plugin_menus] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginMenus($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_menus !== $v) {
            $this->plugin_menus = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_MENUS;
        }

    } // setPluginMenus()

    /**
     * Set the value of [plugin_folders] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginFolders($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_folders !== $v) {
            $this->plugin_folders = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_FOLDERS;
        }

    } // setPluginFolders()

    /**
     * Set the value of [plugin_triggers] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginTriggers($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_triggers !== $v) {
            $this->plugin_triggers = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_TRIGGERS;
        }

    } // setPluginTriggers()

    /**
     * Set the value of [plugin_pm_functions] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginPmFunctions($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_pm_functions !== $v) {
            $this->plugin_pm_functions = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_PM_FUNCTIONS;
        }

    } // setPluginPmFunctions()

    /**
     * Set the value of [plugin_redirect_login] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginRedirectLogin($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_redirect_login !== $v) {
            $this->plugin_redirect_login = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_REDIRECT_LOGIN;
        }

    } // setPluginRedirectLogin()

    /**
     * Set the value of [plugin_steps] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginSteps($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_steps !== $v) {
            $this->plugin_steps = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_STEPS;
        }

    } // setPluginSteps()

    /**
     * Set the value of [plugin_css] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginCss($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_css !== $v) {
            $this->plugin_css = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_CSS;
        }

    } // setPluginCss()

    /**
     * Set the value of [plugin_js] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginJs($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_js !== $v) {
            $this->plugin_js = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_JS;
        }

    } // setPluginJs()

    /**
     * Set the value of [plugin_rest_service] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginRestService($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_rest_service !== $v) {
            $this->plugin_rest_service = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_REST_SERVICE;
        }

    } // setPluginRestService()

    /**
     * Set the value of [plugin_attributes] column.
     * 
     * @param      string $v new value
     * @return     void
     */
    public function setPluginAttributes($v)
    {

        // Since the native PHP type for this column is string,
        // we will cast the input to a string (if it is not).
        if ($v !== null && !is_string($v)) {
            $v = (string) $v;
        }

        if ($this->plugin_attributes !== $v) {
            $this->plugin_attributes = $v;
            $this->modifiedColumns[] = PluginsRegistryPeer::PLUGIN_ATTRIBUTES;
        }

    } // setPluginAttributes()

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

            $this->id = $rs->getInt($startcol + 0);

            $this->pr_uid = $rs->getString($startcol + 1);

            $this->plugin_namespace = $rs->getString($startcol + 2);

            $this->plugin_description = $rs->getString($startcol + 3);

            $this->class_name = $rs->getString($startcol + 4);

            $this->friendly_name = $rs->getString($startcol + 5);

            $this->file_name = $rs->getString($startcol + 6);

            $this->plugin_folder = $rs->getString($startcol + 7);

            $this->plugin_version = $rs->getString($startcol + 8);

            $this->plugin_enable = $rs->getBoolean($startcol + 9);

            $this->plugin_private = $rs->getBoolean($startcol + 10);

            $this->plugin_menus = $rs->getString($startcol + 11);

            $this->plugin_folders = $rs->getString($startcol + 12);

            $this->plugin_triggers = $rs->getString($startcol + 13);

            $this->plugin_pm_functions = $rs->getString($startcol + 14);

            $this->plugin_redirect_login = $rs->getString($startcol + 15);

            $this->plugin_steps = $rs->getString($startcol + 16);

            $this->plugin_css = $rs->getString($startcol + 17);

            $this->plugin_js = $rs->getString($startcol + 18);

            $this->plugin_rest_service = $rs->getString($startcol + 19);

            $this->plugin_attributes = $rs->getString($startcol + 20);

            $this->resetModified();

            $this->setNew(false);

            // FIXME - using NUM_COLUMNS may be clearer.
            return $startcol + 21; // 21 = PluginsRegistryPeer::NUM_COLUMNS - PluginsRegistryPeer::NUM_LAZY_LOAD_COLUMNS).

        } catch (Exception $e) {
            throw new PropelException("Error populating PluginsRegistry object", $e);
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
            $con = Propel::getConnection(PluginsRegistryPeer::DATABASE_NAME);
        }

        try {
            $con->begin();
            PluginsRegistryPeer::doDelete($this, $con);
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
            $con = Propel::getConnection(PluginsRegistryPeer::DATABASE_NAME);
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
                    $pk = PluginsRegistryPeer::doInsert($this, $con);
                    $affectedRows += 1; // we are assuming that there is only 1 row per doInsert() which
                                         // should always be true here (even though technically
                                         // BasePeer::doInsert() can insert multiple rows).

                    $this->setNew(false);
                } else {
                    $affectedRows += PluginsRegistryPeer::doUpdate($this, $con);
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


            if (($retval = PluginsRegistryPeer::doValidate($this, $columns)) !== true) {
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
        $pos = PluginsRegistryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                return $this->getId();
                break;
            case 1:
                return $this->getPrUid();
                break;
            case 2:
                return $this->getPluginNamespace();
                break;
            case 3:
                return $this->getPluginDescription();
                break;
            case 4:
                return $this->getClassName();
                break;
            case 5:
                return $this->getFriendlyName();
                break;
            case 6:
                return $this->getFileName();
                break;
            case 7:
                return $this->getPluginFolder();
                break;
            case 8:
                return $this->getPluginVersion();
                break;
            case 9:
                return $this->getPluginEnable();
                break;
            case 10:
                return $this->getPluginPrivate();
                break;
            case 11:
                return $this->getPluginMenus();
                break;
            case 12:
                return $this->getPluginFolders();
                break;
            case 13:
                return $this->getPluginTriggers();
                break;
            case 14:
                return $this->getPluginPmFunctions();
                break;
            case 15:
                return $this->getPluginRedirectLogin();
                break;
            case 16:
                return $this->getPluginSteps();
                break;
            case 17:
                return $this->getPluginCss();
                break;
            case 18:
                return $this->getPluginJs();
                break;
            case 19:
                return $this->getPluginRestService();
                break;
            case 20:
                return $this->getPluginAttributes();
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
        $keys = PluginsRegistryPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getId(),
            $keys[1] => $this->getPrUid(),
            $keys[2] => $this->getPluginNamespace(),
            $keys[3] => $this->getPluginDescription(),
            $keys[4] => $this->getClassName(),
            $keys[5] => $this->getFriendlyName(),
            $keys[6] => $this->getFileName(),
            $keys[7] => $this->getPluginFolder(),
            $keys[8] => $this->getPluginVersion(),
            $keys[9] => $this->getPluginEnable(),
            $keys[10] => $this->getPluginPrivate(),
            $keys[11] => $this->getPluginMenus(),
            $keys[12] => $this->getPluginFolders(),
            $keys[13] => $this->getPluginTriggers(),
            $keys[14] => $this->getPluginPmFunctions(),
            $keys[15] => $this->getPluginRedirectLogin(),
            $keys[16] => $this->getPluginSteps(),
            $keys[17] => $this->getPluginCss(),
            $keys[18] => $this->getPluginJs(),
            $keys[19] => $this->getPluginRestService(),
            $keys[20] => $this->getPluginAttributes(),
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
        $pos = PluginsRegistryPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
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
                $this->setId($value);
                break;
            case 1:
                $this->setPrUid($value);
                break;
            case 2:
                $this->setPluginNamespace($value);
                break;
            case 3:
                $this->setPluginDescription($value);
                break;
            case 4:
                $this->setClassName($value);
                break;
            case 5:
                $this->setFriendlyName($value);
                break;
            case 6:
                $this->setFileName($value);
                break;
            case 7:
                $this->setPluginFolder($value);
                break;
            case 8:
                $this->setPluginVersion($value);
                break;
            case 9:
                $this->setPluginEnable($value);
                break;
            case 10:
                $this->setPluginPrivate($value);
                break;
            case 11:
                $this->setPluginMenus($value);
                break;
            case 12:
                $this->setPluginFolders($value);
                break;
            case 13:
                $this->setPluginTriggers($value);
                break;
            case 14:
                $this->setPluginPmFunctions($value);
                break;
            case 15:
                $this->setPluginRedirectLogin($value);
                break;
            case 16:
                $this->setPluginSteps($value);
                break;
            case 17:
                $this->setPluginCss($value);
                break;
            case 18:
                $this->setPluginJs($value);
                break;
            case 19:
                $this->setPluginRestService($value);
                break;
            case 20:
                $this->setPluginAttributes($value);
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
        $keys = PluginsRegistryPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }

        if (array_key_exists($keys[1], $arr)) {
            $this->setPrUid($arr[$keys[1]]);
        }

        if (array_key_exists($keys[2], $arr)) {
            $this->setPluginNamespace($arr[$keys[2]]);
        }

        if (array_key_exists($keys[3], $arr)) {
            $this->setPluginDescription($arr[$keys[3]]);
        }

        if (array_key_exists($keys[4], $arr)) {
            $this->setClassName($arr[$keys[4]]);
        }

        if (array_key_exists($keys[5], $arr)) {
            $this->setFriendlyName($arr[$keys[5]]);
        }

        if (array_key_exists($keys[6], $arr)) {
            $this->setFileName($arr[$keys[6]]);
        }

        if (array_key_exists($keys[7], $arr)) {
            $this->setPluginFolder($arr[$keys[7]]);
        }

        if (array_key_exists($keys[8], $arr)) {
            $this->setPluginVersion($arr[$keys[8]]);
        }

        if (array_key_exists($keys[9], $arr)) {
            $this->setPluginEnable($arr[$keys[9]]);
        }

        if (array_key_exists($keys[10], $arr)) {
            $this->setPluginPrivate($arr[$keys[10]]);
        }

        if (array_key_exists($keys[11], $arr)) {
            $this->setPluginMenus($arr[$keys[11]]);
        }

        if (array_key_exists($keys[12], $arr)) {
            $this->setPluginFolders($arr[$keys[12]]);
        }

        if (array_key_exists($keys[13], $arr)) {
            $this->setPluginTriggers($arr[$keys[13]]);
        }

        if (array_key_exists($keys[14], $arr)) {
            $this->setPluginPmFunctions($arr[$keys[14]]);
        }

        if (array_key_exists($keys[15], $arr)) {
            $this->setPluginRedirectLogin($arr[$keys[15]]);
        }

        if (array_key_exists($keys[16], $arr)) {
            $this->setPluginSteps($arr[$keys[16]]);
        }

        if (array_key_exists($keys[17], $arr)) {
            $this->setPluginCss($arr[$keys[17]]);
        }

        if (array_key_exists($keys[18], $arr)) {
            $this->setPluginJs($arr[$keys[18]]);
        }

        if (array_key_exists($keys[19], $arr)) {
            $this->setPluginRestService($arr[$keys[19]]);
        }

        if (array_key_exists($keys[20], $arr)) {
            $this->setPluginAttributes($arr[$keys[20]]);
        }

    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return     Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(PluginsRegistryPeer::DATABASE_NAME);

        if ($this->isColumnModified(PluginsRegistryPeer::ID)) {
            $criteria->add(PluginsRegistryPeer::ID, $this->id);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PR_UID)) {
            $criteria->add(PluginsRegistryPeer::PR_UID, $this->pr_uid);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_NAMESPACE)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_NAMESPACE, $this->plugin_namespace);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_DESCRIPTION)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_DESCRIPTION, $this->plugin_description);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::CLASS_NAME)) {
            $criteria->add(PluginsRegistryPeer::CLASS_NAME, $this->class_name);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::FRIENDLY_NAME)) {
            $criteria->add(PluginsRegistryPeer::FRIENDLY_NAME, $this->friendly_name);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::FILE_NAME)) {
            $criteria->add(PluginsRegistryPeer::FILE_NAME, $this->file_name);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_FOLDER)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_FOLDER, $this->plugin_folder);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_VERSION)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_VERSION, $this->plugin_version);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_ENABLE)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_ENABLE, $this->plugin_enable);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_PRIVATE)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_PRIVATE, $this->plugin_private);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_MENUS)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_MENUS, $this->plugin_menus);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_FOLDERS)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_FOLDERS, $this->plugin_folders);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_TRIGGERS)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_TRIGGERS, $this->plugin_triggers);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_PM_FUNCTIONS)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_PM_FUNCTIONS, $this->plugin_pm_functions);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_REDIRECT_LOGIN)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_REDIRECT_LOGIN, $this->plugin_redirect_login);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_STEPS)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_STEPS, $this->plugin_steps);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_CSS)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_CSS, $this->plugin_css);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_JS)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_JS, $this->plugin_js);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_REST_SERVICE)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_REST_SERVICE, $this->plugin_rest_service);
        }

        if ($this->isColumnModified(PluginsRegistryPeer::PLUGIN_ATTRIBUTES)) {
            $criteria->add(PluginsRegistryPeer::PLUGIN_ATTRIBUTES, $this->plugin_attributes);
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
        $criteria = new Criteria(PluginsRegistryPeer::DATABASE_NAME);

        $criteria->add(PluginsRegistryPeer::PR_UID, $this->pr_uid);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return     string
     */
    public function getPrimaryKey()
    {
        return $this->getPrUid();
    }

    /**
     * Generic method to set the primary key (pr_uid column).
     *
     * @param      string $key Primary key.
     * @return     void
     */
    public function setPrimaryKey($key)
    {
        $this->setPrUid($key);
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param      object $copyObj An object of PluginsRegistry (or compatible) type.
     * @param      boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @throws     PropelException
     */
    public function copyInto($copyObj, $deepCopy = false)
    {

        $copyObj->setId($this->id);

        $copyObj->setPluginNamespace($this->plugin_namespace);

        $copyObj->setPluginDescription($this->plugin_description);

        $copyObj->setClassName($this->class_name);

        $copyObj->setFriendlyName($this->friendly_name);

        $copyObj->setFileName($this->file_name);

        $copyObj->setPluginFolder($this->plugin_folder);

        $copyObj->setPluginVersion($this->plugin_version);

        $copyObj->setPluginEnable($this->plugin_enable);

        $copyObj->setPluginPrivate($this->plugin_private);

        $copyObj->setPluginMenus($this->plugin_menus);

        $copyObj->setPluginFolders($this->plugin_folders);

        $copyObj->setPluginTriggers($this->plugin_triggers);

        $copyObj->setPluginPmFunctions($this->plugin_pm_functions);

        $copyObj->setPluginRedirectLogin($this->plugin_redirect_login);

        $copyObj->setPluginSteps($this->plugin_steps);

        $copyObj->setPluginCss($this->plugin_css);

        $copyObj->setPluginJs($this->plugin_js);

        $copyObj->setPluginRestService($this->plugin_rest_service);

        $copyObj->setPluginAttributes($this->plugin_attributes);


        $copyObj->setNew(true);

        $copyObj->setPrUid(''); // this is a pkey column, so set to default value

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
     * @return     PluginsRegistry Clone of current object.
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
     * @return     PluginsRegistryPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new PluginsRegistryPeer();
        }
        return self::$peer;
    }
}

