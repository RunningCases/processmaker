@ProcessMakerMichelangelo @RestAPI
Feature: User
    Background:
        Given that I have a valid access_token

    #GET /api/1.0/{workspace}/users?filter=abc&start=0&limit=25
    #    Get list Users
    Scenario: Get list Users
        And I request "users"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"

    #GET /api/1.0/{workspace}/users?filter=abc&start=0&limit=25
    #    Get list Users
    Scenario: Get list Users
        And I request "users?filter=for basic behat"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #GET /api/1.0/{workspace}/user/{usr_uid}
    #    Get list Users
    Scenario: Get list Users
        And I request "user/00000000000000000000000000000001"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    #Para que funcione este test, debe existir el archivo que se quiere subir
    #GET /api/1.0/{workspace}/user/{usr_uid}
    #    Upload a image
    Scenario: Upload a image
        Given POST I want to upload the image "/photo/pic3.jpg" to user "00000000000000000000000000000001". Url "user/"
    
    #POST /api/1.0/{workspace}/user
    #     Create new User
    Scenario Outline: Create new User
        Given POST this data:
        """
            {
                "usr_firstname": "<usr_firstname>",
                "usr_lastname": "<usr_lastname>",
                "usr_username": "<usr_username>",
                "usr_email": "<usr_email>",
                "usr_address": "",
                "usr_zip_code": "",
                "usr_country": "",
                "usr_city": "",
                "usr_location": "",
                "usr_phone": "",
                "usr_position": "",
                "usr_replaced_by": "",
                "usr_due_date": "<usr_due_date>",
                "usr_calendar": "",
                "usr_status": "<usr_status>",
                "usr_role": "<usr_role>",
                "usr_new_pass": "admin",
                "usr_cnf_pass": "admin"
            }
        """
        And I request "user"
        And the content type is "application/json"
        Then the response status code should be <http_code>
        And the response charset is "UTF-8"
        And the type is "<type>"
        

        Examples:
|usr_firstname | usr_lastname | usr_username | usr_email | usr_due_date | usr_status | usr_role        |http_code| type |

|              | bbb          | ab           | ab@ab.com | 2014-02-10   | ACTIVE     | PROCESSMAKER_MANAGER |400 |string|

|aaa           |              | ab           | ab@ab.com | 2014-02-10   | ACTIVE     | PROCESSMAKER_MANAGER |400 |string|

|aaa           | bbb          |              | ab@ab.com | 2014-02-10   | ACTIVE     | PROCESSMAKER_MANAGER |400 |string|

|aaa           | bbb          | ab           |           | 2014-02-10   | ACTIVE     | PROCESSMAKER_MANAGER |400 |string|

|aaa           | bbb          | ab           | ab@ab.com |              | ACTIVE     | PROCESSMAKER_MANAGER |400 |string|

|aaa           | bbb          | ab           | ab@ab.com |2014-02-10    |            | PROCESSMAKER_MANAGER |400 |string|

|aaa           | bbb          | ab           | ab@ab.com |2014-02-10    | ACTIVE     |                      |400 |string|


    #POST /api/1.0/{workspace}/user
    #     Create new User
    Scenario Outline: Create new User
        Given POST this data:
        """
            {
                "usr_firstname": "<usr_firstname>",
                "usr_lastname": "<usr_lastname>",
                "usr_username": "<usr_username>",
                "usr_email": "<usr_email>",
                "usr_address": "",
                "usr_zip_code": "",
                "usr_country": "",
                "usr_city": "",
                "usr_location": "",
                "usr_phone": "",
                "usr_position": "",
                "usr_replaced_by": "",
                "usr_due_date": "<usr_due_date>",
                "usr_calendar": "",
                "usr_status": "<usr_status>",
                "usr_role": "<usr_role>",
                "usr_new_pass": "admin",
                "usr_cnf_pass": "admin"
            }
        """
        And I request "user"
        And the content type is "application/json"
        Then the response status code should be <http_code>
        And the response charset is "UTF-8"
        And the type is "<type>"
        And store "usr_uid" in session array as variable "usr_uid"        

        Examples:
|usr_firstname | usr_lastname | usr_username | usr_email | usr_due_date | usr_status | usr_role        |http_code| type |
|xxx           | yyy          | zzz          | xy@zzz.com| 2014-02-20   | ACTIVE     | PROCESSMAKER_MANAGER |201 |object|


    #PUT /api/1.0/{workspace}/user/{usr_uid}
    #    Update User
    Scenario Outline: Update User
        Given PUT this data:
        """
            {
                "usr_firstname": "<usr_firstname>",
                "usr_lastname": "<usr_lastname>",
                "usr_username": "<usr_username>",
                "usr_email": "<usr_email>",
                "usr_address": "",
                "usr_zip_code": "",
                "usr_country": "",
                "usr_city": "",
                "usr_location": "",
                "usr_phone": "",
                "usr_position": "",
                "usr_replaced_by": "",
                "usr_due_date": "<usr_due_date>",
                "usr_calendar": "",
                "usr_status": "<usr_status>",
                "usr_role": "<usr_role>",
                "usr_new_pass": "admin",
                "usr_cnf_pass": "admin"
            }
        """
        And that I want to update a resource with the key "usr_uid" stored in session array as variable "usr_uid"
        And I request "user"
        And the content type is "application/json"
        Then the response status code should be <http_code>
        And the response charset is "UTF-8"
        And the type is "<type>"

        Examples:
|usr_firstname | usr_lastname | usr_username | usr_email | usr_due_date | usr_status | usr_role        |http_code| type |
|aaa           | bbb          | ccc          | ab@ccc.com| 2014-06-22   | ACTIVE     | PROCESSMAKER_OPERATOR |200 |object|

    #DELETE /api/1.0/{workspace}/user/{usr_uid}
    #       Delete User
    Scenario: Delete User
        Given that I want to delete a resource with the key "usr_uid" stored in session array as variable "usr_uid"
        And I request "user"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

  