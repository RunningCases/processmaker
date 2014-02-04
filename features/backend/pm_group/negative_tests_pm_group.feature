@ProcessMakerMichelangelo @RestAPI
Feature: PM Group Negative Tests

  Background:
    Given that I have a valid access_token

    Scenario Outline: Get list Groups of workspace using different filters with bad parameters (negative tests)
            And I request "groups?filter=<filter>&start=<start>&limit=<limit>"
            And the content type is "application/json"
            Then the response status code should be <error_code>
                    
    
            Examples:
    
            | test_description      | filter | start | limit   | records | http_code |
            | Invalid start         | a      |   b   | c       | 0       |  400      |
            | Invalid limit         | a      |   0   | c       | 0       |  400      |
            | real numbers          | a      |  0.1  | 1.4599  | 0       |  400      |
            | real numbers          | a      |  1.5  | 1.4599  | 0       |  400      |
    
    
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
    
            | grp_title                  | grp_status | error_code | error_message |
            | Field requered grp_title   | ACTIVE     |    400     | grp_title     |
            | Field requered grp_status  | ACTIVE     |    400     | grp_status    |
          
           