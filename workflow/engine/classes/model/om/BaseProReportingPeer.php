<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by ProReportingPeer::getOMClass()
include_once 'classes/model/ProReporting.php';

/**
 * Base static class for performing query and update operations on the 'PRO_REPORTING' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseProReportingPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'PRO_REPORTING';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.ProReporting';

    /** The total number of columns. */
    const NUM_COLUMNS = 14;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the PRO_UID field */
    const PRO_UID = 'PRO_REPORTING.PRO_UID';

    /** the column name for the MONTH field */
    const MONTH = 'PRO_REPORTING.MONTH';

    /** the column name for the YEAR field */
    const YEAR = 'PRO_REPORTING.YEAR';

    /** the column name for the AVG_TIME field */
    const AVG_TIME = 'PRO_REPORTING.AVG_TIME';

    /** the column name for the SDV_TIME field */
    const SDV_TIME = 'PRO_REPORTING.SDV_TIME';

    /** the column name for the TOTAL_CASES_IN field */
    const TOTAL_CASES_IN = 'PRO_REPORTING.TOTAL_CASES_IN';

    /** the column name for the TOTAL_CASES_OUT field */
    const TOTAL_CASES_OUT = 'PRO_REPORTING.TOTAL_CASES_OUT';

    /** the column name for the CONFIGURED_PROCESS_TIME field */
    const CONFIGURED_PROCESS_TIME = 'PRO_REPORTING.CONFIGURED_PROCESS_TIME';

    /** the column name for the CONFIGURED_PROCESS_COST field */
    const CONFIGURED_PROCESS_COST = 'PRO_REPORTING.CONFIGURED_PROCESS_COST';

    /** the column name for the TOTAL_CASES_OPEN field */
    const TOTAL_CASES_OPEN = 'PRO_REPORTING.TOTAL_CASES_OPEN';

    /** the column name for the TOTAL_CASES_OVERDUE field */
    const TOTAL_CASES_OVERDUE = 'PRO_REPORTING.TOTAL_CASES_OVERDUE';

    /** the column name for the TOTAL_CASES_ON_TIME field */
    const TOTAL_CASES_ON_TIME = 'PRO_REPORTING.TOTAL_CASES_ON_TIME';

    /** the column name for the PRO_COST field */
    const PRO_COST = 'PRO_REPORTING.PRO_COST';

    /** the column name for the PRO_UNIT_COST field */
    const PRO_UNIT_COST = 'PRO_REPORTING.PRO_UNIT_COST';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('ProUid', 'Month', 'Year', 'AvgTime', 'SdvTime', 'TotalCasesIn', 'TotalCasesOut', 'ConfiguredProcessTime', 'ConfiguredProcessCost', 'TotalCasesOpen', 'TotalCasesOverdue', 'TotalCasesOnTime', 'ProCost', 'ProUnitCost', ),
        BasePeer::TYPE_COLNAME => array (ProReportingPeer::PRO_UID, ProReportingPeer::MONTH, ProReportingPeer::YEAR, ProReportingPeer::AVG_TIME, ProReportingPeer::SDV_TIME, ProReportingPeer::TOTAL_CASES_IN, ProReportingPeer::TOTAL_CASES_OUT, ProReportingPeer::CONFIGURED_PROCESS_TIME, ProReportingPeer::CONFIGURED_PROCESS_COST, ProReportingPeer::TOTAL_CASES_OPEN, ProReportingPeer::TOTAL_CASES_OVERDUE, ProReportingPeer::TOTAL_CASES_ON_TIME, ProReportingPeer::PRO_COST, ProReportingPeer::PRO_UNIT_COST, ),
        BasePeer::TYPE_FIELDNAME => array ('PRO_UID', 'MONTH', 'YEAR', 'AVG_TIME', 'SDV_TIME', 'TOTAL_CASES_IN', 'TOTAL_CASES_OUT', 'CONFIGURED_PROCESS_TIME', 'CONFIGURED_PROCESS_COST', 'TOTAL_CASES_OPEN', 'TOTAL_CASES_OVERDUE', 'TOTAL_CASES_ON_TIME', 'PRO_COST', 'PRO_UNIT_COST', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('ProUid' => 0, 'Month' => 1, 'Year' => 2, 'AvgTime' => 3, 'SdvTime' => 4, 'TotalCasesIn' => 5, 'TotalCasesOut' => 6, 'ConfiguredProcessTime' => 7, 'ConfiguredProcessCost' => 8, 'TotalCasesOpen' => 9, 'TotalCasesOverdue' => 10, 'TotalCasesOnTime' => 11, 'ProCost' => 12, 'ProUnitCost' => 13, ),
        BasePeer::TYPE_COLNAME => array (ProReportingPeer::PRO_UID => 0, ProReportingPeer::MONTH => 1, ProReportingPeer::YEAR => 2, ProReportingPeer::AVG_TIME => 3, ProReportingPeer::SDV_TIME => 4, ProReportingPeer::TOTAL_CASES_IN => 5, ProReportingPeer::TOTAL_CASES_OUT => 6, ProReportingPeer::CONFIGURED_PROCESS_TIME => 7, ProReportingPeer::CONFIGURED_PROCESS_COST => 8, ProReportingPeer::TOTAL_CASES_OPEN => 9, ProReportingPeer::TOTAL_CASES_OVERDUE => 10, ProReportingPeer::TOTAL_CASES_ON_TIME => 11, ProReportingPeer::PRO_COST => 12, ProReportingPeer::PRO_UNIT_COST => 13, ),
        BasePeer::TYPE_FIELDNAME => array ('PRO_UID' => 0, 'MONTH' => 1, 'YEAR' => 2, 'AVG_TIME' => 3, 'SDV_TIME' => 4, 'TOTAL_CASES_IN' => 5, 'TOTAL_CASES_OUT' => 6, 'CONFIGURED_PROCESS_TIME' => 7, 'CONFIGURED_PROCESS_COST' => 8, 'TOTAL_CASES_OPEN' => 9, 'TOTAL_CASES_OVERDUE' => 10, 'TOTAL_CASES_ON_TIME' => 11, 'PRO_COST' => 12, 'PRO_UNIT_COST' => 13, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/ProReportingMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.ProReportingMapBuilder');
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
            $map = ProReportingPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. ProReportingPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(ProReportingPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(ProReportingPeer::PRO_UID);

        $criteria->addSelectColumn(ProReportingPeer::MONTH);

        $criteria->addSelectColumn(ProReportingPeer::YEAR);

        $criteria->addSelectColumn(ProReportingPeer::AVG_TIME);

        $criteria->addSelectColumn(ProReportingPeer::SDV_TIME);

        $criteria->addSelectColumn(ProReportingPeer::TOTAL_CASES_IN);

        $criteria->addSelectColumn(ProReportingPeer::TOTAL_CASES_OUT);

        $criteria->addSelectColumn(ProReportingPeer::CONFIGURED_PROCESS_TIME);

        $criteria->addSelectColumn(ProReportingPeer::CONFIGURED_PROCESS_COST);

        $criteria->addSelectColumn(ProReportingPeer::TOTAL_CASES_OPEN);

        $criteria->addSelectColumn(ProReportingPeer::TOTAL_CASES_OVERDUE);

        $criteria->addSelectColumn(ProReportingPeer::TOTAL_CASES_ON_TIME);

        $criteria->addSelectColumn(ProReportingPeer::PRO_COST);

        $criteria->addSelectColumn(ProReportingPeer::PRO_UNIT_COST);

    }

    const COUNT = 'COUNT(PRO_REPORTING.PRO_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT PRO_REPORTING.PRO_UID)';

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
            $criteria->addSelectColumn(ProReportingPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(ProReportingPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = ProReportingPeer::doSelectRS($criteria, $con);
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
     * @return     ProReporting
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = ProReportingPeer::doSelect($critcopy, $con);
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
        return ProReportingPeer::populateObjects(ProReportingPeer::doSelectRS($criteria, $con));
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
            ProReportingPeer::addSelectColumns($criteria);
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
        $cls = ProReportingPeer::getOMClass();
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
        return ProReportingPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a ProReporting or Criteria object.
     *
     * @param      mixed $values Criteria or ProReporting object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from ProReporting object
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
     * Method perform an UPDATE on the database, given a ProReporting or Criteria object.
     *
     * @param      mixed $values Criteria or ProReporting object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(ProReportingPeer::PRO_UID);
            $selectCriteria->add(ProReportingPeer::PRO_UID, $criteria->remove(ProReportingPeer::PRO_UID), $comparison);

            $comparison = $criteria->getComparison(ProReportingPeer::MONTH);
            $selectCriteria->add(ProReportingPeer::MONTH, $criteria->remove(ProReportingPeer::MONTH), $comparison);

            $comparison = $criteria->getComparison(ProReportingPeer::YEAR);
            $selectCriteria->add(ProReportingPeer::YEAR, $criteria->remove(ProReportingPeer::YEAR), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the PRO_REPORTING table.
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
            $affectedRows += BasePeer::doDeleteAll(ProReportingPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a ProReporting or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or ProReporting object or primary key or array of primary keys
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
            $con = Propel::getConnection(ProReportingPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof ProReporting) {

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
            }

            $criteria->add(ProReportingPeer::PRO_UID, $vals[0], Criteria::IN);
            $criteria->add(ProReportingPeer::MONTH, $vals[1], Criteria::IN);
            $criteria->add(ProReportingPeer::YEAR, $vals[2], Criteria::IN);
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
     * Validates all modified columns of given ProReporting object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      ProReporting $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(ProReporting $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(ProReportingPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(ProReportingPeer::TABLE_NAME);

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

        return BasePeer::doValidate(ProReportingPeer::DATABASE_NAME, ProReportingPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve object using using composite pkey values.
     * @param string $pro_uid
       * @param int $month
       * @param int $year
        * @param      Connection $con
     * @return     ProReporting
     */
    public static function retrieveByPK($pro_uid, $month, $year, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }
        $criteria = new Criteria();
        $criteria->add(ProReportingPeer::PRO_UID, $pro_uid);
        $criteria->add(ProReportingPeer::MONTH, $month);
        $criteria->add(ProReportingPeer::YEAR, $year);
        $v = ProReportingPeer::doSelect($criteria, $con);

        return !empty($v) ? $v[0] : null;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseProReportingPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/ProReportingMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.ProReportingMapBuilder');
}

