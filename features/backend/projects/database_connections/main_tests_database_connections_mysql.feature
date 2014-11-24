@ProcessMakerMichelangelo @RestAPI
Feature: DataBase Connections Main Tests MySQL
  Requirements:
    a workspace with the process 74737540052e1641ab88249082085472 ("Data Base Connenctions") already loaded
    and workspace with the project 87648819953a85c0abc01d3080475981 ("testExecutionOfDerivationScreen") already loaded
    there are zero Database Connections in the process

    Background:
    Given that I have a valid access_token

    Scenario Outline: Get the DataBase Connections List when there are exactly zero DataBase Connections
        Given I request "project/<project>/database-connections"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <record> record

        Examples:

        | test_description                                            | project                          | record |
        | List DB in the process Data Base Connenctions .pm           | 74737540052e1641ab88249082085472 | 0      |
        | List DB in the process testExecutionOfDerivationScreen .pmx | 87648819953a85c0abc01d3080475981 | 0      |

    
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
        And I request "project/<project>/database-connection/test"
        Then if database-connection with id "<dbs_uid_number>" is active
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        
        Examples:

        | test_description                   | dbs_uid_number | project                          | dbs_type             | dbs_server    | dbs_database_name | dbs_username | dbs_password | dbs_port | dbs_encode | dbs_description       |
        | Test SQL Server db connection .pm  | 1              | 74737540052e1641ab88249082085472 | microsoft sql server | 192.168.11.99 | wf_michelangelo   | sa           | mafe12345    | 1433     | utf8       | SQL Server connection |
        | Test SQL Server db connection .pmx | 2              | 87648819953a85c0abc01d3080475981 | microsoft sql server | 192.168.11.99 | wf_michelangelo   | sa           | mafe12345    | 1433     | utf8       | SQL Server connection |


    Scenario Outline: Create a new database connection
        Given database-connection with id "<dbs_uid_number>" is active
        And POST this data:
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
        And I request "project/<project>/database-connection"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "dbs_uid" in session array as variable "dbs_uid_<dbs_uid_number>"

        Examples:

        | test_description                     | dbs_uid_number | project                          | dbs_type | dbs_server    | dbs_database_name | dbs_username | dbs_password | dbs_port | dbs_encode | dbs_description       |
        | Create SQL Server db connection .pm  | 1              | 74737540052e1641ab88249082085472 | mssql    | 192.168.11.99 | wf_michelangelo   | sa           | mafe12345    | 1433     | utf8       | SQL Server connection |
        | Create SQL Server db connection .pmx | 2              | 87648819953a85c0abc01d3080475981 | mssql    | 192.168.11.99 | wf_michelangelo   | sa           | mafe12345    | 1433     | utf8       | SQL Server connection |


    Scenario Outline: Get the DataBase Connections List when there are exactly one DataBase Connections in each process
        Given database-connection with id "<dbs_uid_number>" is active
        And I request "project/<project>/database-connections"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <record> record

        Examples:

        | test_description                                            | project                          | record | dbs_uid_number |
        | List DB in the process Data Base Connenctions .pm           | 74737540052e1641ab88249082085472 | 1      | 1              |
        | List DB in the process testExecutionOfDerivationScreen .pmx | 87648819953a85c0abc01d3080475981 | 1      | 2              |


    
    Scenario Outline: Update a database connection
        Given database-connection with id "<dbs_uid_number>" is active
        And PUT this data:
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
        And I request "project/<project>/database-connection"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | test_description                     | dbs_uid_number | project                          | dbs_type | dbs_server    | dbs_database_name | dbs_username | dbs_password | dbs_port | dbs_encode | dbs_description              |
        | Update sql server db connection .pm  | 1              | 74737540052e1641ab88249082085472 | mssql    | 192.168.11.99 | wf_michelangelo   | sa           | mafe12345    | 1433     | utf8       | update SQL Server connection |
        | Update sql server db connection .pmx | 2              | 87648819953a85c0abc01d3080475981 | mssql    | 192.168.11.99 | wf_michelangelo   | sa           | mafe12345    | 1433     | utf8       | update SQL Server connection |


    Scenario Outline: Get a single database connection and check some properties
        Given database-connection with id "<dbs_uid_number>" is active
        And that I want to get a resource with the key "dbs_uid" stored in session array as variable "dbs_uid_<dbs_uid_number>"
        And I request "project/<project>/database-connection"
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

        | test_description                     | dbs_uid_number | project                          | dbs_type | dbs_server    | dbs_database_name | dbs_username | dbs_password | dbs_port | dbs_encode | dbs_description              |
        | Update sql server db connection .pm  | 1              | 74737540052e1641ab88249082085472 | mssql    | 192.168.11.99 | wf_michelangelo   | sa           | mafe12345    | 1433     | utf8       | update SQL Server connection |
        | Update sql server db connection .pmx | 2              | 87648819953a85c0abc01d3080475981 | mssql    | 192.168.11.99 | wf_michelangelo   | sa           | mafe12345    | 1433     | utf8       | update SQL Server connection |


    Scenario Outline: Delete all Database Connection created previously in this script
        Given database-connection with id "<dbs_uid_number>" is active
        And that I want to delete a resource with the key "dbs_uid" stored in session array as variable "dbs_uid_<dbs_uid_number>"
        And I request "project/<project>/database-connection"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | project                          | dbs_uid_number |
        | 74737540052e1641ab88249082085472 | 1              |
        | 87648819953a85c0abc01d3080475981 | 2              |
              

    Scenario Outline: Get the DataBase Connections List when there are exactly zero DataBase Connections
        Given database-connection with id "<dbs_uid_number>" is active
        And I request "project/<project>/database-connections"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <record> record

        Examples:

        | test_description                                            | project                          | record | dbs_uid_number |
        | List DB in the process Data Base Connenctions .pm           | 74737540052e1641ab88249082085472 | 0      | 1              |
        | List DB in the process testExecutionOfDerivationScreen .pmx | 87648819953a85c0abc01d3080475981 | 0      | 2              |