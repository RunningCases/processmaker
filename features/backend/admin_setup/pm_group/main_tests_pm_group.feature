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
        | Search letters 'c'    | c      |   0   | 5       | 5       |  200      |
        | Search letters 'de    | de     |   0   | 5       | 2       |  200      |
       

     Scenario Outline: Create 3 new Groups 
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

    
    Scenario: Get the Groups list when there are 23 records
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

       
    #ASSIGN USER TO GROUP
    
    Scenario Outline: Get list Users of workspace using different filters for a group
        And I request "group/36775342552d5674146d9c2078497230/users?filter=<filter>&start=<start>&limit=<limit>"
        And the content type is "application/json"
        Then the response status code should be <http_code>
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <record> record

        Examples:

        | test_description      | filter | start | limit   | record  | http_code |
        | lowercase             | admin  |   0   | 9       | 0       |  200      |
        | uppercase             | ADMIN  |   0   | 9       | 0       |  200      |
        | limit=3               | a      |   0   | 60      | 37      |  200      |
        | limit and start       | a      |   1   | 2       | 2       |  200      |
        | high number for start | a      | 1000  | 1       | 0       |  200      |
        | high number for start | a      | 1000  | 0       | 0       |  200      |
        | empty result          | xyz    |   0   | 0       | 0       |  200      |
        | empty string          |        |   0   | 10000   | 43      |  200      |
        | empty string          |        |   1   | 2       | 2       |  200      |
        | search 0              | 0      |   0   | 0       | 0       |  200      |
        | search 0              | 0      |   0   | 100     | 0       |  200      |
        | Search letters 'c'    | c      |   0   | 40      | 21      |  200      |
        | Search letters 'de    | de     |   0   | 5       | 1       |  200      |


    Scenario Outline: Assign users to groups created from the endpoint
        Given POST this data:
        """
        {
            "usr_uid": "<usr_uid>"
        }
        """
        And I request "group/grp_uid/user"  with the key "grp_uid" stored in session array as variable "grp_uid_<grp_uid_number>"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | grp_uid_number | usr_uid                          |
        | 0              | 00000000000000000000000000000001 |
        | 1              | 51049032352d56710347233042615067 |
        | 2              | 25286582752d56713231082039265791 |


    Scenario Outline: List assigned Users to Group
        And I request "group/grp_uid/users"  with the key "grp_uid" stored in session array as variable "grp_uid_<grp_uid_number>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "usr_uid" property in row 0 equals "<usr_uid>"
        And the "usr_username" property in row 0 equals "<usr_username>"
        And the "usr_status" property in row 0 equals "<usr_status>"

        Examples:
        | grp_uid_number | usr_uid                          | usr_username | usr_status |
        | 0              | 00000000000000000000000000000001 | admin        | ACTIVE     |
        | 1              | 51049032352d56710347233042615067 | aaron        | ACTIVE     |
        | 2              | 25286582752d56713231082039265791 | amy          | ACTIVE     |

    
    Scenario Outline: List available Users to assign to Group
        And I request "group/grp_uid/available-users?filter=none"  with the key "grp_uid" stored in session array as variable "grp_uid_<grp_uid_number>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

        Examples:
        | grp_uid_number |
        | 0              |
        | 1              |
        | 2              |

    
    Scenario Outline: Unassign User of the Group
        Given that I want to delete a resource with the key "<usr_uid>" stored in session array
        And I request "group/grp_uid/user/<usr_uid>"  with the key "grp_uid" stored in session array as variable "grp_uid_<grp_uid_number>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
    
        | grp_uid_number | usr_uid                          |
        | 0              | 00000000000000000000000000000001 |
        | 1              | 51049032352d56710347233042615067 |
        | 2              | 25286582752d56713231082039265791 |

    
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

    Scenario: Get list Groups
        And I request "groups?filter=Update Demo Gro"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array


    Scenario: Get list Groups of workspace
        And I request "groups?filter=&start=0&limit=50"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 20 records