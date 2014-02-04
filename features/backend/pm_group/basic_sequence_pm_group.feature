@ProcessMakerMichelangelo @RestAPI
Feature: Group
    Background:
        Given that I have a valid access_token

    #GET /api/1.0/{workspace}/groups?filter=abc&start=0&limit=25
    #    Get list Groups
    Scenario: Get list Groups
        And I request "groups?filter=for basic behat"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #POST /api/1.0/{workspace}/group
    #     Create new Group
    Scenario Outline: Create new Group
        Given POST this data:
        """
        {
            "grp_title": "<grp_title>",
            "grp_status": "<grp_status>"
        }
        """
        And I request "group"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "grp_uid" in session array as variable "grp_uid<i>"

        Examples:
        | i | grp_title                   | grp_status |
        | 0 | Demo Group1 for basic behat | ACTIVE     |
        | 1 | Demo Group2 for basic behat | ACTIVE     |

    #PUT /api/1.0/{workspace}/group/{grp_uid}
    #    Update Group
    Scenario Outline: Update Group
        Given PUT this data:
        """
        {
            "grp_status": "<grp_status>"
        }
        """
        And that I want to update a resource with the key "grp_uid" stored in session array as variable "grp_uid<i>"
        And I request "group"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i | grp_status |
        | 0 | INACTIVE   |
        | 1 | INACTIVE   |

    #GET /api/1.0/{workspace}/groups?filter=abc&start=0&limit=25
    #    Get list Groups
    Scenario Outline: Get list Groups
        And I request "groups?filter=for basic behat"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "grp_title" property in row <i> equals "<grp_title>"
        And the "grp_status" property in row <i> equals "<grp_status>"
        And the "grp_users" property in row <i> equals "<grp_users>"
        And the "grp_tasks" property in row <i> equals "<grp_tasks>"

        Examples:
        | i | grp_title                   | grp_status | grp_users | grp_tasks |
        | 0 | Demo Group1 for basic behat | INACTIVE   | 0         | 0         |
        | 1 | Demo Group2 for basic behat | INACTIVE   | 0         | 0         |

    #GET /api/1.0/{workspace}/group/{grp_uid}
    #    Get a single Group
    Scenario Outline: Get a single Group
        Given that I want to get a resource with the key "grp_uid" stored in session array as variable "grp_uid<i>"
        And I request "group"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "grp_title" is set to "<grp_title>"
        And that "grp_status" is set to "<grp_status>"
        And that "grp_users" is set to "<grp_users>"
        And that "grp_tasks" is set to "<grp_tasks>"

        Examples:
        | i | grp_title                   | grp_status | grp_users | grp_tasks |
        | 0 | Demo Group1 for basic behat | INACTIVE   | 0         | 0         |
        | 1 | Demo Group2 for basic behat | INACTIVE   | 0         | 0         |

    #DELETE /api/1.0/{workspace}/group/{grp_uid}
    #       Delete Group
    Scenario Outline: Delete Group
        Given that I want to delete a resource with the key "grp_uid" stored in session array as variable "grp_uid<i>"
        And I request "group"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i |
        | 0 |
        | 1 |

    #GET /api/1.0/{workspace}/groups?filter=abc&start=0&limit=25
    #    Get list Groups
    Scenario: Get list Groups
        And I request "groups?filter=for basic behat"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

