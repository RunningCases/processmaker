ProcessMakerMichelangelo @RestAPI
Feature: Case Note Negative Tests
Requirements:
    a workspace with three cases of the process "Test Users-Step-Properties End Point"


Background:
    Given that I have a valid access_token


Scenario: create a new case note for specified case (Negative Tests)
        Given POST this data:
            """
            {
                "note_content": "prueba de creacion de un case note, sn permisos para poder enviar",
                "send_email": 1
            }
            """
        And I request "cases/6441974235335ced24785c4035070430/note"
        Then the response status code should be 400
        And the response status message should have the following text "You do not have permission to cases notes"

        



#case 124