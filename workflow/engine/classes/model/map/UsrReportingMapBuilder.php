<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'USR_REPORTING' table to 'workflow' DatabaseMap object.
 *
 *
 *
 * These statically-built map classes are used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    workflow.classes.model.map
 */
class UsrReportingMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.UsrReportingMapBuilder';

    /**
     * The database map.
     */
    private $dbMap;

    /**
     * Tells us if this DatabaseMapBuilder is built so that we
     * don't have to re-build it every time.
     *
     * @return     boolean true if this DatabaseMapBuilder is built, false otherwise.
     */
    public function isBuilt()
    {
        return ($this->dbMap !== null);
    }

    /**
     * Gets the databasemap this map builder built.
     *
     * @return     the databasemap
     */
    public function getDatabaseMap()
    {
        return $this->dbMap;
    }

    /**
     * The doBuild() method builds the DatabaseMap
     *
     * @return     void
     * @throws     PropelException
     */
    public function doBuild()
    {
        $this->dbMap = Propel::getDatabaseMap('workflow');

        $tMap = $this->dbMap->addTable('USR_REPORTING');
        $tMap->setPhpName('UsrReporting');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('USR_UID', 'UsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('TAS_UID', 'TasUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addPrimaryKey('MONTH', 'Month', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addPrimaryKey('YEAR', 'Year', 'int', CreoleTypes::INTEGER, true, null);

        $tMap->addColumn('TOTAL_QUEUE_TIME_BY_TASK', 'TotalQueueTimeByTask', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('TOTAL_TIME_BY_TASK', 'TotalTimeByTask', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('TOTAL_CASES_IN', 'TotalCasesIn', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('TOTAL_CASES_OUT', 'TotalCasesOut', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('USER_HOUR_COST', 'UserHourCost', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('AVG_TIME', 'AvgTime', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('SDV_TIME', 'SdvTime', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('CONFIGURED_TASK_TIME', 'ConfiguredTaskTime', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('TOTAL_CASES_OVERDUE', 'TotalCasesOverdue', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('TOTAL_CASES_ON_TIME', 'TotalCasesOnTime', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('PRO_COST', 'ProCost', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('PRO_UNIT_COST', 'ProUnitCost', 'string', CreoleTypes::VARCHAR, false, 50);

    } // doBuild()

} // UsrReportingMapBuilder
