@ProcessMakerMichelangelo @RestAPI
Feature: Triggers Wizard Negative Tests


  Background:
    Given that I have a valid access_token

    
    Scenario: Get a single Library with bad parameters (negative tests)
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/sampleqa"
        Then the response status code should be 400
        And the response status message should have the following text "not exist"


    Scenario: Get a single Function of the Library with bad parameters (negative tests)
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/qadesarrollo/funcionexterna"
        Then the response status code should be 400
        And the response status message should have the following text "not exist"


    Scenario Outline: Create new Trigger with bad parameters (negative tests)
        Given POST this data:
        """
        {
            "tri_title": "<tri_title>",
            "tri_description": "<tri_description>",
            "tri_type": "<tri_type>",
            "tri_params": {
                    "input": {
                    "arrayData": "<tri_params.input.arrayData>",
                    "index": "<tri_params.input.index>",
                    "value": "<tri_params.input.value>",
                    "suffix": "<tri_params.input.suffix>"
                },
                "output": {
                    "tri_answer": "<tri_params.output.tri_answer>"
                }
            }
        }
        """
        And I request "project/<project>/trigger-wizard/<lib_name>/<fn_name>"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:
        | test_description                     | project                          | lib_name             | fn_name                 | tri_title    | tri_description | tri_type | tri_params.input.arrayData | tri_params.input.index | tri_params.input.value | tri_params.input.suffix | tri_params.output.tri_answer | error_code | error_message |
        | Field required project               |                                  | pmFunctions          | PMFAddAttachmentToArray | My trigger   | sample          | SCRIPT   | array(1, 2)                | 1                      | 2                      | My Copy({i})            | $respuesta                   | 400        | prj_uid       |
        | Field required tri_title             | 14414793652a5d718b65590036026581 | pmFunctions          | PMFAddAttachmentToArray |              | sample          | SCRIPT   | array(1, 2)                | 1                      | 2                      | My Copy({i})            | $respuesta                   | 400        | tri_title     |
        | Field required tri_type              | 14414793652a5d718b65590036026581 | pmFunctions          | PMFAddAttachmentToArray | My trigger   | sample          |          | array(1, 2)                | 1                      | 2                      | My Copy({i})            | $respuesta                   | 400        | tri_type      |
        | Field required tri_params input      | 14414793652a5d718b65590036026581 | pmFunctions          | PMFAddAttachmentToArray | My trigger   | sample          | SCRIPT   |                            |                        |                        |                         | $respuesta                   | 400        | tri_params    |
        | Field required tri_params output     | 14414793652a5d718b65590036026581 | pmFunctions          | PMFAddAttachmentToArray | My trigger   | sample          | SCRIPT   | array(1, 2)                | 1                      | 2                      | My Copy({i})            |                              | 400        | tri_params    |
        | Invalid lib_name                     | 14414793652a5d718b65590036026581 | processmakerfunction | PMFAddAttachmentToArray | My trigger   | sample          | SCRIPT   | array(1, 2)                | 1                      | 2                      | My Copy({i})            | $respuesta                   | 400        | lib_name      |
        | Invalid tri_type                     | 14414793652a5d718b65590036026581 | pmFunctions          | PMFAddAttachmentToArray | My trigger   | sample          | sample   | array(1, 2)                | 1                      | 2                      | My Copy({i})            | $respuesta                   | 400        | tri_type      |