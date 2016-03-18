<?php
/**
 * Description of Granular Importer
 *
 */

namespace ProcessMaker\BusinessModel\Migrator;

class GranularImporter
{

    protected $factory;
    protected $data;
    /**
     * GranularImporter constructor.
     */
    public function __construct()
    {
        $this->factory = new MigratorFactory();
    }

    public function import($objectList)
    {
        foreach ($objectList as $key => $data) {
            $objClass = $this->factory->create($key);
            if(is_object($objClass)) {
                $migratorData = $objClass->import($data);
            }
        }
    }
}