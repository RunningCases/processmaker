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


Scenario:  Returns information about a given case of the list Inbox of process "Derivation rules - Parallel"
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
    Given I request "cases/356811158533b13641ef789000630231/current-task"
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
                "variables": [{"name": "admin", "amount":"1030"}]
            }
            """
        And I request "cases"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "app_uid" in session array as variable "app_uid_<case_number>"
        And store "app_number" in session array as variable "app_number_<case_number>"
        
        Examples:
        | Description                                                           | case_number | pro_uid                          | tas_uid                          |
        | Create new case with process "Derivation rules - sequential"          | 1           | 99209594750ec27ea338927000421575 | 68707275350ec281ada1c95068712556 |
        | Create new case with process "Derivation rules - evaluation"          | 2           | 46279907250ec73b9b25a78031279680 | 99371337850ec73c0a38eb6024620271 |
        | Create new case with process "Derivation rules - Parallel"            | 3           | 35894775350ec7daa099378048029617 | 52838134750ec7dd0989fc0015625952 |
        | Create new case with process "Derivation rules - parallel evaluation" | 4           | 34579467750ec8d55e8b115057818502 | 89648437550ec8d593c2159010276089 |
        | Create new case with process "Derivation rules - selection"           | 5           | 82458496050ec668981ecc7039804404 | 56900024450ec668e4a9243080698854 |
        | Create new case with process "Derivation rules - sequential"          | 6           | 99209594750ec27ea338927000421575 | 68707275350ec281ada1c95068712556 |
        | Create new case with process "Derivation rules - evaluation"          | 7           | 46279907250ec73b9b25a78031279680 | 99371337850ec73c0a38eb6024620271 |
        | Create new case with process "Derivation rules - Parallel"            | 8           | 35894775350ec7daa099378048029617 | 52838134750ec7dd0989fc0015625952 |
        | Create new case with process "Derivation rules - parallel evaluation" | 9           | 34579467750ec8d55e8b115057818502 | 89648437550ec8d593c2159010276089 |
        | Create new case with process "Derivation rules - selection"           | 10          | 82458496050ec668981ecc7039804404 | 56900024450ec668e4a9243080698854 |
        | Create new case with process "Derivation rules - sequential"          | 11          | 99209594750ec27ea338927000421575 | 68707275350ec281ada1c95068712556 |
        | Create new case with process "Derivation rules - evaluation"          | 12          | 46279907250ec73b9b25a78031279680 | 99371337850ec73c0a38eb6024620271 |
        | Create new case with process "Derivation rules - Parallel"            | 13          | 35894775350ec7daa099378048029617 | 52838134750ec7dd0989fc0015625952 |
        | Create new case with process "Derivation rules - parallel evaluation" | 14          | 34579467750ec8d55e8b115057818502 | 89648437550ec8d593c2159010276089 |
        | Create new case with process "Derivation rules - selection"           | 15          | 82458496050ec668981ecc7039804404 | 56900024450ec668e4a9243080698854 |
        | Create new case with process "Derivation rules - sequential"          | 16          | 99209594750ec27ea338927000421575 | 68707275350ec281ada1c95068712556 |
        | Create new case with process "Derivation rules - evaluation"          | 17          | 46279907250ec73b9b25a78031279680 | 99371337850ec73c0a38eb6024620271 |
        | Create new case with process "Derivation rules - Parallel"            | 18          | 35894775350ec7daa099378048029617 | 52838134750ec7dd0989fc0015625952 |
        | Create new case with process "Derivation rules - parallel evaluation" | 19          | 34579467750ec8d55e8b115057818502 | 89648437550ec8d593c2159010276089 |
        | Create new case with process "Derivation rules - selection"           | 20          | 82458496050ec668981ecc7039804404 | 56900024450ec668e4a9243080698854 |
        | Create new case with process "Derivation rules - sequential"          | 21          | 99209594750ec27ea338927000421575 | 68707275350ec281ada1c95068712556 |
        | Create new case with process "Derivation rules - evaluation"          | 22          | 46279907250ec73b9b25a78031279680 | 99371337850ec73c0a38eb6024620271 |
        | Create new case with process "Derivation rules - Parallel"            | 23          | 35894775350ec7daa099378048029617 | 52838134750ec7dd0989fc0015625952 |
        | Create new case with process "Derivation rules - parallel evaluation" | 24          | 34579467750ec8d55e8b115057818502 | 89648437550ec8d593c2159010276089 |
        | Create new case with process "Derivation rules - selection"           | 25          | 82458496050ec668981ecc7039804404 | 56900024450ec668e4a9243080698854 |


Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 30 records


Scenario Outline: Create a new case Impersonate in workspace with process "Derivation rules - sequential", "Derivation rules - evaluation", "Derivation rules - Parallel", "Derivation rules - parallel evaluation", "Derivation rules - selection"
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
        And store "app_uid" in session array as variable "app_uid_<case_number>"
        And store "app_number" in session array as variable "app_number_<case_number>"
        
        Examples:
        | Description                                                                                     | case_number | usr_uid                          |
        | Create new case with process "Derivation rules - sequential" - Case Start with chris            | 26          | 51049032352d56710347233042615067 |
        | Create new case with process "Derivation rules - evaluation" - Case Start with adam             | 27          | 44811996752d567110634a1013636964 |
        | Create new case with process "Derivation rules - Parallel" - Case Start with aaron              | 28          | 24166330352d56730cdd525035621101 |
        | Create new case with process "Derivation rules - parallel evaluation"- Case Start with jeremiah | 29          | 86677227852d5671f40ba25017213081 |
        | Create new case with process "Derivation rules - selection" - Case Start with admin             | 30          | 62625000752d5672d6661e6072881167 |


Scenario Outline: Reassigns a case to a different user
    Given PUT this data:
        """
        {
            "usr_uid_source": "<usr_uid_source>",
            "usr_uid_target": "<usr_uid_target>"
        }
        """
    And I request "cases/app_uid/reassign-case"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"

    Examples:

    | test_description           | case_number | usr_uid_source                   | usr_uid_target                   |
    | Reassign the user adam     | 1           | 00000000000000000000000000000001 | 44811996752d567110634a1013636964 |
    | Reassign the user aaron    | 2           | 00000000000000000000000000000001 | 51049032352d56710347233042615067 |
    | Reassign the user jeremiah | 3           | 00000000000000000000000000000001 | 86677227852d5671f40ba25017213081 |
    | Reassign the user chris    | 4           | 00000000000000000000000000000001 | 24166330352d56730cdd525035621101 |
    | Reassign the user zachary  | 5           | 00000000000000000000000000000001 | 62625000752d5672d6661e6072881167 |
    | Reassign the user admin    | 26          | 99209594750ec27ea338927000421575 | 00000000000000000000000000000001 |


Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 30 records



Scenario Outline: Returns case variables generated by triggers before assignment (ba), before routing (br) and after routing (ar).
    Given I request "cases/app_uid/variables"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"    
    And the "ar" property equals ""
    And the "br" property equals ""
    And the "ba" property equals ""

Examples:

        | test_description                                                            | case_number |
        | Derivate case of the process "Derivation rules - sequential" with triggers  | 6           |


Scenario Outline: Sends variables to a case
        Given PUT this data:
            """
            {
                "continue": "yes",
                "tasks": "Cyclical"
                
            }
            """
        And I request "cases/app_uid/variable"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"


        Examples:

        | test_description                                                      | case_number |
        | Derivate case of the process "Derivation rules - sequential"          | 6           |
        | Derivate case of the process "Derivation rules - evaluation"          | 7           |
        | Derivate case of the process "Derivation rules - Parallel"            | 8           |
        | Derivate case of the process "Derivation rules - parallel evaluation" | 9           |
        | Derivate case of the process "Derivation rules - selection"           | 10          |  
        | Derivate case of the process "Derivation rules - evaluation"          | 27          |





Scenario Outline: Route a case to the next task in the process
        Given PUT this data:
            """
            {
                "case_uid": "<case_number>",
                "del_index": "1"
            }
            """
        And I request "cases/app_uid/route-case"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
      

        Examples:

        | test_description                                                      | case_number |
        | Derivate case of the process "Derivation rules - sequential"          | 6           |
        | Derivate case of the process "Derivation rules - evaluation"          | 7           |
        | Derivate case of the process "Derivation rules - Parallel"            | 8           |
        | Derivate case of the process "Derivation rules - parallel evaluation" | 9           |
        

Scenario Outline: Returns the variables can be system variables and/or case variables.
    Given I request "cases/app_uid/variables"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    
    
    And the "ar" property equals "OK"
    And the "br" property equals "OK"
    And the "ba" property equals "OK"

Examples:

        | test_description                                                      | case_number |
        | Derivate case of the process "Derivation rules - sequential"          | 6           |

Scenario: Returns a list of the cases for the logged in user (Inbox)
    Given I request "cases"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 15 records


Scenario Outline: Cancel a case
    Given PUT this data:
        """
        {
        
        }
        """
    And I request "cases/app_uid/cancel"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
    
    | Description                                                    | case_number |
    | Cancel of the process "Derivation rules - sequential"          | 11          |
    | Cancel of the process "Derivation rules - evaluation"          | 12          |
    | Cancel of the process "Derivation rules - Parallel"            | 13          |
    | Cancel of the process "Derivation rules - parallel evaluation" | 14          |
    | Cancel of the process "Derivation rules - selection"           | 15          |
    | Cancel of the process "Derivation rules - Parallel"            | 28          |


Scenario Outline: Pause a case
    Given PUT this data:
        """
            {
              "unpaused_date": "2016-12-12"  
            }
            """
    And I request "cases/app_uid/pause"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
    
    | Description                                                   | case_number |
    | Pause of the process "Derivation rules - sequential"          | 16          |
    | Pause of the process "Derivation rules - evaluation"          | 17          |
    | Pause of the process "Derivation rules - Parallel"            | 18          |
    | Pause of the process "Derivation rules - parallel evaluation" | 19          |
    | Pause of the process "Derivation rules - selection"           | 20          |
    | Pause of the process "Derivation rules - parallel evaluation" | 29          | 


Scenario: Returns a list of the cases for the logged in user (Paused)
    Given I request "cases/paused"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 17 records


Scenario Outline: Unpause a case
    Given PUT this data:
        """
        {
        
        }
        """
    And I request "cases/app_uid/unpause"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
    
    | Description                                                     | case_number |
    | Unpause of the process "Derivation rules - sequential"          | 16          |
    | Unpause of the process "Derivation rules - evaluation"          | 17          |
    | Unpause of the process "Derivation rules - Parallel"            | 18          |
    | Unpause of the process "Derivation rules - parallel evaluation" | 19          |
    | Unpause of the process "Derivation rules - selection"           | 20          |
    | Unpause of the process "Derivation rules - parallel evaluation" | 29          |


Scenario: Returns a list of the cases for the logged in user (Paused)
    Given I request "cases/paused"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 12 records


Scenario Outline: Executes a ProcessMaker trigger for a case
    Given PUT this data:
        """
        {
            
        }
        """
    And I request "cases/app_uid/execute-trigger/<tri_uid>"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object"


    Examples:

    | test_description                       | case_number | tri_uid                          |
    | Ejecucion de trigger                   | 5           | 54962158250ec613ba5bc89016850103 |


Scenario Outline: Delete a case
        Given PUT this data:
            """
            {
                
            }
            """
        
        And that I want to delete a resource with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
        And I request "cases"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
           

        Examples:

        | test_description                         | case_number |
        | Delete a case 1, created in this script  | 1           |
        | Delete a case 2, created in this script  | 2           |       
        | Delete a case 3, created in this script  | 3           |
        | Delete a case 4, created in this script  | 4           |
        | Delete a case 5, created in this script  | 5           |
        | Delete a case 6, created in this script  | 6           |
        | Delete a case 7, created in this script  | 7           |
        | Delete a case 8, created in this script  | 8           |
        | Delete a case 9, created in this script  | 9           |
        | Delete a case 10, created in this script | 10          |
        | Delete a case 11, created in this script | 11          |
        | Delete a case 12, created in this script | 12          |
        | Delete a case 13, created in this script | 13          |
        | Delete a case 14, created in this script | 14          |
        | Delete a case 15, created in this script | 15          |
        | Delete a case 16, created in this script | 16          |
        | Delete a case 17, created in this script | 17          |
        | Delete a case 18, created in this script | 18          |
        | Delete a case 19, created in this script | 19          |
        | Delete a case 20, created in this script | 20          |
        | Delete a case 21, created in this script | 21          |
        | Delete a case 22, created in this script | 22          |
        | Delete a case 23, created in this script | 23          |
        | Delete a case 24, created in this script | 24          |
        | Delete a case 25, created in this script | 25          |
        | Delete a case 26, created in this script | 26          |
        | Delete a case 27, created in this script | 27          |
        | Delete a case 28, created in this script | 28          |
        | Delete a case 29, created in this script | 29          |
        | Delete a case 30, created in this script | 30          |


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