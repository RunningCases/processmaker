<?php

/**
 * class.toolBar.php
 *
 * @package workflow.engine.ProcessMaker
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
 *
 */

/**
 * ToolBar - ToolBar 
/**
 * XmlForm_Field_ToolBar - XmlForm_Field_ToolBar class
 *
 * @package workflow.engine.ProcessMaker
 */class XmlForm_Field_ToolBar extends XmlForm_Field
{

    public $xmlfile = '';
    public $type = 'toolbar';
    public $toolBar;
    public $home = '';
    public $withoutLabel = true;

    /**
     * Constructor of the class XmlForm_Field_ToolBar
     *
     * @param string $xmlNode
     * @param string $lang
     * @param string $home
     * @param string $owner
     * @return void
     */
    public function XmlForm_Field_ToolBar($xmlNode, $lang = 'en', $home = '', $owner = ' ')
    {
        parent::XmlForm_Field($xmlNode, $lang, $home, $owner);
        $this->home = $home;
    }

    /**
     * Prints the ToolBar
     *
     * @param string $value
     * @return string
     */
    public function render($value)
    {
        $this->toolBar = new ToolBar($this->xmlfile, $this->home);
        $template = PATH_CORE . 'templates/' . $this->type . '.html';
        $out = $this->toolBar->render($template, $scriptCode);
        $oHeadPublisher = & headPublisher::getSingleton();
        $oHeadPublisher->addScriptFile($this->toolBar->scriptURL);
        $oHeadPublisher->addScriptCode($scriptCode);
        return $out;
    }
}
