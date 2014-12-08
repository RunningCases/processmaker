@ProcessMakerMichelangelo @RestAPI
Feature: DataBase Connections

  Scenario: List all the database connections (result 0 database connections)
    Given that I have a valid access_token
    And I request "project/74737540052e1641ab88249082085472/database-connections"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has 0 record


  Scenario Outline: Create a new database connection
    Given that I have a valid access_token
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
    And I request "project/74737540052e1641ab88249082085472/database-connection"
    Then the response status code should be 201
    And store "dbs_uid" in session array

  Examples:
    | dbs_type      | dbs_server      | dbs_database_name | dbs_username      | dbs_password      | dbs_port      | dbs_encode      | dbs_description      |
    | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |


  @3: TEST FOR GET DATABASE CONNECTIONS /----------------------------------------------------------------------
  Scenario: List all the database connections (result 1 database connection)
    Given that I have a valid access_token
    And I request "project/74737540052e1641ab88249082085472/database-connections"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has 1 record

  @4: TEST FOR PUT DATABASE CONNECTION /-----------------------------------------------------------------------
  Scenario Outline: Update a database connection
    Given that I have a valid access_token
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
    And that I want to update a resource with the key "dbs_uid" stored in session array
    And I request "project/74737540052e1641ab88249082085472/database-connection"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

  Examples:
  | dbs_type      | dbs_server      | dbs_database_name | dbs_username      | dbs_password      | dbs_port      | dbs_encode      | dbs_description      |
  | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |


  Scenario: Get a database connection (with change in "dbs_description" and "dbs_database_name")
    Given that I have a valid access_token
    And that I want to get a resource with the key "dbs_uid" stored in session array
    And I request "project/74737540052e1641ab88249082085472/database-connection"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "dbs_description" is set to "conection correcta a workflow"
    And that "dbs_database_name" is set to "wf_cochalo"


  Scenario: Delete a database connection
    Given that I have a valid access_token
    And that I want to delete a resource with the key "dbs_uid" stored in session array
    And I request "project/74737540052e1641ab88249082085472/database-connection"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

  @7: TEST FOR GET DATABASE CONNECTIONS /----------------------------------------------------------------------
  Scenario: List all the database connections (result 0 database connections)
    Given that I have a valid access_token
    And I request "project/74737540052e1641ab88249082085472/database-connections"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has 0 record