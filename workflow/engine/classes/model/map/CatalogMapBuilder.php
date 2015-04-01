<?php

require_once 'propel/map/MapBuilder.php';
include_once 'creole/CreoleTypes.php';


/**
 * This class adds structure of 'CATALOG' table to 'workflow' DatabaseMap object.
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
class CatalogMapBuilder
{

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = 'classes.model.map.CatalogMapBuilder';

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

        $tMap = $this->dbMap->addTable('CATALOG');
        $tMap->setPhpName('Catalog');

        $tMap->setUseIdGenerator(false);

        $tMap->addPrimaryKey('CAT_UID', 'CatUid', 'string', CreoleTypes::VARCHAR, true, 32);

        $tMap->addColumn('CAT_LABEL_ID', 'CatLabelId', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addPrimaryKey('CAT_TYPE', 'CatType', 'string', CreoleTypes::VARCHAR, true, 100);

        $tMap->addColumn('CAT_FLAG', 'CatFlag', 'string', CreoleTypes::VARCHAR, false, 50);

        $tMap->addColumn('CAT_OBSERVATION', 'CatObservation', 'string', CreoleTypes::LONGVARCHAR, false, null);

        $tMap->addColumn('CAT_CREATE_DATE', 'CatCreateDate', 'int', CreoleTypes::TIMESTAMP, true, null);

        $tMap->addColumn('CAT_UPDATE_DATE', 'CatUpdateDate', 'int', CreoleTypes::TIMESTAMP, false, null);

    } // doBuild()

} // CatalogMapBuilder
