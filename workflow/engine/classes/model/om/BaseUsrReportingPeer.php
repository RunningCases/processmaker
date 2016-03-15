<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by UsrReportingPeer::getOMClass()
include_once 'classes/model/UsrReporting.php';

/**
 * Base static class for performing query and update operations on the 'USR_REPORTING' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseUsrReportingPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'USR_REPORTING';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.UsrReporting';

    /** The total number of columns. */
    const NUM_COLUMNS = 17;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the USR_UID field */
    const USR_UID = 'USR_REPORTING.USR_UID';

    /** the column name for the TAS_UID field */
    const TAS_UID = 'USR_REPORTING.TAS_UID';

    /** the column name for the PRO_UID field */
    const PRO_UID = 'USR_REPORTING.PRO_UID';

    /** the column name for the MONTH field */
    const MONTH = 'USR_REPORTING.MONTH';

    /** the column name for the YEAR field */
    const YEAR = 'USR_REPORTING.YEAR';

    /** the column name for the TOTAL_QUEUE_TIME_BY_TASK field */
    const TOTAL_QUEUE_TIME_BY_TASK = 'USR_REPORTING.TOTAL_QUEUE_TIME_BY_TASK';

    /** the column name for the TOTAL_TIME_BY_TASK field */
    const TOTAL_TIME_BY_TASK = 'USR_REPORTING.TOTAL_TIME_BY_TASK';

    /** the column name for the TOTAL_CASES_IN field */
    const TOTAL_CASES_IN = 'USR_REPORTING.TOTAL_CASES_IN';

    /** the column name for the TOTAL_CASES_OUT field */
    const TOTAL_CASES_OUT = 'USR_REPORTING.TOTAL_CASES_OUT';

    /** the column name for the USER_HOUR_COST field */
    const USER_HOUR_COST = 'USR_REPORTING.USER_HOUR_COST';

    /** the column name for the AVG_TIME field */
    const AVG_TIME = 'USR_REPORTING.AVG_TIME';

    /** the column name for the SDV_TIME field */
    const SDV_TIME = 'USR_REPORTING.SDV_TIME';

    /** the column name for the CONFIGURED_TASK_TIME field */
    const CONFIGURED_TASK_TIME = 'USR_REPORTING.CONFIGURED_TASK_TIME';

    /** the column name for the TOTAL_CASES_OVERDUE field */
    const TOTAL_CASES_OVERDUE = 'USR_REPORTING.TOTAL_CASES_OVERDUE';

    /** the column name for the TOTAL_CASES_ON_TIME field */
    const TOTAL_CASES_ON_TIME = 'USR_REPORTING.TOTAL_CASES_ON_TIME';

    /** the column name for the PRO_COST field */
    const PRO_COST = 'USR_REPORTING.PRO_COST';

    /** the column name for the PRO_UNIT_COST field */
    const PRO_UNIT_COST = 'USR_REPORTING.PRO_UNIT_COST';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('UsrUid', 'TasUid', 'ProUid', 'Month', 'Year', 'TotalQueueTimeByTask', 'TotalTimeByTask', 'TotalCasesIn', 'TotalCasesOut', 'UserHourCost', 'AvgTime', 'SdvTime', 'ConfiguredTaskTime', 'TotalCasesOverdue', 'TotalCasesOnTime', 'ProCost', 'ProUnitCost', ),
        BasePeer::TYPE_COLNAME => array (UsrReportingPeer::USR_UID, UsrReportingPeer::TAS_UID, UsrReportingPeer::PRO_UID, UsrReportingPeer::MONTH, UsrReportingPeer::YEAR, UsrReportingPeer::TOTAL_QUEUE_TIME_BY_TASK, UsrReportingPeer::TOTAL_TIME_BY_TASK, UsrReportingPeer::TOTAL_CASES_IN, UsrReportingPeer::TOTAL_CASES_OUT, UsrReportingPeer::USER_HOUR_COST, UsrReportingPeer::AVG_TIME, UsrReportingPeer::SDV_TIME, UsrReportingPeer::CONFIGURED_TASK_TIME, UsrReportingPeer::TOTAL_CASES_OVERDUE, UsrReportingPeer::TOTAL_CASES_ON_TIME, UsrReportingPeer::PRO_COST, UsrReportingPeer::PRO_UNIT_COST, ),
        BasePeer::TYPE_FIELDNAME => array ('USR_UID', 'TAS_UID', 'PRO_UID', 'MONTH', 'YEAR', 'TOTAL_QUEUE_TIME_BY_TASK', 'TOTAL_TIME_BY_TASK', 'TOTAL_CASES_IN', 'TOTAL_CASES_OUT', 'USER_HOUR_COST', 'AVG_TIME', 'SDV_TIME', 'CONFIGURED_TASK_TIME', 'TOTAL_CASES_OVERDUE', 'TOTAL_CASES_ON_TIME', 'PRO_COST', 'PRO_UNIT_COST', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('UsrUid' => 0, 'TasUid' => 1, 'ProUid' => 2, 'Month' => 3, 'Year' => 4, 'TotalQueueTimeByTask' => 5, 'TotalTimeByTask' => 6, 'TotalCasesIn' => 7, 'TotalCasesOut' => 8, 'UserHourCost' => 9, 'AvgTime' => 10, 'SdvTime' => 11, 'ConfiguredTaskTime' => 12, 'TotalCasesOverdue' => 13, 'TotalCasesOnTime' => 14, 'ProCost' => 15, 'ProUnitCost' => 16, ),
        BasePeer::TYPE_COLNAME => array (UsrReportingPeer::USR_UID => 0, UsrReportingPeer::TAS_UID => 1, UsrReportingPeer::PRO_UID => 2, UsrReportingPeer::MONTH => 3, UsrReportingPeer::YEAR => 4, UsrReportingPeer::TOTAL_QUEUE_TIME_BY_TASK => 5, UsrReportingPeer::TOTAL_TIME_BY_TASK => 6, UsrReportingPeer::TOTAL_CASES_IN => 7, UsrReportingPeer::TOTAL_CASES_OUT => 8, UsrReportingPeer::USER_HOUR_COST => 9, UsrReportingPeer::AVG_TIME => 10, UsrReportingPeer::SDV_TIME => 11, UsrReportingPeer::CONFIGURED_TASK_TIME => 12, UsrReportingPeer::TOTAL_CASES_OVERDUE => 13, UsrReportingPeer::TOTAL_CASES_ON_TIME => 14, UsrReportingPeer::PRO_COST => 15, UsrReportingPeer::PRO_UNIT_COST => 16, ),
        BasePeer::TYPE_FIELDNAME => array ('USR_UID' => 0, 'TAS_UID' => 1, 'PRO_UID' => 2, 'MONTH' => 3, 'YEAR' => 4, 'TOTAL_QUEUE_TIME_BY_TASK' => 5, 'TOTAL_TIME_BY_TASK' => 6, 'TOTAL_CASES_IN' => 7, 'TOTAL_CASES_OUT' => 8, 'USER_HOUR_COST' => 9, 'AVG_TIME' => 10, 'SDV_TIME' => 11, 'CONFIGURED_TASK_TIME' => 12, 'TOTAL_CASES_OVERDUE' => 13, 'TOTAL_CASES_ON_TIME' => 14, 'PRO_COST' => 15, 'PRO_UNIT_COST' => 16, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/UsrReportingMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.UsrReportingMapBuilder');
    }
    /**
     * Gets a map (hash) of PHP names to DB column names.
     *
     * @return     array The PHP to DB name map for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     * @deprecated Use the getFieldNames() and translateFieldName() methods instead of this.
     */
    public static function getPhpNameMap()
    {
        if (self::$phpNameMap === null) {
            $map = UsrReportingPeer::getTableMap();
            $columns = $map->getColumns();
            $nameMap = array();
            foreach ($columns as $column) {
                $nameMap[$column->getPhpName()] = $column->getColumnName();
            }
            self::$phpNameMap = $nameMap;
        }
        return self::$phpNameMap;
    }
    /**
     * Translates a fieldname to another type
     *
     * @param      string $name field name
     * @param      string $fromType One of the class type constants TYPE_PHPNAME,
     *                         TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @param      string $toType   One of the class type constants
     * @return     string translated name of the field.
     */
    static public function translateFieldName($name, $fromType, $toType)
    {
        $toNames = self::getFieldNames($toType);
        $key = isset(self::$fieldKeys[$fromType][$name]) ? self::$fieldKeys[$fromType][$name] : null;
        if ($key === null) {
            throw new PropelException("'$name' could not be found in the field names of type '$fromType'. These are: " . print_r(self::$fieldKeys[$fromType], true));
        }
        return $toNames[$key];
    }

    /**
     * Returns an array of of field names.
     *
     * @param      string $type The type of fieldnames to return:
     *                      One of the class type constants TYPE_PHPNAME,
     *                      TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM
     * @return     array A list of field names
     */

    static public function getFieldNames($type = BasePeer::TYPE_PHPNAME)
    {
        if (!array_key_exists($type, self::$fieldNames)) {
            throw new PropelException('Method getFieldNames() expects the parameter $type to be one of the class constants TYPE_PHPNAME, TYPE_COLNAME, TYPE_FIELDNAME, TYPE_NUM. ' . $type . ' was given.');
        }
        return self::$fieldNames[$type];
    }

    /**
     * Convenience method which changes table.column to alias.column.
     *
     * Using this method you can maintain SQL abstraction while using column aliases.
     * <code>
     *      $c->addAlias("alias1", TablePeer::TABLE_NAME);
     *      $c->addJoin(TablePeer::alias("alias1", TablePeer::PRIMARY_KEY_COLUMN), TablePeer::PRIMARY_KEY_COLUMN);
     * </code>
     * @param      string $alias The alias for the current table.
     * @param      string $column The column name for current table. (i.e. UsrReportingPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(UsrReportingPeer::TABLE_NAME.'.', $alias.'.', $column);
    }

    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param      criteria object containing the columns to add.
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria)
    {

        $criteria->addSelectColumn(UsrReportingPeer::USR_UID);

        $criteria->addSelectColumn(UsrReportingPeer::TAS_UID);

        $criteria->addSelectColumn(UsrReportingPeer::PRO_UID);

        $criteria->addSelectColumn(UsrReportingPeer::MONTH);

        $criteria->addSelectColumn(UsrReportingPeer::YEAR);

        $criteria->addSelectColumn(UsrReportingPeer::TOTAL_QUEUE_TIME_BY_TASK);

        $criteria->addSelectColumn(UsrReportingPeer::TOTAL_TIME_BY_TASK);

        $criteria->addSelectColumn(UsrReportingPeer::TOTAL_CASES_IN);

        $criteria->addSelectColumn(UsrReportingPeer::TOTAL_CASES_OUT);

        $criteria->addSelectColumn(UsrReportingPeer::USER_HOUR_COST);

        $criteria->addSelectColumn(UsrReportingPeer::AVG_TIME);

        $criteria->addSelectColumn(UsrReportingPeer::SDV_TIME);

        $criteria->addSelectColumn(UsrReportingPeer::CONFIGURED_TASK_TIME);

        $criteria->addSelectColumn(UsrReportingPeer::TOTAL_CASES_OVERDUE);

        $criteria->addSelectColumn(UsrReportingPeer::TOTAL_CASES_ON_TIME);

        $criteria->addSelectColumn(UsrReportingPeer::PRO_COST);

        $criteria->addSelectColumn(UsrReportingPeer::PRO_UNIT_COST);

    }

    const COUNT = 'COUNT(USR_REPORTING.USR_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT USR_REPORTING.USR_UID)';

    /**
     * Returns the number of rows matching criteria.
     *
     * @param      Criteria $criteria
     * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
     * @param      Connection $con
     * @return     int Number of matching rows.
     */
    public static function doCount(Criteria $criteria, $distinct = false, $con = null)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // clear out anything that might confuse the ORDER BY clause
        $criteria->clearSelectColumns()->clearOrderByColumns();
        if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->addSelectColumn(UsrReportingPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(UsrReportingPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = UsrReportingPeer::doSelectRS($criteria, $con);
        if ($rs->next()) {
            return $rs->getInt(1);
        } else {
            // no rows returned; we infer that means 0 matches.
            return 0;
        }
    }
    /**
     * Method to select one object from the DB.
     *
     * @param      Criteria $criteria object used to create the SELECT statement.
     * @param      Connection $con
     * @return     UsrReporting
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = UsrReportingPeer::doSelect($critcopy, $con);
        if ($objects) {
            return $objects[0];
        }
        return null;
    }
    /**
     * Method to do selects.
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      Connection $con
     * @return     array Array of selected Objects
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelect(Criteria $criteria, $con = null)
    {
        return UsrReportingPeer::populateObjects(UsrReportingPeer::doSelectRS($criteria, $con));
    }
    /**
     * Prepares the Criteria object and uses the parent doSelect()
     * method to get a ResultSet.
     *
     * Use this method directly if you want to just get the resultset
     * (instead of an array of objects).
     *
     * @param      Criteria $criteria The Criteria object used to build the SELECT statement.
     * @param      Connection $con the connection to use
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     * @return     ResultSet The resultset object with numerically-indexed fields.
     * @see        BasePeer::doSelect()
     */
    public static function doSelectRS(Criteria $criteria, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        if (!$criteria->getSelectColumns()) {
            $criteria = clone $criteria;
            UsrReportingPeer::addSelectColumns($criteria);
        }

        // Set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        // BasePeer returns a Creole ResultSet, set to return
        // rows indexed numerically.
        return BasePeer::doSelect($criteria, $con);
    }
    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function populateObjects(ResultSet $rs)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = UsrReportingPeer::getOMClass();
        $cls = Propel::import($cls);
        // populate the object(s)
        while ($rs->next()) {

            $obj = new $cls();
            $obj->hydrate($rs);
            $results[] = $obj;

        }
        return $results;
    }
    /**
     * Returns the TableMap related to this peer.
     * This method is not needed for general use but a specific application could have a need.
     * @return     TableMap
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getDatabaseMap(self::DATABASE_NAME)->getTable(self::TABLE_NAME);
    }

    /**
     * The class that the Peer will make instances of.
     *
     * This uses a dot-path notation which is tranalted into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @return     string path.to.ClassName
     */
    public static function getOMClass()
    {
        return UsrReportingPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a UsrReporting or Criteria object.
     *
     * @param      mixed $values Criteria or UsrReporting object containing data that is used to create the INSERT statement.
     * @param      Connection $con the connection to use
     * @return     mixed The new primary key.
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doInsert($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } else {
            $criteria = $values->buildCriteria(); // build Criteria from UsrReporting object
        }


        // Set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        try {
            // use transaction because $criteria could contain info
            // for more than one table (I guess, conceivably)
            $con->begin();
            $pk = BasePeer::doInsert($criteria, $con);
            $con->commit();
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }

        return $pk;
    }

    /**
     * Method perform an UPDATE on the database, given a UsrReporting or Criteria object.
     *
     * @param      mixed $values Criteria or UsrReporting object containing data create the UPDATE statement.
     * @param      Connection $con The connection to use (specify Connection exert more control over transactions).
     * @return     int The number of affected rows (if supported by underlying database driver).
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doUpdate($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $selectCriteria = new Criteria(self::DATABASE_NAME);

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity

            $comparison = $criteria->getComparison(UsrReportingPeer::USR_UID);
            $selectCriteria->add(UsrReportingPeer::USR_UID, $criteria->remove(UsrReportingPeer::USR_UID), $comparison);

            $comparison = $criteria->getComparison(UsrReportingPeer::TAS_UID);
            $selectCriteria->add(UsrReportingPeer::TAS_UID, $criteria->remove(UsrReportingPeer::TAS_UID), $comparison);

            $comparison = $criteria->getComparison(UsrReportingPeer::MONTH);
            $selectCriteria->add(UsrReportingPeer::MONTH, $criteria->remove(UsrReportingPeer::MONTH), $comparison);

            $comparison = $criteria->getComparison(UsrReportingPeer::YEAR);
            $selectCriteria->add(UsrReportingPeer::YEAR, $criteria->remove(UsrReportingPeer::YEAR), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the USR_REPORTING table.
     *
     * @return     int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll($con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }
        $affectedRows = 0; // initialize var to track total num of affected rows
        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->begin();
            $affectedRows += BasePeer::doDeleteAll(UsrReportingPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a UsrReporting or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or UsrReporting object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param      Connection $con the connection to use
     * @return     int  The number of affected rows (if supported by underlying database driver).
     *             This includes CASCADE-related rows
     *              if supported by native driver or if emulated using Propel.
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
    */
    public static function doDelete($values, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(UsrReportingPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof UsrReporting) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            // primary key is composite; we therefore, expect
            // the primary key passed to be an array of pkey
            // values
            if (count($values) == count($values, COUNT_RECURSIVE)) {
                // array is not multi-dimensional
                $values = array($values);
            }
            $vals = array();
            foreach ($values as $value) {

                $vals[0][] = $value[0];
                $vals[1][] = $value[1];
                $vals[2][] = $value[2];
                $vals[3][] = $value[3];
            }

            $criteria->add(UsrReportingPeer::USR_UID, $vals[0], Criteria::IN);
            $criteria->add(UsrReportingPeer::TAS_UID, $vals[1], Criteria::IN);
            $criteria->add(UsrReportingPeer::MONTH, $vals[2], Criteria::IN);
            $criteria->add(UsrReportingPeer::YEAR, $vals[3], Criteria::IN);
        }

        // Set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        $affectedRows = 0; // initialize var to track total num of affected rows

        try {
            // use transaction because $criteria could contain info
            // for more than one table or we could emulating ON DELETE CASCADE, etc.
            $con->begin();

            $affectedRows += BasePeer::doDelete($criteria, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Validates all modified columns of given UsrReporting object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      UsrReporting $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(UsrReporting $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(UsrReportingPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(UsrReportingPeer::TABLE_NAME);

            if (! is_array($cols)) {
                $cols = array($cols);
            }

            foreach ($cols as $colName) {
                if ($tableMap->containsColumn($colName)) {
                    $get = 'get' . $tableMap->getColumn($colName)->getPhpName();
                    $columns[$colName] = $obj->$get();
                }
            }
        } else {

        }

        return BasePeer::doValidate(UsrReportingPeer::DATABASE_NAME, UsrReportingPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param string $usr_uid
       * @param string $tas_uid
       * @param int $month
       * @param int $year
        * @param      Connection $con
     * @return     UsrReporting
     */
    public static function retrieveByPK($usr_uid, $tas_uid, $month, $year, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }
        $criteria = new Criteria();
        $criteria->add(UsrReportingPeer::USR_UID, $usr_uid);
        $criteria->add(UsrReportingPeer::TAS_UID, $tas_uid);
        $criteria->add(UsrReportingPeer::MONTH, $month);
        $criteria->add(UsrReportingPeer::YEAR, $year);
        $v = UsrReportingPeer::doSelect($criteria, $con);

        return !empty($v) ? $v[0] : null;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseUsrReportingPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/UsrReportingMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.UsrReportingMapBuilder');
}

