@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (inbox, draftCaseList, participatedCaseList, unassignedCaseList, pausedCaseList and advanced Search) and (getCaseInfo, taskCase, newCase, newCaseImpersonate, reassignCase and routeCase)
Requirements:
    a workspace with five of the process "Derivation rules - evaluation", "Derivation rules - Parallel", "Derivation rules - parallel evaluation", "Derivation rules - selection", 
    "Derivation rules - sequential, Test Case Note, Test Case Note - Negative test, Test Case Variables, Test Designer Report Tables, Test Input Document Case, Test Michelangelo, Test Output Document Case"
    
Background:
    Given that I have a valid access_token


#Obtener la cantidad de casos ACTUALES por cada listado

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


Scenario: Returns a list of the cases for the logged in user (Advanced-Search)
    Given I request "cases/advanced-search"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 30 records



#Scenarios para filtros y paginacion en listas
Scenario Outline: Get paging of list inbox
    Given I request "cases/paged?start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the response has <records> records in property "data"


    Examples:
    
    | test_description           | start | limit   | records | http_code |
    | lowercase in start         |   a   | 1       | 1       |  200      |
    | uppercase in start         |   A   | 1       | 1       |  200      |
    | lowercase in Limit         |   1   | a       | 14      |  200      |
    | uppercase in Limit         |   1   | A       | 14      |  200      |
    | limit=3                    |   1   | 3       | 3       |  200      |
    | start=3                    |   3   | 5       | 5       |  200      |
    | limit and start =3         |   3   | 3       | 3       |  200      |
    | high number for start      | 1000  | 1       | 0       |  200      |
    | high number for start      | 1000  | 0       | 0       |  200      |
    | empty result               |   1   | 0       | 14      |  200      |
    | empty string               |   1   | 10000   | 14      |  200      |
    | invalid start              |   b   | 25      | 14      |  200      |
    | invalid limit              |   1   | c       | 14      |  200      |
    | start equals zero          |   0   | 20      | 14      |  200      |
    | search 0                   |   0   | 0       | 14      |  200      |
    | search 0                   |   0   | 100     | 14      |  200      |
    | negative numbers in start  |  -10  | 25      | 5       |  200      |
    | negative numbers in limit  |   1   | -25     | 14      |  200      |
    | real numbers               |  0.0  | 1.0     | 1       |  200      |
    | real numbers in start      |  0.0  | 12      | 12      |  200      |
    | real numbers in limit      |  1    | 1.4599  | 1       |  200      |
    | only start                 |  1    |         | 14      |  200      |
    | only limit                 |       | 25      | 14      |  200      |
    | without start and limit    |       |         | 14      |  200      |


Scenario Outline: Get order type of Descending and Acending
    Given I request "cases?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  | 14      |
    | Order for Descending       | desc | 14      |


Scenario Outline: Get order type of Process Category
    Given I request "cases?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  | 1       |
    | Filter all categories                      |                                   | 14      |


Scenario Outline: Get order type of Process
    Given I request "cases?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 99209594750ec27ea338927000421575 | 1       |
    | Filter all cases                                 |                                  | 14      |


Scenario Outline: Get order type of Search of number the process
    Given I request "cases?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 18                               | 1       |
    | Filter all cases                                                |                                  | 14      |


Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 15 records


Scenario Outline: Get paging of list Draft
    Given I request "cases/draft/paged?start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the response has <records> records in property "data" 

     Examples:
    
    | test_description           | start | limit   | records | http_code |
    | lowercase in start         |   a   | 1       | 1       |  200      |
    | uppercase in start         |   A   | 1       | 1       |  200      |
    | lowercase in Limit         |   1   | a       | 15      |  200      |
    | uppercase in Limit         |   1   | A       | 15      |  200      |
    | limit=3                    |   1   | 3       | 3       |  200      |
    | start=3                    |   3   | 5       | 5       |  200      |
    | limit and start =3         |   3   | 3       | 3       |  200      |
    | high number for start      | 1000  | 1       | 0       |  200      |
    | high number for start      | 1000  | 0       | 0       |  200      |
    | empty result               |   1   | 0       | 15      |  200      |
    | empty string               |   1   | 10000   | 15      |  200      |
    | invalid start              |   b   | 25      | 15      |  200      |
    | invalid limit              |   1   | c       | 15      |  200      |
    | start equals zero          |   0   | 20      | 15      |  200      |
    | search 0                   |   0   | 0       | 15      |  200      |
    | search 0                   |   0   | 100     | 15      |  200      |
    | negative numbers in start  |  -10  | 25      | 6       |  200      |
    | negative numbers in limit  |   1   | -25     | 15      |  200      |
    | real numbers               |  0.0  | 1.0     | 1       |  200      |
    | real numbers in start      |  0.0  | 12      | 12      |  200      |
    | real numbers in limit      |  1    | 1.4599  | 1       |  200      |
    | only start                 |  1    |         | 15      |  200      |
    | only limit                 |       | 25      | 15      |  200      |
    | without start and limit    |       |         | 15      |  200      |


Scenario Outline: Get order type of Descending and Ascending
    Given I request "cases/draft?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  | 15      |
    | Order for Descending       | desc | 15      |

    

Scenario Outline: Get order type of Process Category
    Given I request "cases/draft?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  | 8       |
    | Filter all categories                      |                                   | 15      |


Scenario Outline: Get order type of Process
    Given I request "cases/draft?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 99209594750ec27ea338927000421575 | 1       |
    | Filter all cases                                 |                                  | 15      |


Scenario Outline: Get order type of Search of the process
    Given I request "cases/draft?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 16                               | 4       |
    | Filter all cases                                                |                                  | 15      |


Scenario: Returns a list of the cases for the logged in user (Participated)
    Given I request "cases/participated"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 30 records

Scenario Outline: Get paging of list Participated
    Given I request "cases/participated/paged?start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the response has <records> records in property "data"

     Examples:
    
    | test_description           | start | limit   | records | http_code |
    | lowercase in start         |   a   | 1       | 1       |  200      |
    | uppercase in start         |   A   | 1       | 1       |  200      |
    | lowercase in Limit         |   1   | a       | 30      |  200      |
    | uppercase in Limit         |   1   | A       | 30      |  200      |
    | limit=3                    |   1   | 3       | 3       |  200      |
    | start=3                    |   3   | 5       | 5       |  200      |
    | limit and start =3         |   3   | 3       | 3       |  200      |
    | high number for start      | 1000  | 1       | 0       |  200      |
    | high number for start      | 1000  | 0       | 0       |  200      |
    | empty result               |   1   | 0       | 30      |  200      |
    | empty string               |   1   | 10000   | 57      |  200      |
    | invalid start              |   b   | 25      | 25      |  200      |
    | invalid limit              |   1   | c       | 30      |  200      |
    | start equals zero          |   0   | 20      | 20      |  200      |
    | search 0                   |   0   | 0       | 30      |  200      |
    | search 0                   |   0   | 100     | 57      |  200      |
    | negative numbers in start  |  -10  | 25      | 25      |  200      |
    | negative numbers in limit  |   1   | -25     | 25      |  200      |
    | real numbers               |  0.0  | 1.0     | 1       |  200      |
    | real numbers in start      |  0.0  | 12      | 12      |  200      |
    | real numbers in limit      |  1    | 1.4599  | 1       |  200      |
    | only start                 |  1    |         | 30      |  200      |
    | only limit                 |       | 25      | 25      |  200      |
    | without start and limit    |       |         | 30      |  200      |


Scenario Outline: Get order type of Descending an Descending
    Given I request "cases/participated?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  | 30      |
    | Order for Descending       | desc | 30      |

    

Scenario Outline: Get order type of Process Category 
    Given I request "cases/participated?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  | 26      |
    | Filter all categories                      |                                   | 30      |


Scenario Outline: Get order type of Process 
    Given I request "cases/participated?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 99209594750ec27ea338927000421575 | 2       |
    | Filter all cases                                 |                                  | 30      |



Scenario Outline: Get order type of Search 
    Given I request "cases/participated?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 17                               | 10      |
    | Filter all cases                                                |                                  | 30      |


Scenario: Returns a list of the cases for the logged in user (Unassigned)
    Given I request "cases/unassigned"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 12 records


Scenario Outline: Get paging of list Unassigned
    Given I request "cases/unassigned/paged?start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the response has <records> records in property "data"

     Examples:
    
    | test_description           | start | limit   | records | http_code |
    | lowercase in start         |   a   | 1       | 1       |  200      |
    | uppercase in start         |   A   | 1       | 1       |  200      |
    | lowercase in Limit         |   1   | a       | 12      |  200      |
    | uppercase in Limit         |   1   | A       | 12      |  200      |
    | limit=3                    |   1   | 3       | 3       |  200      |
    | start=3                    |   3   | 5       | 5       |  200      |
    | limit and start =3         |   3   | 3       | 3       |  200      |
    | high number for start      | 1000  | 1       | 0       |  200      |
    | high number for start      | 1000  | 0       | 0       |  200      |
    | empty result               |   1   | 0       | 12      |  200      |
    | empty string               |   1   | 10000   | 12      |  200      |
    | invalid start              |   b   | 25      | 12      |  200      |
    | invalid limit              |   1   | c       | 12      |  200      |
    | start equals zero          |   0   | 20      | 12      |  200      |
    | search 0                   |   0   | 0       | 12      |  200      |
    | search 0                   |   0   | 100     | 12      |  200      |
    | negative numbers in start  |  -10  | 25      | 3       |  200      |
    | negative numbers in limit  |   1   | -25     | 12      |  200      |
    | real numbers               |  0.0  | 1.0     | 1       |  200      |
    | real numbers in start      |  0.0  | 12      | 12      |  200      |
    | real numbers in limit      |  1    | 1.4599  | 1       |  200      |
    | only start                 |  1    |         | 12      |  200      |
    | only limit                 |       | 25      | 12      |  200      |
    | without start and limit    |       |         | 12      |  200      |


Scenario Outline: Get order type of Descending and Acending
    Given I request "cases/unassigned?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  | 12      |
    | Order for Descending       | desc | 12      |
    

Scenario Outline: Get order type of Process Category 
    Given I request "cases/unassigned?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  | 12      |
    | Filter all categories                      |                                   | 12      |


Scenario Outline: Get order type of Process 
    Given I request "cases/unassigned?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 35894775350ec7daa099378048029617 | 6       |
    | Filter all cases                                 |                                  | 12      |


Scenario Outline: Get order type of Search 
    Given I request "cases/unassigned?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 16                               | 1       |
    | Filter all cases                                                |                                  | 12      |



Scenario: Returns a list of the cases for the logged in user (Paused)
    Given I request "cases/paused"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 12 records


Scenario Outline: Get paging of list Paused
    Given I request "cases/paused/paged?start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

     Examples:
    
    | test_description           | start | limit   | records | http_code |
    | lowercase in start         |   a   | 1       | 1       |  200      |
    | uppercase in start         |   A   | 1       | 1       |  200      |
    | lowercase in Limit         |   1   | a       | 12      |  200      |
    | uppercase in Limit         |   1   | A       | 12      |  200      |
    | limit=3                    |   1   | 3       | 3       |  200      |
    | start=3                    |   3   | 5       | 5       |  200      |
    | limit and start =3         |   3   | 3       | 3       |  200      |
    | high number for start      | 1000  | 1       | 0       |  200      |
    | high number for start      | 1000  | 0       | 0       |  200      |
    | empty result               |   1   | 0       | 12      |  200      |
    | empty string               |   1   | 10000   | 12      |  200      |
    | invalid start              |   b   | 25      | 12      |  200      |
    | invalid limit              |   1   | c       | 12      |  200      |
    | start equals zero          |   0   | 20      | 12      |  200      |
    | search 0                   |   0   | 0       | 12      |  200      |
    | search 0                   |   0   | 100     | 12      |  200      |
    | negative numbers in start  |  -10  | 25      | 3       |  200      |
    | negative numbers in limit  |   1   | -25     | 12      |  200      |
    | real numbers               |  0.0  | 1.0     | 1       |  200      |
    | real numbers in start      |  0.0  | 12      | 12      |  200      |
    | real numbers in limit      |  1    | 1.4599  | 1       |  200      |
    | only start                 |  1    |         | 12      |  200      |
    | only limit                 |       | 25      | 12      |  200      |
    | without start and limit    |       |         | 12      |  200      |


Scenario Outline: Get order type of Descending and Acending
    Given I request "cases/paused?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  | 12      |
    | Order for Descending       | desc | 12      |

    
Scenario Outline: Get order type of Process Category 
    Given I request "cases/paused?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  | 1       |
    | Filter all categories                      |                                   | 12      |


Scenario Outline: Get order type of Process 
    Given I request "cases/paused?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 48270290453359748c82a76038662132 | 2       |
    | Filter all cases                                 |                                  | 12      |


Scenario Outline: Get order type of Search 
    Given I request "cases/paused?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records 

Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 16                               | 4       |
    | Filter all cases                                                |                                  | 12      |


Scenario: Returns a list of the cases for the logged in user (Advanced Search)
    Given I request "cases/advanced-search"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 30 records


Scenario Outline: Get paging of list Advanced Search
    Given I request "cases/advanced-search/paged?start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the response has <records> records in property "data"

     Examples:
    
    | test_description           | start | limit   | records | http_code |
    | lowercase in start         |   a   | 1       | 1       |  200      |
    | uppercase in start         |   A   | 1       | 1       |  200      |
    | lowercase in Limit         |   1   | a       | 30      |  200      |
    | uppercase in Limit         |   1   | A       | 30      |  200      |
    | limit=3                    |   1   | 3       | 3       |  200      |
    | start=3                    |   3   | 5       | 5       |  200      |
    | limit and start =3         |   3   | 3       | 3       |  200      |
    | high number for start      | 1000  | 1       | 0       |  200      |
    | high number for start      | 1000  | 0       | 0       |  200      |
    | empty result               |   1   | 0       | 30      |  200      |
    | empty string               |   1   | 10000   | 83      |  200      |
    | invalid start              |   b   | 25      | 25      |  200      |
    | invalid limit              |   1   | c       | 30      |  200      |
    | start equals zero          |   0   | 20      | 20      |  200      |
    | search 0                   |   0   | 0       | 30      |  200      |
    | search 0                   |   0   | 100     | 83      |  200      |
    | negative numbers in start  |  -10  | 25      | 25      |  200      |
    | negative numbers in limit  |   1   | -25     | 25      |  200      |
    | real numbers               |  0.0  | 1.0     | 1       |  200      |
    | real numbers in start      |  0.0  | 12      | 12      |  200      |
    | real numbers in limit      |  1    | 1.4599  | 1       |  200      |
    | only start                 |  1    |         | 30      |  200      |
    | only limit                 |       | 25      | 25      |  200      |
    | without start and limit    |       |         | 30      |  200      |


Scenario Outline: Get order type of Descending and Acending
    Given I request "cases/advanced-search?dir=<dir>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    
    | test_description           | dir  | records |
    | Order for Acending         | asc  | 30      |
    | Order for Descending       | desc | 30      |
    

Scenario Outline: Get order type of Process Category 
    Given I request "cases/advanced-search?cat_uid=<cat_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    
    | test_description                           | cat_uid                           | records |
    | Filter for Category "Category Cases Lists" | 4177095085330818c324501061677193  | 30      |
    | Filter all categories                      |                                   | 30      |



Scenario Outline: Get order type of Process 
    Given I request "cases/advanced-search?pro_uid=<pro_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records 

    Examples:
    
    | test_description                                 | pro_uid                          | records |
    | Filter for cases "Derivation rules - sequential" | 99209594750ec27ea338927000421575 | 2       |
    | Filter all cases                                 |                                  | 30      |



Scenario Outline: Get order type of Search 
    Given I request "cases/advanced-search?search=<search>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    
    | test_description                                                | search                           | records |
    | Filter for cases "Derivation rules - Parallel -> Case number 6" | 17                               | 16      |
    | Filter all cases                                                |                                  | 30      |


Scenario Outline: Get order for Status
    Given I request "cases/advanced-search?app_status=<app_status>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    
    | test_description           | app_status  | records |
    | Filter Status = All Status |             | 30      |
    | Filter Status = Completed  | COMPLETED   | 2       |
    | Filter Status = Draft      | DRAFT       | 15      |             
    | Filter Status = To Do      | TO_DO       | 30      |


Scenario Outline: Get order for User
    Given I request "cases/advanced-search?usr_uid=<usr_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    
    | test_description           | usr_uid                          | records |
    | Filter Status = All User   |                                  | 30      |
    | Filter Status = aaron      | 51049032352d56710347233042615067 | 4       |
    | Filter Status = admin      | 00000000000000000000000000000001 | 30      |             
    | Filter Status = chris      | 24166330352d56730cdd525035621101 | 7       |


Scenario Outline: Get order for date
    Given I request "cases/advanced-search?date_from=<date_from>&date_to=<date_to>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    
    | test_description         | date_from  | date_to    | records |
    | Filter date = 2014-03-01 | 2014-03-01 | 2014-03-31 | 7       |
    | Filter date = 2014-03-15 | 2014-03-15 | 2014-04-01 | 30      |