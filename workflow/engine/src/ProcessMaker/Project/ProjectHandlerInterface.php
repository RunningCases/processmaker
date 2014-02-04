<?php
namespace ProcessMaker\Project;

interface ProjectHandlerInterface
{
    public function save();
    public function update();
    public function delete();
    public static function load();
}