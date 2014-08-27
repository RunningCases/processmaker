@ProcessMakerMichelangelo @RestAPI
Feature: DataBase Connections Main Tests
  Requirements:
    a workspace with the process 74737540052e1641ab88249082085472 ("Data Base Connenctions") already loaded
    there are zero Database Connections in the process

    Background:
    Given that I have a valid access_token

    Scenario: Get the DataBase Connections List when there are exactly zero DataBase Connections
        Given I request "project/74737540052e1641ab88249082085472/database-connections"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record

    Scenario Outline: Test database connection to test
        Given POST this data:
            """
            {
                "dbs_type": "<dbs_type>",
                "dbs_server": "<dbs_server>",
                "dbs_database_name": "<dbs_database_name>",
                "dbs_username": "<dbs_username>",
                "dbs_password": "<dbs_password>",
                "dbs_port": <dbs_port>,
                "dbs_encode": "<dbs_encode>",
                "dbs_description": "<dbs_description>"
            }
            """
        And I request "project/74737540052e1641ab88249082085472/database-connection/test"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        

        Examples:

        | test_description              | dbs_uid_number | dbs_type             | dbs_server                 | dbs_database_name | dbs_username   | dbs_password | dbs_port | dbs_encode | dbs_description       |
        | Test mysql db connection      | 1              | mysql                | michelangelo-be.colosa.net | test              | testuser       | sample       | 3306     | utf8       | mysql connection      |
        | Test SQL Server db connection | 2              | microsoft sql server | 192.168.11.99              | wf_michelangelo   | sa             | mafe12345    | 1433     | utf8       | SQL Server connection |


    Scenario Outline: Create a new database connection
        Given POST this data:
            """
            {
                "dbs_type": "<dbs_type>",
                "dbs_server": "<dbs_server>",
                "dbs_database_name": "<dbs_database_name>",
                "dbs_username": "<dbs_username>",
                "dbs_password": "<dbs_password>",
                "dbs_port": <dbs_port>,
                "dbs_encode": "<dbs_encode>",
                "dbs_description": "<dbs_description>"
            }
            """
        And I request "project/74737540052e1641ab88249082085472/database-connection"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "dbs_uid" in session array as variable "dbs_uid_<dbs_uid_number>"


        Examples:

        | test_description                | dbs_uid_number | dbs_type | dbs_server                 | dbs_database_name | dbs_username   | dbs_password | dbs_port | dbs_encode | dbs_description       |
        | Create mysql db connection      | 1              | mysql    | michelangelo-be.colosa.net | test              | testuser       | sample       | 3306     | utf8       | mysql connection      |
        | Create SQL Server db connection | 2              | mssql    | 192.168.11.99              | wf_michelangelo   | sa             | mafe12345    | 1433     | utf8       | SQL Server connection |


    Scenario: Get the DataBase Connections List when there are exactly three DataBase Connections
        Given I request "project/74737540052e1641ab88249082085472/database-connections"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 2 record

    
    Scenario Outline: Update a database connection
        Given PUT this data:
            """
            {
                "dbs_type": "<dbs_type>",
                "dbs_server": "<dbs_server>",
                "dbs_database_name": "<dbs_database_name>",
                "dbs_username": "<dbs_username>",
                "dbs_password": "<dbs_password>",
                "dbs_port": <dbs_port>,
                "dbs_encode": "<dbs_encode>",
                "dbs_description": "<dbs_description>"
            }
            """
        And that I want to update a resource with the key "dbs_uid" stored in session array as variable "dbs_uid_<dbs_uid_number>"
        And I request "project/74737540052e1641ab88249082085472/database-connection"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"


        Examples:

        | test_description                | dbs_uid_number | dbs_type | dbs_server                 | dbs_database_name | dbs_username   | dbs_password | dbs_port | dbs_encode | dbs_description              |
        | Update mysql db connection      | 1              | mysql    | michelangelo-be.colosa.net | test              | testuser       | sample       | 3306     | utf8       | update mysql connection      |
        | Update sql server db connection | 2              | mssql    | 192.168.11.99              | wf_michelangelo   | sa             | mafe12345    | 1433     | utf8       | update SQL Server connection |


    Scenario Outline: Get a single database connection and check some properties
        Given that I want to get a resource with the key "dbs_uid" stored in session array as variable "dbs_uid_<dbs_uid_number>"
        And I request "project/74737540052e1641ab88249082085472/database-connection"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "dbs_type" is set to "<dbs_type>"
        And that "dbs_server" is set to "<dbs_server>"
        And that "dbs_database_name" is set to "<dbs_database_name>"
        And that "dbs_username" is set to "<dbs_username>"
        And that "dbs_password" is set to "<dbs_password>"
        And that "dbs_port" is set to "<dbs_port>"
        And that "dbs_encode" is set to "<dbs_encode>"
        And that "dbs_description" is set to "<dbs_description>"

        Examples:

        | test_description                | dbs_uid_number | dbs_type | dbs_server                 | dbs_database_name | dbs_username   | dbs_password | dbs_port | dbs_encode | dbs_description              |
        | Update mysql db connection      | 1              | mysql    | michelangelo-be.colosa.net | test              | testuser       | sample       | 3306     | utf8       | update mysql connection      |
        | Update sql server db connection | 2              | mssql    | 192.168.11.99              | wf_michelangelo   | sa             | mafe12345    | 1433     | utf8       | update SQL Server connection |
 

    Scenario Outline: Delete all Database Connection created previously in this script
        Given that I want to delete a resource with the key "dbs_uid" stored in session array as variable "dbs_uid_<dbs_uid_number>"
        And I request "project/74737540052e1641ab88249082085472/database-connection"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | dbs_uid_number |
        | 1              |
        | 2              |
       

    Scenario: Get the DataBase Connections List when there are exactly zero DataBase Connections
        Given I request "project/74737540052e1641ab88249082085472/database-connections"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record