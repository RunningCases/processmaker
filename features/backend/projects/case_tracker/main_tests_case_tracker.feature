@ProcessMakerMichelangelo @RestAPI
Feature: Case Tracker Main Tests
    Requirements:
    a workspace with the process 50259961452d82bf57f4f62051572528 ("Sample Project #4 (Case Tracker)") already loaded
    there are one Output Documents in the process and one Input Document
    and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded

    Background:
        Given that I have a valid access_token

    Scenario Outline: Get Case Tracker data of a Project
        And I request "project/<project>/case-tracker/property"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the "map_type" property equals "<map_type>"
        And the "routing_history" property equals "<routing_history>"
        And the "message_history" property equals "<message_history>"

        Examples:

        | test_description                                | project                          | map_type   | routing_history | message_history |
        | Get of process Sample Project #4 (Case Tracker) | 50259961452d82bf57f4f62051572528 | PROCESSMAP | 1               | 1               |
        | Get of process Process Complete BPMN            | 1455892245368ebeb11c1a5001393784 | PROCESSMAP | 1               | 1               |
        
    
    Scenario Outline: Update Case Tracker data of a Project and then check if the values has changed
      Given PUT this data:
      """
        {
            "map_type": "<map_type>",
            "routing_history": <routing_history>,
            "message_history": <message_history>
        }
        """
        And I request "project/<project>/case-tracker/property"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | test_description                                                 | project                          | map_type   | routing_history | message_history |
        | Update map_type = STAGE, Routing=true, message=true         .pm  | 50259961452d82bf57f4f62051572528 | STAGES     | 1               | 1               |
        | Update map_type = STAGE, Routing=false, message=false       .pm  | 50259961452d82bf57f4f62051572528 | STAGES     | 0               | 0               |
        | Update map_type = STAGE, Routing=true, message=false        .pm  | 50259961452d82bf57f4f62051572528 | STAGES     | 1               | 0               |
        | Update map_type = STAGE, Routing=false, message=true        .pm  | 50259961452d82bf57f4f62051572528 | STAGES     | 0               | 1               |
        | Update map_type = NONE, Routing=yes, message=true           .pm  | 50259961452d82bf57f4f62051572528 | STAGES     | 1               | 1               |
        | Update map_type = PROCESS MAP, Routing=false, message=true  .pm  | 50259961452d82bf57f4f62051572528 | PROCESSMAP | 0               | 1               |
        | Update map_type = PROCESS MAP, Routing=false, message=false .pm  | 50259961452d82bf57f4f62051572528 | PROCESSMAP | 0               | 0               |
        | Update map_type = PROCESS MAP, Routing=true, message=false  .pm  | 50259961452d82bf57f4f62051572528 | PROCESSMAP | 1               | 0               |
        | Update map_type = PROCESS MAP, Routing=true, message=true   .pm  | 50259961452d82bf57f4f62051572528 | PROCESSMAP | 1               | 1               |
        | Update map_type = STAGE, Routing=true, message=true         .pmx | 1455892245368ebeb11c1a5001393784 | STAGES     | 1               | 1               |
        | Update map_type = STAGE, Routing=false, message=false       .pmx | 1455892245368ebeb11c1a5001393784 | STAGES     | 0               | 0               |
        | Update map_type = STAGE, Routing=true, message=false        .pmx | 1455892245368ebeb11c1a5001393784 | STAGES     | 1               | 0               |
        | Update map_type = STAGE, Routing=false, message=true        .pmx | 1455892245368ebeb11c1a5001393784 | STAGES     | 0               | 1               |
        | Update map_type = NONE, Routing=yes, message=true           .pmx | 1455892245368ebeb11c1a5001393784 | STAGES     | 1               | 1               |
        | Update map_type = PROCESS MAP, Routing=false, message=true  .pmx | 1455892245368ebeb11c1a5001393784 | PROCESSMAP | 0               | 1               |
        | Update map_type = PROCESS MAP, Routing=false, message=false .pmx | 1455892245368ebeb11c1a5001393784 | PROCESSMAP | 0               | 0               |
        | Update map_type = PROCESS MAP, Routing=true, message=false  .pmx | 1455892245368ebeb11c1a5001393784 | PROCESSMAP | 1               | 0               |
        | Update map_type = PROCESS MAP, Routing=true, message=true   .pmx | 1455892245368ebeb11c1a5001393784 | PROCESSMAP | 1               | 1               |


    Scenario Outline: Get Case Tracker data of a Project
        And I request "project/<project>/case-tracker/property"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "map_type" is set to "<map_type>"
        And that "routing_history" is set to "<routing_history>"
        And that "message_history" is set to "<message_history>"

        Examples:

        | test_description                                | project                          | map_type   | routing_history | message_history |
        | Get of process Sample Project #4 (Case Tracker) | 50259961452d82bf57f4f62051572528 | PROCESSMAP | true            | true            |
        | Get of process Process Complete BPMN            | 1455892245368ebeb11c1a5001393784 | PROCESSMAP | true            | true            |
        
    
    Scenario: Get the Case Trackers Objects of a Project when there are exactly zero objects in the process .pm
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array


      Scenario: Get the Case Trackers Objects of a Project when there are exactly one objects in the process .pmx
        And I request "project/1455892245368ebeb11c1a5001393784/case-tracker/objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 1 record
                

    Scenario Outline: Get list available Case Tracker Objects of a Project
        And I request "project/<project>/case-tracker/available-objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

        Examples:

        | test_description                    | project                          | records |
        | Available objects of a project .pm  | 50259961452d82bf57f4f62051572528 | 3       | 
        | Available objects of a project .pmx | 1455892245368ebeb11c1a5001393784 | 19      |   
  

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
        And I request "project/<project>/case-tracker/object"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "cto_uid" in session array as variable "cto_uid<i>"

        Examples:

        | test_description                         | i | project                          | cto_type_obj    | cto_uid_obj                      | cto_condition | cto_position |
        | Assign dynaform        of a project .pm  | 1 | 50259961452d82bf57f4f62051572528 | DYNAFORM        | 14761752652d82c592fc180020076851 |               | 1            |
        | Assign Input Document  of a project .pm  | 2 | 50259961452d82bf57f4f62051572528 | INPUT_DOCUMENT  | 87236534052d82c6d8c67d1001895377 |               | 2            |
        | Assign Output Document of a project .pm  | 3 | 50259961452d82bf57f4f62051572528 | OUTPUT_DOCUMENT | 76247354052d82ca9d04509043789234 |               | 3            |
        | Assign dynaform        of a project .pmx | 4 | 1455892245368ebeb11c1a5001393784 | DYNAFORM        | 216663520536be3024555e8038205940 |               | 2            |
        | Assign Input Document  of a project .pmx | 5 | 1455892245368ebeb11c1a5001393784 | INPUT_DOCUMENT  | 880391746536be961e594e7014524130 |               | 3            |
        | Assign Output Document of a project .pmx | 6 | 1455892245368ebeb11c1a5001393784 | OUTPUT_DOCUMENT | 218529141536be955f0b646092366402 |               | 4            |

    
    Scenario Outline: Get list available Case Tracker Objects of a Project when there are exactly 0 objects
        And I request "project/<project>/case-tracker/available-objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

        Examples:

        | test_description                    | project                          | records |
        | Available objects of a project .pm  | 50259961452d82bf57f4f62051572528 | 0       | 
        | Available objects of a project .pmx | 1455892245368ebeb11c1a5001393784 | 16      | 
    

    Scenario Outline: Update a Case Tracker object of a Project and then check if the values has changed
        Given PUT this data:
        """
        {
            "cto_condition": "<cto_condition>"
        }
        """
        And that I want to update a resource with the key "cto_uid" stored in session array as variable "cto_uid<i>"
        And I request "project/<project>/case-tracker/object"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | test_description                                 | i | project                          | cto_condition  |
        | Update dynaform, field cto_condition        .pm  | 1 | 50259961452d82bf57f4f62051572528 | @@YEAR == 2011 |
        | Update input document, field cto_condition  .pm  | 2 | 50259961452d82bf57f4f62051572528 | @@YEAR == 2012 |
        | Update output document, field cto_condition .pm  | 3 | 50259961452d82bf57f4f62051572528 | @@YEAR == 2013 |
        | Update dynaform, field cto_condition        .pmx | 4 | 1455892245368ebeb11c1a5001393784 | @@YEAR == 2011 |
        | Update input document, field cto_condition  .pmx | 5 | 1455892245368ebeb11c1a5001393784 | @@YEAR == 2012 |
        | Update output document, field cto_condition .pmx | 6 | 1455892245368ebeb11c1a5001393784 | @@YEAR == 2013 |

    
    Scenario Outline: Get a single Case Tracker Object of a Project to verify the update 
        Given that I want to get a resource with the key "cto_uid" stored in session array as variable "cto_uid<i>"
        And I request "project/<project>/case-tracker/object"
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
        | test_description                 | i | project                          | cto_type_obj    | cto_uid_obj                      | cto_condition  | cto_position | obj_title           | obj_description |
        | Get after update of process .pm  | 1 | 50259961452d82bf57f4f62051572528 | DYNAFORM        | 14761752652d82c592fc180020076851 | @@YEAR == 2011 | 1            | DynaForm Demo       | Description     |
        | Get after update of process .pm  | 2 | 50259961452d82bf57f4f62051572528 | INPUT_DOCUMENT  | 87236534052d82c6d8c67d1001895377 | @@YEAR == 2012 | 2            | InputDocument Demo  | Description     |
        | Get after update of process .pm  | 3 | 50259961452d82bf57f4f62051572528 | OUTPUT_DOCUMENT | 76247354052d82ca9d04509043789234 | @@YEAR == 2013 | 3            | OutputDocument Demo | Description     |
        | Get after update of process .pmx | 4 | 1455892245368ebeb11c1a5001393784 | DYNAFORM        | 216663520536be3024555e8038205940 | @@YEAR == 2011 | 2            | DynaForm Demo       | Description     |
        | Get after update of process .pmx | 5 | 1455892245368ebeb11c1a5001393784 | INPUT_DOCUMENT  | 880391746536be961e594e7014524130 | @@YEAR == 2012 | 3            | InputDocument Demo  | Description     |
        | Get after update of process .pmx | 6 | 1455892245368ebeb11c1a5001393784 | OUTPUT_DOCUMENT | 218529141536be955f0b646092366402 | @@YEAR == 2013 | 4            | OutputDocument Demo | Description     |


    Scenario Outline: Get the Case Trackers Objects of a Project when there are exactly three objects
        And I request "project/<project>/case-tracker/objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

        Examples:

        | test_description                    | project                          | records |
        | Available objects of a project .pm  | 50259961452d82bf57f4f62051572528 | 3       | 
        | Available objects of a project .pmx | 1455892245368ebeb11c1a5001393784 | 4       |  

       
    Scenario Outline: Delete all Case Tracker Objects of a Project created previously in this script
        Given that I want to delete a resource with the key "cto_uid" stored in session array as variable "cto_uid<i>"
        And I request "project/50259961452d82bf57f4f62051572528/case-tracker/object"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i |
        | 1 |
        | 2 |
        | 3 |
        | 4 |
        | 5 |
        | 6 |