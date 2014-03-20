@ProcessMakerMichelangelo @RestAPI
Feature: Cases Notes
Requirements:
    a workspace with three cases of the process "Test Users-Step-Properties End Point"

Background:
    Given that I have a valid access_token


Scenario: List of case notes for this case
    Given I request "case/{uid}/notes"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    

 
Scenario: create a new case note for specified case
        Given POST this data:
            """
            {

                

            }
            """
        And I request "case/{uid}/case"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "" in session array


Scenario: Get a specified case note for this case 
    Given that I want to get a resource with the key "" stored in session array
    Given I request "case/{uid}/note/{uid}"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the " property equals ""
    And the "" property equals ""



Scenario: Modify a case note for specified case
        Given PUT this data:
            """
            {
                


            }
            """
        And that I want to update a resource with the key "" stored in session array
        And I request "case/{uid}/case/{uid}"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"


Scenario: Delete a case note for specified case
        Given that I want to delete a resource with the key "" stored in session array
        And I request "case/{uid}/case/{uid}"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
