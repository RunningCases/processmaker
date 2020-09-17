<?php

namespace Tests\unit\workflow\engine\src\ProcessMaker\Services\Api;

use Luracast\Restler\Data\ApiMethodInfo;
use Luracast\Restler\Defaults;
use Luracast\Restler\HumanReadableCache;
use Luracast\Restler\RestException;
use Maveriks\Extension\Restler;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\User;
use ProcessMaker\Services\Api\Cases;
use RBAC;
use ReflectionClass;
use Tests\TestCase;

class CasesTest extends TestCase
{

    /**
     * Initialize Rest API.
     * @param string $userUid
     * @return Restler
     */
    private function initializeRestApi(string $userUid)
    {
        //server
        $reflection = new ReflectionClass('\ProcessMaker\Services\OAuth2\Server');

        $reflectionPropertyUserId = $reflection->getProperty('userId');
        $reflectionPropertyUserId->setAccessible(true);
        $reflectionPropertyUserId->setValue($userUid);

        $reflectionPropertyDSN = $reflection->getProperty('dsn');
        $reflectionPropertyDSN->setAccessible(true);
        $reflectionPropertyDSN->setValue('mysql:host=' . env('DB_HOST') . ';dbname=' . env('DB_DATABASE'));

        $reflectionPropertyUserName = $reflection->getProperty('dbUser');
        $reflectionPropertyUserName->setAccessible(true);
        $reflectionPropertyUserName->setValue(env('DB_USERNAME'));

        $reflectionPropertyPassword = $reflection->getProperty('dbPassword');
        $reflectionPropertyPassword->setAccessible(true);
        $reflectionPropertyPassword->setValue(env('DB_PASSWORD'));

        //application
        Defaults::$cacheDirectory = PATH_DB . config('system.workspace') . PATH_SEP;
        HumanReadableCache::$cacheDir = PATH_DB . config('system.workspace') . PATH_SEP;

        $rest = new Restler(true);
        $rest->setFlagMultipart(false);
        $rest->setAPIVersion('1.0');
        $rest->addAuthenticationClass('ProcessMaker\\Services\\OAuth2\\Server', '');
        $rest->addAuthenticationClass('ProcessMaker\\Policies\\AccessControl');
        $rest->addAuthenticationClass('ProcessMaker\\Policies\\ControlUnderUpdating');

        $rest->apiMethodInfo = new ApiMethodInfo();
        return $rest;
    }

    /**
     * This test verify isAllowed method expecting RestException.
     * @test
     * @covers ProcessMaker\Services\Api\Cases::__isAllowed
     */
    public function it_should_test_isAllowed_method_try_exception()
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();
        $rest = $this->initializeRestApi($user->USR_UID);

        //assert exception
        $this->expectException(RestException::class);

        $cases = new Cases();
        $cases->parameters = [];
        $cases->__isAllowed();
    }

    /**
     * This test verify isAllowed method doGetCaseVariables option.
     * @test
     * @covers ProcessMaker\Services\Api\Cases::__isAllowed
     */
    public function it_should_test_isAllowed_method_doGetCaseVariables_option()
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();
        $process = factory(\ProcessMaker\Model\Process::class)->create();
        $task = factory(\ProcessMaker\Model\Task::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID
        ]);
        $dynaform = factory(\ProcessMaker\Model\Dynaform::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $application = factory(\ProcessMaker\Model\Application::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID
        ]);
        $delegation = factory(\ProcessMaker\Model\Delegation::class)->create([
            'APP_UID' => $application->APP_UID,
            'APP_NUMBER' => $application->APP_NUMBER,
            'DEL_INDEX' => 1,
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID,
            'TAS_UID' => $task->TAS_UID,
            'TAS_ID' => $task->TAS_ID,
            'USR_UID' => $user->USR_UID,
            'USR_ID' => $user->USR_ID
        ]);

        $rest = $this->initializeRestApi($user->USR_UID);
        $rest->apiMethodInfo->methodName = 'doGetCaseVariables';
        $rest->apiMethodInfo->arguments = [
            'app_uid' => 0,
            'dyn_uid' => 1,
            'app_index' => 2
        ];

        //assert
        $cases = new Cases();
        $cases->parameters = [
            $application->APP_UID,
            $dynaform->DYN_UID,
            1
        ];
        $cases->restler = $rest;
        $expected = $cases->__isAllowed();

        $this->assertTrue($expected);
    }

    /**
     * This test verify isAllowed method doGetCaseVariables option with delegation user.
     * @test
     * @covers ProcessMaker\Services\Api\Cases::__isAllowed
     */
    public function it_should_test_isAllowed_method_doGetCaseVariables_option_without_delegation_user()
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();
        $process = factory(\ProcessMaker\Model\Process::class)->create();
        $task = factory(\ProcessMaker\Model\Task::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID
        ]);
        $dynaform = factory(\ProcessMaker\Model\Dynaform::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $application = factory(\ProcessMaker\Model\Application::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID
        ]);

        $rest = $this->initializeRestApi($user->USR_UID);
        $rest->apiMethodInfo->methodName = 'doGetCaseVariables';
        $rest->apiMethodInfo->arguments = [
            'app_uid' => 0,
            'dyn_uid' => 1,
            'app_index' => 2
        ];

        //assert
        $cases = new Cases();
        $cases->parameters = [
            $application->APP_UID,
            $dynaform->DYN_UID,
            1
        ];
        $cases->restler = $rest;
        $expected = $cases->__isAllowed();

        $this->assertFalse($expected);
    }

    /**
     * This test verify isAllowed method doGetCaseVariables option with guest user.
     * @test
     * @covers ProcessMaker\Services\Api\Cases::__isAllowed
     */
    public function it_should_test_isAllowed_method_doGetCaseVariables_option_with_guest_user()
    {
        $user = factory(\ProcessMaker\Model\User::class)->create();
        $process = factory(\ProcessMaker\Model\Process::class)->create();
        $task = factory(\ProcessMaker\Model\Task::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'PRO_ID' => $process->PRO_ID
        ]);
        $dynaform = factory(\ProcessMaker\Model\Dynaform::class)->create([
            'PRO_UID' => $process->PRO_UID
        ]);
        $application = factory(\ProcessMaker\Model\Application::class)->create([
            'PRO_UID' => $process->PRO_UID,
            'APP_INIT_USER' => $user->USR_UID,
            'APP_CUR_USER' => $user->USR_UID
        ]);

        $rest = $this->initializeRestApi(RBAC::GUEST_USER_UID);
        $rest->apiMethodInfo->methodName = 'doGetCaseVariables';
        $rest->apiMethodInfo->arguments = [
            'app_uid' => 0,
            'dyn_uid' => 1,
            'app_index' => 2
        ];

        //assert
        $cases = new Cases();
        $cases->parameters = [
            $application->APP_UID,
            $dynaform->DYN_UID,
            1
        ];
        $cases->restler = $rest;
        $expected = $cases->__isAllowed();

        $this->assertTrue($expected);
    }

    /**
     * Test the uploadDocumentToCase method
     * 
     * @covers ProcessMaker\Services\Api\Cases::uploadDocumentToCase
     * @test
     */
    public function test_upload_document_to_case_method()
    {
        $user = factory(User::class)->create();
        $application = factory(Application::class)->create([
            'APP_CUR_USER' => $user->USR_UID
        ]);
        $delegation = factory(Delegation::class)->create([
            'APP_UID' => $application->APP_UID
        ]);
        $varName = "/tmp/test.pdf";

        $varName = "/tmp/test.pdf";
        fopen($varName, "w");
        $_FILES = ["form" =>
        [
            "name" => ["test"],
            "type" => ["application/pdf"],
            "tmp_name" => ["/tmp/test.pdf"],
            "error" => [0],
            "size" => [0]
        ]];

        $case = new Cases();

        //Call the uploadDocumentToCase method without a post delindex
        $res = $case->uploadDocumentToCase($application->APP_UID, $varName);
        //Asserts the result is not empty
        $this->assertNotEmpty($res);
        $_POST['delIndex'] = $delegation->DEL_INDEX;
        //Call the uploadDocumentToCase method with a post delindex
        $res = $case->uploadDocumentToCase($application->APP_UID, $varName, -1, null, $delegation->DEL_INDEX);
        //Asserts the result is not empty
        $this->assertNotEmpty($res);
    }

    /**
     * Test the exception in the uploadDocumentToCase method
     * 
     * @covers ProcessMaker\Services\Api\Cases::uploadDocumentToCase
     * @test
     */
    public function test_exception_upload_document_to_case_method()
    {
        $user = factory(User::class)->create();
        $application = factory(Application::class)->create([
            'APP_CUR_USER' => $user->USR_UID
        ]);
        $delegation = factory(Delegation::class)->create([
            'APP_UID' => $application->APP_UID
        ]);
        $varName = "/tmp/test.pdf";

        $varName = "/tmp/test.pdf";
        fopen($varName, "w");
        $_FILES = [];

        $case = new Cases();

        //Asserts the expected exception
        $this->expectExceptionMessage("**ID_ERROR_UPLOAD_FILE_CONTACT_ADMINISTRATOR**");
        //Call the uploadDocumentToCase method without a post delindex
        $res = $case->uploadDocumentToCase($application->APP_UID, $varName);
    }
}
