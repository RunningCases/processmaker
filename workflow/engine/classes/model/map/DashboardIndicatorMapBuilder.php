<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'DASHBOARD_INDICATOR' table to 'workflow' DatabaseMap object.
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
class DashboardIndicatorMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.DashboardIndicatorMapBuilder';

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

        $tMap = $this->dbMap->addTable('DASHBOARD_INDICATOR');
        $tMap->setPhpName('DashboardIndicator');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('DAS_IND_UID', 'DasIndUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addForeignKey('DAS_UID', 'DasUid', 'string', CreoleTypes::VARCHAR, 'DASHBOARD', 'DAS_UID', true, 32);

        $tMap->addColumn('DAS_IND_TYPE', 'DasIndType', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DAS_IND_TITLE', 'DasIndTitle', 'string', CreoleTypes::VARCHAR, true, 255);

        $tMap->addColumn('DAS_IND_GOAL', 'DasIndGoal', 'double', CreoleTypes::DECIMAL, false, 7,2);

        $tMap->addColumn('DAS_IND_DIRECTION', 'DasIndDirection', 'int', CreoleTypes::TINYINT, true, null);

        $tMap->addColumn('DAS_UID_PROCESS', 'DasUidProcess', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('DAS_IND_FIRST_FIGURE', 'DasIndFirstFigure', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('DAS_IND_FIRST_FREQUENCY', 'DasIndFirstFrequency', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('DAS_IND_SECOND_FIGURE', 'DasIndSecondFigure', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('DAS_IND_SECOND_FREQUENCY', 'DasIndSecondFrequency', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('DAS_IND_CREATE_DATE', 'DasIndCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('DAS_IND_UPDATE_DATE', 'DasIndUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

        $tMap->addColumn('DAS_IND_STATUS', 'DasIndStatus', 'int', CreoleTypes::TINYINT, true, null);

    } // doBuild()

} // DashboardIndicatorMapBuilder
