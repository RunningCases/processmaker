@ProcessMakerMichelangelo @RestAPI
Feature: Report Tables
Requirements:
    a workspace with the process 922677707524ac7417ce345089010125 ("Test Designer Report Tables") already loaded
    there are zero Report Table in the process
    and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded

Background:
    Given that I have a valid access_token

Scenario Outline: Verify that there are no report tables
    Given I request "project/<project>/report-tables"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has <record> record

    Examples:
    | test_description                                           | project                          | record |
    | Get of report table of process Test Designer Report Tables | 922677707524ac7417ce345089010125 | 0      |
    | Get of report table of process Process Complete BPMN       | 1455892245368ebeb11c1a5001393784 | 1      |
    

Scenario Outline: Create new report tables from dynaform and grid
    Given POST this data:
        """
        {
            "rep_tab_name" : "<rep_tab_name>",
            "rep_tab_dsc" : "<rep_tab_dsc>",
            "rep_tab_connection" : "<rep_tab_connection>",
            "rep_tab_type" : "<rep_tab_type>",
            "rep_tab_grid" : "<rep_tab_grid>",
            "fields" : [
                {
                    "fld_dyn" : "<fld_dyn_1>",
                    "fld_name" : "<fld_name_1>",
                    "fld_label" : "<fld_label_1>",
                    "fld_type" : "<fld_type_1>",
                    "fld_size" : <fld_size_1>
                },{
                    "fld_dyn" : "<fld_dyn_2>",
                    "fld_name" : "<fld_name_2>",
                    "fld_label" : "<fld_label_2>",
                    "fld_type" : "<fld_type_2>",
                    "fld_size" : <fld_size_2>
                },{
                    "fld_dyn" : "<fld_dyn_3>",
                    "fld_name" : "<fld_name_3>",
                    "fld_label" : "<fld_label_3>",
                    "fld_type" : "<fld_type_3>",
                    "fld_size" : <fld_size_3>
                }
            ]
        }
        """
    And I request "project/<project>/report-table"
    Then the response status code should be 201
    And store "rep_uid" in session array as variable "rep_uid_<rep_uid_number>"

    Examples:

    | test_description                    | project                          | rep_uid_number | rep_tab_name   | rep_tab_dsc         | rep_tab_connection | rep_tab_type | rep_tab_grid                     | fld_dyn_1 | fld_name_1 | fld_label_1      | fld_type_1 | fld_size_1 | fld_dyn_2 | fld_name_2 | fld_label_2 | fld_type_2 | fld_size_2 | fld_dyn_3 | fld_name_3     | fld_label_3    | fld_type_3 | fld_size_3 |
    | Create a Report Table - Normal .pm  | 922677707524ac7417ce345089010125 | 1              | REPORT_TABLE_1 | Report Table Desc 1 | workflow           | NORMAL       |                                  | nameany   | NAME_ANY   | Name Any         | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       | 0          |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         |          
    | Create a Report Table - Grid   .pm  | 922677707524ac7417ce345089010125 | 2              | REPORT_TABLE_2 | Report Table Desc 2 | workflow           | GRID         | 267480685524ac9b3bd5e23004484669 | text1     | TEXT_1     | Text 1           | VARCHAR    | 64         | fecha1    | DATE_1     | Date        | DATE       | 0          |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         |
    

Scenario: Create a new database connection with same name
        Given POST this data:
            """
            {
                "rep_tab_name" : "REPORT_TABLE_1",
                "rep_tab_dsc" : "Report Table Desc 1",
                "rep_tab_connection" : "workflow",
                "rep_tab_type" : "NORMAL",
                "rep_tab_grid" : "",
                "fields" : [
                    {
                        "fld_dyn" : "nameany",
                        "fld_name" : "NAME_ANY",
                        "fld_label" : "Name Any ",
                        "fld_type" : "VARCHAR",
                        "fld_size" : 64
                    }
                ]
            }
            """
        And I request "project/922677707524ac7417ce345089010125/report-table"
        Then the response status code should be 400
        And the response status message should have the following text "already exits"
        

Scenario Outline: Verify that there are 2 report tables
    Given I request "project/<project>/report-tables"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has <record> record

    Examples:
    | test_description                                           | project                          | record |
    | Get of report table of process Test Designer Report Tables | 922677707524ac7417ce345089010125 | 2      |
    

Scenario Outline: Update a created report tables
    Given PUT this data:
        """
        {
            "rep_tab_dsc" : "<rep_tab_dsc>",
            "fields" : [
                {
                    "fld_dyn" : "<fld_dyn_1>",
                    "fld_name" : "<fld_name_1>",
                    "fld_label" : "<fld_label_1>",
                    "fld_type" : "<fld_type_1>",
                    "fld_size" : <fld_size_1>
                },{
                    "fld_dyn" : "<fld_dyn_2>",
                    "fld_name" : "<fld_name_2>",
                    "fld_label" : "<fld_label_2>",
                    "fld_type" : "<fld_type_2>",
                    "fld_size" : <fld_size_2>
                },{
                    "fld_dyn" : "<fld_dyn_3>",
                    "fld_name" : "<fld_name_3>",
                    "fld_label" : "<fld_label_3>",
                    "fld_type" : "<fld_type_3>",
                    "fld_size" : <fld_size_3>
                }
            ]
        }
        """

    And that I want to update a resource with the key "rep_uid" stored in session array as variable "rep_uid_<rep_uid_number>"
    And I request "project/<project>/report-table"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
   
    Examples:

    | test_description                    | project                          | rep_uid_number | rep_tab_name   | rep_tab_dsc                 | rep_tab_connection | rep_tab_type | rep_tab_grid                     | fld_dyn_1 | fld_name_1 | fld_label_1      | fld_type_1 | fld_size_1 | fld_dyn_2 | fld_name_2 | fld_label_2 | fld_type_2 | fld_size_2 | fld_dyn_3 | fld_name_3     | fld_label_3    | fld_type_3 | fld_size_3 |
    | Update a Report Table - Normal .pm  | 922677707524ac7417ce345089010125 | 1              | REPORT_TABLE_1 | Report Table Desc Updated 1 | workflow           | NORMAL       |                                  | nameany   | NAME_ANY   | Name Any         | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       | 0          |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 150        |          
    | Update a Report Table - Grid   .pm  | 922677707524ac7417ce345089010125 | 2              | REPORT_TABLE_2 | Report Table Desc Updated 2 | workflow           | GRID         | 267480685524ac9b3bd5e23004484669 | text1     | TEXT_1     | Text 1           | VARCHAR    | 64         | fecha1    | DATE_1     | Date        | DATE       | 0          |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 150        |          
    

Scenario Outline: Get a details of created report tables
        Given that I want to get a resource with the key "rep_uid" stored in session array as variable "rep_uid_<rep_uid_number>"
        And I request "project/<project>/report-table"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "rep_tab_name" is set to "<rep_tab_name>"
        And that "rep_tab_dsc" is set to "<rep_tab_dsc>"

    Examples:

    | test_description                    | project                          | rep_uid_number | rep_tab_name   | rep_tab_dsc                 | rep_tab_connection | rep_tab_type | rep_tab_grid | fld_dyn_1 | fld_name_1 | fld_label_1      | fld_type_1 | fld_size_1 | fld_dyn_2 | fld_name_2 | fld_label_2 | fld_type_2 | fld_size_2 | fld_dyn_3 | fld_name_3     | fld_label_3    | fld_type_3 | fld_size_3  |
    | Update a Report Table - Normal .pm  | 922677707524ac7417ce345089010125 | 1              | REPORT_TABLE_1 | Report Table Desc Updated 1 | workflow           | NORMAL       |              | nameany   | NAME_ANY   | Name Any         | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       | 0          |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 150         |          
    | Update a Report Table - Grid   .pm  | 922677707524ac7417ce345089010125 | 2              | REPORT_TABLE_2 | Report Table Desc Updated 2 | workflow           | GRID         | grid         | text1     | TEXT_1     | Text 1           | VARCHAR    | 64         | fecha1    | DATE_1     | Date        | DATE       | 0          |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 150         |          
   

Scenario Outline: Populate report tables
        Given I request "project/<project>/report-table/rep_uid/populate"  with the key "rep_uid" stored in session array as variable "rep_uid_<rep_uid_number>"
        Then the response status code should be 200
        

    Examples:
 
    | test_description                  | project                          | rep_uid_number | rep_tab_name   | rep_tab_dsc                 | rep_tab_connection | rep_tab_type | rep_tab_grid | fld_dyn_1 | fld_name_1 | fld_label_1      | fld_type_1 | fld_size_1 | fld_dyn_2 | fld_name_2 | fld_label_2 | fld_type_2 | fld_size_2 | fld_dyn_3 | fld_name_3     | fld_label_3    | fld_type_3 | fld_size_3  |
    | Populate Report Table Normal .pm  | 922677707524ac7417ce345089010125 | 1              | REPORT_TABLE_1 | Report Table Desc Updated 1 | workflow           | NORMAL       |              | nameany   | NAME_ANY   | Name Any         | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       | 0          |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 150         |          
    | Populate Report Table Grid   .pm  | 922677707524ac7417ce345089010125 | 2              | REPORT_TABLE_2 | Report Table Desc Updated 2 | workflow           | GRID         | grid         | text1     | TEXT_1     | Text 1           | VARCHAR    | 64         | fecha1    | DATE_1     | Date        | DATE       | 0          |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 150         |          
   

Scenario Outline: Delete a created report tables
        Given that I want to delete a resource with the key "rep_uid" stored in session array as variable "rep_uid_<rep_uid_number>"
        And I request "project/<project>/report-table"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    Examples:

    | test_description                | project                          | rep_uid_number |
    | Delete Report Table Normal .pm  | 922677707524ac7417ce345089010125 | 1              |
    | Delete Report Table Grid   .pm  | 922677707524ac7417ce345089010125 | 2              |
    
    

Scenario Outline: Verify that the report tables were deleted correctly
    Given I request "project/<project>/report-tables"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has <record> record

    Examples:
    | test_description                                           | project                          | record |
    | Get of report table of process Test Designer Report Tables | 922677707524ac7417ce345089010125 | 0      |
    | Get of report table of process Process Complete BPMN       | 1455892245368ebeb11c1a5001393784 | 1      |