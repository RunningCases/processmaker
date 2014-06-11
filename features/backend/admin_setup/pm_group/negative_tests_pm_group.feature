@ProcessMakerMichelangelo @RestAPI
Feature: PM Group Negative Tests

  Background:
    Given that I have a valid access_token

    Scenario Outline: Get list Groups of workspace using different filters with bad parameters (negative tests)
            And I request "groups?filter=<filter>&start=<start>&limit=<limit>"
            And the content type is "application/json"
            Then the response status code should be <error_code>
                    
    
            Examples:
    
            | test_description      | filter | start | limit   | records | error_code |
            | Invalid start         | a      |   b   | c       | 0       |  400       |
            | Invalid limit         | a      |   0   | c       | 0       |  400       |
            | real numbers          | a      |  0.1  | 1.4599  | 0       |  400       |
            | real numbers          | a      |  1.5  | 1.4599  | 0       |  400       |
            | real numbers          | a      |  0.0  | 1.0     | 1       |  400       |
            | real numbers          | a      |  0.0  | 0.0     | 0       |  400       |
    
    
    Scenario Outline: Create new Group with bad parameters (negative tests)
            Given POST this data:
            """
            {
                "grp_title": "<grp_title>",
                "grp_status": "<grp_status>"
            }
            """
            And I request "group"
            Then the response status code should be <error_code>
            And the response status message should have the following text "<error_message>"
    
            Examples:
    
            | grp_title                   | grp_uid_number | grp_status | grp_title | error_code | error_message |
            | Field required grp_title    | 1              | ACTIVE     |           |    400     | grp_title     |
            | Field required grp_status   | 2              |            | test      |    400     | grp_status    |
            | Create group with same name | 4              | ACTIVE     | Employees |    400     | exists        |


       

    Scenario: Assign users to groups exist in workspace with bad parameters (negative tests)
        Given POST this data:
        """
        {
            "usr_uid": "0000000000000000444500000001"
        }
        """
        And I request "group/66623507552d56742865613066097298/user"
        And the content type is "application/json"
        Then the response status code should be 400
        And the response status message should have the following text "usr_uid"


    Scenario: Assign the same user to the group
        Given POST this data:
        """
        {
            "usr_uid": "00000000000000000000000000000001"
        }
        """
        And I request "group/70084316152d56749e0f393054862525/user"
        Then the response status code should be 400
        And the response status message should have the following text "already assigned"