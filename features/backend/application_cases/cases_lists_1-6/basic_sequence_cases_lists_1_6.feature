@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (inbox, draftCaseList, participatedCaseList, unassignedCaseList, pausedCaseList and advanced Search) and (getCaseInfo, taskCase, newCase, newCaseImpersonate, reassignCase and routeCase)
Requirements:
    a workspace with five of the process "Derivation rules - evaluation", "Derivation rules - Parallel", "Derivation rules - parallel evaluation", "Derivation rules - selection", "Derivation rules - sequential"
    
Background:
    Given that I have a valid access_token


Scenario: Returns a list of the cases for the logged in user (Inbox)
    Given I request "cases"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 1 records


Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 1 records


Scenario: Returns a list of the cases for the logged in user (Participated)
    Given I request "cases/participated"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 6 records


Scenario: Returns a list of the cases for the logged in user (Unassigned)
    Given I request "cases/unassigned"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 1 records


Scenario: Returns a list of the cases for the logged in user (Paused)
    Given I request "cases/paused"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 1 records


Scenario: Returns a list of the cases for the logged in user (Advanced Search)
    Given I request "cases/advanced-search"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 1 records