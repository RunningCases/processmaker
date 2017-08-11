<?php

/**
 * class.wsResponse.php
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

/**
 *
 * @package workflow.engine.classes
 */


/**
 * Class wsGetVariableResponse
 *
 * @package workflow.engine.classes
 */class wsGetVariableResponse
{
    public $status_code = 0;
    public $message = '';
    public $variables = null;
    public $timestamp = '';

    /**
     * Function __construct
     * Constructor of the class
     *
     * @param string $status
     * @param string $message
     * @param string $variables
     * @return void
     */
    function __construct ($status, $message, $variables)
    {
        $this->status_code = $status;
        $this->message = $message;
        $this->variables = $variables;
        $this->timestamp = date( 'Y-m-d H:i:s' );
    }
}
