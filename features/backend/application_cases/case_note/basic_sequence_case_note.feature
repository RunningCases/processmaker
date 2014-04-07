@ProcessMakerMichelangelo @RestAPI
Feature: Cases Notes
Requirements:
    a workspace with one case of the process "Test Case Note"

Background:
    Given that I have a valid access_token


Scenario: List of case notes for this case
    Given I request "cases/1185553665335d2e209f723099733152/notes"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"


Scenario: Get a List of cases notes of a case with paged
    Given I request "cases/1185553665335d2e209f723099733152/notes/paged"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    
 
Scenario: Create a new case note for specified case
        Given POST this data:
            """
            {
                "note_content": "tercer case note creado desde script",
                "send_email": 1
            }
            """
        And I request "cases/1185553665335d2e209f723099733152/note"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"

#case 125