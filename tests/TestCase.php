<?php
namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Propel;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

}
