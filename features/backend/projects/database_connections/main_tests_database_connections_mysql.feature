@ProcessMakerMichelangelo @RestAPI
Feature: DataBase Connections Main Tests Mysql
  Requirements:
  A workspace with previous creation of process with ID=74737540052e1641ab88249082085472 ("Data Base Connections") already loaded
  and workspace with the project 87648819953a85c0abc01d3080475981 ("testExecutionOfDerivationScreen") already loaded
  there are zero Database Connections in the processes.

  # MySQL is tagged like 1
  Background:
    Given that I have a valid access_token
    And database tagged like 1


  # GET /api/1.0/{workspace}/project/<project-id>/database-connections
  #     Get list DataBase| dbs_type         | dbs_server         | dbs_database_name | dbs_username         | dbs_password         | dbs_port         | dbs_encode         | dbs_description         | Connections
  Scenario Outline: Get the DataBase Connections List when there are exactly zero DataBase Connections
    Given I request "project/<project>/database-connections"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has <record> record

  Examples:
    | project                          | record  |
    | 74737540052e1641ab88249082085472 | 0       |
    | 87648819953a85c0abc01d3080475981 | 0       |


  # POST /api/1.0/{workspace}/project/<project-id>/database-connection/test
  #      Test DataBase Connection
  @MysqlDbConnection
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
    | dbs_uid_number | project                          | dbs_type      | dbs_server      | dbs_database_name | dbs_username      | dbs_password      | dbs_port      | dbs_encode      | dbs_description      |
    | 1              | 74737540052e1641ab88249082085472 | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |
    | 2              | 87648819953a85c0abc01d3080475981 | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |


  # POST /api/1.0/{workspace}/project/<project-id>/database-connection
  #      Create new DataBase Connection
  @MysqlDbConnection
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
    | dbs_uid_number | project                          | dbs_type      | dbs_server      | dbs_database_name | dbs_username      | dbs_password      | dbs_port      | dbs_encode      | dbs_description      |
    | 1              | 74737540052e1641ab88249082085472 | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |
    | 2              | 87648819953a85c0abc01d3080475981 | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |


  # GET /api/1.0/{workspace}/project/<project-id>/database-connection
  #     Get DataBase Connections list of each process
  Scenario Outline: Get the DataBase Connections List when there are exactly one DataBase Connections in each process
    Given database-connection with id "<dbs_uid_number>" is active
    And I request "project/<project>/database-connections"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has <record> record

  Examples:
    | project                          | record | dbs_uid_number |
    | 74737540052e1641ab88249082085472 | 1      | 1              |
    | 87648819953a85c0abc01d3080475981 | 1      | 2              |


  # PUT /api/1.0/{workspace}/project/<project-id>/database-connection
  #     Update a DataBase Connection
  @MysqlDbConnection
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
    | dbs_uid_number | project                          | dbs_type      | dbs_server      | dbs_database_name | dbs_username      | dbs_password      | dbs_port      | dbs_encode      | dbs_description      |
    | 1              | 74737540052e1641ab88249082085472 | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |
    | 2              | 87648819953a85c0abc01d3080475981 | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |


  # GET /api/1.0/{workspace}/project/<project-id>/database-connection
  #     Get a single DataBase Connection and their properties
  @MysqlDbConnection
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
    | dbs_uid_number | project                          | dbs_type      | dbs_server      | dbs_database_name | dbs_username      | dbs_password      | dbs_port      | dbs_encode      | dbs_description      |
    | 1              | 74737540052e1641ab88249082085472 | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |
    | 2              | 87648819953a85c0abc01d3080475981 | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |


  # DELETE /api/1.0/{workspace}/project/<project-id>/database-connection
  #        Delete all DataBase Connections created in this script
  Scenario Outline: Delete all Database Connection created in this script
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


  # GET /api/1.0/{workspace}/project/<project-id>/database-connection
  #     Get DataBase Connections list
  Scenario Outline: Get the DataBase Connections List when there are exactly zero DataBase Connections
    Given database-connection with id "<dbs_uid_number>" is active
    And I request "project/<project>/database-connections"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has <record> record

  Examples:
    | project                          | record | dbs_uid_number |
    | 74737540052e1641ab88249082085472 | 0      | 1              |
    | 87648819953a85c0abc01d3080475981 | 0      | 2              |