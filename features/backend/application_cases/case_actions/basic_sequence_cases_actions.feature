@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (getCaseInfo, taskCase, newCase, newCaseImpersonate, reassignCase and routeCase)
Requirements:
    a workspace with five cases of the process "Test micheangelo" and "Test Users-Step-Properties End Point"

Background:
    Given that I have a valid access_token

Scenario: Returns information about a given case of the list Inbox
    Given I request "cases/48177942153275bfa28bd04070312685"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the "app_uid" property equals "48177942153275bfa28bd04070312685"
    And the "app_number" property equals 16
    And the "app_name" property equals "#16"
    And the "app_status" property equals "TO_DO"
    And the "app_init_usr_uid" property equals "00000000000000000000000000000001"
    And the "app_init_usr_username" property equals "Administrator"
    And the "pro_uid" property equals "99209594750ec27ea338927000421575"
    And the "pro_name" property equals "Derivation rules - sequential"
    And the "app_create_date" property equals "2014-03-17 16:32:58"
    And the "app_update_date" property equals "2014-03-17 16:33:01"
   

Scenario: Returns the current task for a given case of the list Inbox
    Given I request "cases/48177942153275bfa28bd04070312685/current-task"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the "tas_uid" property equals "73641967750ec281cf015d9009265327"
    And the "tas_title" property equals "Cyclical"
    And the "del_index" property equals "2"


Scenario Outline: Create a new case in workspace with process "Derivation rules - sequential"
        Given POST this data:
            """
            {
                "pro_uid": "99209594750ec27ea338927000421575",
                "tas_uid": "68707275350ec281ada1c95068712556",
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
                "pro_uid": "99209594750ec27ea338927000421575",
                "usr_uid": "24166330352d56730cdd525035621101",
                "tas_uid": "68707275350ec281ada1c95068712556",
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


Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 2 records


Scenario: Reassigns a case to a different user
        Given PUT this data:
            """
            {
                "usr_uid_source": "62625000752d5672d6661e6072881167",
                "usr_uid_target": "24166330352d56730cdd525035621101",
            }
            """
        And that I want to update a resource with the key "case_number" stored in session array
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