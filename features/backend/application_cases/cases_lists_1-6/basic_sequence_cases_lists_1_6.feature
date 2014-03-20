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


Scenario: Returns information about a given case of the list Inbox
    Given I request "cases/48177942153275bfa28bd04070312685"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the "guid" property equals "48177942153275bfa28bd04070312685"
    And the "name" property equals "16"
    And the "status" property equals "TO_DO"
    And the "delIndex" property equals "2"
    And the "processId" property equals "99209594750ec27ea338927000421575"


Scenario: Returns the current task for a given case of the list Inbox
    Given I request "cases/48177942153275bfa28bd04070312685/current-task"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the "guid" property equals "73641967750ec281cf015d9009265327"
    And the "name" property equals "Cyclical"
    And the "delegate" property equals "2"

    
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


Scenario Outline: Create a new case in workspace with process "Derivation rules - sequential"
        Given POST this data:
            """
            {
                "prj_uid": "99209594750ec27ea338927000421575",
                "act_uid": "68707275350ec281ada1c95068712556",
                "variables": [{"name": "admin", "lastname":"admin"}]
            }
            """
        And I request "cases"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "caseId" in session array as variable "caseId_<case_number>"
        And store "caseNumber" in session array as variable "caseNumber_<case_number>"
        
        Examples:
        | case_number |
        | 1           |



Scenario Outline: Create a new case Impersonate in workspace with process "Derivation rules - sequential"
        Given POST this data:
            """
            {
                "prj_uid": "99209594750ec27ea338927000421575",
                "usr_uid": "24166330352d56730cdd525035621101",
                "act_uid": "68707275350ec281ada1c95068712556",
                "variables": [{"name": "pruebaQA", "amount":"10400"}]
            }
            """
        And I request "cases/impersonate"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "caseId" in session array as variable "caseId_<case_number>"
        And store "caseNumber" in session array as variable "caseNumber_<case_number>"
        
        Examples:
        | case_number |
        | 1           |



Scenario: Reassigns a case to a different user
        Given PUT this data:
            """
            {
                "del_index": "53643749052af5bdef3ca79050192707",
                "usr_uid_source": "62625000752d5672d6661e6072881167",
                "usr_uid_target": "24166330352d56730cdd525035621101"
            }
            """
        And that I want to update a resource with the key "" stored in session array
        And I request "case/{uid}/reassign-case"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"




Scenario: Autoderivate a case to the next task in the process
        Given PUT this data:
            """
            {
                "case_uid": "78ef3ca7905019270643749052af5bd7",
                "del_index": "1"
            }
            """
        And that I want to update a resource with the key "" stored in session array
        And I request "case/{uid}/route-case"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        