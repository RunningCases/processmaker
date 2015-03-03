<?php

/**
 * class.featureRegistry.php
 *
 * @package workflow.engine.classes
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2011 Colosa Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * For more information, contact Colosa Inc, 2566 Le Jeune Rd.,
 * Coral Gables, FL, 33134, USA, or email info@colosa.com.
 */

class PMFeatureRegistry
{
    private $features = array ();
    private $triggers = array ();
    private static $instance = null;

    /**
     * This function is the constructor of the PMPluginRegistry class
     * param
     *
     * @return void
     */
    private function __construct ()
    {
        
    }

    /**
     * This function is instancing to this class
     * param
     *
     * @return object
     */
    public static function getSingleton ()
    {
        if (self::$instance == null) {
            self::$instance = new PMFeatureRegistry();
        }
        return self::$instance;
    }

    /**
     * Register the feature in the singleton
     *
     * @param unknown_type $className
     * @param unknown_type $featureName
     * @param unknown_type $filename
     */
    public function registerFeature ($featureName, $filename = null)
    {
        $className = $featureName . "Feature";
        $feature = new $className( $featureName, $filename );
        if (isset( $this->features[$featureName] )) {
            $this->features[$featureName]->iVersion = $feature->iVersion;
            return;
        } else {
            $this->features[$featureName] = $feature;
        }
    }

    /**
     * Enable the plugin in the singleton
     *
     * @param unknown_type $featureName
     */
    public function enableFeature ($featureName)
    {
        $feature = $this->retrieveFeature($featureName);
        if ($feature) {
            $feature->enable();
        }
        throw new Exception( "Unable to enable feature '$featureName' (feature not found)" );
    }

    /**
     * disable the plugin in the singleton
     *
     * @param unknown_type $featureName
     */
    public function disableFeature ($featureName)
    {
        $feature = $this->retrieveFeature($featureName);
        if ($feature) {
            $feature->enable();
        }
        throw new Exception( "Unable to disable feature '$featureName' (feature not found)" );
    }

    /**
     * install the plugin
     *
     * @param unknown_type $featureName
     */
    public function installFeature ($featureName)
    {
        try {
            $this->registerFeature($featureName);
            $feature = $this->retrieveFeature($featureName);
            if ($feature) {
                $feature->install();
            } else {
                throw new Exception( "Unable to install feature '$featureName' (feature not found)" );
            }
        } catch (Exception $e) {
            global $G_PUBLISH;
            $aMessage['MESSAGE'] = $e->getMessage();
            $G_PUBLISH = new Publisher();
            $G_PUBLISH->AddContent( 'xmlform', 'xmlform', 'login/showMessage', '', $aMessage );
            G::RenderPage( 'publish' );
            die();
        }
    }
    
    public function retrieveFeature($name)
    {
        foreach ($this->features as $featureName => $feature) {
            if ($featureName === $name) {
                return $feature;
            }
        }
        return false;
    }

    /**
     * execute all triggers related to a triggerId
     *
     * @param unknown_type $menuId
     * @return object
     */
    public function executeTriggers ($triggerId, $data)
    {
        foreach ($this->features as $feature) {
            $feature->executeTrigger($triggerId, $data);
        }
    }

    public function setupFeatures ()
    {
        return true;
        $featureDirList = glob(PATH_FEATURES . "/*", GLOB_ONLYDIR);
        foreach ($featureDirList as $directory) {
            if ($directory == 'ViewContainers') {
                continue;
            }
            $featureApiClassList = Util\Common::rglob($directory . DS . 'Services' . DS . 'Api' . "/*");
            foreach ($featureApiClassList as $classFile) {
                if (pathinfo($classFile, PATHINFO_EXTENSION) === 'php') {
                    $relClassPath = str_replace('.php', '', str_replace($servicesDir, '', $classFile));
                    $namespace = '\\ProcessMaker\\Services\\Api\\' . basename($classFile, '.php');
                    $namespace = strpos($namespace, "//") === false? $namespace: str_replace("//", '', $namespace);
                    require_once $classFile;
                    $this->rest->addAPIClass($namespace);
                }
            }
        }
        foreach ($this->features as $feature) {
            $feature->setup();
        }
    }

}

