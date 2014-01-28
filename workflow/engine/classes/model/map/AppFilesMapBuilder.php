<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'APP_FILES' table to 'workflow' DatabaseMap object.
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
class AppFilesMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.AppFilesMapBuilder';

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

        $tMap = $this->dbMap->addTable('APP_FILES');
        $tMap->setPhpName('AppFiles');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('APF_UID', 'ApfUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('PRO_UID', 'ProUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('CREATE_USR_UID', 'CreateUsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('LAST_UPDATE_USR_UID', 'LastUpdateUsrUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('APF_PATH', 'ApfPath', 'string', CreoleTypes::VARCHAR, true, 256);

        $tMap->addColumn('APF_TYPE', 'ApfType', 'string', CreoleTypes::VARCHAR, false, 32);

        $tMap->addColumn('APF_EDITABLE', 'ApfEditable', 'int', CreoleTypes::TINYINT, false, null);

        $tMap->addColumn('APF_CREATE_DATE', 'ApfCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('APF_UPDATE_DATE', 'ApfUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

    } // doBuild()

} // AppFilesMapBuilder
