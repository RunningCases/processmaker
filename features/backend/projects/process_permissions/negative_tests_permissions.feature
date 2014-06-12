@ProcessMakerMichelangelo @RestAPI
Feature: Process Permissions Negative tests

  Background:
    Given that I have a valid access_token

  Scenario Outline: Create a new Process Permission with bad parameters (negative tests)
    Given POST this data:
        """
        {
            "op_case_status": "<op_case_status>",
            "tas_uid": "<tas_uid>",
            "op_user_relation": "<op_user_relation>",
            "usr_uid": "<usr_uid>",
            "op_task_source" : "<op_task_source>",
            "op_participate": "<op_participate>",
            "op_obj_type": "<op_obj_type>",
            "dynaforms" : "<dynaforms>",
            "inputs" : "<inputs>",
            "outputs" : "<outputs>",                
            "op_action": "<op_action>"
        }
        """
        And I request "project/67021149152e27240dc54d2095572343/process-permission"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"
        
        Examples:

        | test_description         | op_case_status  | tas_uid                          | op_user_relation| usr_uid                          | op_task_source                   | op_participate | op_obj_type  | dynaforms                        | inputs                           | outputs                          | op_action | error_code | error_message    |
        | Invalid urs_uid          | ALL             |                                  | 1               | 00000002256780000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      | 400        | user             |     
        | Invalid op_user_relation | ALL             |                                  | 5               | 25286582752d56713231082039265791 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     | 400        | op_user_relation |
        | Invalid op_user_relation | ALL             |                                  | 0               | 25286582752d56713231082039265791 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     | 400        | op_user_relation |
        | Invalid op_case_status   | LISTALL         |                                  | 2               | 54731929352d56741de9d42002704749 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     | 400        | op_case_status   |
        | Invalid op_participate   | ALL             |                                  | 1               | 32444503652d5671778fd20059078570 | 36792129552e27247a483f6069605623 | 3              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     | 400        | op_participate   |          
        | Invalid op_obj_type      | ALL             |                                  | 1               | 16333273052d567284e6766029512960 | 36792129552e27247a483f6069605623 | 0              | TRIGGERS     |                                  |                                  |                                  | BLOCK     | 400        | op_obj_type      |
        | Invalid tas_uid          | ALL             | 12345678909876543210000000000000 | 1               | 34289569752d5673d310e82094574281 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     | 400        | tas_uid          |
        | Invalid op_task_source   | DRAFT           | 55416900252e272492318b9024750146 | 1               | 00000000000000000000000000000001 | 36792129552000000000000069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      | 400        | task             |
        | Inavlid uid Dynaforms    | DRAFT           | 55416900252e272492318b9024750146 | 1               | 11206717452d5673913aa69053050085 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859000000000000084654094971976 |                                  |                                  | BLOCK     | 400        | dynaform         |
        | Inavlid uid Inputs       | ALL             |                                  | 1               | 34289569752d5673d310e82094574281 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 35345345346999999999999888888888 |                                  | BLOCK     | 400        | inp_uid          |
        | Inavlid uid Outputs      | DRAFT           | 55416900252e272492318b9024750146 | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 22424242424242442424242424242424 | VIEW      | 400        | out_uid          |
        | Invalid op_user_relation |                 |                                  |                 |                                  |                                  |                |              |                                  |                                  |                                  |           | 400        | usr_uid          |
        | Invalid op_action        | ALL             |                                  | 5               | 25286582752d56713231082039265791 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  |           | 400        | op_user_relation |