<?php

/**
 * class.pluginRegistry.php
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

use ProcessMaker\Plugins\PluginRegistry;

/**
 *
 * @package workflow.engine.classes
 */

require_once 'class.plugin.php';



/**
 *
 * @package workflow.engine.classes
 */class pluginDetail
{
    public $sNamespace;
    public $sClassName;
    public $sFriendlyName = null;
    public $sDescription = null;
    public $sSetupPage = null;
    public $sFilename;
    public $sPluginFolder = '';
    public $sCompanyLogo = '';
    public $iVersion = 0;
    public $enabled = false;
    public $aWorkspaces = null;
    public $bPrivate = false;

    /**
     * This function is the constructor of the pluginDetail class
     *
     * @param string $sNamespace
     * @param string $sClassName
     * @param string $sFilename
     * @param string $sFriendlyName
     * @param string $sPluginFolder
     * @param string $sDescription
     * @param string $sSetupPage
     * @param integer $iVersion
     * @return void
     */
    public function __construct ($sNamespace, $sClassName, $sFilename, $sFriendlyName = '', $sPluginFolder = '', $sDescription = '', $sSetupPage = '', $iVersion = 0)
    {
        $this->sNamespace = $sNamespace;
        $this->sClassName = $sClassName;
        $this->sFriendlyName = $sFriendlyName;
        $this->sDescription = $sDescription;
        $this->sSetupPage = $sSetupPage;
        $this->iVersion = $iVersion;
        $this->sFilename = $sFilename;
        if ($sPluginFolder == '') {
            $this->sPluginFolder = $sNamespace;
        } else {
            $this->sPluginFolder = $sPluginFolder;
        }
    }
}
