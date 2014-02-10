@ProcessMakerMichelangelo @RestAPI
Feature: Report Tables

    Scenario: List all the report tables (result 0 report tables)
        Given that I have a valid access_token
        And I request "project/96189226752f3e5e23c1303036042196/report-tables"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record


    Scenario: Create a new report table
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

    @3: TEST FOR GET report tableS /----------------------------------------------------------------------
    Scenario: List all the report tables (result 1 report table)
        Given that I have a valid access_token
        And I request "project/96189226752f3e5e23c1303036042196/report-tables"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 1 record

    @4: TEST FOR PUT report table /-----------------------------------------------------------------------
    Scenario: Update a report table
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
        And that I want to update a resource with the key "rep_uid" stored in session array
        And I request "project/96189226752f3e5e23c1303036042196/report-table"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"


    Scenario: Get a report table (with change in "rep_tab_dsc")
        Given that I have a valid access_token
        And that I want to get a resource with the key "rep_uid" stored in session array
        And I request "project/96189226752f3e5e23c1303036042196/report-table"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "rep_tab_dsc" is set to "nueva descripcion"


    Scenario: Delete a report table
        Given that I have a valid access_token
        And that I want to delete a resource with the key "rep_uid" stored in session array
        And I request "project/96189226752f3e5e23c1303036042196/report-table"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    @7: TEST FOR GET report tableS /----------------------------------------------------------------------
    Scenario: List all the report tables (result 0 report tables)
        Given that I have a valid access_token
        And I request "project/96189226752f3e5e23c1303036042196/report-tables"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record