@ProcessMakerMichelangelo @RestAPI
Feature: Input Documents cases
Requirements:
    a workspace with one case of the process "Test Input Document Case" 
    and there are three Input Document in the process

Background:
    Given that I have a valid access_token


Scenario: Returns a list of the uploaded documents for a given case
    Given I request "cases/64654381053382b8bb4c415067063003/input-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 5 records


Scenario: Returns an uploaded documents for a given case
    Given I request "cases/64654381053382b8bb4c415067063003/input-document/6075490825331a1c5eebff9015468244"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    

Scenario: Post metadata and then upload documents for a given case
        Given POST upload an input document "/home/wendy/uploadfiles/test1.html" to "cases/64654381053382b8bb4c415067063003/input-document"
            """
            {

                "inp_doc_uid": "68671480353319e5e1dee74089764900",
                "tas_uid": "19582733053319e304cfa76025663570",
                "app_doc_comment": "app_doc_comment"
             

            }
            """
         Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "" in session array


Scenario: Delete an uploaded or generated document from a case.
        Given that I want to delete a resource with the key "" stored in session array
        And I request "cases/64654381053382b8bb4c415067063003/input-document/{app_doc_uid}"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
