<?php
namespace ProcessMaker\BusinessModel\Migrator;

// Declare the interface 'Exportable'
interface Exportable
{
    public function beforeExport();
    public function export($prj_uid);
    public function afterExport();
}
