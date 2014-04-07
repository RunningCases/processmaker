@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (getCaseInfo, taskCase, newCase, newCaseImpersonate, reassignCase and routeCase)
Requirements:
    a workspace with five cases of the process "Test micheangelo" and "Test Users-Step-Properties End Point"

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
    And the type is "array"
    And the "app_uid" property equals "220090038533b0c40688174019225585"
    And the "app_number" property equals 137
    And the "app_name" property equals "#137"
    And the "app_status" property equals "TO_DO"
    And the "app_init_usr_uid" property equals "00000000000000000000000000000001"
    And the "app_init_usr_username" property equals "Administrator"
    And the "pro_uid" property equals "35894775350ec7daa099378048029617"
    And the "pro_name" property equals "Derivation rules - Parallel"
    And the "app_create_date" property equals "2014-04-01 14:58:08"
    And the "app_update_date" property equals "2014-04-01 14:58:20"
   

Scenario: Returns the current task for a given case of the list Inbox
    Given I request "cases/48177942153275bfa28bd04070312685/current-task"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the "tas_uid" property equals "73641967750ec281cf015d9009265327"
    And the "tas_title" property equals "Cyclical"
    And the "del_index" property equals "2"


Scenario Outline: Create a new case in workspace with process "Derivation rules - sequential", "Derivation rules - evaluation", "Derivation rules - Parallel", "Derivation rules - parallel evaluation", "Derivation rules - selection"
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
    And store "app_uid" in session array as variable "app_uid_<app_uid_number>"
    And store "app_number" in session array as variable "app_number_<app_uid_number>"
    
    Examples:
    | Description                                                           | app_uid_number | pro_uid                          | tas_uid                          | variables |
    | Create new case with process "Derivation rules - sequential"          | 1              | 99209594750ec27ea338927000421575 | 68707275350ec281ada1c95068712556 |           |
    | Create new case with process "Derivation rules - evaluation"          | 2              | 46279907250ec73b9b25a78031279680 | 99371337850ec73c0a38eb6024620271 |           |
    | Create new case with process "Derivation rules - Parallel"            | 3              | 35894775350ec7daa099378048029617 | 52838134750ec7dd0989fc0015625952 |           |
    | Create new case with process "Derivation rules - parallel evaluation" | 4              | 34579467750ec8d55e8b115057818502 | 89648437550ec8d593c2159010276089 |           |
    | Create new case with process "Derivation rules - selection"           | 5              | 82458496050ec668981ecc7039804404 | 56900024450ec668e4a9243080698854 |           |



Scenario Outline: Create a new case Impersonate in workspace with process "Derivation rules - sequential", "Derivation rules - evaluation", "Derivation rules - Parallel", "Derivation rules - parallel evaluation", "Derivation rules - selection"
    Given POST this data:
        """
        {
            "pro_uid": "<pro_uid>",
            "usr_uid": "<usr_uid>",
            "tas_uid": "<tas_uid>",
            "variables": [{"name": "pruebaQA", "amount":"10400"}]
        }
        """
    And I request "cases/impersonate"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "app_uid" in session array as variable "app_uid_<app_uid_number>"
    And store "app_number" in session array as variable "app_number_<app_uid_number>"
    
    Examples:
    | Description                                                                                     | app_uid_number | pro_uid                          | usr_uid                          | tas_uid                          | variables |
    | Create new case with process "Derivation rules - sequential" - Case Start with chris            | 6              | 99209594750ec27ea338927000421575 | 24166330352d56730cdd525035621101 | 68707275350ec281ada1c95068712556 |           |
    | Create new case with process "Derivation rules - evaluation" - Case Start with adam             | 7              | 46279907250ec73b9b25a78031279680 | 44811996752d567110634a1013636964 | 99371337850ec73c0a38eb6024620271 |           |
    | Create new case with process "Derivation rules - Parallel" - Case Start with aaron              | 8              | 35894775350ec7daa099378048029617 | 51049032352d56710347233042615067 | 52838134750ec7dd0989fc0015625952 |           |
    | Create new case with process "Derivation rules - parallel evaluation"- Case Start with jeremiah | 9              | 34579467750ec8d55e8b115057818502 | 86677227852d5671f40ba25017213081 | 89648437550ec8d593c2159010276089 |           |
    | Create new case with process "Derivation rules - selection" - Case Start with admin             | 10             | 82458496050ec668981ecc7039804404 | 62625000752d5672d6661e6072881167 | 56900024450ec668e4a9243080698854 |           |



Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 11 records


Scenario Outline: Reassigns a case to a different user
    Given PUT this data:
        """
        {
            "usr_uid_source": "<usr_uid_source>",
            "usr_uid_target": "<usr_uid_target>",
        }
        """
    And that I want to update a resource with the key "app_uid_<app_uid_number>" stored in session array
    And I request "cases/app_uid/reassign-case" with the key "app_uid" stored in session array as variable "app_uid_<app_uid_number>"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
    
    | Description                           | app_uid_number | usr_uid_source                   | usr_uid_target                   |
    | Reassign the user adam                | 1              | 00000000000000000000000000000001 | 44811996752d567110634a1013636964 |
    | Reassign the user aaron               | 2              | 00000000000000000000000000000001 | 51049032352d56710347233042615067 |
    | Reassign the user admin               | 6              | 24166330352d56730cdd525035621101 | 00000000000000000000000000000001 |
    | Reassign the user admin               | 7              | 44811996752d567110634a1013636964 | 00000000000000000000000000000001 |
   

Scenario Outline: Cancel a case
    Given PUT this data:
        """
        {
        
        }
        """
    And that I want to update a resource with the key "app_uid_<app_uid_number>" stored in session array
    And I request "cases/app_uid/cancel" with the key "app_uid" stored in session array as variable "app_uid_<app_uid_number>"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
    
    | Description                                                    | app_uid_number |
    | Cancel of the process "Derivation rules - Parallel"            | 3              |
    | Cancel of the process "Derivation rules - parallel evaluation" | 5              |
    | Cancel of the process "Derivation rules - selection"           | 10             |


Scenario Outline: Pause a case
    Given PUT this data:
        """
        {
        
        }
        """
    And that I want to update a resource with the key "app_uid_<app_uid_number>" stored in session array
    And I request "cases/app_uid/pause" with the key "app_uid" stored in session array as variable "app_uid_<app_uid_number>"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
    
    | Description                                                    | app_uid_number |
    | Pause of the process "Derivation rules - parallel evaluation"  | 4              |
    | Pause of the process "Derivation rules - Parallel"             | 8              |
    | Pause of the process "Derivation rules - parallel evaluation"  | 9              |


Scenario Outline: Unpause a case
    Given PUT this data:
        """
        {
        
        }
        """
    And that I want to update a resource with the key "app_uid_<app_uid_number>" stored in session array
    And I request "cases/app_uid/unpause" with the key "app_uid" stored in session array as variable "app_uid_<app_uid_number>"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
    
    | Description                                                     | app_uid_number |
    | Unpause of the process "Derivation rules - parallel evaluation" | 4              |
    | Unpause of the process "Derivation rules - Parallel"            | 8              |
    | Unpause of the process "Derivation rules - parallel evaluation" | 9              |


Scenario Outline: Trigger a case
    Given PUT this data:
        """
        {
        
        }
        """
    And that I want to update a resource with the key "app_uid_<app_uid_number>" stored in session array
    And I request "cases/app_uid/execute-trigger/{tri_uid}" with the key "app_uid" stored in session array as variable "app_uid_<app_uid_number>"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
    
    | Description                                                     | app_uid_number |
    |                                                                 |                |
    |                                                                 |                |
    |                                                                 |                |




Scenario Outline: Route a case to the next task in the process
    Given PUT this data:
        """
        {
            
        }
        """
    And that I want to update a resource with the key "app_uid_<app_uid_number>" stored in session array
    And I request "case/app_uid/route-case" with the key "app_uid" stored in session array as variable "app_uid_<app_uid_number>"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object" 

    Examples:
    
    | Description                                                  | app_uid_number |
    | Route next activity "Derivation rules - sequential"          | 1              |
    | Route next activity "Derivation rules - evaluation"          | 2              |
    | Route next activity "Derivation rules - Parallel"            | 3              |
    | Route next activity "Derivation rules - parallel evaluation" | 4              |
    | Route next activity "Derivation rules - selection"           | 5              |
    | Route next activity "Derivation rules - sequential"          | 6              |
    | Route next activity "Derivation rules - evaluation"          | 7              |
    | Route next activity "Derivation rules - Parallel"            | 8              |
    | Route next activity "Derivation rules - parallel evaluation" | 9              |
    | Route next activity "Derivation rules - selection"           | 10             |



Scenario Outline: Delete of the cases created in this script.
    Given that I want to delete a resource with the key "app_doc_uid" stored in session array as variable "app_doc_uid_<app_doc_uid_number>"
    And I request "cases/551390121533343ee886813057396196/output-document"
    And the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object" 

    Examples:

    | app_doc_uid_number |
    | 1                  |
    | 2                  |
    | 3                  |
    | 4                  |
    | 5                  |
    | 6                  |
    | 7                  |
    | 8                  |
    | 9                  |
    | 10                 |