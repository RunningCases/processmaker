<?php
namespace ProcessMaker\BusinessModel\Migrator;

// Declare the interface 'iTemplate'
interface Exportable
{
    public function beforeExport();
    public function export();
    public function afterExport();
}
