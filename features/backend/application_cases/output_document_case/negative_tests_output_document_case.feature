@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents cases Main Tests
Requirements:
    a workspace with one case of the process "Test Output Document Case" 
    and there are six Output Documents in the process

Background:
    Given that I have a valid access_token

Scenario Outline: Pull information of an inexistent input document. should return an error
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