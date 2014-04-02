@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (getCaseInfo, taskCase, newCase, newCaseImpersonate, reassignCase, routeCase, cancelCase, pauseCase, unpauseCase, executeTrigger, DELETE Case)
Requirements:
    a workspace with 57 cases distributed in the processes "Derivation rules - evaluation", "Derivation rules - Parallel", "Derivation rules - parallel evaluation", "Derivation rules - selection", "Derivation rules - sequential"

Background:
    Given that I have a valid access_token

#Listado de casos
Scenario: Returns a list of the cases for the logged in user (Inbox)
    Given I request "cases"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 14 records


Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 15 records


Scenario: Returns a list of the cases for the logged in user (Participated)
    Given I request "cases/participated"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 30 records


Scenario: Returns a list of the cases for the logged in user (Unassigned)
    Given I request "cases/unassigned"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 12 records


Scenario: Returns a list of the cases for the logged in user (Paused)
    Given I request "cases/paused"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 12 records


Scenario: Returns information about a given case of the list Inbox of process "Derivation rules - Parallel"
    Given I request "cases/220090038533b0c40688174019225585"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the "app_uid" property equals "220090038533b0c40688174019225585"
    And the "app_number" property equals 137
    And the "app_name" property equals "#137"
    And the "app_status" property equals "TO_DO"
    
   
Scenario: Returns the current task for a given case of the list Inbox, case 167 of process "derivation rules - sequencial" 
    Given I request "cases/356811158533b13641ef789000630231/current-task"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the "tas_uid" property equals "73641967750ec281cf015d9009265327"
    And the "tas_title" property equals "Cyclical"
    And the "del_index" property equals "2"


Scenario Outline: Create a new case in workspace with process "Derivation rules - sequential"
        Given POST this data:
            """
            {
                "pro_uid": "<pro_uid>",
                "tas_uid": "<tas_uid>",
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
        | Description             | case_number | pro_uid                          | tas_uid                          |
        | Create case 16 in draft | 1           | 99209594750ec27ea338927000421575 | 68707275350ec281ada1c95068712556 |
        | Create case 17 in draft | 2           | 99209594750ec27ea338927000421575 | 68707275350ec281ada1c95068712556 |


Scenario Outline: Create a new case Impersonate in workspace with process "Derivation rules - sequential"
        Given POST this data:
            """
            {
                "pro_uid": "99209594750ec27ea338927000421575",
                "usr_uid": "<usr_uid>",
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
        | Description                        | case_number | usr_uid                          |
        | Create new case 18 with user chris | 3           | 24166330352d56730cdd525035621101 |
        | Create new case 18 with user adam  | 4           | 44811996752d567110634a1013636964 |


Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 18 records


Scenario Outline: Reassigns a case to a different user, from user "administrator" to user "aaron"
        Given PUT this data:
            """
            {
                "usr_uid_source": "00000000000000000000000000000001",
                "usr_uid_target": "51049032352d56710347233042615067",
            }
            """
        And I request "case/<case_number>/reassign-case"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        And that I want to update a resource with the key "case_number" stored in session array as variable "caseNumber_<case_number>"
        And that I want to update a resource with the key "caseId" stored in session array as variable "caseId_<case_number>" 

        Examples:

        | test_description                       | case_number |
        | Reassig case 1, created in this script | 1           |


Scenario: Route a case to the next task in the process
        Given PUT this data:
            """
            {
                "case_uid": "<case_number>",
                "del_index": "1"
            }
            """
        And I request "cases/<case_number>/route-case"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        And that I want to update a resource with the key "case_number" stored in session array as variable "caseNumber_<case_number>"
        And that I want to update a resource with the key "caseId" stored in session array as variable "caseId_<case_number>"   


        Examples:

        | test_description                                                  | case_number |
        | Derivate case 2 to inbox of Administrator, created in this script | 2           | 



Scenario: Cancel a case
        Given PUT this data:
            """
            {
                
            }
            """
        And I request "cases/<app_uid>/cancel"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        And that I want to update a resource with the key "case_number" stored in session array as variable "caseNumber_<case_number>"
        And that I want to update a resource with the key "caseId" stored in session array as variable "caseId_<case_number>"   


        Examples:

        | test_description                      | case_number |
        | Cancel case 3, created in this script | 3           |


Scenario: Pause a case
        Given PUT this data:
            """
            {
              "unpaused_date": "2016-12-12"  
            }
            """
        And I request "cases/<app_uid>/pause"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        And that I want to update a resource with the key "case_number" stored in session array as variable "caseNumber_<case_number>"
        And that I want to update a resource with the key "caseId" stored in session array as variable "caseId_<case_number>"   


        Examples:

        | test_description                      | case_number |
        | Pause case 4, created in this script  | 4           |


Scenario: Unpause a case
        Given PUT this data:
            """
            {
                
            }
            """
        And I request "cases/<app_uid>/unpause"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        And that I want to update a resource with the key "case_number" stored in session array as variable "caseNumber_<case_number>"
        And that I want to update a resource with the key "caseId" stored in session array as variable "caseId_<case_number>"   


        Examples:

        | test_description                       | case_number |
        | Unpause case 4, created in this script | 4           |


Scenario: Executes a ProcessMaker trigger for a case
        Given PUT this data:
            """
            {
                
            }
            """
        And I request "cases/<app_uid>/execute-trigger/{tri_uid}"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        And that I want to update a resource with the key "case_number" stored in session array as variable "caseNumber_<case_number>"
        And that I want to update a resource with the key "caseId" stored in session array as variable "caseId_<case_number>"   


        Examples:

        | test_description                       | case_number |
        |                                        |             |


Scenario: Delete a case
        Given PUT this data:
            """
            {
                
            }
            """
        And I request "cases/<app_uid>"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        And that I want to update a resource with the key "case_number" stored in session array as variable "caseNumber_<case_number>"
        And that I want to update a resource with the key "caseId" stored in session array as variable "caseId_<case_number>"   


        Examples:

        | test_description                        | case_number |
        | Delete a case 1, created in this script | 1           |
        | Delete a case 2, created in this script | 2           |       
        | Delete a case 3, created in this script | 3           |
        | Delete a case 4, created in this script | 4           |


#Listado de casos
Scenario: Returns a list of the cases for the logged in user (Inbox)
    Given I request "cases"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 14 records


Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 15 records


Scenario: Returns a list of the cases for the logged in user (Participated)
    Given I request "cases/participated"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 30 records


Scenario: Returns a list of the cases for the logged in user (Unassigned)
    Given I request "cases/unassigned"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 12 records


Scenario: Returns a list of the cases for the logged in user (Paused)
    Given I request "cases/paused"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 12 records