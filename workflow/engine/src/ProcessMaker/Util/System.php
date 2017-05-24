<?php
namespace ProcessMaker\Util;

class System
{
    /**
     * Get Time Zone
     *
     * @return string Return Time Zone
     */
    public static function getTimeZone()
    {
        try {
            $arraySystemConfiguration = \System::getSystemConfiguration('', '', SYS_SYS);

            //Return
            return $arraySystemConfiguration['time_zone'];
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Flush the cache files for the specified workspace(s).
     * If no workspace is specified, then the cache will be flushed in all available 
     * workspaces.
     * 
     * @param array $args
     * @param array $opts
     */
    public static function flushCache($args, $opts)
    {
        $workspaces = get_workspaces_from_args($args);

        if (!defined("PATH_C")) {
            die("ERROR: seems processmaker is not properly installed (System constants are missing)." . PHP_EOL);
        }

        //Update singleton file by workspace
        foreach ($workspaces as $workspace) {
            eprint("Update singleton in workspace " . $workspace->name . " ... ");
            \Bootstrap::setConstantsRelatedWs($workspace->name);
            $pathSingleton = PATH_DATA . "sites" . PATH_SEP . $workspace->name . PATH_SEP . "plugin.singleton";
            $oPluginRegistry = \PMPluginRegistry::loadSingleton($pathSingleton);
            $items = \PMPlugin::getListAllPlugins($workspace->name);
            foreach ($items as $item) {
                if ($item->enabled === true) {
                    require_once($item->sFilename);
                    $details = $oPluginRegistry->getPluginDetails(basename($item->sFilename));
                    //Only if the API directory structure is defined
                    $pathApiDirectory = PATH_PLUGINS . $details->sPluginFolder . PATH_SEP . "src" . PATH_SEP . "Services" . PATH_SEP . "Api";
                    if (is_dir($pathApiDirectory)) {
                        $pluginSrcDir = PATH_PLUGINS . $details->sNamespace . PATH_SEP . 'src';
                        $loader = \Maveriks\Util\ClassLoader::getInstance();
                        $loader->add($pluginSrcDir);
                        $oPluginRegistry->registerRestService($details->sNamespace);
                        if (class_exists($details->sClassName)) {
                            $oPlugin = new $details->sClassName($details->sNamespace, $details->sFilename);
                            $oPlugin->setup();
                            file_put_contents($pathSingleton, $oPluginRegistry->serializeInstance());
                        }
                    }
                }
            }
            eprintln("DONE");
        }

        //flush the cache files
        \CLI::logging("Flush " . \pakeColor::colorize("system", "INFO") . " cache ... ");
        \G::rm_dir(PATH_C);
        \G::mk_dir(PATH_C, 0777);
        echo "DONE" . PHP_EOL;

        foreach ($workspaces as $workspace) {
            echo "Flush workspace " . \pakeColor::colorize($workspace->name, "INFO") . " cache ... ";

            \G::rm_dir($workspace->path . "/cache");
            \G::mk_dir($workspace->path . "/cache", 0777);
            \G::rm_dir($workspace->path . "/cachefiles");
            \G::mk_dir($workspace->path . "/cachefiles", 0777);
            if (file_exists($workspace->path . '/routes.php')) {
                unlink($workspace->path . '/routes.php');
            }
            echo "DONE" . PHP_EOL;
        }
    }

}
