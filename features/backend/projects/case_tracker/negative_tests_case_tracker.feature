@ProcessMakerMichelangelo @RestAPI
Feature: Case Tracker Negative Tests


  Background:
    Given that I have a valid access_token

  Scenario Outline: Update Case Tracker with bad parameters (negative tests)
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
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:

        | test_description        | project                          | map_type | routing_history | message_history | error_code | error_message   |
        | Invalid map type        | 50259961452d82bf57f4f62051572528 | STAGGEES | 1               | 1               | 400        | map_type        |
        | Invalid Routing History | 50259961452d82bf57f4f62051572528 | STAGES   | 20              | 0               | 400        | routing_history |
        | Invalid Message History | 50259961452d82bf57f4f62051572528 | STAGES   | 1               | 20              | 400        | message_history |
        | Field requered project  |                                  | STAGES   | 0               | 1               | 400        | prj_uid         |
       

   Scenario Outline: Assigning objects to process case tracker with bad parameters (negative tests)
        Given POST this data:
        """
        {
            "cto_type_obj": "<cto_type_obj>",
            "cto_uid_obj": "<cto_uid_obj>",
            "cto_condition": "<cto_condition>",
            "cto_position": "<cto_position>"
        }
        """
        And I request "project/<project>/case-tracker/object"
        Then the response status code should be 400
        And the response status message should have the following text "<error_message>"
        

        Examples:

        | test_description       | project                          | cto_type_obj    | cto_uid_obj                      | cto_condition | cto_position | error_code | error_message  |
        | Invalid cto_type_obj   | 50259961452d82bf57f4f62051572528 | SAMPLE          | 76247354052d82ca9d04509043789234 |               | 1            | 400        | cto_type_obj   |
        | Invalid cto_uid_obj    | 50259961452d82bf57f4f62051572528 | INPUT_DOCUMENT  | 8700000000000006d8c67d1001895377 |               | 2            | 400        | cto_uid_obj    |
        | Invalid cto_position   | 50259961452d82bf57f4f62051572528 | OUTPUT_DOCUMENT | 76247354052d82ca9d04509043789234 |               | 3,9999.87    | 400        | cto_position   | 
        | Field requered project |                                  | DYNAFORM        | 14761752652d82c592fc180020076851 |               | 1            | 400        | prj_uid        |