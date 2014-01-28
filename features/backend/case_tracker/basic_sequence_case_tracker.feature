@ProcessMakerMichelangelo @RestAPI
Feature: Case Tracker
    Background:
        Given that I have a valid access_token

    #CASE TRACKER

    #GET /api/1.0/{workspace}/project/{prj_uid}/case-tracker/property
    #    Get Case Tracker data of a Project
    Scenario: Get Case Tracker data of a Project
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/property"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    #PUT /api/1.0/{workspace}/project/{prj_uid}/case-tracker/property
    #    Update Case Tracker data of a Project
    Scenario: Update Case Tracker data of a Project
        And PUT this data:
        """
        {
            "map_type": "NONE",
            "routing_history": 1,
            "message_history": 0
        }
        """
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/property"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    #GET /api/1.0/{workspace}/project/{prj_uid}/case-tracker/property
    #    Get Case Tracker data of a Project
    Scenario: Get Case Tracker data of a Project
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/property"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "map_type" is set to "NONE"
        And that "routing_history" is set to "1"
        And that "message_history" is set to "0"

    #CASE TRACKER OBJECT

    #GET /api/1.0/{workspace}/project/{prj_uid}/case-tracker/objects
    #    Get list Case Tracker Objects of a Project
    Scenario: Get list Case Tracker Objects of a Project
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #POST /api/1.0/{workspace}/project/{prj_uid}/case-tracker/object
    #     Assign an Object to a Project
    Scenario Outline: Assign an Object to a Project
        Given POST this data:
        """
        {
            "cto_type_obj": "<cto_type_obj>",
            "cto_uid_obj": "<cto_uid_obj>",
            "cto_condition": "<cto_condition>",
            "cto_position": <cto_position>
        }
        """
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/object"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "cto_uid" in session array as variable "cto_uid<i>"

        Examples:
        | i | cto_type_obj    | cto_uid_obj                      | cto_condition | cto_position |
        | 0 | DYNAFORM        | 14761752652d82c592fc180020076851 |               | 1            |
        | 1 | INPUT_DOCUMENT  | 87236534052d82c6d8c67d1001895377 |               | 2            |

    #PUT /api/1.0/{workspace}/project/{prj_uid}/case-tracker/object/{cto_uid}
    #    Update a Case Tracker Object for a Project
    Scenario Outline: Update a Case Tracker Object for a Project
        Given PUT this data:
        """
        {
            "cto_condition": "<cto_condition>"
        }
        """
        And that I want to update a resource with the key "cto_uid" stored in session array as variable "cto_uid<i>"
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/object"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i | cto_condition  |
        | 0 | @@YEAR == 2011 |
        | 1 | @@YEAR == 2012 |

    #GET /api/1.0/{workspace}/project/{prj_uid}/case-tracker/objects
    #    Get list Case Tracker Objects of a Project
    Scenario Outline: Get list Case Tracker Objects of a Project
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "cto_type_obj" property in row <i> equals "<cto_type_obj>"
        And the "cto_uid_obj" property in row <i> equals "<cto_uid_obj>"
        And the "cto_condition" property in row <i> equals "<cto_condition>"
        And the "cto_position" property in row <i> equals "<cto_position>"
        And the "obj_title" property in row <i> equals "<obj_title>"
        And the "obj_description" property in row <i> equals "<obj_description>"

        Examples:
        | i | cto_type_obj   | cto_uid_obj                      | cto_condition  | cto_position | obj_title          | obj_description |
        | 0 | DYNAFORM       | 14761752652d82c592fc180020076851 | @@YEAR == 2011 | 1            | DynaForm Demo      | Description     |
        | 1 | INPUT_DOCUMENT | 87236534052d82c6d8c67d1001895377 | @@YEAR == 2012 | 2            | InputDocument Demo | Description     |

    #GET /api/1.0/{workspace}/project/{prj_uid}/case-tracker/available-objects
    #    Get list available Case Tracker Objects of a Project
    Scenario Outline: Get list available Case Tracker Objects of a Project
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/available-objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "obj_uid" property in row <i> equals "<obj_uid>"
        And the "obj_title" property in row <i> equals "<obj_title>"
        And the "obj_description" property in row <i> equals "<obj_description>"
        And the "obj_type" property in row <i> equals "<obj_type>"

        Examples:
        | i | obj_uid                          | obj_title           | obj_description | obj_type        |
        | 0 | 76247354052d82ca9d04509043789234 | OutputDocument Demo | Description     | OUTPUT_DOCUMENT |

    #GET /api/1.0/{workspace}/project/{prj_uid}/case-tracker/object/{cto_uid}
    #    Get a single Case Tracker Object of a Project
    Scenario Outline: Get a single Case Tracker Object of a Project
        Given that I want to get a resource with the key "cto_uid" stored in session array as variable "cto_uid<i>"
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/object"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "cto_type_obj" is set to "<cto_type_obj>"
        And that "cto_uid_obj" is set to "<cto_uid_obj>"
        And that "cto_condition" is set to "<cto_condition>"
        And that "cto_position" is set to "<cto_position>"
        And that "obj_title" is set to "<obj_title>"
        And that "obj_description" is set to "<obj_description>"

        Examples:
        | i | cto_type_obj   | cto_uid_obj                      | cto_condition  | cto_position | obj_title          | obj_description |
        | 0 | DYNAFORM       | 14761752652d82c592fc180020076851 | @@YEAR == 2011 | 1            | DynaForm Demo      | Description     |
        | 1 | INPUT_DOCUMENT | 87236534052d82c6d8c67d1001895377 | @@YEAR == 2012 | 2            | InputDocument Demo | Description     |

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/case-tracker/object/{cto_uid}
    #       Delete a Case Tracker Object of a Project
    Scenario Outline: Delete a Case Tracker Object of a Project
        Given that I want to delete a resource with the key "cto_uid" stored in session array as variable "cto_uid<i>"
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/object"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i |
        | 0 |
        | 1 |

