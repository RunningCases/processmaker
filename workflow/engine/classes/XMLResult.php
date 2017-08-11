<?php

/**
 * class.xmlDb.php
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
 * XMLDB
 *
 * ProcessMaker Open Source Edition
 *
 * @copyright (C) 2004 - 2008 Colosa Inc.23
 * @package workflow.engine.ProcessMaker
 *
 */

/**
 * XMLResult
 *
 * ProcessMaker Open Source Edition
 *
 * @copyright (C) 2004 - 2008 Colosa Inc.23
 * @package workflow.engine.ProcessMaker
 *
 */class XMLResult
{
    var $result = array ();
    var $cursor = 0;

    /**
     * XMLResult
     *
     * @param array $result
     * @return void
     */
    public function XMLResult ($result = array())
    {
        $this->result = $result;
        $this->cursor = 0;
    }

    /**
     * numRows
     *
     * @return integer sizeof($this->result)
     */
    public function numRows ()
    {
        return sizeof( $this->result );
    }

    /**
     * fetchRow
     *
     * @param string $const
     * @return integer $this->result[ $this->cursor-1 ];
     */
    public function fetchRow ($const)
    {
        if ($this->cursor >= $this->numRows()) {
            return null;
        }
        $this->cursor ++;
        return $this->result[$this->cursor - 1];
    }
}
