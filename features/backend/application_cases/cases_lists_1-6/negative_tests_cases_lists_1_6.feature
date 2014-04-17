@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (inbox, draftCaseList, participatedCaseList, unassignedCaseList, pausedCaseList and advanced Search) Negative Tests

Background:
    Given that I have a valid access_token

    
Scenario: Get paging of list inbox (Negative Test)
    Given I request "cases/pawdd?start=4&limit=10"
    Then the response status code should be 400
    And the response status message should have the following text "app_uid"


Scenario: Get paging of list inbox (Negative Test)
    Given I request "cases/draft/pawdd?start=4&limit=10"
    Then the response status code should be 404
    And the response status message should have the following text "Not Found"


Scenario: Get paging of list inbox (Negative Test)
    Given I request "cases/participated/pawdd?start=4&limit=10"
    Then the response status code should be 404
    And the response status message should have the following text "Not Found"


Scenario: Get paging of list inbox (Negative Test)
    Given I request "cases/unassigned/pawdd?start=4&limit=10"
    Then the response status code should be 404
    And the response status message should have the following text "Not Found"


Scenario: Get paging of list inbox (Negative Test)
    Given I request "cases/paused/pawdd?start=4&limit=10"
    Then the response status code should be 404
    And the response status message should have the following text "Not Found"


Scenario: Get paging of list inbox (Negative Test)
    Given I request "cases/advanced-search/pawdd?start=4&limit=10"
    Then the response status code should be 404
    And the response status message should have the following text "Not Found"