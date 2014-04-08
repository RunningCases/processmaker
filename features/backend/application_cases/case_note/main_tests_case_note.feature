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
    And the "note_content" property in row 0 equals "tercer case note creado desde script"
    

Scenario: Get a List of cases notes of a case with paged
    Given I request "cases/1185553665335d2e209f723099733152/notes/paged"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    

Scenario Outline: Create a new case note for specified case
    Given POST this data:
        """
        {
            "note_content": "<note_content>",
            "send_email": <send_email>
        }
        """
    And I request "cases/1185553665335d2e209f723099733152/note"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"


    Examples:
    
    | test_description                              | note_content                                                                                                                                           | send_email |
    | Create case note with charater special        | Test!@#$ %^&*£                                                                                                                                         | 1          |
    | Create case note with 150 chacarters          | Este es una prueba con varios caracteres 112334@#$Este es una prueba con varios caracteres 112334@#$Este es una prueba con varios caracteres 112334@#$ | 1          |
    | Create case note with character without space | Estaesunapruebasinespaciosentrepalabraslamismadebeseraceptado                                                                                          | 1          |
    | Create case note without send mail            | Test sin envio de email                                                                                                                                | 0          |
    | Create case normal with character normal      | tercer case note creado desde script                                                                                                                   | 1          |



Scenario: List of case notes for this case
    Given I request "cases/1185553665335d2e209f723099733152/notes"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the "note_content" property in row 0 equals "tercer case note creado desde script"
    And the "note_content" property in row 1 equals "Test sin envio de email"