@ProcessMakerMichelangelo @RestAPI
Feature: Input Documents cases - negative
Requirements:
    a workspace with one case of the process "Test Input Document Case" 
    and there are three Input Document in the process and the verify in one case

Background:
    Given that I have a valid access_token


Scenario: Returns a list of the uploaded documents for a given case
    Given I request "cases/170220159534214f642abb8058832900/input-documents"
    Then the response status code should be 400

Scenario Outline: Post metadata and then upload documents for a given case
        Given POST this data:
            """
            {

                "inp_doc_uid": "<inp_doc_uid>",
                "tas_uid": "<tas_uid>",
                "app_doc_comment": "<app_doc_comment>"
             

            }
            """
        And I request "cases/<case_uid>/input-document"
         Then the response status code should be 400
         And the response status message should have the following text "<error_message>"
        

        Examples:
        | test_description      | case_uid                         | inp_doc_uid                      | tas_uid                          | app_doc_comment | error_message |
        | Incorrect tas_uid     | 170220159534214f642abb8058832933 | 68671480353319e5e1dee74089764900 |                                  | comment 1       | tas_uid       |
        | Incorrect inp_doc_uid | 170220159534214f642abb8058832933 |                                  | 19582733053319e304cfa76025663570 | comment 1       | inp_doc_uid   |
        | No file               | 170220159534214f642abb8058832933 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       | filename      |
        