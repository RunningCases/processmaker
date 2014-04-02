@ProcessMakerMichelangelo @RestAPI
Feature: Input Documents cases
Requirements:
    a workspace with one case of the process "Test Input Document Case" 
    and there are three Input Document in the process and the verify in one case

Background:
    Given that I have a valid access_token


Scenario: Returns a list of the uploaded documents for a given case
    Given I request "cases/64654381053382b8bb4c415067063003/input-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
   
Scenario Outline: Post metadata and then upload documents for a given case
        Given POST upload an input document "<document_file>" to "cases/<case_uid>/input-document"
            """
            {

                "inp_doc_uid": "<inp_doc_uid>",
                "tas_uid": "<tas_uid>",
                "app_doc_comment": "<app_doc_comment>"
             

            }
            """
         Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "app_doc_uid" in session array as variable "app_doc_uid_<app_doc_uid_number>"

        Examples:
        | app_doc_uid_number | document_file                      | case_uid                         | inp_doc_uid                      | tas_uid                          | app_doc_comment |
        | 1                  | /home/wendy/uploadfiles/test1.html | 64654381053382b8bb4c415067063003 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       |
        | 2                  | /home/wendy/uploadfiles/random.jpg | 64654381053382b8bb4c415067063003 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       |
        | 3                  | /home/wendy/uploadfiles/test.pm    | 64654381053382b8bb4c415067063003 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       |
        | 4                  | /home/wendy/uploadfiles/test.txt   | 64654381053382b8bb4c415067063003 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       |

Scenario Outline: Returns an uploaded documents for a given case
    Given I request "cases/<case_uid>/output-document/app_doc_uid"  with the key "app_doc_uid" stored in session array as variable "app_doc_uid_<app_doc_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the "app_doc_uid" property equals "<app_doc_uid>"
    And the "app_doc_filename" property equals "<app_doc_filename>"
    And the "doc_uid" property equals "<doc_uid>"
    And the "app_doc_version" property equals "<app_doc_version>"
    And the "app_doc_create_date" property equals "<app_doc_create_date>"
    And the "app_doc_create_user" property equals "<app_doc_create_user>"
    And the "app_doc_type" property equals "<app_doc_type>"
    And the "app_doc_index" property equals "<app_doc_index>"
    And the "app_doc_link" property equals "<app_doc_link>"

    
    Examples:
    | test_description                         | app_doc_uid_number | case_uid                         | inp_doc_uid                      | tas_uid                          | app_doc_comment |
    | Get Input "Desert.jpg"                   | 1                  | 64654381053382b8bb4c415067063003 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       |
    | Get Input "Screenshot Case Archive.docx" | 2                  | 64654381053382b8bb4c415067063003 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       |
    | Get Input "alert_message.html"           | 3                  | 64654381053382b8bb4c415067063003 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       |
    | Get Input "actionsByEmail-2.5.0.28.tar"  | 4                  | 64654381053382b8bb4c415067063003 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       |


    
Scenario Outline: Delete an uploaded or generated document from a case.
        Given that I want to delete a resource with the key "app_doc_uid_<app_doc_uid_number>" stored in session array
        And I request "cases/64654381053382b8bb4c415067063003/input-document/"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | app_doc_uid_number |
        | 1                  |
        | 2                  |
        | 3                  |
        | 4                  |