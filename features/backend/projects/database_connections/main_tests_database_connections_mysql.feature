@ProcessMakerMichelangelo @RestAPI
Feature: DataBase Connections Main Tests Mysql
  Requirements:
  A workspace with previous creation of process with ID=74737540052e1641ab88249082085472 ("Data Base Connections") already loaded
  and workspace with the project 87648819953a85c0abc01d3080475981 ("testExecutionOfDerivationScreen") already loaded
  there are zero Database Connections in the processes.

  Background:
    Given that I have a valid access_token


  # GET /api/1.0/{workspace}/project/<project-id>/database-connections
  #     Get list DataBase Connections
  Scenario Outline: Get the DataBase Connections List when there are exactly zero DataBase Connections
    Given I request "project/<project>/database-connections"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has <record> record

  Examples:

    | project                          | record |
    | 74737540052e1641ab88249082085472 | 1      |
    | 87648819953a85c0abc01d3080475981 | 1      |


  # POST /api/1.0/{workspace}/project/<project-id>/database-connection/test
  #      Test DataBase Connection
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
    Then database-connection with id "<dbs_uid_number>" is active
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"

  Examples:
    | dbs_uid_number | project                          | dbs_type      | dbs_server      | dbs_database_name | dbs_username      | dbs_password      | dbs_port      | dbs_encode      | dbs_description      |
    | 46110938554821d2ddb8d01076533986              | 74737540052e1641ab88249082085472 | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |
    | 40639901154821d2e3bb7e8061116901              | 87648819953a85c0abc01d3080475981 | <mys_db_type> | <mys_db_server> | <mys_db_name>     | <mys_db_username> | <mys_db_password> | <mys_db_port> | <mys_db_encode> | <mys_db_description> |