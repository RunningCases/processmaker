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
 * XMLDB
 *
 * ProcessMaker Open Source Edition
 *
 * @copyright (C) 2004 - 2008 Colosa Inc.23
 * @package workflow.engine.ProcessMaker
 *
 */class XMLDB
{

    /**
     * &connect
     *
     * @param string $dsn
     * @return array $options
     */
    public function &connect ($dsn, $options = array())
    {
        //Needed for $mysql_real_escape_string
        $mresdbc = new DBConnection();

        if (! file_exists( $dsn )) {
            $err = new DB_Error( "File $dsn not found." );
            return $err;
        }
        $dbc = new XMLConnection( $dsn );
        return $dbc;
    }

    /**
     * isError
     *
     * @param string $result
     * @return boolean is_a($result, 'DB_Error')
     */
    public function isError ($result)
    {
        return is_a( $result, 'DB_Error' );
    }
}
