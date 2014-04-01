@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents cases
Requirements:
    a workspace with one case of the process "Test Output Document Case" 
    and there are six Output Documents in the process

Background:
    Given that I have a valid access_token


Scenario: Returns a list of the generated documents for a given cases
    Given I request "cases/33125846153383cecdf64f1079330191/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 6 records
    

Scenario: Returns an generated document for a given case
    Given I request "cases/33125846153383cecdf64f1079330191/output-document/3000248055333006ab56a01005891659"
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
        And I request "cases/33125846153383cecdf64f1079330191/output-document"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And store "app_doc_uid" in session array as variable "app_doc_uid_0"
        
        

Scenario: Delete an uploaded or generated document from a case.
        And that I want to delete a resource with the key "app_doc_uid_0" stored in session array
        Gie I request "output-document"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
