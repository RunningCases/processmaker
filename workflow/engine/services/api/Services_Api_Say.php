<?php
/**
 * Created by JetBrains PhpStorm.
 * User: erik
 * Date: 8/21/13
 * Time: 4:55 PM
 * To change this template use File | Settings | File Templates.
 */

class Services_Api_Say
{
    public function hello($to='world') 
    {
        return array('success'=>true, "message"=>"Hello $to!");
    }
    
    public function hi($to, $name) 
    {
        return  "Hi $to -> $name";
    }
}