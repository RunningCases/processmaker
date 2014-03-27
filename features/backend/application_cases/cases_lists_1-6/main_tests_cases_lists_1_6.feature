@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (inbox, draftCaseList, participatedCaseList, unassignedCaseList, pausedCaseList and advanced Search) and (getCaseInfo, taskCase, newCase, newCaseImpersonate, reassignCase and routeCase)
Requirements:
    a workspace with five of the process "Derivation rules - evaluation", "Derivation rules - Parallel", "Derivation rules - parallel evaluation", "Derivation rules - selection", "Derivation rules - sequential"
    
Background:
    Given that I have a valid access_token


#Obtener la cantidad de casos ACTUALES por cada listado

Scenario: Returns a list of the cases for the logged in user (Inbox)
    Given I request "cases"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And store response count in session variable as "count_inbox"


Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And store response count in session variable as "count_draft"


Scenario: Returns a list of the cases for the logged in user (Participated)
    Given I request "cases/participated"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And store response count in session variable as "count_participated"


Scenario: Returns a list of the cases for the logged in user (Unassigned)
    Given I request "cases/unassigned"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And store response count in session variable as "count_unassigned"


Scenario: Returns a list of the cases for the logged in user (Paused)
    Given I request "cases/paused"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And store response count in session variable as "count_paused"


Scenario: Returns a list of the cases for the logged in user (Advanced-Search)
    Given I request "cases/paused"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And store response count in session variable as "count_advanced-search"


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



#Scenarios para filtros y paginacion de las listas
Scenario Outline: Get paging of list inbox
    Given I request "cases/paged?Start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records more than "<count_inbox>"


     Examples:
    
    | test_description           | start | limit   | records | http_code | type   |
    | lowercase in Start         |   a   | 1       | 0       |  400      | string |
    | uppercase in Start         |   A   | 1       | 0       |  400      | string |
    | lowercase in Limit         |   1   | a       | 0       |  400      | string |
    | uppercase in Limit         |   1   | A       | 0       |  400      | string |
    | limit=3                    |   1   | 3       | 3       |  200      | array  |
    | start=3                    |   3   | 5       | 3       |  200      | array  |
    | limit and start =3         |   3   | 3       | 1       |  200      | array  |
    | high number for start      | 1000  | 1       | 0       |  200      | array  |
    | high number for start      | 1000  | 0       | 0       |  200      | array  |
    | empty result               |   1   | 0       | 1       |  200      | array  |
    | empty string               |   1   | 10000   | 1       |  200      | array  |
    | invalid start              |   b   | 25      | 0       |  400      | string |
    | invalid limit              |   1   | c       | 0       |  400      | string |
    | start equals zero          |   0   | 20      | 20      |  200      | array  |
    | search 0                   |   0   | 0       | 0       |  200      | array  |
    | search 0                   |   0   | 100     | 100     |  200      | array  |
    | negative numbers in start  |  -10  | 25      | 25      |  200      | array  |
    | negative numbers in limit  |   1   | -25     | 25      |  200      | array  |
    | real numbers               |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers in start      |  0.0  | 25      | 0       |  400      | string |
    | real numbers in limit      |  1    | 1.4599  | 0       |  400      | string |
    | only start                 |  1    |         | 1       |  200      | array  |
    | only limit                 |       | 25      | 1       |  200      | array  |
    | without start and limit    |       |         | 1       |  200      | array  |


Scenario Outline: Get order type of Descending and Acending
    Given I request "cases?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_inbox>"

Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  | 1       |
    | Order for Descending       | desc | 1       |


Scenario Outline: Get order type of Process Category
    Given I request "cases?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_inbox>"

Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  | 1       |
    | Filter all categories                      |                                   |         |


Scenario Outline: Get order type of Process
    Given I request "cases?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_inbox>"

Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 99209594750ec27ea338927000421575 |         |
    | Filter all cases                                 |                                  |         |


Scenario Outline: Get order type of Search of number the process
    Given I request "cases?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_inbox>"

Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 92535130653271a60de2e73021469732 |         |
    | Filter all cases                                                |                                  |         |


Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records more than "<count_draft>"


Scenario Outline: Get paging of list Draft
    Given I request "cases/draft/paged?Start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records more than "<count_draft>"

     Examples:
    
    | test_description           | start | limit   | records | http_code | type   |
    | lowercase in Start         |   a   | 1       | 0       |  400      | string |
    | uppercase in Start         |   A   | 1       | 0       |  400      | string |
    | lowercase in Limit         |   1   | a       | 0       |  400      | string |
    | uppercase in Limit         |   1   | A       | 0       |  400      | string |
    | limit=3                    |   1   | 3       | 3       |  200      | array  |
    | start=3                    |   3   | 25      | 22      |  200      | array  |
    | limit and start =3         |   3   | 3       | 1       |  200      | array  |
    | high number for start      | 1000  | 1       | 0       |  200      | array  |
    | high number for start      | 1000  | 0       | 0       |  200      | array  |
    | empty result               |   1   | 0       | 1       |  200      | array  |
    | empty string               |   1   | 10000   | 1       |  200      | array  |
    | invalid start              |   b   | 25      | 0       |  400      | string |
    | invalid limit              |   1   | c       | 0       |  400      | string |
    | start equals zero          |   0   | 20      | 20      |  200      | array  |
    | search 0                   |   0   | 0       | 0       |  200      | array  |
    | search 0                   |   0   | 100     | 100     |  200      | array  |
    | negative numbers in start  |  -10  | 25      | 25      |  200      | array  |
    | negative numbers in limit  |   1   | -25     | 25      |  200      | array  |
    | real numbers               |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers in start      |  0.0  | 25      | 0       |  400      | string |
    | real numbers in limit      |  1    | 1.4599  | 0       |  400      | string |
    | only start                 |  1    |         | 1       |  200      | array  |
    | only limit                 |       | 25      | 1       |  200      | array  |
    | without start and limit    |       |         | 1       |  200      | array  |


Scenario Outline: Get order type of Descending and Ascending
    Given I request "cases/draft?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_draft>"

Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  |         |
    | Order for Descending       | desc |         |

    

Scenario Outline: Get order type of Process Category
    Given I request "cases/draft?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_draft>"

Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  |         |
    | Filter all categories                      |                                   |         |


Scenario Outline: Get order type of Process
    Given I request "cases/draft?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_draft>"

Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 99209594750ec27ea338927000421575 |         |
    | Filter all cases                                 |                                  |         |


Scenario Outline: Get order type of Search of the process
    Given I request "cases/draft?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_draft>"

Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 92535130653271a60de2e73021469732 |         |
    | Filter all cases                                                |                                  |         |


Scenario: Returns a list of the cases for the logged in user (Participated)
    Given I request "cases/participated"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records more than "<count_participated>"


Scenario Outline: Get paging of list Participated
    Given I request "cases/participated/paged?Start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records more than "<count_participated>"

     Examples:
    
    | test_description           | start | limit   | records | http_code | type   |
    | lowercase in Start         |   a   | 1       | 0       |  400      | string |
    | uppercase in Start         |   A   | 1       | 0       |  400      | string |
    | lowercase in Limit         |   1   | a       | 0       |  400      | string |
    | uppercase in Limit         |   1   | A       | 0       |  400      | string |
    | limit=3                    |   1   | 3       | 3       |  200      | array  |
    | start=3                    |   3   | 25      | 22      |  200      | array  |
    | limit and start =3         |   3   | 3       | 1       |  200      | array  |
    | high number for start      | 1000  | 1       | 0       |  200      | array  |
    | high number for start      | 1000  | 0       | 0       |  200      | array  |
    | empty result               |   1   | 0       | 1       |  200      | array  |
    | empty string               |   1   | 10000   | 1       |  200      | array  |
    | invalid start              |   b   | 25      | 0       |  400      | string |
    | invalid limit              |   1   | c       | 0       |  400      | string |
    | start equals zero          |   0   | 20      | 20      |  200      | array  |
    | search 0                   |   0   | 0       | 0       |  200      | array  |
    | search 0                   |   0   | 100     | 100     |  200      | array  |
    | negative numbers in start  |  -10  | 25      | 25      |  200      | array  |
    | negative numbers in limit  |   1   | -25     | 25      |  200      | array  |
    | real numbers               |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers in start      |  0.0  | 25      | 0       |  400      | string |
    | real numbers in limit      |  1    | 1.4599  | 0       |  400      | string |
    | only start                 |  1    |         | 1       |  200      | array  |
    | only limit                 |       | 25      | 1       |  200      | array  |
    | without start and limit    |       |         | 1       |  200      | array  |


Scenario Outline: Get order type of Descending an Descending
    Given I request "cases/participated?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_participated>"

Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  |         |
    | Order for Descending       | desc |         |

    

Scenario Outline: Get order type of Process Category 
    Given I request "cases/participated?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_participated>"

Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  |         |
    | Filter all categories                      |                                   |         |


Scenario Outline: Get order type of Process 
    Given I request "cases/participated?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_participated>"

Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 99209594750ec27ea338927000421575 |         |
    | Filter all cases                                 |                                  |         |



Scenario Outline: Get order type of Search 
    Given I request "cases/participated?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_participated>"

Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 92535130653271a60de2e73021469732 |         |
    | Filter all cases                                                |                                  |         |


Scenario: Returns a list of the cases for the logged in user (Unassigned)
    Given I request "cases/unassigned"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records more than "<count_unassigned>"


Scenario Outline: Get paging of list Unassigned
    Given I request "cases/Unassigned/paged?Start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records more than "<count_unassigned>"

     Examples:
    
    | test_description           | start | limit   | records | http_code | type   |
    | lowercase in Start         |   a   | 1       | 0       |  400      | string |
    | uppercase in Start         |   A   | 1       | 0       |  400      | string |
    | lowercase in Limit         |   1   | a       | 0       |  400      | string |
    | uppercase in Limit         |   1   | A       | 0       |  400      | string |
    | limit=3                    |   1   | 3       | 3       |  200      | array  |
    | start=3                    |   3   | 25      | 22      |  200      | array  |
    | limit and start =3         |   3   | 3       | 1       |  200      | array  |
    | high number for start      | 1000  | 1       | 0       |  200      | array  |
    | high number for start      | 1000  | 0       | 0       |  200      | array  |
    | empty result               |   1   | 0       | 1       |  200      | array  |
    | empty string               |   1   | 10000   | 1       |  200      | array  |
    | invalid start              |   b   | 25      | 0       |  400      | string |
    | invalid limit              |   1   | c       | 0       |  400      | string |
    | start equals zero          |   0   | 20      | 20      |  200      | array  |
    | search 0                   |   0   | 0       | 0       |  200      | array  |
    | search 0                   |   0   | 100     | 100     |  200      | array  |
    | negative numbers in start  |  -10  | 25      | 25      |  200      | array  |
    | negative numbers in limit  |   1   | -25     | 25      |  200      | array  |
    | real numbers               |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers in start      |  0.0  | 25      | 0       |  400      | string |
    | real numbers in limit      |  1    | 1.4599  | 0       |  400      | string |
    | only start                 |  1    |         | 1       |  200      | array  |
    | only limit                 |       | 25      | 1       |  200      | array  |
    | without start and limit    |       |         | 1       |  200      | array  |


Scenario Outline: Get order type of Descending and Acending
    Given I request "cases/unassigned?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_unassigned>"

Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  |         |
    | Order for Descending       | desc |         |
    

Scenario Outline: Get order type of Process Category 
    Given I request "cases/unassigned?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_unassigned>"

Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  |         |
    | Filter all categories                      |                                   |         |


Scenario Outline: Get order type of Process 
    Given I request "cases/unassigned?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_unassigned>"

Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 99209594750ec27ea338927000421575 |         |
    | Filter all cases                                 |                                  |         |


Scenario Outline: Get order type of Search 
    Given I request "cases/unassigned?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_unassigned>"

Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 92535130653271a60de2e73021469732 |         |
    | Filter all cases                                                |                                  |         |



Scenario: Returns a list of the cases for the logged in user (Paused)
    Given I request "cases/paused"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records more than "<count_paused>"


Scenario Outline: Get paging of list Paused
    Given I request "cases/paused/paged?Start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records more than "<count_paused>"

     Examples:
    
    | test_description           | start | limit   | records | http_code | type   |
    | lowercase in Start         |   a   | 1       | 0       |  400      | string |
    | uppercase in Start         |   A   | 1       | 0       |  400      | string |
    | lowercase in Limit         |   1   | a       | 0       |  400      | string |
    | uppercase in Limit         |   1   | A       | 0       |  400      | string |
    | limit=3                    |   1   | 3       | 3       |  200      | array  |
    | start=3                    |   3   | 25      | 22      |  200      | array  |
    | limit and start =3         |   3   | 3       | 1       |  200      | array  |
    | high number for start      | 1000  | 1       | 0       |  200      | array  |
    | high number for start      | 1000  | 0       | 0       |  200      | array  |
    | empty result               |   1   | 0       | 1       |  200      | array  |
    | empty string               |   1   | 10000   | 1       |  200      | array  |
    | invalid start              |   b   | 25      | 0       |  400      | string |
    | invalid limit              |   1   | c       | 0       |  400      | string |
    | start equals zero          |   0   | 20      | 20      |  200      | array  |
    | search 0                   |   0   | 0       | 0       |  200      | array  |
    | search 0                   |   0   | 100     | 100     |  200      | array  |
    | negative numbers in start  |  -10  | 25      | 25      |  200      | array  |
    | negative numbers in limit  |   1   | -25     | 25      |  200      | array  |
    | real numbers               |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers in start      |  0.0  | 25      | 0       |  400      | string |
    | real numbers in limit      |  1    | 1.4599  | 0       |  400      | string |
    | only start                 |  1    |         | 1       |  200      | array  |
    | only limit                 |       | 25      | 1       |  200      | array  |
    | without start and limit    |       |         | 1       |  200      | array  |


Scenario Outline: Get order type of Descending and Acending
    Given I request "cases/paused?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_paused>"

Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  |         |
    | Order for Descending       | desc |         |

    
Scenario Outline: Get order type of Process Category 
    Given I request "cases/paused?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_paused>"

Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  |         |
    | Filter all categories                      |                                   |         |


Scenario Outline: Get order type of Process 
    Given I request "cases/paused?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_paused>"

Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 99209594750ec27ea338927000421575 |         |
    | Filter all cases                                 |                                  |         |


Scenario Outline: Get order type of Search 
    Given I request "cases/paused?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_paused>"

Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 92535130653271a60de2e73021469732 |         |
    | Filter all cases                                                |                                  |         |


Scenario: Returns a list of the cases for the logged in user (Advanced Search)
    Given I request "cases/advanced-search"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records more than "<count_advanced-search>"


Scenario Outline: Get paging of list Advanced Search
    Given I request "cases/advanced-search/paged?Start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records more than "<count_advanced-search>"

     Examples:
    
    | test_description           | start | limit   | records | http_code | type   |
    | lowercase in Start         |   a   | 1       | 0       |  400      | string |
    | uppercase in Start         |   A   | 1       | 0       |  400      | string |
    | lowercase in Limit         |   1   | a       | 0       |  400      | string |
    | uppercase in Limit         |   1   | A       | 0       |  400      | string |
    | limit=3                    |   1   | 3       | 3       |  200      | array  |
    | start=3                    |   3   | 25      | 22      |  200      | array  |
    | limit and start =3         |   3   | 3       | 1       |  200      | array  |
    | high number for start      | 1000  | 1       | 0       |  200      | array  |
    | high number for start      | 1000  | 0       | 0       |  200      | array  |
    | empty result               |   1   | 0       | 1       |  200      | array  |
    | empty string               |   1   | 10000   | 1       |  200      | array  |
    | invalid start              |   b   | 25      | 0       |  400      | string |
    | invalid limit              |   1   | c       | 0       |  400      | string |
    | start equals zero          |   0   | 20      | 20      |  200      | array  |
    | search 0                   |   0   | 0       | 0       |  200      | array  |
    | search 0                   |   0   | 100     | 100     |  200      | array  |
    | negative numbers in start  |  -10  | 25      | 25      |  200      | array  |
    | negative numbers in limit  |   1   | -25     | 25      |  200      | array  |
    | real numbers               |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers in start      |  0.0  | 25      | 0       |  400      | string |
    | real numbers in limit      |  1    | 1.4599  | 0       |  400      | string |
    | only start                 |  1    |         | 1       |  200      | array  |
    | only limit                 |       | 25      | 1       |  200      | array  |
    | without start and limit    |       |         | 1       |  200      | array  |


Scenario Outline: Get order type of Descending and Acending
    Given I request "cases/advanced-search?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_advanced-search>"

    Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  |         |
    | Order for Descending       | desc |         |
    

Scenario Outline: Get order type of Process Category 
    Given I request "cases/advanced-search?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_advanced-search>"

    Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  |         |
    | Filter all categories                      |                                   |         |



Scenario Outline: Get order type of Process 
    Given I request "cases/advanced-search?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_advanced-search>"

    Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 99209594750ec27ea338927000421575 |         |
    | Filter all cases                                 |                                  |         |



Scenario Outline: Get order type of Search 
    Given I request "cases/advanced-search?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_advanced-search>"

    Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 92535130653271a60de2e73021469732 |         |
    | Filter all cases                                                |                                  |         |


Scenario Outline: Get order for Status
    Given I request "cases/advanced-search?app_status=<app_status>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_advanced-search>"

    Examples:
    
    | test_description           | app_status  | records |
    | Filter Status = All Status |             |         |
    | Filter Status = Completed  | COMPLETED   |         |
    | Filter Status = Draft      | DRAFT       |         |             
    | Filter Status = To Do      | TO_DO       |         |


Scenario Outline: Get order for User
    Given I request "cases/advanced-search?usr_uid=<usr_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_advanced-search>"

    Examples:
    
    | test_description           | usr_uid                          | records |
    | Filter Status = All User   |                                  |         |
    | Filter Status = aaron      | 51049032352d56710347233042615067 |         |
    | Filter Status = admin      | 00000000000000000000000000000001 |         |             
    | Filter Status = chris      | 24166330352d56730cdd525035621101 |         |


Scenario Outline: Get order for date
    Given I request "cases/advanced-search?date_from=<date_from>&date_to=<date_to>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records more than "<count_advanced-search>"

    Examples:
    
    | test_description         | date_from  | date_to    | records |
    | Filter date = 2014-03-01 | 2014-03-01 | 2014-03-20 |         |
    | Filter date = 2014-03-15 | 2014-03-15 | 2014-03-20 |         |