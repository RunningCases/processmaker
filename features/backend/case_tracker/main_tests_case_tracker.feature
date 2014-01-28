@ProcessMakerMichelangelo @RestAPI
Feature: Case Tracker Main Tests
    Requirements:
    a workspace with the process 50259961452d82bf57f4f62051572528 ("Sample Project #4 (Case Tracker)") already loaded
    there are one Output Documents in the process and one Input Document

    Background:
        Given that I have a valid access_token

    
    Scenario: Get Case Tracker data of a Project
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/property"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    
    Scenario Outline: Update Case Tracker data of a Project and then check if the values has changed
      Given PUT this data:
      """
        {
            "map_type": "<map_type>",
            "routing_history": <routing_history>,
            "message_history": <message_history>
        }
        """
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/property"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | test_description                                            | map_type   | routing_history | message_history |
        | Update map_type = STAGE, Routing=true, message=true         | STAGES     | 1               | 1               |
        | Update map_type = STAGE, Routing=false, message=false       | STAGES     | 0               | 0               |
        | Update map_type = STAGE, Routing=true, message=false        | STAGES     | 1               | 0               |
        | Update map_type = STAGE, Routing=false, message=true        | STAGES     | 0               | 1               |
        | Update map_type = NONE, Routing=yes, message=true           | STAGES     | 1               | 1               |
        | Update map_type = PROCESS MAP, Routing=false, message=true  | PROCESSMAP | 0               | 1               |
        | Update map_type = PROCESS MAP, Routing=false, message=false | PROCESSMAP | 0               | 0               |
        | Update map_type = PROCESS MAP, Routing=true, message=false  | PROCESSMAP | 1               | 0               |
        | Update map_type = PROCESS MAP, Routing=true, message=true   | PROCESSMAP | 1               | 1               |


    Scenario: Get Case Tracker data of a Project
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/property"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "map_type" is set to "PROCESSMAP"
        And that "routing_history" is set to "true"
        And that "message_history" is set to "true"

    
    Scenario: Get the Case Trackers Objects of a Project when there are exactly zero objects
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array


    Scenario: Get list available Case Tracker Objects of a Project when there are exactly 3 objects (one dynaform, one input document and 1 output document)
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/available-objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 3 records


    Scenario Outline: Assigning objects to process case tracker
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

        | test_description       | i | cto_type_obj    | cto_uid_obj                      | cto_condition | cto_position |
        | Assign dynaform        | 0 | DYNAFORM        | 14761752652d82c592fc180020076851 |               | 1            |
        | Assign Input Document  | 1 | INPUT_DOCUMENT  | 87236534052d82c6d8c67d1001895377 |               | 2            |
        | Assign Output Document | 2 | OUTPUT_DOCUMENT | 76247354052d82ca9d04509043789234 |               | 3            |

    
    Scenario: Get list available Case Tracker Objects of a Project when there are exactly 0 objects
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/available-objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 records

    

    Scenario Outline: Update a Case Tracker object of a Project and then check if the values has changed
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

        | test_description                            | i | cto_condition  |
        | Update dynaform, field cto_condition        | 0 | @@YEAR == 2011 |
        | Update input document, field cto_condition  | 1 | @@YEAR == 2012 |
        | Update output document, field cto_condition | 2 | @@YEAR == 2013 |

    
    Scenario Outline: Get a single Case Tracker Object of a Project to verify the update 
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
        | i | cto_type_obj    | cto_uid_obj                      | cto_condition  | cto_position | obj_title           | obj_description |
        | 0 | DYNAFORM        | 14761752652d82c592fc180020076851 | @@YEAR == 2011 | 1            | DynaForm Demo       | Description     |
        | 1 | INPUT_DOCUMENT  | 87236534052d82c6d8c67d1001895377 | @@YEAR == 2012 | 2            | InputDocument Demo  | Description     |
        | 2 | OUTPUT_DOCUMENT | 76247354052d82ca9d04509043789234 | @@YEAR == 2013 | 3            | OutputDocument Demo | Description     |


    Scenario: Get the Case Trackers Objects of a Project when there are exactly three objects
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 3 records


       
    Scenario Outline: Delete all Case Tracker Objects of a Project created previously in this script
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
        | 2 |

