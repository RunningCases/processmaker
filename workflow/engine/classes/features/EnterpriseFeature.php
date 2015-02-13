<?php

namespace Features\ActionsByEnmail;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 *
 * @author gustav
 */
interface EnterpriseFeature 
{
    public function setup();
    public function install();
    public function enable();
    public function disable();
}
