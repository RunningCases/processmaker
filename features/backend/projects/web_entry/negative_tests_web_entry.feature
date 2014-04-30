@ProcessMakerMichelangelo @RestAPI
Feature: Webentry Negative Tests


Background:
    Given that I have a valid access_token

Scenario Outline: Create a new Web Entry using the method: PHP pages with Web Services with bad parameters (negative tests)
    Given POST this data:
    """
    {
        "tas_uid": "<tas_uid>",
        "dyn_uid": "<dyn_uid>",
        "usr_uid": "<usr_uid>",
        "we_title": "<we_title>",
        "we_description": "pruebas negativas",
        "we_method": "<we_method>",
        "we_input_document_access": 1
    }
    """
    And I request "project/<project>/web-entry"
    And the content type is "application/json"
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"

    Examples:

    | test_description            | project                          | tas_uid                          | dyn_uid                          | usr_uid                          | we_title       | we_method | error_code | error_message |
    | Invalid method              | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | 60308801852e66b7181ae21045247174 | 00000000000000000000000000000001 | DynaForm Demo1 | JS        | 400        | method        |
    | Fiel  required prj_uid      |                                  | 56118778152e66babcc2103002009439 | 99869771852e66b7dc4b858088901665 | 00000000000000000000000000000001 | DynaForm Demo2 | HTML      | 400        | prj_uid       |
    | Field required tas_uid      | 28733629952e66a362c4f63066393844 |                                  | 60308801852e66b7181ae21045247174 | 00000000000000000000000000000001 | DynaForm Demo1 | WS        | 400        | tas_uid       |
    | Field required dyn_uid      | 28733629952e66a362c4f63066393844 | 56118778152e66babcc2103002009439 |                                  | 00000000000000000000000000000001 | DynaForm Demo2 | HTML      | 400        | dyn_uid       |
    | Field required method       | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | 60308801852e66b7181ae21045247174 | 00000000000000000000000000000001 | DynaForm Demo1 |           | 400        | method        |
    | Field required usr_uid      | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | 60308801852e66b7181ae21045247174 |                                  | DynaForm Demo1 | WS        | 400        | usr_uid       |
    | Field required we_title     | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | 60308801852e66b7181ae21045247174 | 00000000000000000000000000000001 |                | WS        | 400        | we_title      |
    | Field required we_method    | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | 60308801852e66b7181ae21045247174 | 00000000000000000000000000000001 | DynaForm Demo1 |           | 400        | method        |
         
    
Scenario Outline: Create a new Web Entry using the method: Single HTML
    Given POST this data:
    """
    {
        "tas_uid": "<tas_uid>",
        "dyn_uid": "<dyn_uid>",
        "we_title": "<we_title>",
        "we_description": "pruebas negativas",
        "we_method": "<we_method>",
        "we_input_document_access": 1
    }
    """
    And I request "project/<project>/web-entry"
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"

    Examples:

    | test_description            | project                          | tas_uid                          | dyn_uid                          | we_title       | we_method | error_code | error_message |
    | Invalid method              | 28733629952e66a362c4f63066393844 | 18096002352e66bc1643af8048493068 | 37977455352e66b892babe6071295002 | DynaForm Demo1 | JS        | 400        | method        |
    | Field required prj_uid      |                                  | 18096002352e66bc1643af8048493068 | 37977455352e66b892babe6071295002 | DynaForm Demo2 | HTML      | 400        | prj_uid       |
    | Field required tas_uid      | 28733629952e66a362c4f63066393844 |                                  | 37977455352e66b892babe6071295002 | DynaForm Demo1 | WS        | 400        | tas_uid       |
    | Field required dyn_uid      | 28733629952e66a362c4f63066393844 | 18096002352e66bc1643af8048493068 |                                  | DynaForm Demo2 | HTML      | 400        | dyn_uid       |
    | Field required method       | 28733629952e66a362c4f63066393844 | 18096002352e66bc1643af8048493068 | 37977455352e66b892babe6071295002 | DynaForm Demo1 |           | 400        | method        |


Scenario: Create a new Web Entry using the method: PHP pages with Web Services with bad parameters (we_input_document_access)
    Given POST this data:
    """
    {
        "tas_uid": "18096002352e66bc1643af8048493068",
        "dyn_uid": "37977455352e66b892babe6071295002",
        "usr_uid": "00000000000000000000000000000001",
        "we_title": "Field wrong - we_method",
        "we_description": "pruebas negativas",
        "we_method": "WS",
        "we_input_document_access": 67
    }
    """
    And I request "project/28733629952e66a362c4f63066393844/web-entry"
    Then the response status code should be 400
    And the response status message should have the following text "we_input_document_access"


