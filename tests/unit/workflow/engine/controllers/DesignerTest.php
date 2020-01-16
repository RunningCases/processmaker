<?php

namespace Tests\unit\workflow\engine\controllers;

use Designer;
use G;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\Process;
use Tests\TestCase;

class DesignerTest extends TestCase
{
    /**
     * Tests that the Designer::index() method is not throwing an exception
     *
     * @test
     */
    public function it_should_test_that_the_index_method_is_not_throwing_an_exception()
    {
        //Create the process factory
        $process = factory(Process::class)->create();
        //Create the application factory
        $application = factory(Application::class)->create(
            [
                'APP_PIN' => G::encryptOld('LJ5W'),
            ]
        );

        //Start the session for the user logged
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['CASE'] = $application->APP_NUMBER;
        $_SESSION['PIN'] = "LJ5W";
        $_SESSION['USER_LOGGED'] = '00000000000000000000000000000001';

        session_commit();

        //Create the data sent to the tracker request
        $httpData = (object)[
            "prj_uid" => $process->PRO_UID,
            "prj_readonly" => "true",
            "app_uid" => $application->APP_UID,
            "tracker_designer" => "1"
        ];

        //Create the Designer object
        $object = new Designer();

        //Turn on output buffering
        ob_start();

        //Creates the buildhash file just in case it does not exist
        if (!file_exists(PATH_HTML . "lib/buildhash")) {
            if (!file_exists(PATH_HTML . "lib")) {
                if (!file_exists(PATH_HTML)) {
                    mkdir(PATH_HTML);
                }
                mkdir(PATH_HTML . "lib");
            }
            fopen(PATH_HTML . "lib/buildhash", "w");
        }

        //Call the index method
        $object->index($httpData);

        //Get current buffer contents and delete current output buffer in $res variable
        $res = ob_get_clean();

        //Assert the result does not have errors
        $this->assertNotContains('Call to a member function getUsrUid() on null', $res);
        $this->assertNotContains('Uncaught TypeError: Argument 2 passed to Illumincate\Routing\UrlGenerator::_construct() must be an instance of Illuminate\Http\Request, null given',
            $res);
    }

    /**
     * Tests the Designer::index() method when the user logged is empty
     *
     * @test
     */
    public function it_should_test_the_index_method_when_the_user_logged_is_empty()
    {
        //Create the process factory
        $process = factory(Process::class)->create();
        //Create the application factory
        $application = factory(Application::class)->create(
            [
                'APP_PIN' => G::encryptOld('LJ5W'),
            ]
        );

        $_SESSION['CASE'] = $application->APP_NUMBER;
        $_SESSION['PIN'] = "LJ5W";

        session_commit();

        //Create the data sent to the tracker request
        $httpData = (object)[
            "prj_uid" => $process->PRO_UID,
            "prj_readonly" => "true",
            "app_uid" => $application->APP_UID,
            "tracker_designer" => "1"
        ];

        //Create the Designer object
        $object = new Designer();

        //An exception is expected if the user logged is empty
        $this->expectExceptionMessage("Local Authentication Error, user session is not started.");

        //Call the index method
        $object->index($httpData);
    }
}