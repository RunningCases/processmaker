@ProcessMakerMichelangelo @RestAPI
Feature: Input Documents cases
Requirements:
    a workspace with one case of the process "Test Input Document Case" 
    and there are three Input Document in the process

Background:
    Given that I have a valid access_token


Scenario: Returns a list of the uploaded documents for a given case
    Given I request "cases/170220159534214f642abb8058832933/input-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 9 records


Scenario: Returns an uploaded documents for a given case
    Given I request "cases/170220159534214f642abb8058832933/input-document/925833635534215b9148a64026212674"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    

Scenario: Post metadata and then upload documents for a given case
        Given POST upload an input document "/home/wendy/uploadfiles/test1.html" to "cases/170220159534214f642abb8058832933/input-document"
            """
            {

                "inp_doc_uid": "68671480353319e5e1dee74089764900",
                "tas_uid": "19582733053319e304cfa76025663570",
                "app_doc_comment": "app_doc_comment"
             

            }
            """
         Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "app_doc_uid" in session array


Scenario: Delete an uploaded or generated document from a case.
        Given that I want to delete a resource with the key "app_doc_uid" stored in session array
        And I request "cases/170220159534214f642abb8058832933/input-document"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
