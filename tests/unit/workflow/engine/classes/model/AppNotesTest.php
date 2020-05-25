<?php

namespace Tests\unit\workflow\engine\classes\model;

use AppNotes as ModelAppNotes;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\AppMessage;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppNotes;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class AppNotesTest
 *
 * @coversDefaultClass AppNotes
 */
class AppNotesTest extends TestCase
{
    /**
     * It test the cases notes creation
     * 
     * @test
     */
    public function it_test_case_notes_creation() 
    {
        $application = factory(Application::class)->create();
        $user = factory(User::class)->create();
        $reason = "The case was canceled due to:";
        $appNotes = new ModelAppNotes();
        $noteContent = addslashes($reason);
        $appNotes->postNewNote(
            $application->APP_UID, $user->USR_UID, $noteContent, false
        );

        // Query to get the cases notes
        $query = AppNotes::query();
        $query->select()->where('APP_UID', $application->APP_UID)->where('USR_UID', $user->USR_UID);
        $result = $query->get()->values()->toArray();
        $this->assertNotEmpty($result);       
    }

    /**
     * It test the cases notes creation and send a email to specific user
     * 
     * @test
     */
    public function it_test_case_notes_creation_and_send_email_to_user() 
    {
        $application = factory(Application::class)->create();
        $user = factory(User::class)->create();
        $reason = "The case was canceled due to:";
        $appNotes = new ModelAppNotes();
        $noteContent = addslashes($reason);
        $appNotes->postNewNote(
            $application->APP_UID, $user->USR_UID, $noteContent, true, 'PUBLIC', $user->USR_UID
        );

        // Query to get the cases notes
        $query = AppNotes::query();
        $query->select()->where('APP_UID', $application->APP_UID)->where('USR_UID', $user->USR_UID);
        $result = $query->get()->values()->toArray();
        $this->assertNotEmpty($result);

        // Query to get the emails
        $query = AppMessage::query();
        $query->select()->where('APP_UID', $application->APP_UID)->where('APP_MSG_TYPE', 'CASE_NOTE');
        $result = $query->get()->values()->toArray();
        $this->assertNotEmpty($result);
    }

    /**
     * It test the cases notes creation and send a email to user with participaion in the case
     * 
     * @test
     */
    public function it_test_case_notes_creation_and_send_email() 
    {
        $application = factory(Application::class)->create();
        $user = factory(User::class)->create();
        factory(Delegation::class)->create([
            'APP_UID' => $application->APP_UID,
            'USR_UID' => $user->USR_UID
        ]);
        $reason = "The case was canceled due to:";
        $appNotes = new ModelAppNotes();
        $noteContent = addslashes($reason);
        $appNotes->postNewNote(
            $application->APP_UID, $user->USR_UID, $noteContent, true, 'PUBLIC'
        );

        // Query to get the cases notes
        $query = AppNotes::query();
        $query->select()->where('APP_UID', $application->APP_UID)->where('USR_UID', $user->USR_UID);
        $result = $query->get()->values()->toArray();
        $this->assertNotEmpty($result);

        // Query to get the emails
        $query = AppMessage::query();
        $query->select()->where('APP_UID', $application->APP_UID)->where('APP_MSG_TYPE', 'CASE_NOTE');
        $result = $query->get()->values()->toArray();
        $this->assertNotEmpty($result);
    }
}