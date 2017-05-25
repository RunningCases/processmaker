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
     * Flush the cache files for the specified workspace.
     * 
     * @param object $workspace
     */
    public static function flushCache($workspace)
    {
        try {
            //Update singleton file by workspace
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
                        }
                        file_put_contents($pathSingleton, $oPluginRegistry->serializeInstance());
                    }
                }
            }

            //flush the cache files
            \G::rm_dir(PATH_C);
            \G::mk_dir(PATH_C, 0777);
            \G::rm_dir($workspace->path . "/cache");
            \G::mk_dir($workspace->path . "/cache", 0777);
            \G::rm_dir($workspace->path . "/cachefiles");
            \G::mk_dir($workspace->path . "/cachefiles", 0777);
            if (file_exists($workspace->path . '/routes.php')) {
                unlink($workspace->path . '/routes.php');
            }
        } catch (\Exception $e) {
            throw new \Exception("Error: cannot perform this task. " . $e->getMessage());
        }
    }

}
