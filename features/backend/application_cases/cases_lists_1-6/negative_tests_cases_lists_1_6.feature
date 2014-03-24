@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (inbox, draftCaseList, participatedCaseList, unassignedCaseList, pausedCaseList and advanced Search) Negative Tests

Background:
    Given that I have a valid access_token


Scenario: Returns a list of the cases for the logged in user (Inbox)
    Given I request "cases"
    Then the response status code should be 400
    And the response has 4 records
    And the response status message should have the following text "<records>"
    

Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 400
    And the response has 4 records
    And the response status message should have the following text "<records>"


Scenario: Returns a list of the cases for the logged in user (Participated)
    Given I request "cases/participated"
    Then the response status code should be 400
    And the response has 4 records
    And the response status message should have the following text "<records>"


Scenario: Returns a list of the cases for the logged in user (Unassigned)
    Given I request "cases/unassigned"
    Then the response status code should be 400
    And the response has 4 records
    And the response status message should have the following text "<records>"


Scenario: Returns a list of the cases for the logged in user (Paused)
    Given I request "cases/paused"
    Then the response status code should be 400
    And the response has 4 records
    And the response status message should have the following text "<records>"

Scenario: Returns a list of the cases for the logged in user (Advanced Search)
    Given I request "cases/advanced-search"
    Then the response status code should be 400
    And the response has 4 records
    And the response status message should have the following text "<records>"