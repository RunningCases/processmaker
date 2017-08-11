<?php

/**
 * class.popupMenu.php
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
 * popupMenu - popupMenu 
/**
 * XmlForm_Field_popupOption - XmlForm_Field_popupOption class
 *
 * @package workflow.engine.ProcessMaker
 * @copyright COLOSA
 */class XmlForm_Field_popupOption extends XmlForm_Field
{
    var $launch = '';

    /**
     * Get Events
     *
     * @return string
     */
    function getEvents ()
    {
        $script = '{name:"' . $this->name . '",text:"' . addcslashes( $this->label, '\\"' ) . '", launch:leimnud.closure({Function:function(target){' . $this->launch . '}, args:target})}';
        return $script;
    }
}
