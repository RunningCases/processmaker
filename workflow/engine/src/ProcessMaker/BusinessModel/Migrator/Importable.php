<?php
namespace ProcessMaker\BusinessModel\Migrator;

// Declare the interface 'Importable'
interface Importable
{
    public function beforeImport($data);
    public function import($data, $replace);
    public function afterImport($data);
}
