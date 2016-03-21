<?php
namespace ProcessMaker\BusinessModel\Migrator;

// Declare the interface 'Importable'
interface Importable
{
    public function beforeImport($data);
    public function import($data);
    public function afterImport($data);
}
