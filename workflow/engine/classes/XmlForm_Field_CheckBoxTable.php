<?php

/**
 * class.xmlfield_InputPM.php
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
 *
 * @package workflow.engine.classes
 **/


/**
 * Class XmlForm_Field_CheckBoxTable
 */class XmlForm_Field_CheckBoxTable extends XmlForm_Field_Checkbox
{

    /**
     * Function render
     *
     * @author The Answer
     * @access public
     * @param eter string value
     * @param eter string owner
     * @return string
     */
    public function render ($value = null, $owner = null)
    {
        //$optionName = $owner->values['USR_UID'];
        $optionName = $value;
        $onclick = (($this->onclick) ? ' onclick="' . G::replaceDataField( $this->onclick, $owner->values ) . '" ' : '');
        $html = '<input class="FormCheck" id="form[' . $this->name . '][' . $optionName . ']" name="form[' . $this->name . '][' . $optionName . ']" type=\'checkbox\' value="' . $value . '"' . $onclick . '> <span class="FormCheck"></span></input>';
        return $html;
    }
}
