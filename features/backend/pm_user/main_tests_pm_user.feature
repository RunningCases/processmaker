@ProcessMakerMichelangelo @RestAPI
Feature: User Main Tests
    Requirements:
    a workspace with the 63 users created already loaded
    there are one users Active Directory in the process

    Background:
        Given that I have a valid access_token

    Scenario Outline: Get list Users of workspace using different filters
        And I request "users?filter=<filter>&start=<start>&limit=<limit>"
        And the content type is "application/json"
        Then the response status code should be <http_code>
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

        Examples:

        | test_description      | filter | start | limit   | records | http_code |
        | lowercase             | admin  |   0   | 9       | 1       |  200      |
        | uppercase             | ADMIN  |   0   | 9       | 1       |  200      |
        | limit=3               | a      |   0   | 3       | 3       |  200      |
        | limit and start       | a      |   1   | 2       | 2       |  200      |
        | high number for start | a      | 1000  | 1       | 0       |  200      |
        | high number for start | a      | 1000  | 0       | 0       |  200      |
        | empty result          | xyz    |   0   | 0       | 0       |  200      |
        | empty string          |        |   0   | 10000   | 63      |  200      |
        | empty string          |        |   1   | 2       | 2       |  200      |
        | search 0              | 0      |   0   | 0       | 0       |  200      |
        | search 0              | 0      |   0   | 100     | 0       |  200      |
        | Search letters 'c'    | c      |   0   | 5       | 5       |  200      |
        | Search letters 'de    | de     |   0   | 5       | 2       |  200      |
        | Search not created    | for    |   0   | 25      | 0       |  200      |


    Scenario: Get the users List when there are exactly 63 users
        And I request "users"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 63 records

    
    Scenario: Get list Users
        And I request "user/00000000000000000000000000000001"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the "usr_username" property equals "admin"
        And the "usr_firstname" property equals "Administrator"
        
    
    Scenario Outline: Create new User
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
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "usr_uid" in session array as variable "usr_uid_<usr_number>"
        

        Examples:

        | Test_description                     | usr_number | usr_firstname | usr_lastname | usr_username | usr_email         | usr_address | usr_zip_code | usr_country | usr_city | usr_location | usr_phone    | usr_position   | usr_replaced_by                  | usr_due_date | usr_calendar                     | usr_status | usr_role               | usr_new_pass | usr_cnf_pass |
        | Create without replaced by, calendar | 1          | jhon          | smith        | jhon         | jhon@gmail.com    | grenn #344  | 555-6555     | US          | FL       | MIA          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  | ACTIVE     | PROCESSMAKER_OPERATOR  | sample       | sample       |
        | Create without calendar              | 2          | will          | carter       | will         | will@gmail.com    | saim #45    | 555-6522     | BO          | L        | LPB          | 23344444     | Adminsitracion | 44811996752d567110634a1013636964 | 2014-12-12   |                                  | ACTIVE     | PROCESSMAKER_MANAGER   | sample       | sample       |
        | Create with all fields               | 3          | saraah        | sandler      | saraah       | saraah@gmail.com  | laberh #985 | 555-9999     | AR          | B        | BUE          | 2353643644   | Desarrollo     | 61364466452d56711adb378002702791 | 2014-12-12   | 99159704252f501c63f8c58025859967 | ACTIVE     | PROCESSMAKER_ADMIN     | admin        | admin        |
        | Create user Inactive                 | 4          | daniela       | perez        | daniela      | daniela@gmail.com | grenn #544  | 555-6565     | US          | FL       | MIA          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  | INACTIVE   | PROCESSMAKER_OPERATOR  | sample       | sample       |
        | Create user Vacation                 | 5          | micaela       | sanchez      | micaela      | micaela@gmail.com | sancjh #544 | 555-6652     | US          | FL       | MIA          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   |                                  | VACATION   | PROCESSMAKER_OPERATOR  | sample       | sample       |
        
    
    Scenario Outline: Upload a image 
        Given POST I want to upload the image "<usr_photo>" to user with the key "usr_uid" stored in session array as variable "usr_uid_<usr_number>". Url "user/" 

        Examples:

        | Test_description                     | usr_number | usr_photo            |
        | Create without replaced by, calendar | 1          | /home/wendy/photo/pic1.jpg |
        | Create without calendar              | 2          | /home/wendy/photo/pic2.jpg |
        | Create with all fields               | 3          | /home/wendy/photo/pic3.jpg |
        

    Scenario: Get the users List when there are exactly 63 users
        And I request "users"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 68 records



    Scenario: Get the users List when there are exactly 63 users
        And I request "users"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 68 records


    Scenario Outline: Update User and then check if the values had changed
        Given PUT this data:
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
                "usr_due_date": "<usr_dgit addue_date>",
                "usr_calendar": "<usr_calendar>",
                "usr_status": "<usr_status>",
                "usr_role": "<usr_role>",
                "usr_new_pass": "<usr_new_pass>",
                "usr_cnf_pass": "<usr_cnf_pass>"
            }
        """
        And that I want to update a resource with the key "usr_uid" stored in session array as variable "usr_uid_<usr_number>"
        And I request "user"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        
        Examples:

        | Test_description                                 | usr_number | usr_firstname | usr_lastname | usr_username | usr_email         | usr_address | usr_zip_code | usr_country | usr_city | usr_location | usr_phone    | usr_position   | usr_replaced_by                  | usr_due_date | usr_calendar                     | usr_status | usr_role               | usr_new_pass | usr_cnf_pass |
        | Update usr_calendar, usr_role                    | 1          | jhoohan       | smith        | jhoohan      | jhon@gmail.com    | grenn #344  | 555-6555     | US          | FL       | MIA          | 555-6655-555 | Gerencia       |                                  | 2014-02-15   | 99159704252f501c63f8c58025859967 | ACTIVE     | PROCESSMAKER_ADMIN     | sample       | sample       |
        | Update usr_firstname, usr_lastname, usr_username | 2          | wilian        | carters      | wilian       | will@gmail.com    | saim #45    | 555-6522     | BO          | L        | LPB          | 23344444     | Adminsitracion | 44811996752d567110634a1013636964 | 2014-12-12   |                                  | ACTIVE     | PROCESSMAKER_MANAGER   | sample       | sample       |
        | Update usr_status                                | 3          | sarita        | sandler      | sarita       | saraah@gmail.com  | laberh #985 | 555-9999     | AR          | B        | BUE          | 2353643644   | Desarrollo     | 61364466452d56711adb378002702791 | 2014-12-12   | 99159704252f501c63f8c58025859967 | INACTIVE   | PROCESSMAKER_ADMIN     | admin        | admin        |
        

    Scenario Outline: Get a single Users and check some properties
    Given that I want to get a resource with the key "usr_uid" stored in session array as variable "usr_uid_<usr_number>"
        And I request "user"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "usr_firstname" is set to "<usr_firstname>"
        And that "usr_lastname" is set to "<usr_lastname>"
        And that "usr_username" is set to "<usr_username>"
        And that "usr_email" is set to "<usr_email>"
        And that "usr_address" is set to "<usr_address>"
        And that "usr_zip_code" is set to "<usr_zip_code>"
        And that "usr_country" is set to "<usr_country>"
        And that "usr_city" is set to "<usr_city>"
        And that "usr_location" is set to "<usr_location>"
        And that "usr_phone" is set to "<usr_phone>"
        And that "usr_position" is set to "<usr_position>"
        And that "usr_replaced_by" is set to "<usr_replaced_by>"
        And that "usr_due_date" is set to "<usr_due_date>"
        And that "usr_calendar" is set to "<usr_calendar>"
        And that "usr_status" is set to "<usr_status>"
        And that "usr_role" is set to "<usr_role>"
        And that "usr_new_pass" is set to "<usr_new_pass>"
        And that "usr_cnf_pass" is set to "<usr_cnf_pass>"
        
        Examples:

        | Test_description                                 | usr_number | usr_firstname | usr_lastname | usr_username | usr_email         | usr_address | usr_zip_code | usr_country | usr_city | usr_location | usr_phone    | usr_position   | usr_replaced_by                  | usr_due_date | usr_calendar                     | usr_status | usr_role               | usr_new_pass | usr_cnf_pass |
        | Update usr_calendar, usr_role                    | 1          | jhoohan       | smith        | jhoohan      | jhon@gmail.com    | grenn #344  | 555-6555     | US          | FL       | MIA          | 555-6655-555 | Gerencia       |                                  | 2016-02-15   | 99159704252f501c63f8c58025859967 | ACTIVE     | PROCESSMAKER_ADMIN     | sample       | sample       |
        | Update usr_firstname, usr_lastname, usr_username | 2          | wilian        | carters      | wilian       | will@gmail.com    | saim #45    | 555-6522     | BO          | L        | LPB          | 23344444     | Adminsitracion | 44811996752d567110634a1013636964 | 2014-12-12   |                                  | ACTIVE     | PROCESSMAKER_MANAGER   | sample       | sample       |
        | Update usr_status                                | 3          | sarita        | sandler      | sarita       | saraah@gmail.com  | laberh #985 | 555-9999     | AR          | B        | BUE          | 2353643644   | Desarrollo     | 61364466452d56711adb378002702791 | 2014-12-12   | 99159704252f501c63f8c58025859967 | INACTIVE   | PROCESSMAKER_ADMIN     | admin        | admin        |
        

    Scenario: Get the users List when there are exactly 63 users
        And I request "users"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 68 records
    
    Scenario Outline: Delete all users created previously in this script
        Given that I want to delete a resource with the key "usr_uid" stored in session array as variable "usr_uid_<usr_number>"
        And I request "user"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        
        | usr_number |
        | 1          |
        | 2          |
        | 3          |
        | 4          |
        | 5          |


    Scenario: Get the users List when there are exactly 63 users
        And I request "users"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 63 records
    