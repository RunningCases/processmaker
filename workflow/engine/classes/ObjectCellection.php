<?php
/**
 * class.processes.php
 *
 * @package workflow.engine.ProcessMaker
 *
 * ProcessMaker Open Source Edition
 * Copyright (C) 2004 - 2008 Colosa Inc.23
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
 * ObjectDocument Collection
 *
 * @package workflow.engine.ProcessMaker
 */class ObjectCellection
{
    public $num;
    public $swapc;
    public $objects;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->objects = Array();
        $this->num = 0;
        $this->swapc = $this->num;
        array_push($this->objects, 'void');
    }

    /**
     * add in the collecetion a new object Document
     *
     * @param $name name object document
     * @param $type type object document
     * @param $data data object document
     * @param $origin origin object document
     * @return void
     */
    public function add($name, $type, $data, $origin)
    {
        $o = new ObjectDocument();
        $o->name = $name;
        $o->type = $type;
        $o->data = $data;
        $o->origin = $origin;

        $this->num++;
        array_push($this->objects, $o);
        $this->swapc = $this->num;
    }

    /**
     * get the collection of ObjectDocument
     *
     * @param $name name object document
     * @param $type type object document
     * @param $data data object document
     * @param $origin origin object document
     * @return void
     */
    public function get()
    {
        if ($this->swapc > 0) {
            $e = $this->objects[$this->swapc];
            $this->swapc--;
            return $e;
        } else {
            $this->swapc = $this->num;
            return false;
        }
    }
}
