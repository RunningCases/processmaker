@ProcessMakerMichelangelo @RestAPI
Feature: DataBase Connections

    Scenario: List all the database connections (result 0 database connections)
        Given that I have a valid access_token
        And I request "project/74737540052e1641ab88249082085472/database-connections"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record


    Scenario: Create a new database connection
        Given that I have a valid access_token
        And POST this data:
            """
            {
                "dbs_type": "mysql",
                "dbs_server": "192.168.11.71",
                "dbs_database_name": "rb_cochalo",
                "dbs_username": "root",
                "dbs_password": "atopml2005",
                "dbs_port": 3306,
                "dbs_encode": "utf8",
                "dbs_description": "conection correcta"
            }
            """
        And I request "project/74737540052e1641ab88249082085472/database-connection"
        Then the response status code should be 201
        And store "dbs_uid" in session array

    @3: TEST FOR GET DATABASE CONNECTIONS /----------------------------------------------------------------------
    Scenario: List all the database connections (result 1 database connection)
        Given that I have a valid access_token
        And I request "project/74737540052e1641ab88249082085472/database-connections"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 1 record

    @4: TEST FOR PUT DATABASE CONNECTION /-----------------------------------------------------------------------
    Scenario: Update a database connection
        Given that I have a valid access_token
        And PUT this data:
            """
            {
                "dbs_type": "mysql",
                "dbs_server": "192.168.11.71",
                "dbs_database_name": "wf_cochalo",
                "dbs_username": "root",
                "dbs_password": "atopml2005",
                "dbs_port": 3306,
                "dbs_encode": "utf8",
                "dbs_description": "conection correcta a workflow"
            }
            """
        And that I want to update a resource with the key "dbs_uid" stored in session array
        And I request "project/74737540052e1641ab88249082085472/database-connection"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"


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