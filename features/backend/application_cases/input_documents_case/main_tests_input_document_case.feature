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
    And the response has 5 records


Scenario Outline: Returns an uploaded documents for a given case
    Given I request "cases/64654381053382b8bb4c415067063003/input-document/<input-document>"
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

    | test_description                         | input-document                   | app_doc_uid                      | app_doc_filename             | doc_uid                          | app_doc_version | app_doc_create_date | app_doc_create_user     | app_doc_type | app_doc_index | app_doc_link                                                    |
    | Get Input "Desert.jpg"                   | 6075490825331a1c5eebff9015468244 | 6075490825331a1c5eebff9015468244 | Desert.jpg                   | 68671480353319e5e1dee74089764900 | 1               | 2014-03-25 11:33:25 | , Administrator (admin) | INPUT        | 1             | cases/cases_ShowDocument?a=6075490825331a1c5eebff9015468244&v=1 |
    | Get Input "Screenshot Case Archive.docx" | 3770386635331a1f49c78e8070071944 | 3770386635331a1f49c78e8070071944 | Screenshot Case Archive.docx | 68671480353319e5e1dee74089764900 | 1               | 2014-03-25 11:34:12 | , Administrator (admin) | INPUT        | 2             | cases/cases_ShowDocument?a=3770386635331a1f49c78e8070071944&v=1 |
    | Get Input "alert_message.html"           | 6382509235331a235b27a82003894796 | 6382509235331a235b27a82003894796 | alert_message.htm            | 68588088053319e68d88f67081331478 | 1               | 2014-03-25 11:35:17 | , Administrator (admin) | INPUT        | 4             | cases/cases_ShowDocument?a=6382509235331a235b27a82003894796&v=1 |
    | Get Input "actionsByEmail-2.5.0.28.tar"  | 3548449385331a24a34d273018695729 | 3548449385331a24a34d273018695729 | actionsByEmail-2.5.0.28.tar  | 68588088053319e68d88f67081331478 | 1               | 2014-03-25 11:35:38 | , Administrator (admin) | INPUT        | 5             | cases/cases_ShowDocument?a=3548449385331a24a34d273018695729&v=1 |
    | Get Input "Step_ordenamiento (5).pm"     | 3814366275331a21b80d603018480738 | 3814366275331a21b80d603018480738 | Step_ordenamiento (5).pm     | 68588088053319e68d88f67081331478 | 2               | 2014-03-25 11:36:11 | , Administrator (admin) | INPUT        | 6             | cases/cases_ShowDocument?a=3814366275331a21b80d603018480738&v=2 |


    Scenario Outline: Post metadata and then upload documents for a given case
        Given POST upload an input document "<document_file>" to "cases/<case_uid>/input-document"
            """
            {

                "inp_doc_uid": "<inp_doc_uid>",
                "tas_uid": "<tas_uid>",
                "app_doc_comment": "<app_doc_comment>"
             

            }
            """
         Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "app_doc_uid" in session array as variable "app_doc_uid_<app_doc_uid_number>"

        Examples:
        | app_doc_uid_number | document_file                      | case_uid                         | inp_doc_uid                      | tas_uid                          | app_doc_comment |
        | 1                  | /home/wendy/uploadfiles/test1.html | 64654381053382b8bb4c415067063003 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       |


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