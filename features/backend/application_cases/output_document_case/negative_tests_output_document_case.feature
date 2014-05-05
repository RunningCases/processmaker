@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents cases Main Tests
Requirements:
    a workspace with one case of the process "Test Output Document Case" 
    and there are six Output Documents in the process

Background:
    Given that I have a valid access_token

Scenario Outline: Pull information of an inexistent output document. should return an error
    Given I request "cases/64654381053382b8bb4c415067063003/output-document/<output-document>"
    Then the response status code should be 400
    And the response charset is "app_doc_uid"
    And the type is "array"
    
    Examples:

    | test_description       | output-document                  | app_doc_uid                      | app_doc_filename | doc_uid                          | app_doc_version | app_doc_create_date | app_doc_create_user     | app_doc_type | app_doc_index | app_doc_link                                                    |
    | Get Input "Desert.jpg" | 6075490825eebff9015468244        | 6075490825331a1c5eebff9015468244 | Desert.jpg       | 68671480353319e5e1dee74089764900 | 1               | 2014-03-25 11:33:25 | , Administrator (admin) | OUTPUT       | 1             | cases/cases_ShowDocument?a=6075490825331a1c5eebff9015468244&v=1 |