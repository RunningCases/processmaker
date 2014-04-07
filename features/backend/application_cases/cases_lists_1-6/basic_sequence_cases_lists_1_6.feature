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
    

Scenario: Get paging of list inbox
    Given I request "cases/paged"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Descending 
    Given I request "cases?dir=DESC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Acending 
    Given I request "cases?dir=ASC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process Category 
    Given I request "cases?category=4177095085330818c324501061677193"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process 
    Given I request "cases?process=99209594750ec27ea338927000421575"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Search 
    Given I request "cases?search=92535130653271a60de2e73021469732"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

     
Scenario: Returns a list of the cases for the logged in user (Draft)
    Given I request "cases/draft"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    

Scenario: Get paging of list Draft
    Given I request "cases/draft/paged"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Descending 
    Given I request "cases/draft?dir=DESC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Acending 
    Given I request "cases/draft?dir=ASC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process Category 
    Given I request "cases/draft?category=4177095085330818c324501061677193"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process 
    Given I request "cases/draft?process=99209594750ec27ea338927000421575"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Search 
    Given I request "cases/draft?search=92535130653271a60de2e73021469732"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Returns a list of the cases for the logged in user (Participated)
    Given I request "cases/participated"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    

Scenario: Get paging of list Participated
    Given I request "cases/participated/paged"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Descending 
    Given I request "cases/participated?dir=DESC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Acending 
    Given I request "cases/participated?dir=ASC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process Category 
    Given I request "cases/participated?category=4177095085330818c324501061677193"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process 
    Given I request "cases/participated?process=99209594750ec27ea338927000421575"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Search 
    Given I request "cases/participated?search=92535130653271a60de2e73021469732"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

     
Scenario: Returns a list of the cases for the logged in user (Unassigned)
    Given I request "cases/unassigned"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    

Scenario: Get paging of list Unassigned
    Given I request "cases/unassigned/paged"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Descending 
    Given I request "cases/unassigned?dir=DESC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Acending 
    Given I request "cases/unassigned?dir=ASC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process Category 
    Given I request "cases/unassigned?category=4177095085330818c324501061677193"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process 
    Given I request "cases/unassigned?process=99209594750ec27ea338927000421575"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Search 
    Given I request "cases/unassigned?search=92535130653271a60de2e73021469732"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

    
Scenario: Returns a list of the cases for the logged in user (Paused)
    Given I request "cases/paused"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    

Scenario: Get paging of list Paused
    Given I request "cases/paused/paged"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Descending 
    Given I request "cases/paused?dir=DESC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Acending 
    Given I request "cases/paused?dir=ASC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process Category 
    Given I request "cases/paused?category=4177095085330818c324501061677193"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process 
    Given I request "cases/paused?process=99209594750ec27ea338927000421575"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Search 
    Given I request "cases/paused?search=92535130653271a60de2e73021469732"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

    
Scenario: Returns a list of the cases for the logged in user (Advanced Search)
    Given I request "cases/advanced-search"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    

Scenario: Get paging of list Advanced Search
    Given I request "cases/advanced-search/paged"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Descending 
    Given I request "cases/advanced-search?dir=DESC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get order type of Acending 
    Given I request "cases/advanced-search?dir=ASC"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process Category 
    Given I request "cases/advanced-search?category=4177095085330818c324501061677193"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Process 
    Given I request "cases/advanced-search?process=99209594750ec27ea338927000421575"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario: Get order type of Search 
    Given I request "cases/advanced-search?search=92535130653271a60de2e73021469732"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"