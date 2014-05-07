@ProcessMakerMichelangelo @RestAPI
Feature: Group
    Background:
        Given that I have a valid access_token

    #GROUP

    #GET /api/1.0/{workspace}/groups?filter={filter}&start={start}&limit={limit}
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

    #GET /api/1.0/{workspace}/groups?filter={filter}&start={start}&limit={limit}
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

    #GET /api/1.0/{workspace}/groups?filter={filter}&start={start}&limit={limit}
    #    Get list Groups
    Scenario: Get list Groups
        And I request "groups?filter=for basic behat"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #GROUP - USER

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

    #POST /api/1.0/{workspace}/group/{grp_uid}/user
    #     Assign User to Group
    Scenario Outline: Assign User to Group
        Given POST this data:
        """
        {
            "usr_uid": "<usr_uid>"
        }
        """
        And I request "group/grp_uid<i>/user" with the key "grp_uid<i>" stored in session array
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i | usr_uid                          |
        | 0 | 00000000000000000000000000000001 |

    #GET /api/1.0/{workspace}/group/{grp_uid}/users?filter={filter}&start={start}&limit={limit}
    #    List assigned Users to Group
    Scenario Outline: List assigned Users to Group
        And I request "group/grp_uid<i>/users" with the key "grp_uid<i>" stored in session array
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "usr_uid" property in row <i> equals "<usr_uid>"
        And the "usr_username" property in row <i> equals "<usr_username>"
        And the "usr_status" property in row <i> equals "<usr_status>"

        Examples:
        | i | usr_uid                          | usr_username | usr_status |
        | 0 | 00000000000000000000000000000001 | admin        | ACTIVE     |

    #GET /api/1.0/{workspace}/group/{grp_uid}/available-users?filter={filter}&start={start}&limit={limit}
    #    List available Users to assign to Group
    Scenario Outline: List available Users to assign to Group
        And I request "group/grp_uid<i>/available-users?filter=none" with the key "grp_uid<i>" stored in session array
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

        Examples:
        | i |
        | 0 |

    #DELETE /api/1.0/{workspace}/group/{grp_uid}/user/{usr_uid}
    #       Unassign User of the Group
    Scenario Outline: Unassign User of the Group
        Given that I want to delete a resource with the key "obj_uid" stored in session array
        And I request "group/grp_uid<i>/user/<usr_uid>" with the key "grp_uid<i>" stored in session array
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i | usr_uid                          |
        | 0 | 00000000000000000000000000000001 |

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

    #GET /api/1.0/{workspace}/groups?filter={filter}&start={start}&limit={limit}
    #    Get list Groups
    Scenario: Get list Groups
        And I request "groups?filter=for basic behat"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

