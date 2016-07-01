<?php
namespace ProcessMaker\BusinessModel\Migrator;

/**
 * Class PluginMigratorAdapter
 * @package ProcessMaker\BusinessModel\Migrator
 */
class PluginMigratorAdapter implements  Exportable, Importable
{

    private $migrator;

    /**
     * PluginMigratorAdapter constructor.
     */
    public function __construct($pluginName)
    {
        \G::LoadClass('pluginRegistry');
        $registry = \PMPluginRegistry::getSingleton();
        $plugin = $registry->getPluginByCode($pluginName);
        require_once (
            PATH_PLUGINS.PATH_SEP.
            $plugin->sPluginFolder.PATH_SEP.
            'classes'.PATH_SEP.
            $plugin->sMigratorClassName.'.php'
        );
        $this->migrator = new $plugin->sMigratorClassName();
    }

    public function beforeExport()
    {
        return $this->migrator->beforeExport();
    }

    public function export($prj_uid)
    {
        return $this->migrator->export($prj_uid);
    }

    public function afterExport()
    {
        return $this->migrator->afterExport();
    }

    public function beforeImport($data)
    {
        return $this->migrator->beforeImport($data);
    }

    public function import($data, $replace)
    {
        return $this->migrator->import($data, $replace);
    }

    public function afterImport($data)
    {
        return $this->migrator->afterImport($data);
    }

}