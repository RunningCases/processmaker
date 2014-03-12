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
            "method": "<method>",
            "input_document_access": 1,
            "usr_username": "<usr_username>",
            "usr_password": "<usr_password>"
        }
        """
        And I request "project/<project>/web-entry"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:

        | test_description            | project                          | tas_uid                          | tas_title | dyn_uid                          | dyn_title      | method | usr_username | usr_password | error_code | error_message |
        | Invalid method              | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | Task 1    | 60308801852e66b7181ae21045247174 | DynaForm Demo1 | JS     | admin        | admin        | 400        | method        |
        | Invalid usr_username        | 28733629952e66a362c4f63066393844 | 56118778152e66babcc2103002009439 | Task 2    | 99869771852e66b7dc4b858088901665 | DynaForm Demo2 | JS     | aaro         | admin        | 400        | method        |        
        | Invalid usr_password        | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | Task 1    | 60308801852e66b7181ae21045247174 | DynaForm Demo1 | WS     | admin        | sample       | 400        | password      |
        | Field required prj_uid      |                                  | 56118778152e66babcc2103002009439 | Task 2    | 99869771852e66b7dc4b858088901665 | DynaForm Demo2 | HTML   | admin        | admin        | 400        | prj_uid       |
        | Field required tas_uid      | 28733629952e66a362c4f63066393844 |                                  | Task 1    | 60308801852e66b7181ae21045247174 | DynaForm Demo1 | WS     | admin        | admin        | 400        | tas_uid       |
        | Field required dyn_uid      | 28733629952e66a362c4f63066393844 | 56118778152e66babcc2103002009439 | Task 2    |                                  | DynaForm Demo2 | HTML   | admin        | admin        | 400        | dyn_uid       |
        | Field required method       | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | Task 1    | 60308801852e66b7181ae21045247174 | DynaForm Demo1 |        | admin        | admin        | 400        | method        |
        
    
    Scenario Outline: Create a new Web Entry using the method: Single HTML
        Given POST this data:
        """
        {
            "tas_uid": "<tas_uid>",
            "dyn_uid": "<dyn_uid>",
            "method": "<method>",
            "input_document_access": 1
        }
        """
        And I request "project/<project>/web-entry"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:

        | test_description            | project                          | tas_uid                          | tas_title | dyn_uid                          | dyn_title      | method | error_code | error_message |
        | Invalid method              | 28733629952e66a362c4f63066393844 | 18096002352e66bc1643af8048493068 | Task 1    | 37977455352e66b892babe6071295002 | DynaForm Demo1 | JS     | 400        | method        |
        | Field required prj_uid      |                                  | 18096002352e66bc1643af8048493068 | Task 2    | 37977455352e66b892babe6071295002 | DynaForm Demo2 | HTML   | 400        | prj_uid       |
        | Field required tas_uid      | 28733629952e66a362c4f63066393844 |                                  | Task 1    | 37977455352e66b892babe6071295002 | DynaForm Demo1 | WS     | 400        | tas_uid       |
        | Field required dyn_uid      | 28733629952e66a362c4f63066393844 | 18096002352e66bc1643af8048493068 | Task 2    |                                  | DynaForm Demo2 | HTML   | 400        | dyn_uid       |
        | Field required method       | 28733629952e66a362c4f63066393844 | 18096002352e66bc1643af8048493068 | Task 1    | 37977455352e66b892babe6071295002 | DynaForm Demo1 |        | 400        | method        |
