@ProcessMakerMichelangelo @RestAPI
Feature: PM User Main Tests
  Requirements:
    a workspace with the 63 users created already loaded
    there are one users Active Directory in the process

    Background:
    Given that I have a valid access_token

    Scenario Outline: Get list Users of workspace using different filters with bad parameters (negative tests)
        And I request "users?filter=<filter>&start=<start>&limit=<limit>"
        And the content type is "application/json"
        Then the response status code should be <error_code>
                          
        Examples:
    
        | test_description      | filter | start | limit   | records | error_code |
        | Invalid start         | a      |   b   | c       | 0       |  400       |
        | Invalid limit         | a      |   0   | c       | 0       |  400       |
        | real numbers          | a      |  0.1  | 1.4599  | 0       |  400       |
        | real numbers          | a      |  1.5  | 1.4599  | 0       |  400       |
        

    Scenario Outline: Create new User with bad parameters (negative tests)
        Given POST this data:
        """
            {
                "usr_firstname": "<usr_firstname>",
                "usr_lastname": "<usr_lastname>",
                "usr_username": "<usr_username>",
                "usr_email": "<usr_email>",
                "usr_address": "<usr_address>",
                "usr_zip_code": "<usr_zip_code>",
                "usr_country": "<usr_country>",
                "usr_city": "<usr_city>",
                "usr_location": "<usr_location>",
                "usr_phone": "<usr_phone>",
                "usr_position": "<usr_position>",
                "usr_replaced_by": "<usr_replaced_by>",
                "usr_due_date": "<usr_due_date>",
                "usr_calendar": "<usr_calendar>",
                "usr_status": "<usr_status>",
                "usr_role": "<usr_role>",
                "usr_new_pass": "<usr_new_pass>",
                "usr_cnf_pass": "<usr_cnf_pass>"
            }
        """
        And I request "user"
        Then the response status code should be <error_code>
        And the type is "<type>"
        And the response status message should have the following text "<error_message>"
        

        Examples:
     
        | Test_description             | usr_firstname | usr_lastname | usr_username | usr_email              | usr_address | usr_zip_code | usr_country   | usr_city | usr_location | usr_phone    | usr_position   | usr_replaced_by                  | usr_due_date | usr_calendar                     | usr_status | usr_role               | usr_new_pass | usr_cnf_pass | error_code |  type  | error_message   |   
        | Invalid usr_email            | jhon          | smith        | jhon         | jhonsmith              | grenn #344  | 555-6555     | US            | FL       | MIA          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  | ACTIVE     | PROCESSMAKER_OPERATOR  | sample       | sample       |    400     | string | usr_email       | 
        | Invalid usr_country          | will          | carter       | will         | will@gmail.com         | saim #45    | 555-6522     | BOLIVIA       | L        | LPB          | 23344444     | Adminsitracion | 44811996752d567110634a1013636964 | 2014-12-12   |                                  | ACTIVE     | PROCESSMAKER_MANAGER   | sample       | sample       |    400     | string | usr_country     | 
        | Invalid usr_city             | saraah        | sandler      | saraah       | saraah@gmail.com       | laberh #985 | 555-9999     | AR            | BOLIVIA  | BUE          | 2353643644   | Desarrollo     | 61364466452d56711adb378002702791 | 2014-12-12   | 99159704252f501c63f8c58025859967 | ACTIVE     | PROCESSMAKER_ADMIN     | admin        | admin        |    400     | string | usr_city        | 
        | Invalid usr_location         | daniela       | perez        | daniela      | daniela@gmail.com      | grenn #544  | 555-6565     | US            | FL       | MIAMI        | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  | INACTIVE   | PROCESSMAKER_OPERATOR  | sample       | sample       |    400     | string | usr_location    | 
        | Invalid usr_replaced_by      | micaela       | sanchez      | micaela      | micaela@gmail.com      | sancjh #544 | 555-6652     | BO            | L        | LPB          | 555-6655-555 | Gerencia       | 61364466400000000000333333333333 | 2016-02-15   |                                  | VACATION   | PROCESSMAKER_OPERATOR  | sample       | sample       |    400     | string | usr_replaced_by | 
        | Invalid usr_due_date         | jhon          | smith        | jhon         | jhon@gmail.com         | grenn #344  | 555-6555     | AR            | B        | BUE          | 555-6655-555 | Gerencia       |                                  | sample       |                                  | ACTIVE     | PROCESSMAKER_OPERATOR  | sample       | sample       |    400     | string | usr_due_date    | 
        | Invalid usr_status           | will          | carter       | will         | will@gmail.com         | saim #45    | 555-6522     | US            | FL       | MIA          | 23344444     | Adminsitracion | 44811996752d567110634a1013636964 | 2014-12-12   |                                  | INPUT      | PROCESSMAKER_MANAGER   | sample       | sample       |    400     | string | usr_status      | 
        | Invalid usr_role             | saraah        | sandler      | saraah       | saraah@gmail.com       | laberh #985 | 555-9999     | BO            | L        | LPB          | 2353643644   | Desarrollo     | 61364466452d56711adb378002702791 | 2014-12-12   | 99159704252f501c63f8c58025859967 | ACTIVE     | INPUT_DOCUMENT         | admin        | admin        |    400     | string | usr_role        | 
        | Without usr_firstname        |               | perez        | daniel       | daniela@gmail.com      | grenn #544  | 555-6565     | AR            | B        | BUE          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  | INACTIVE   | PROCESSMAKER_OPERATOR  | sample       | sample       |    400     | string | usr_firstname   | 
        | Without usr_lastname         | micaela       |              | brayan       | micaela@gmail.com      | sancjh #544 | 555-6652     | US            | FL       | MIA          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  | VACATION   | PROCESSMAKER_OPERATOR  | sample       | sample       |    400     | string | usr_lastname    | 
        | Without usr_username         | jhon          | smith        |              | jhon@gmail.com         | grenn #344  | 555-6555     | BO            | L        | LPB          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  | ACTIVE     | PROCESSMAKER_OPERATOR  | sample       | sample       |    400     | string | usr_username    | 
        | Without usr_email            | will          | carter       | herbert      |                        | saim #45    | 555-6522     | AR            | B        | BUE          | 23344444     | Adminsitracion | 44811996752d567110634a1013636964 | 2014-12-12   |                                  | ACTIVE     | PROCESSMAKER_MANAGER   | sample       | sample       |    400     | string | usr_email       | 
        | Without usr_due_date         | saraah        | sandler      | sarahh       | saraah@gmail.com       | laberh #985 | 555-9999     | US            | FL       | MIA          | 2353643644   | Desarrollo     | 61364466452d56711adb378002702791 |              | 99159704252f501c63f8c58025859967 | ACTIVE     | PROCESSMAKER_ADMIN     | admin        | admin        |    400     | string | usr_due_date    | 
        | Without usr_status           | daniela       | perez        | daniella     | daniela@gmail.com      | grenn #544  | 555-6565     | US            | FL       | MIA          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  |            | PROCESSMAKER_OPERATOR  | sample       | sample       |    400     | string | usr_status      | 
        | Without usr_role             | micaela       | sanchez      | micaeella    | micaela@gmail.com      | sancjh #544 | 555-6652     | BO            | L        | LPB          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  | VACATION   |                        | sample       | sample       |    400     | string | usr_role        | 
        | Wrong password               | jhon          | smith        | jhon         | jhon@gmail.com         | grenn #344  | 555-6555     | AR            | B        | BUE          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  | ACTIVE     | PROCESSMAKER_OPERATOR  | sample       | igual        |    400     | string | same            | 
        | Short length of the password | will          | carter       | will         | will@gmail.com         | saim #45    | 555-6522     | US            | FL       | MIA          | 23344444     | Adminsitracion | 44811996752d567110634a1013636964 | 2014-12-12   |                                  | ACTIVE     | PROCESSMAKER_MANAGER   | hol          | hol          |    400     | string | Minimum length  |
        | Create with User exist       | Peter         | Vanko        | peter        | colosaqatest@gmail.com |             |              |               |          |              |              |                |                                  | 2016-02-15   |                                  | ACTIVE     | PROCESSMAKER_OPERATOR  | sample       | sample       |    400     | string | exists          |  


Scenario: Delete a pm_user when have asigned cases (negative tests) "amy with 4 cases"
    Given that I want to delete a "User"
    And I request "user/25286582752d56713231082039265791"
    Then the response status code should be 400
    And the response status message should have the following text "cannot be deleted"   