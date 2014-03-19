@ProcessMakerMichelangelo @RestAPI
Feature: Input Documents cases
Requirements:
    a workspace with three cases of the process "Test Users-Step-Properties End Point"

Background:
    Given that I have a valid access_token


Scenario: Returns a list of the uploaded documents for a given case
    Given I request "case{uid}/input-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    

Scenario: Returns an uploaded documents for a given case
    Given I request "case/{uid}/input-document/{uid}"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    

Scenario: Post metadata and then upload documents for a given case
        Given POST this data:
            """
            {

                

            }
            """
        And I request "case/{uid}/input-document"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "" in session array


Scenario: Delete an uploaded or generated document from a case.
        Given that I want to delete a resource with the key "" stored in session array
        And I request "case/{uid}/input-document/{uid}"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
