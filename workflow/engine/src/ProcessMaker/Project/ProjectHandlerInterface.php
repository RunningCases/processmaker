<?php
namespace ProcessMaker\Project;

interface ProjectHandlerInterface
{
    public function create($data);
    public function update($prjUid, $data);
    public function delete($prjUid);
    public function load($prjUid);
}