@ProcessMakerMichelangelo @RestAPI
Feature: Cases Notes
Requirements:
    a workspace with one case of the process "Test Case Note"

Background:
    Given that I have a valid access_token


Scenario: List of case notes for this case
    Given I request "cases/{uid}/notes"
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
        And I request "case/{uid}/case"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"