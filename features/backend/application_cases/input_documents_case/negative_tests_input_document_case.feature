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
        Given POST upload an input document "<document_file>" to "cases/<case_uid>/input-document"
            """
            {

                "inp_doc_uid": "<inp_doc_uid>",
                "tas_uid": "<tas_uid>",
                "app_doc_comment": "<app_doc_comment>"
             

            }
            """
         Then the response status code should be 400
        

        Examples:
        | app_doc_uid_number | document_file                      | case_uid                         | inp_doc_uid                      | tas_uid                          | app_doc_comment |
        | 1                  | /inexistent_test1.html             | 170220159534214f642abb8058832933 | 68671480353319e5e1dee74089764900 | 19582733053319e304cfa76025663570 | comment 1       |
        | 1                  | /home/wendy/uploadfiles/test1.html | 170220159534214f642abb8058832933 | 68671480353319e5e1dee74089764900 |                                  | comment 1       |
        | 1                  | /home/wendy/uploadfiles/test1.html | 170220159534214f642abb8058832933 |                                  | 19582733053319e304cfa76025663570 | comment 1       |
        