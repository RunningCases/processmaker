@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents cases
Requirements:
    a workspace with one case of the process "Test Output Document Case" 
    and there are six Output Documents in the process

Background:
    Given that I have a valid access_token


Scenario: Returns a list of the generated documents for a given cases
    Given I request "cases/24438110553330068247694030259829/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 6 records
    

Scenario: Returns an generated document for a given case
    Given I request "cases/24438110553330068247694030259829/output-document/3000248055333006ab56a01005891659"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "Object"
    

Scenario: Generate or regenerates an output documents for a given case
        Given POST this data:
            """
            {
                "out_doc_uid": "2087233055331ef4127d238097105696"         
            }
            """
        And I request "case/24438110553330068247694030259829/output-document"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        
        

Scenario: Delete an uploaded or generated document from a case.
        Given that I want to delete a resource with the key "" stored in session array
        And I request "output-document/{uid}"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
