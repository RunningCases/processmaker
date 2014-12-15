@ProcessMakerMichelangelo @RestAPI
Feature: Case Tasks Negative Tests
Requirements:
    a workspace with the case 383652497533b1492846753088523464 "case #174" and 587530805533b0d5031bd35011041644 "case #146"  of the process ("Test Case Variables and Derivation rules-selection") already loaded
    
Background:
    Given that I have a valid access_token

Scenario: Get list case tasks of case 174
    Given I request "cases/38365249000000000046753088523464/tasks"
    Then the response status code should be 400
    And the response status message should have the following text "app_uid"


Scenario: Get list case tasks of case 174
    Given I request "cases//tasks"
    Then the response status code should be 400
    And the response status message should have the following text "app_uid"