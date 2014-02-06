@ProcessMakerMichelangelo @RestAPI
Feature: DataBase Connections

    Scenario: List all the database connections (result 0 database connections)
        Given that I have a valid access_token
        And I request "project/96189226752f3e5e23c1303036042196/report-tables"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record


    Scenario: Create a new database connection
        Given that I have a valid access_token
        And POST this data:
            """
            {
                "rep_tab_name" : "PMT_TEST",
                "rep_tab_dsc" : "descripcion de la tabla",
                "rep_tab_connection" : "workflow",
                "rep_tab_type" : "NORMAL",
                "rep_tab_grid" : "",
                "fields" : [
                    {
                        "fld_dyn" : "COMBO_ACEPTACION",
                        "fld_name" : "ACEPTACION",
                        "fld_label" : "ACEPTACION",
                        "fld_type" : "VARCHAR",
                        "fld_size" : 5
                    },{
                        "fld_name" : "CAMPO_PROPIO",
                        "fld_label" : "CAMPO_PROPIO",
                        "fld_type" : "VARCHAR",
                        "fld_size" : 200
                    }
                ]
            }
            """
        And I request "project/96189226752f3e5e23c1303036042196/report-table"
        Then the response status code should be 201
        And store "rep_uid" in session array

    @3: TEST FOR GET DATABASE CONNECTIONS /----------------------------------------------------------------------
    Scenario: List all the database connections (result 1 database connection)
        Given that I have a valid access_token
        And I request "project/96189226752f3e5e23c1303036042196/report-tables"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 1 record

    @4: TEST FOR PUT DATABASE CONNECTION /-----------------------------------------------------------------------
    Scenario: Update a database connection
        Given that I have a valid access_token
        And PUT this data:
            """
            {
                "rep_tab_dsc" : "nueva descripcion",
                "fields" : [
                    {
                        "fld_dyn" : "CAMPO_TEXTO",
                        "fld_name" : "TEXTO",
                        "fld_label" : "TEXTO",
                        "fld_type" : "VARCHAR",
                        "fld_size" : 100
                    },{
                        "fld_name" : "CAMPO_PROPIO",
                        "fld_label" : "CAMPO_PROPIO",
                        "fld_type" : "VARCHAR",
                        "fld_size" : 200
                    }
                ]
            }
            """
        And that I want to update a resource with the key "dbs_uid" stored in session array
        And I request "project/96189226752f3e5e23c1303036042196/report-table"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"


    Scenario: Get a database connection (with change in "dbs_description" and "dbs_database_name")
        Given that I have a valid access_token
        And that I want to get a resource with the key "dbs_uid" stored in session array
        And I request "project/96189226752f3e5e23c1303036042196/report-table"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "rep_tab_dsc" is set to "nueva descripcion"


    Scenario: Delete a database connection
        Given that I have a valid access_token
        And that I want to delete a resource with the key "dbs_uid" stored in session array
        And I request "project/96189226752f3e5e23c1303036042196/report-table"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    @7: TEST FOR GET DATABASE CONNECTIONS /----------------------------------------------------------------------
    Scenario: List all the database connections (result 0 database connections)
        Given that I have a valid access_token
        And I request "project/96189226752f3e5e23c1303036042196/report-tables"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record