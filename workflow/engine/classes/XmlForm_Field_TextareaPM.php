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
 * Class XmlForm_Field_TextareaPM
 */class XmlForm_Field_TextareaPM extends XmlForm_Field
{
    public $rows = 12;
    public $cols = 40;
    public $required = false;
    public $readOnly = false;
    public $wrap = 'OFF';
    public $showVars = 0;
    public $process = '';
    public $symbol = '@@';

    /**
     * Function render
     *
     * @author Julio Cesar Laura Avendao <juliocesar@colosa.com>
     * @access public
     * @param eter string value
     * @param eter string owner
     * @return string
     */
    public function render ($value = null, $owner)
    {
        if ($this->showVars == 1) {
            $this->process = G::replaceDataField( $this->process, $owner->values );
            $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
        } else {
            $sShowVars = '';
        }
        if ($this->mode === 'edit') {
            if ($this->readOnly) {
                return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '" class="FormTextPM" readOnly>' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>' . $sShowVars;
            } else {
                return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '" class="FormTextPM" >' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>' . $sShowVars;
            }
        } elseif ($this->mode === 'view') {
            return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" readOnly style="border:0px;backgroud-color:inherit;' . $this->style . '" wrap="' . htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '"  class="FormTextPM" >' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>';
        } else {
            return '<textarea id="form[' . $this->name . ']" name="form[' . $this->name . ']" cols="' . $this->cols . '" rows="' . $this->rows . '" style="' . $this->style . '" wrap="' . htmlentities( $this->wrap, ENT_QUOTES, 'UTF-8' ) . '"  class="FormTextArea" >' . $this->htmlentities( $value, ENT_COMPAT, 'utf-8' ) . '</textarea>';
        }
    }

    /**
     * Function renderGrid
     *
     * @author Julio Cesar Laura Avendano <juliocesar@colosa.com>
     * @access public
     * @param eter string values
     * @param eter string owner
     * @return string
     */
    public function renderGrid ($owner, $values = null)
    {
        $result = array ();
        $r = 1;
        foreach ($values as $v) {
            if ($this->showVars == 1) {
                $this->process = G::replaceDataField( $this->process, $owner->values );
                //$sShowVars = '&nbsp;<a href="#" onclick="showDynaformsFormVars(\'form['.$owner->name .']['.$r.']['.$this->name.']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;">' . $this->symbol . '</a>';
                $sShowVars = '&nbsp;<input type="button" value="' . $this->symbol . '" onclick="showDynaformsFormVars(\'form[' . $owner->name . '][' . $r . '][' . $this->name . ']\', \'../controls/varsAjax\', \'' . $this->process . '\', \'' . $this->symbol . '\');return false;"/>';
            } else {
                $sShowVars = '';
            }
            if ($this->mode === 'edit') {
                if ($this->readOnly) {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $v, ENT_COMPAT, 'utf-8' ) . '\' readOnly="readOnly"/>' . $sShowVars;
                } else {
                    $result[] = '<input class="module_app_input___gray" id="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" name="form[' . $owner->name . '][' . $r . '][' . $this->name . ']" type ="text" size="' . $this->size . '" maxlength="' . $this->maxLength . '" value=\'' . $this->htmlentities( $v, ENT_COMPAT, 'utf-8' ) . '\' />' . $sShowVars;
                }
            } elseif ($this->mode === 'view') {
                if (stristr( $_SERVER['HTTP_USER_AGENT'], 'iPhone' )) {
                    //$result[] = '<div style="overflow:hidden;height:25px;padding:0px;margin:0px;">'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'</div>';
                    $result[] = $this->htmlentities( $v, ENT_COMPAT, 'utf-8' );
                } else {
                    //$result[] = '<div style="overflow:hidden;width:inherit;height:2em;padding:0px;margin:0px;">'.$this->htmlentities( $v , ENT_COMPAT, 'utf-8').'</div>';
                    $result[] = $this->htmlentities( $v, ENT_COMPAT, 'utf-8' );
                }
            } else {
                $result[] = $this->htmlentities( $v, ENT_COMPAT, 'utf-8' );
            }
            $r ++;
        }
        return $result;
    }
}
