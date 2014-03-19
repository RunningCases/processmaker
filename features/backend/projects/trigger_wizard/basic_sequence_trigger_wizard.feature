@ProcessMakerMichelangelo @RestAPI
Feature: Group
    Requirements:
        a workspace with the process 14414793652a5d718b65590036026581 ("Sample Project #1") already loaded
        there are three activities in the process

    Background:
        Given that I have a valid access_token

    #GET /api/1.0/{workspace}/project/{prj_uid}/trigger-wizards
    #    Get list Trigger Wizards
    Scenario Outline: Get list Trigger Wizards
        And I request "project/14414793652a5d718b65590036026581/trigger-wizards"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "lib_name" property in row <i> equals "<lib_name>"
        And the "lib_title" property in row <i> equals "<lib_title>"
        And the "lib_class_name" property in row <i> equals "<lib_class_name>"

        Examples:
        | i | lib_name    | lib_title              | lib_class_name        |
        | 0 | pmFunctions | ProcessMaker Functions | class.pmFunctions.php |

    #GET /api/1.0/{workspace}/project/{prj_uid}/trigger-wizard/{lib_name}
    #    Get a single Library
    Scenario Outline: Get a single Library
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "lib_name" is set to "<lib_name>"
        And that "lib_title" is set to "<lib_title>"
        And that "lib_class_name" is set to "<lib_class_name>"

        Examples:
        | lib_name    | lib_title              | lib_class_name        |
        | pmFunctions | ProcessMaker Functions | class.pmFunctions.php |

    #GET /api/1.0/{workspace}/project/{prj_uid}/trigger-wizard/{lib_name}/{fn_name}
    #    Get a single Function of the Library
    Scenario Outline: Get a single Function of the Library
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "fn_name" is set to "<fn_name>"
        And that "fn_label" is set to "<fn_label>"

        Examples:
        | lib_name    | fn_name                 | fn_label             |
        | pmFunctions | PMFAddAttachmentToArray | Add Element in Array |

    #GET /api/1.0/{workspace}/project/{prj_uid}/triggers
    #    Get a List of triggers of a project
    Scenario: Get a List of triggers of a project
        And I request "project/14414793652a5d718b65590036026581/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #POST /api/1.0/{workspace}/project/{prj_uid}/trigger-wizard/{lib_name}/{fn_name}
    #     Create new Trigger
    Scenario Outline: Create new Trigger
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
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "tri_uid" in session array as variable "tri_uid<i>"

        Examples:
        | i | lib_name    | fn_name                 | tri_title  | tri_description | tri_type | tri_params.input.arrayData | tri_params.input.index | tri_params.input.value | tri_params.input.suffix | tri_params.output.tri_answer |
        | 0 | pmFunctions | PMFAddAttachmentToArray | My trigger |                 | SCRIPT   | array(1, 2)                | 1                      | 2                      | My Copy({i})            | $respuesta                   |

    #PUT /api/1.0/{workspace}/project/{prj_uid}/trigger-wizard/{lib_name}/{fn_name}/{tri_uid}
    #    Update Trigger
    Scenario Outline: Update Trigger
        Given PUT this data:
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
        And that I want to update a resource with the key "tri_uid" stored in session array as variable "tri_uid<i>"
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i | lib_name    | fn_name                 | tri_title     | tri_description | tri_type | tri_params.input.arrayData | tri_params.input.index | tri_params.input.value | tri_params.input.suffix | tri_params.output.tri_answer |
        | 0 | pmFunctions | PMFAddAttachmentToArray | My trigger... | ...             | SCRIPT   | array(1, 2, 3, 4)          | 1                      | 2                      | My Copy2({i})           | $r                           |

    #GET /api/1.0/{workspace}/project/{prj_uid}/trigger-wizard/{lib_name}/{fn_name}/{tri_uid}
    #    Get a Trigger that was created with the wizard
    Scenario Outline: Get a Trigger that was created with the wizard
        Given that I want to get a resource with the key "tri_uid" stored in session array as variable "tri_uid<i>"
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "tri_title" is set to "<tri_title>"
        And that "tri_description" is set to "<tri_description>"
        And that "tri_type" is set to "<tri_type>"

        Examples:
        | i | lib_name    | fn_name                 | tri_title     | tri_description | tri_type |
        | 0 | pmFunctions | PMFAddAttachmentToArray | My trigger... | ...             | SCRIPT   |

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/trigger/{tri_uid}
    #       Delete a trigger of a project
    Scenario Outline: Delete a trigger of a project
        Given that I want to delete a resource with the key "tri_uid" stored in session array as variable "tri_uid<i>"
        And I request "project/14414793652a5d718b65590036026581/trigger"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i |
        | 0 |

    #GET /api/1.0/{workspace}/project/{prj_uid}/triggers
    #    Get a List of triggers of a project
    Scenario: Get a List of triggers of a project
        And I request "project/14414793652a5d718b65590036026581/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

