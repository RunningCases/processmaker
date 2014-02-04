@ProcessMakerMichelangelo @RestAPI
Feature: PM Group Main Tests
    Requirements:
    a workspace with the 20 groups already loaded
    
    Background:
        Given that I have a valid access_token

    Scenario Outline: Get list Groups of workspace using different filters
        And I request "groups?filter=<filter>&start=<start>&limit=<limit>"
        And the content type is "application/json"
        Then the response status code should be <http_code>
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

        Examples:

        | test_description      | filter | start | limit   | records | http_code |
        | lowercase             | admin  |   0   | 9       | 2       |  200      |
        | uppercase             | ADMIN  |   0   | 9       | 2       |  200      |
        | limit=3               | a      |   0   | 3       | 3       |  200      |
        | limit and start       | a      |   1   | 2       | 2       |  200      |
        | high number for start | a      | 1000  | 1       | 0       |  200      |
        | high number for start | a      | 1000  | 0       | 0       |  200      |
        | empty result          | xyz    |   0   | 0       | 0       |  200      |
        | empty string          |        |   0   | 10000   | 20      |  200      |
        | empty string          |        |   1   | 2       | 2       |  200      |
        | search 0              | 0      |   0   | 0       | 0       |  200      |
        | search 0              | 0      |   0   | 100     | 0       |  200      |
        | real numbers          | a      |  0.0  | 1.0     | 1       |  200      |
        | real numbers          | a      |  0.0  | 0.0     | 0       |  200      |
        | Search letters 'c'    | c      |   0   | 5       | 8       |  200      |
        | Search letters 'de    | de     |   0   | 5       | 4       |  200      |
       

 
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
        And the content type is "application/json"
        And the type is "object"
        And store "grp_uid" in session array as variable "grp_uid_<grp_uid_number>"

        Examples:

        | grp_uid_number | grp_title                  | grp_status |
        | 0              | Demo Group1 for main behat | ACTIVE     |
        | 1              | Demo Group2 for main behat | ACTIVE     |
        | 2              | Demo Group3 for main behat | INACTIVE   |             

    
    Scenario: Get list Groups of workspace
        And I request "groups?filter=&start=0&limit=50"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 23 records


    Scenario Outline: Update Group and then check if the values had changed
        Given PUT this data:
        """
        {
            "grp_title": "<grp_title>",
            "grp_status": "<grp_status>"
        }
        """
        And that I want to update a resource with the key "grp_uid" stored in session array as variable "grp_uid_<grp_uid_number>"
        And I request "group"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | grp_uid_number | grp_title                         | grp_status |
        | 0              | Update Demo Group1 for main behat | INACTIVE   |
        | 1              | Update Demo Group2 for main behat | INACTIVE   |
        | 2              | Update Demo Group3 for main behat | ACTIVE     |    

   
   Scenario Outline: Get a single Groups and check some properties
        Given that I want to get a resource with the key "grp_uid" stored in session array as variable "grp_uid_<grp_uid_number>"
        And I request "group"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the "grp_title" property equals "<grp_title>"
        And the "grp_status" property equals "<grp_status>"
    

        Examples:

        | grp_uid_number | grp_title                         | grp_status |
        | 0              | Update Demo Group1 for main behat | INACTIVE   |
        | 1              | Update Demo Group2 for main behat | INACTIVE   |
        | 2              | Update Demo Group3 for main behat | ACTIVE     | 

   
    Scenario Outline: Delete all Group created previously in this script
        Given that I want to delete a resource with the key "grp_uid" stored in session array as variable "grp_uid_<grp_uid_number>"
        And I request "group"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | grp_uid_number |
        | 0              |
        | 1              |
        | 2              |

    #GET /api/1.0/{workspace}/groups?filter=abc&start=0&limit=25
    #    Get list Groups
    Scenario: Get list Groups
        And I request "groups?filter=Update Demo Gro"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array