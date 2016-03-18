<?php
namespace ProcessMaker\BusinessModel\Migrator;

// Declare the interface 'iTemplate'
interface Importable
{
    public function beforeImport($data);
    public function import($data);
    public function afterImport($data);
}
