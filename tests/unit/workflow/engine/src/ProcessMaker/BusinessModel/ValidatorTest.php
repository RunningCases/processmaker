<?php

namespace ProcessMaker\BusinessModel;

use Exception;
use G;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use ProcessMaker\BusinessModel\Validator;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Department;
use ProcessMaker\Model\Process;
use ProcessMaker\Model\ProcessCategory;
use ProcessMaker\Model\Triggers;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * @coversDefaultClass ProcessMaker\BusinessModel\Validator
 */
class ValidatorTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::depUid()
     * @test
     */
    public function it_test_exception_dep_uid()
    {
        $this->expectException(Exception::class);
        $result = Validator::depUid('');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::depUid()
     * @test
     */
    public function it_test_exception_dep_uid_doesnot_exist()
    {
        $this->expectException(Exception::class);
        $fakeUid = G::generateUniqueID();
        $result = Validator::depUid($fakeUid);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::depUid()
     * @test
     */
    public function it_test_exception_dep_uid_exist()
    {
        $table = factory(Department::class)->create();
        DB::commit();
        $result = Validator::depUid($table->DEP_UID);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::depStatus()
     * @test
     */
    public function it_test_exception_dep_status()
    {
        $result = Validator::depStatus('ACTIVE');
        $this->assertNotEmpty($result);
        $this->expectException(Exception::class);
        $result = Validator::depStatus('OTHER');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::usrUid()
     * @test
     */
    public function it_test_exception_usr_uid()
    {
        $this->expectException(Exception::class);
        $result = Validator::usrUid('');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::usrUid()
     * @test
     */
    public function it_test_exception_usr_uid_doesnot_exist()
    {
        $this->expectException(Exception::class);
        $fakeUid = G::generateUniqueID();
        $result = Validator::usrUid($fakeUid);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::usrUid()
     * @test
     */
    public function it_test_exception_usr_uid_exist()
    {
        $table = factory(User::class)->create();
        DB::commit();
        $result = Validator::usrUid($table->USR_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::appUid()
     * @test
     */
    public function it_test_exception_app_uid()
    {
        $this->expectException(Exception::class);
        $result = Validator::appUid('');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::appUid()
     * @test
     */
    public function it_test_exception_app_uid_not_32()
    {
        $this->expectException(Exception::class);
        $result = Validator::appUid('null');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::appUid()
     * @test
     */
    public function it_test_exception_app_uid_doesnot_exist()
    {
        $this->expectException(Exception::class);
        $fakeUid = G::generateUniqueID();
        $result = Validator::appUid($fakeUid);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::appUid()
     * @test
     */
    public function it_test_exception_app_uid_exist()
    {
        $table = factory(Application::class)->create();
        DB::commit();
        $result = Validator::appUid($table->APP_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::triUid()
     * @test
     */
    public function it_test_exception_tri_uid()
    {
        $this->expectException(Exception::class);
        $result = Validator::triUid('');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::triUid()
     * @test
     */
    public function it_test_exception_tri_uid_doesnot_exist()
    {
        $this->expectException(Exception::class);
        $fakeUid = G::generateUniqueID();
        $result = Validator::triUid($fakeUid);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::triUid()
     * @test
     */
    public function it_test_exception_tri_uid_exist()
    {
        $table = factory(Triggers::class)->create();
        DB::commit();
        $result = Validator::triUid($table->TRI_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::proUid()
     * @test
     */
    public function it_test_exception_pro_uid()
    {
        $this->expectException(Exception::class);
        $result = Validator::proUid('');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::proUid()
     * @test
     */
    public function it_test_exception_pro_uid_doesnot_exist()
    {
        $this->expectException(Exception::class);
        $fakeUid = G::generateUniqueID();
        $result = Validator::proUid($fakeUid);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::proUid()
     * @test
     */
    public function it_test_exception_pro_uid_exist()
    {
        $table = factory(Process::class)->create();
        DB::commit();
        $result = Validator::proUid($table->PRO_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::catUid()
     * @test
     */
    public function it_test_exception_cat_uid()
    {
        $this->expectException(Exception::class);
        $result = Validator::catUid('');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::catUid()
     * @test
     */
    public function it_test_exception_cat_uid_doesnot_exist()
    {
        $this->expectException(Exception::class);
        $fakeUid = G::generateUniqueID();
        $result = Validator::catUid($fakeUid);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::catUid()
     * @test
     */
    public function it_test_exception_cat_uid_exist()
    {
        $table = factory(ProcessCategory::class)->create();
        DB::commit();
        $result = Validator::catUid($table->CATEGORY_UID);
        $this->assertNotEmpty($result);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isDate()
     * @test
     */
    public function it_test_exception_is_date()
    {
        $this->expectException(Exception::class);
        $result = Validator::isDate('');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isDate()
     * @test
     */
    public function it_test_exception_is_date_invalid_format()
    {
        $this->expectException(Exception::class);
        $result = Validator::isDate('15-Feb-2009', 'j-M');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isDate()
     * @test
     */
    public function it_test_exception_is_date_exist()
    {
        $result = Validator::isDate('15-Feb-2009', 'j-M-Y');
        $this->assertNotEmpty($result);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isArray()
     * @test
     */
    public function it_test_exception_is_array()
    {
        $this->expectException(Exception::class);
        $result = Validator::isArray('', 'nameField');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isArray()
     * @test
     */
    public function it_test_is_array()
    {
        $result = Validator::isArray(['hello'], 'nameField');
        $this->assertTrue($result == null);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isString()
     * @test
     */
    public function it_test_exception_is_string()
    {
        $this->expectException(Exception::class);
        $result = Validator::isString(1, 'nameField');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isString()
     * @test
     */
    public function it_test_is_string()
    {
        $result = Validator::isString('hello', 'nameField');
        $this->assertTrue($result == null);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isInteger()
     * @test
     */
    public function it_test_exception_is_integer()
    {
        $this->expectException(Exception::class);
        $result = Validator::isInteger('', 'nameField');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isInteger()
     * @test
     */
    public function it_test_is_integer()
    {
        $result = Validator::isInteger(1, 'nameField');
        $this->assertTrue($result == null);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isBoolean()
     * @test
     */
    public function it_test_exception_is_bool()
    {
        $this->expectException(Exception::class);
        $result = Validator::isBoolean('', 'nameField');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isBoolean()
     * @test
     */
    public function it_test_is_bool()
    {
        $result = Validator::isBoolean(true, 'nameField');
        $this->assertTrue($result == null);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isNotEmpty()
     * @test
     */
    public function it_test_exception_is_not_empty()
    {
        $this->expectException(Exception::class);
        $result = Validator::isNotEmpty('', 'nameField');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isNotEmpty()
     * @test
     */
    public function it_test_is_not_empty()
    {
        $result = Validator::isNotEmpty('fieldValue', 'nameField');
        $this->assertTrue($result == null);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isValidVariableName()
     * @test
     */
    public function it_test_exception_is_valid_variable()
    {
        $this->expectException(Exception::class);
        $result = Validator::isValidVariableName('1field-t');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::isValidVariableName()
     * @test
     */
    public function it_test_is_valid_variable()
    {
        $result = Validator::isValidVariableName('varName');
        $this->assertTrue($result == null);
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::throwExceptionIfDataIsNotArray()
     * @test
     */
    public function it_test_exception_throw_exception_if_data_is_not_array()
    {
        $this->expectException(Exception::class);
        $result = Validator::throwExceptionIfDataIsNotArray('', 'nameField');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::throwExceptionIfDataNotMetIso8601Format()
     * @test
     */
    public function it_test_exception_throw_exception_if_data_not_meet_date_format_string()
    {
        $this->expectException(Exception::class);
        $_SESSION['__SYSTEM_UTC_TIME_ZONE__'] = 1;
        $result = Validator::throwExceptionIfDataNotMetIso8601Format('value', 'dateFrom');
    }

    /**
     * Test the exception
     *
     * @covers \ProcessMaker\BusinessModel\Validator::throwExceptionIfDataNotMetIso8601Format()
     * @test
     */
    public function it_test_exception_throw_exception_if_data_not_meet_date_format_array()
    {
        $_SESSION['__SYSTEM_UTC_TIME_ZONE__'] = 1;
        $data = [];
        $data['dateFrom'] = date('Y-m-d');
        $data['dateTo'] = date('Y-m-d');
        $result = Validator::throwExceptionIfDataNotMetIso8601Format($data, 'dateFrom');
        $this->assertTrue($result == null);
    }
}