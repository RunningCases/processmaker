<?php

require_once 'propel/util/BasePeer.php';
// The object class -- needed for instanceof checks in this class.
// actual class may be a subclass -- as returned by DashboardIndicatorPeer::getOMClass()
include_once 'classes/model/DashboardIndicator.php';

/**
 * Base static class for performing query and update operations on the 'DASHBOARD_INDICATOR' table.
 *
 * 
 *
 * @package    workflow.classes.model.om
 */
abstract class BaseDashboardIndicatorPeer
{

    /** the default database name for this class */
    const DATABASE_NAME = 'workflow';

    /** the table name for this class */
    const TABLE_NAME = 'DASHBOARD_INDICATOR';

    /** A class that can be returned by this peer. */
    const CLASS_DEFAULT = 'classes.model.DashboardIndicator';

    /** The total number of columns. */
    const NUM_COLUMNS = 14;

    /** The number of lazy-loaded columns. */
    const NUM_LAZY_LOAD_COLUMNS = 0;


    /** the column name for the DAS_IND_UID field */
    const DAS_IND_UID = 'DASHBOARD_INDICATOR.DAS_IND_UID';

    /** the column name for the DAS_UID field */
    const DAS_UID = 'DASHBOARD_INDICATOR.DAS_UID';

    /** the column name for the DAS_IND_TYPE field */
    const DAS_IND_TYPE = 'DASHBOARD_INDICATOR.DAS_IND_TYPE';

    /** the column name for the DAS_IND_TITLE field */
    const DAS_IND_TITLE = 'DASHBOARD_INDICATOR.DAS_IND_TITLE';

    /** the column name for the DAS_IND_GOAL field */
    const DAS_IND_GOAL = 'DASHBOARD_INDICATOR.DAS_IND_GOAL';

    /** the column name for the DAS_IND_DIRECTION field */
    const DAS_IND_DIRECTION = 'DASHBOARD_INDICATOR.DAS_IND_DIRECTION';

    /** the column name for the DAS_UID_PROCESS field */
    const DAS_UID_PROCESS = 'DASHBOARD_INDICATOR.DAS_UID_PROCESS';

    /** the column name for the DAS_IND_FIRST_FIGURE field */
    const DAS_IND_FIRST_FIGURE = 'DASHBOARD_INDICATOR.DAS_IND_FIRST_FIGURE';

    /** the column name for the DAS_IND_FIRST_FREQUENCY field */
    const DAS_IND_FIRST_FREQUENCY = 'DASHBOARD_INDICATOR.DAS_IND_FIRST_FREQUENCY';

    /** the column name for the DAS_IND_SECOND_FIGURE field */
    const DAS_IND_SECOND_FIGURE = 'DASHBOARD_INDICATOR.DAS_IND_SECOND_FIGURE';

    /** the column name for the DAS_IND_SECOND_FREQUENCY field */
    const DAS_IND_SECOND_FREQUENCY = 'DASHBOARD_INDICATOR.DAS_IND_SECOND_FREQUENCY';

    /** the column name for the DAS_IND_CREATE_DATE field */
    const DAS_IND_CREATE_DATE = 'DASHBOARD_INDICATOR.DAS_IND_CREATE_DATE';

    /** the column name for the DAS_IND_UPDATE_DATE field */
    const DAS_IND_UPDATE_DATE = 'DASHBOARD_INDICATOR.DAS_IND_UPDATE_DATE';

    /** the column name for the DAS_IND_STATUS field */
    const DAS_IND_STATUS = 'DASHBOARD_INDICATOR.DAS_IND_STATUS';

    /** The PHP to DB Name Mapping */
    private static $phpNameMap = null;


    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    private static $fieldNames = array (
        BasePeer::TYPE_PHPNAME => array ('DasIndUid', 'DasUid', 'DasIndType', 'DasIndTitle', 'DasIndGoal', 'DasIndDirection', 'DasUidProcess', 'DasIndFirstFigure', 'DasIndFirstFrequency', 'DasIndSecondFigure', 'DasIndSecondFrequency', 'DasIndCreateDate', 'DasIndUpdateDate', 'DasIndStatus', ),
        BasePeer::TYPE_COLNAME => array (DashboardIndicatorPeer::DAS_IND_UID, DashboardIndicatorPeer::DAS_UID, DashboardIndicatorPeer::DAS_IND_TYPE, DashboardIndicatorPeer::DAS_IND_TITLE, DashboardIndicatorPeer::DAS_IND_GOAL, DashboardIndicatorPeer::DAS_IND_DIRECTION, DashboardIndicatorPeer::DAS_UID_PROCESS, DashboardIndicatorPeer::DAS_IND_FIRST_FIGURE, DashboardIndicatorPeer::DAS_IND_FIRST_FREQUENCY, DashboardIndicatorPeer::DAS_IND_SECOND_FIGURE, DashboardIndicatorPeer::DAS_IND_SECOND_FREQUENCY, DashboardIndicatorPeer::DAS_IND_CREATE_DATE, DashboardIndicatorPeer::DAS_IND_UPDATE_DATE, DashboardIndicatorPeer::DAS_IND_STATUS, ),
        BasePeer::TYPE_FIELDNAME => array ('DAS_IND_UID', 'DAS_UID', 'DAS_IND_TYPE', 'DAS_IND_TITLE', 'DAS_IND_GOAL', 'DAS_IND_DIRECTION', 'DAS_UID_PROCESS', 'DAS_IND_FIRST_FIGURE', 'DAS_IND_FIRST_FREQUENCY', 'DAS_IND_SECOND_FIGURE', 'DAS_IND_SECOND_FREQUENCY', 'DAS_IND_CREATE_DATE', 'DAS_IND_UPDATE_DATE', 'DAS_IND_STATUS', ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[BasePeer::TYPE_PHPNAME]['Id'] = 0
     */
    private static $fieldKeys = array (
        BasePeer::TYPE_PHPNAME => array ('DasIndUid' => 0, 'DasUid' => 1, 'DasIndType' => 2, 'DasIndTitle' => 3, 'DasIndGoal' => 4, 'DasIndDirection' => 5, 'DasUidProcess' => 6, 'DasIndFirstFigure' => 7, 'DasIndFirstFrequency' => 8, 'DasIndSecondFigure' => 9, 'DasIndSecondFrequency' => 10, 'DasIndCreateDate' => 11, 'DasIndUpdateDate' => 12, 'DasIndStatus' => 13, ),
        BasePeer::TYPE_COLNAME => array (DashboardIndicatorPeer::DAS_IND_UID => 0, DashboardIndicatorPeer::DAS_UID => 1, DashboardIndicatorPeer::DAS_IND_TYPE => 2, DashboardIndicatorPeer::DAS_IND_TITLE => 3, DashboardIndicatorPeer::DAS_IND_GOAL => 4, DashboardIndicatorPeer::DAS_IND_DIRECTION => 5, DashboardIndicatorPeer::DAS_UID_PROCESS => 6, DashboardIndicatorPeer::DAS_IND_FIRST_FIGURE => 7, DashboardIndicatorPeer::DAS_IND_FIRST_FREQUENCY => 8, DashboardIndicatorPeer::DAS_IND_SECOND_FIGURE => 9, DashboardIndicatorPeer::DAS_IND_SECOND_FREQUENCY => 10, DashboardIndicatorPeer::DAS_IND_CREATE_DATE => 11, DashboardIndicatorPeer::DAS_IND_UPDATE_DATE => 12, DashboardIndicatorPeer::DAS_IND_STATUS => 13, ),
        BasePeer::TYPE_FIELDNAME => array ('DAS_IND_UID' => 0, 'DAS_UID' => 1, 'DAS_IND_TYPE' => 2, 'DAS_IND_TITLE' => 3, 'DAS_IND_GOAL' => 4, 'DAS_IND_DIRECTION' => 5, 'DAS_UID_PROCESS' => 6, 'DAS_IND_FIRST_FIGURE' => 7, 'DAS_IND_FIRST_FREQUENCY' => 8, 'DAS_IND_SECOND_FIGURE' => 9, 'DAS_IND_SECOND_FREQUENCY' => 10, 'DAS_IND_CREATE_DATE' => 11, 'DAS_IND_UPDATE_DATE' => 12, 'DAS_IND_STATUS' => 13, ),
        BasePeer::TYPE_NUM => array (0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, )
    );

    /**
     * @return     MapBuilder the map builder for this peer
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function getMapBuilder()
    {
        include_once 'classes/model/map/DashboardIndicatorMapBuilder.php';
        return BasePeer::getMapBuilder('classes.model.map.DashboardIndicatorMapBuilder');
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
            $map = DashboardIndicatorPeer::getTableMap();
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
     * @param      string $column The column name for current table. (i.e. DashboardIndicatorPeer::COLUMN_NAME).
     * @return     string
     */
    public static function alias($alias, $column)
    {
        return str_replace(DashboardIndicatorPeer::TABLE_NAME.'.', $alias.'.', $column);
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

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_UID);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_UID);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_TYPE);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_TITLE);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_GOAL);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_DIRECTION);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_UID_PROCESS);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_FIRST_FIGURE);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_FIRST_FREQUENCY);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_SECOND_FIGURE);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_SECOND_FREQUENCY);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_CREATE_DATE);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_UPDATE_DATE);

        $criteria->addSelectColumn(DashboardIndicatorPeer::DAS_IND_STATUS);

    }

    const COUNT = 'COUNT(DASHBOARD_INDICATOR.DAS_IND_UID)';
    const COUNT_DISTINCT = 'COUNT(DISTINCT DASHBOARD_INDICATOR.DAS_IND_UID)';

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
            $criteria->addSelectColumn(DashboardIndicatorPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(DashboardIndicatorPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach ($criteria->getGroupByColumns() as $column) {
            $criteria->addSelectColumn($column);
        }

        $rs = DashboardIndicatorPeer::doSelectRS($criteria, $con);
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
     * @return     DashboardIndicator
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectOne(Criteria $criteria, $con = null)
    {
        $critcopy = clone $criteria;
        $critcopy->setLimit(1);
        $objects = DashboardIndicatorPeer::doSelect($critcopy, $con);
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
        return DashboardIndicatorPeer::populateObjects(DashboardIndicatorPeer::doSelectRS($criteria, $con));
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
            DashboardIndicatorPeer::addSelectColumns($criteria);
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
        $cls = DashboardIndicatorPeer::getOMClass();
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
     * Returns the number of rows matching criteria, joining the related Dashboard table
     *
     * @param      Criteria $c
     * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
     * @param      Connection $con
     * @return     int Number of matching rows.
     */
    public static function doCountJoinDashboard(Criteria $criteria, $distinct = false, $con = null)
    {
        // we're going to modify criteria, so copy it first
        $criteria = clone $criteria;

        // clear out anything that might confuse the ORDER BY clause
        $criteria->clearSelectColumns()->clearOrderByColumns();
        if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->addSelectColumn(DashboardIndicatorPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(DashboardIndicatorPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach($criteria->getGroupByColumns() as $column)
        {
            $criteria->addSelectColumn($column);
        }

        $criteria->addJoin(DashboardIndicatorPeer::DAS_UID, DashboardPeer::DAS_UID);

        $rs = DashboardIndicatorPeer::doSelectRS($criteria, $con);
        if ($rs->next()) {
            return $rs->getInt(1);
        } else {
            // no rows returned; we infer that means 0 matches.
            return 0;
        }
    }


    /**
     * Selects a collection of DashboardIndicator objects pre-filled with their Dashboard objects.
     *
     * @return     array Array of DashboardIndicator objects.
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinDashboard(Criteria $c, $con = null)
    {
        $c = clone $c;

        // Set the correct dbName if it has not been overridden
        if ($c->getDbName() == Propel::getDefaultDB()) {
            $c->setDbName(self::DATABASE_NAME);
        }

        DashboardIndicatorPeer::addSelectColumns($c);
        $startcol = (DashboardIndicatorPeer::NUM_COLUMNS - DashboardIndicatorPeer::NUM_LAZY_LOAD_COLUMNS) + 1;
        DashboardPeer::addSelectColumns($c);

        $c->addJoin(DashboardIndicatorPeer::DAS_UID, DashboardPeer::DAS_UID);
        $rs = BasePeer::doSelect($c, $con);
        $results = array();

        while($rs->next()) {

            $omClass = DashboardIndicatorPeer::getOMClass();

            $cls = Propel::import($omClass);
            $obj1 = new $cls();
            $obj1->hydrate($rs);

            $omClass = DashboardPeer::getOMClass();

            $cls = Propel::import($omClass);
            $obj2 = new $cls();
            $obj2->hydrate($rs, $startcol);

            $newObject = true;
            foreach($results as $temp_obj1) {
                $temp_obj2 = $temp_obj1->getDashboard(); //CHECKME
                if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
                    $newObject = false;
                    // e.g. $author->addBookRelatedByBookId()
                    $temp_obj2->addDashboardIndicator($obj1); //CHECKME
                    break;
                }
            }
            if ($newObject) {
                $obj2->initDashboardIndicators();
                $obj2->addDashboardIndicator($obj1); //CHECKME
            }
            $results[] = $obj1;
        }
        return $results;
    }


    /**
     * Returns the number of rows matching criteria, joining all related tables
     *
     * @param      Criteria $c
     * @param      boolean $distinct Whether to select only distinct columns (You can also set DISTINCT modifier in Criteria).
     * @param      Connection $con
     * @return     int Number of matching rows.
     */
    public static function doCountJoinAll(Criteria $criteria, $distinct = false, $con = null)
    {
        $criteria = clone $criteria;

        // clear out anything that might confuse the ORDER BY clause
        $criteria->clearSelectColumns()->clearOrderByColumns();
        if ($distinct || in_array(Criteria::DISTINCT, $criteria->getSelectModifiers())) {
            $criteria->addSelectColumn(DashboardIndicatorPeer::COUNT_DISTINCT);
        } else {
            $criteria->addSelectColumn(DashboardIndicatorPeer::COUNT);
        }

        // just in case we're grouping: add those columns to the select statement
        foreach($criteria->getGroupByColumns() as $column)
        {
            $criteria->addSelectColumn($column);
        }

        $criteria->addJoin(DashboardIndicatorPeer::DAS_UID, DashboardPeer::DAS_UID);

        $rs = DashboardIndicatorPeer::doSelectRS($criteria, $con);
        if ($rs->next()) {
            return $rs->getInt(1);
        } else {
            // no rows returned; we infer that means 0 matches.
            return 0;
        }
    }


    /**
     * Selects a collection of DashboardIndicator objects pre-filled with all related objects.
     *
     * @return     array Array of DashboardIndicator objects.
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function doSelectJoinAll(Criteria $c, $con = null)
    {
        $c = clone $c;

        // Set the correct dbName if it has not been overridden
        if ($c->getDbName() == Propel::getDefaultDB()) {
            $c->setDbName(self::DATABASE_NAME);
        }

        DashboardIndicatorPeer::addSelectColumns($c);
        $startcol2 = (DashboardIndicatorPeer::NUM_COLUMNS - DashboardIndicatorPeer::NUM_LAZY_LOAD_COLUMNS) + 1;

        DashboardPeer::addSelectColumns($c);
        $startcol3 = $startcol2 + DashboardPeer::NUM_COLUMNS;

        $c->addJoin(DashboardIndicatorPeer::DAS_UID, DashboardPeer::DAS_UID);

        $rs = BasePeer::doSelect($c, $con);
        $results = array();

        while($rs->next()) {

            $omClass = DashboardIndicatorPeer::getOMClass();


            $cls = Propel::import($omClass);
            $obj1 = new $cls();
            $obj1->hydrate($rs);


                // Add objects for joined Dashboard rows
    
            $omClass = DashboardPeer::getOMClass();


            $cls = Propel::import($omClass);
            $obj2 = new $cls();
            $obj2->hydrate($rs, $startcol2);

            $newObject = true;
            for ($j=0, $resCount=count($results); $j < $resCount; $j++) {
                $temp_obj1 = $results[$j];
                $temp_obj2 = $temp_obj1->getDashboard(); // CHECKME
                if ($temp_obj2->getPrimaryKey() === $obj2->getPrimaryKey()) {
                    $newObject = false;
                    $temp_obj2->addDashboardIndicator($obj1); // CHECKME
                    break;
                }
            }

            if ($newObject) {
                $obj2->initDashboardIndicators();
                $obj2->addDashboardIndicator($obj1);
            }

            $results[] = $obj1;
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
        return DashboardIndicatorPeer::CLASS_DEFAULT;
    }

    /**
     * Method perform an INSERT on the database, given a DashboardIndicator or Criteria object.
     *
     * @param      mixed $values Criteria or DashboardIndicator object containing data that is used to create the INSERT statement.
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
            $criteria = $values->buildCriteria(); // build Criteria from DashboardIndicator object
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
     * Method perform an UPDATE on the database, given a DashboardIndicator or Criteria object.
     *
     * @param      mixed $values Criteria or DashboardIndicator object containing data create the UPDATE statement.
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

            $comparison = $criteria->getComparison(DashboardIndicatorPeer::DAS_IND_UID);
            $selectCriteria->add(DashboardIndicatorPeer::DAS_IND_UID, $criteria->remove(DashboardIndicatorPeer::DAS_IND_UID), $comparison);

        } else {
            $criteria = $values->buildCriteria(); // gets full criteria
            $selectCriteria = $values->buildPkeyCriteria(); // gets criteria w/ primary key(s)
        }

        // set the correct dbName
        $criteria->setDbName(self::DATABASE_NAME);

        return BasePeer::doUpdate($selectCriteria, $criteria, $con);
    }

    /**
     * Method to DELETE all rows from the DASHBOARD_INDICATOR table.
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
            $affectedRows += BasePeer::doDeleteAll(DashboardIndicatorPeer::TABLE_NAME, $con);
            $con->commit();
            return $affectedRows;
        } catch (PropelException $e) {
            $con->rollback();
            throw $e;
        }
    }

    /**
     * Method perform a DELETE on the database, given a DashboardIndicator or Criteria object OR a primary key value.
     *
     * @param      mixed $values Criteria or DashboardIndicator object or primary key or array of primary keys
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
            $con = Propel::getConnection(DashboardIndicatorPeer::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            $criteria = clone $values; // rename for clarity
        } elseif ($values instanceof DashboardIndicator) {

            $criteria = $values->buildPkeyCriteria();
        } else {
            // it must be the primary key
            $criteria = new Criteria(self::DATABASE_NAME);
            $criteria->add(DashboardIndicatorPeer::DAS_IND_UID, (array) $values, Criteria::IN);
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
     * Validates all modified columns of given DashboardIndicator object.
     * If parameter $columns is either a single column name or an array of column names
     * than only those columns are validated.
     *
     * NOTICE: This does not apply to primary or foreign keys for now.
     *
     * @param      DashboardIndicator $obj The object to validate.
     * @param      mixed $cols Column name or array of column names.
     *
     * @return     mixed TRUE if all columns are valid or the error message of the first invalid column.
     */
    public static function doValidate(DashboardIndicator $obj, $cols = null)
    {
        $columns = array();

        if ($cols) {
            $dbMap = Propel::getDatabaseMap(DashboardIndicatorPeer::DATABASE_NAME);
            $tableMap = $dbMap->getTable(DashboardIndicatorPeer::TABLE_NAME);

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

        return BasePeer::doValidate(DashboardIndicatorPeer::DATABASE_NAME, DashboardIndicatorPeer::TABLE_NAME, $columns);
    }

    /**
     * Retrieve a single object by pkey.
     *
     * @param      mixed $pk the primary key.
     * @param      Connection $con the connection to use
     * @return     DashboardIndicator
     */
    public static function retrieveByPK($pk, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $criteria = new Criteria(DashboardIndicatorPeer::DATABASE_NAME);

        $criteria->add(DashboardIndicatorPeer::DAS_IND_UID, $pk);


        $v = DashboardIndicatorPeer::doSelect($criteria, $con);

        return !empty($v) > 0 ? $v[0] : null;
    }

    /**
     * Retrieve multiple objects by pkey.
     *
     * @param      array $pks List of primary keys
     * @param      Connection $con the connection to use
     * @throws     PropelException Any exceptions caught during processing will be
     *       rethrown wrapped into a PropelException.
     */
    public static function retrieveByPKs($pks, $con = null)
    {
        if ($con === null) {
            $con = Propel::getConnection(self::DATABASE_NAME);
        }

        $objs = null;
        if (empty($pks)) {
            $objs = array();
        } else {
            $criteria = new Criteria();
            $criteria->add(DashboardIndicatorPeer::DAS_IND_UID, $pks, Criteria::IN);
            $objs = DashboardIndicatorPeer::doSelect($criteria, $con);
        }
        return $objs;
    }
}


// static code to register the map builder for this Peer with the main Propel class
if (Propel::isInit()) {
    // the MapBuilder classes register themselves with Propel during initialization
    // so we need to load them here.
    try {
        BaseDashboardIndicatorPeer::getMapBuilder();
    } catch (Exception $e) {
        Propel::log('Could not initialize Peer: ' . $e->getMessage(), Propel::LOG_ERR);
    }
} else {
    // even if Propel is not yet initialized, the map builder class can be registered
    // now and then it will be loaded when Propel initializes.
    require_once 'classes/model/map/DashboardIndicatorMapBuilder.php';
    Propel::registerMapBuilder('classes.model.map.DashboardIndicatorMapBuilder');
}

