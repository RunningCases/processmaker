@ProcessMakerMichelangelo @RestAPI
Feature: Process Variables
    Requirements:
        a workspace with the process 14414793652a5d718b65590036026581 ("Sample Project #1") already loaded
        there are three activities in the process

    Background:
        Given that I have a valid access_token

    #GET /api/1.0/{workspace}/project/{prj_uid}/variables
    #    Get all variables of a Process
    Scenario Outline: Get all variables of a Process
        And I request "project/14414793652a5d718b65590036026581/variables"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "var_name" property in row <i> equals "<var_name>"

        Examples:
        | i | var_name |
        | 0 | SYS_LANG |
        | 1 | SYS_SKIN |
        | 2 | SYS_SYS  |

    #GET /api/1.0/{workspace}/project/{prj_uid}/grid/variables
    #    Get grid variables of a Process
    Scenario: Get grid variables of a Process
        Given I request "project/14414793652a5d718b65590036026581/grid/variables"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #GET /api/1.0/{workspace}/project/{prj_uid}/grid/{grid_uid}/variables
    #    Get all variables of a Grid
    Scenario Outline: Get all variables of a Grid
        Given I request "project/14414793652a5d718b65590036026581/grid/00000000000000000000000000000000/variables"
        And the content type is "application/json"
        Then the response status code should be <status_code>
        And the response charset is "UTF-8"
        And the type is "object"
        And the response status message should have the following text "<status_message>"

        Examples:
        | status_code | status_message |
        | 400         | grid_uid       |

