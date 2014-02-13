@ProcessMakerMichelangelo @RestAPI
Feature: Process of a Project Resources
    Requirements:
        a workspace with the process 14414793652a5d718b65590036026581 ("Sample Project #1") already loaded
        there are three activities in the process

    Background:
        Given that I have a valid access_token

    #GET /api/1.0/{workspace}/project/{prj_uid}/process
    #    Get a single Process
    Scenario Outline: Get a single Process
        Given that I want to get a resource with the key "obj_uid" stored in session array
        And I request "project/14414793652a5d718b65590036026581/process"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "pro_title" is set to "<pro_title>"
        And that "pro_description" is set to "<pro_description>"
        And that "pro_status" is set to "<pro_status>"
        And that "pro_create_user" is set to "<pro_create_user>"
        And that "pro_debug" is set to "<pro_debug>"

        Examples:
        | pro_title         | pro_description | pro_status | pro_create_user                  | pro_debug |
        | Sample Project #1 |                 | ACTIVE     | 00000000000000000000000000000001 | 0         |

    #PUT /api/1.0/{workspace}/project/{prj_uid}/process
    #    Update Process
    Scenario Outline: Update Process
        Given PUT this data:
        """
        {
            "pro_title": "<pro_title>",
            "pro_description": "<pro_description>",
            "pro_status": "<pro_status>",
            "pro_create_user": "<pro_create_user>",
            "pro_debug": <pro_debug>
        }
        """
        And I request "project/14414793652a5d718b65590036026581/process"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | pro_title         | pro_description | pro_status | pro_create_user                  | pro_debug |
        | Sample Project #1 |                 | ACTIVE     | 00000000000000000000000000000001 | 0         |

