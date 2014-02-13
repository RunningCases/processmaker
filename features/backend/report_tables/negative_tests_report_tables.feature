@ProcessMakerMichelangelo @RestAPI
Feature: DataBase Connections Negative Tests

Background:
Given that I have a valid access_token

Scenario Outline: Create new report tables from dynaform and grid with bad parameters (negative tests)
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
                    "fld_size" : "<fld_size_1>"
                },{
                    "fld_dyn" : "<fld_dyn_2>",
                    "fld_name" : "<fld_name_2>",
                    "fld_label" : "<fld_label_2>",
                    "fld_type" : "<fld_type_2>",
                    "fld_size" : "<fld_size_2>"
                },{
                    "fld_dyn" : "<fld_dyn_3>",
                    "fld_name" : "<fld_name_3>",
                    "fld_label" : "<fld_label_3>",
                    "fld_type" : "<fld_type_3>",
                    "fld_size" : "<fld_size_3>"
                }
            ]
        }
        """
    And I request "project/<project>/report-table"
    Then the response status code should be <error_code>
    And store "rep_uid" in session array as variable "rep_uid_<rep_uid_number>"
    And the response status message should have the following text "<error_message>"


    Examples:

    | test_description                  | project                          | rep_uid_number | rep_tab_name   | rep_tab_dsc         | rep_tab_connection | rep_tab_type | rep_tab_grid                     | fld_dyn_1 | fld_name_1 | fld_label_1 | fld_type_1 | fld_size_1 | fld_dyn_2 | fld_name_2 | fld_label_2 | fld_type_2 | fld_size_2 | fld_dyn_3 | fld_name_3     | fld_label_3    | fld_type_3 | fld_size_3 | error_code | error_message      |
    | Field required rep_tab_name       | 922677707524ac7417ce345089010125 | 1              |                | Report Table Desc 1 | workflow           | NORMAL       |                                  | nameany   | NAME_ANY   | Name Any    | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 400        | rep_tab_name       |           
    | Field required rep_tab_dsc        | 922677707524ac7417ce345089010125 | 2              | REPORT_TABLE_2 |                     | workflow           | GRID         | 267480685524ac9b3bd5e23004484669 | text1     | TEXT_1     | Text 1      | VARCHAR    | 64         | fecha1    | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 400        | rep_tab_dsc        |
    | Field required rep_tab_connection | 922677707524ac7417ce345089010125 | 3              | REPORT_TABLE_1 | Report Table Desc 1 | workflow           | NORMAL       |                                  | nameany   | NAME_ANY   | Name Any    | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 400        | rep_tab_connection |           
    | Field required fld_type           | 922677707524ac7417ce345089010125 | 4              | REPORT_TABLE_2 | Report Table Desc 2 | workflow           |              | 267480685524ac9b3bd5e23004484669 | text1     | TEXT_1     | Text 1      | VARCHAR    | 64         | fecha1    | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 400        | fld_type           |
    | Field required fld_name           | 922677707524ac7417ce345089010125 | 5              | REPORT_TABLE_1 | Report Table Desc 1 | workflow           | NORMAL       |                                  | nameany   |            | Name Any    | VARCHAR    | 64         | date1     |            | Date        | DATE       |            |           |                | Custom Field 1 | VARCHAR    | 15         | 400        | fld_name           |           
    | Field required fld_label          | 922677707524ac7417ce345089010125 | 6              | REPORT_TABLE_2 | Report Table Desc 2 |                    | GRID         | 267480685524ac9b3bd5e23004484669 | text1     | TEXT_1     |             | VARCHAR    | 64         | fecha1    | DATE_1     |             | DATE       |            |           | CUSTOM_FIELD_1 |                | VARCHAR    | 15         | 400        | fld_label          |
    | The name is too short             | 922677707524ac7417ce345089010125 | 7              | RE             | Report Table Desc 1 | workflow           | NORMAL       |                                  | nameany   | NAME_ANY   | Name Any    | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 400        | characters         |           
    | Field required Project            | 92267000000000000000000089010125 | 8              | REPORT_TABLE_1 | Report Table Desc 1 | workflow           | NORMAL       |                                  | nameany   | NAME_ANY   | Name Any    | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 400        | prj_uid            |           
    | Invalid rep_tab_connection        | 922677707524ac7417ce345089010125 | 9              | REPORT_TABLE_2 | Report Table Desc 2 | sample             | GRID         | 267480685524ac9b3bd5e23004484669 | text1     | TEXT_1     | Text 1      | VARCHAR    | 64         | fecha1    | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 400        | rep_tab_connection |
    | Invalid rep_tab_type              | 922677707524ac7417ce345089010125 | 10             | REPORT_TABLE_1 | Report Table Desc 1 | workflow           | INPUT        |                                  | nameany   | NAME_ANY   | Name Any    | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 400        | rep_tab_type       |           
    | Invalid fld_type                  | 922677707524ac7417ce345089010125 | 11             | REPORT_TABLE_1 | Report Table Desc 1 | workflow           | NORMAL       |                                  | nameany   | NAME_ANY   | Name Any    | SAMPLE     | 64         | date1     | DATE_1     | Date        | SAMPLE     |            |           | CUSTOM_FIELD_1 | Custom Field 1 | SAMPLE     | 15         | 400        | fld_type           |
    | Invalid fld_size                  | 922677707524ac7417ce345089010125 | 12             | REPORT_TABLE_1 | Report Table Desc 1 | workflow           | NORMAL       |                                  | nameany   | NAME_ANY   | Name Any    | VARCHAR    | 64,34.55   | date1     | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 64,34.55   | 400        | fld_size           |
    | Invalid rep_tab_grid              | 922677707524ac7417ce345089010125 | 13             | REPORT_TABLE_2 | Report Table Desc 2 | workflow           | GRID         | 26748060000000000000000000484669 | text1     | TEXT_1     | Text 1      | VARCHAR    | 64         | fecha1    | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 400        | rep_tab_grid       |
    | Create same rep_tab_name          | 922677707524ac7417ce345089010125 | 14             | REPORT_TABLE_1 | Report Table Desc 1 | workflow           | NORMAL       |                                  | nameany   | NAME_ANY   | Name Any    | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 200        |                    |           
    | Create same rep_tab_name          | 922677707524ac7417ce345089010125 | 15             | REPORT_TABLE_1 | Report Table Desc 1 | workflow           | NORMAL       |                                  | nameany   | NAME_ANY   | Name Any    | VARCHAR    | 64         | date1     | DATE_1     | Date        | DATE       |            |           | CUSTOM_FIELD_1 | Custom Field 1 | VARCHAR    | 15         | 400        | already exits      |           
    

Scenario Outline: Delete a created report tables
        Given that I want to delete a resource with the key "rep_uid" stored in session array as variable "rep_uid_<rep_uid_number>"
        And I request "project/<project>/report-table"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    Examples:

    | test_description           | project                          | rep_uid_number |
    | Delete Report Table Normal | 922677707524ac7417ce345089010125 | 14             |