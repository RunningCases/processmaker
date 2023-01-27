<?php

namespace Tests\unit\workflow\engine\classes\model;

use AppNotes as ModelAppNotes;
use Exception;
use Faker\Factory;
use ProcessMaker\Model\Application;
use ProcessMaker\Model\AppMessage;
use ProcessMaker\Model\AppNotes;
use ProcessMaker\Model\Delegation;
use ProcessMaker\Model\Documents;
use ProcessMaker\Model\EmailServerModel;
use ProcessMaker\Model\User;
use Tests\TestCase;

/**
 * Class AppNotesTest
 *
 * @coversDefaultClass AppNotes
 */
class AppNotesTest extends TestCase
{
    private $faker;

    /**
     * Set up method
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->faker = Factory::create();
    }

    /**
     * It test the cases notes creation
     * 
     * @test
     */
    public function it_test_case_notes_creation()
    {
        $application = Application::factory()->create();
        $user = User::factory()->create();
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
        $application = Application::factory()->create();
        $user = User::factory()->create();
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
        $application = Application::factory()->create();
        $user = User::factory()->create();
        Delegation::factory()->create([
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

    /**
     * This test verifies the sending of the notification note with Exception.
     * @test
     * @covers \AppNotes::sendNoteNotification
     */
    public function it_should_test_send_note_notification_with_exception()
    {
        //assert
        $this->expectException(Exception::class);

        $appNotes = new ModelAppNotes();
        $appNotes->sendNoteNotification(null, null, null, null, null, null);
    }

    /**
     * This test verifies the sending of the notification note.
     * @test
     * @covers \AppNotes::sendNoteNotification
     */
    public function it_should_test_send_note_notification_without_user()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->first();
        $application = Application::factory()->create();
        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'USR_UID' => $user->USR_UID
        ]);

        $params = [
            $application->APP_UID,
            '',
            '',
            $user->USR_UID,
            $this->faker->email,
            $delegation->DEL_INDEX
        ];
        $appNotes = new ModelAppNotes();
        $appNotes->sendNoteNotification(...$params);

        //assert
        $appMessage = AppMessage::where('APP_UID', '=', $application->APP_UID)->first()->toArray();
        $this->assertArrayHasKey('APP_UID', $appMessage);
        $this->assertEquals($appMessage['APP_UID'], $application->APP_UID);
    }

    /**
     * This test verifies the sending of the notification note with attach files.
     * @test
     * @covers \AppNotes::sendNoteNotification
     */
    public function it_should_test_send_note_notification_with_attach_files()
    {
        $user = User::where('USR_UID', '=', '00000000000000000000000000000001')
                ->first();
        $application = Application::factory()->create();
        $delegation = Delegation::factory()->create([
            'APP_UID' => $application->APP_UID,
            'USR_UID' => $user->USR_UID
        ]);
        $appNote = AppNotes::factory()->create();
        $appDocument = Documents::factory()->create([
            'APP_UID' => $application->APP_UID,
            'DOC_ID' => $appNote->NOTE_ID
        ]);
        EmailServerModel::factory()->create([
            'MESS_DEFAULT' => 1
        ]);

        $params = [
            $application->APP_UID,
            $user->USR_UID,
            '',
            $user->USR_UID,
            $this->faker->email,
            $delegation->DEL_INDEX
        ];
        $appNotes = new ModelAppNotes();
        $appNotes->sendNoteNotification(...$params);

        //assert
        $appMessage = AppMessage::where('APP_UID', '=', $application->APP_UID)->first()->toArray();
        $this->assertArrayHasKey('APP_UID', $appMessage);
        $this->assertEquals($appMessage['APP_UID'], $application->APP_UID);
    }

    /**
     * This test verify if exists attachment files.
     * @test
     * @covers \AppNotes::getAttachedFilesFromTheCaseNote
     */
    public function it_should_test_get_attached_files_from_the_casenote()
    {
        $appNote = AppNotes::factory()->create();
        $appDocument = Documents::factory()->create([
            'DOC_ID' => $appNote->NOTE_ID
        ]);

        $appNotes = new ModelAppNotes();
        $result = $appNotes->getAttachedFilesFromTheCaseNote($appNote->NOTE_ID, $appDocument->APP_UID);

        $this->assertNotEmpty($result);
    }
}
