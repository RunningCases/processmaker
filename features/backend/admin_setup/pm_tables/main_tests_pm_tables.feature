@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents Main Tests
  Requirements:
    a workspace with the pmtable 65193158852cc1a93a5a535084878044 ("DYNAFORM") already loaded
    
Background:
    Given that I have a valid access_token


Scenario: Get the PMTABLE List when there are exactly ONE pmtables in this workspace
    Given I request "pmtable"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 records
    

Scenario: Get a single the PMTABLE
    Given I request "pmtable/65193158852cc1a93a5a535084878044"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And that "pmt_uid" is set to "65193158852cc1a93a5a535084878044"
    And that "pmt_tab_name" is set to "PMT_DYNAFORM"
    And that "pmt_tab_description" is set to ""
    And that "pmt_tab_class_name" is set to "PmtDynaform"
    And that "pmt_num_rows" is set to "1"
    

Scenario: Get data of the PMTABLE
    Given I request "pmtable/65193158852cc1a93a5a535084878044/data"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And that "dyn_uid" is set to "1"
    And that "dyn_title" is set to "sample"
    And that "dyn_description" is set to "test"


Scenario Outline: Create news pmtable
    Given POST this data:
    """
    {
    "pmt_tab_name" : "<pmt_tab_name>",
    "pmt_tab_dsc" : "<pmt_tab_dsc>",
    "fields" : [
        {
            "fld_key" : 1,
            "fld_name" : "<fld_name_1>",
            "fld_label" : "<fld_label_1>",
            "fld_type" : "<fld_type_1>",
            "fld_size" : 32
        },{
            "fld_name" : "<fld_name_2>",
            "fld_label" : "<fld_label_2>",
            "fld_type" : "<fld_type_2>",
            "fld_size" : 45
        },{
            "fld_name" : "<fld_name_3>",
            "fld_label" : "<fld_label_3>",
            "fld_type" : "<fld_type_3>",
            "fld_size" : 32
        }
            ]
    }
    """
    And I request "pmtable"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "pmt_uid" in session array as variable "pmt_uid_<pmt_uid_number>"

    Examples:

    | test_description                                          | pmt_uid_number | pmt_tab_name | pmt_tab_dsc                     | fld_name_1 | fld_label_1 | fld_type_1 | fld_name_2 | fld_label_2 | fld_type_2 | fld_name_3 | fld_label_3 | fld_type_3    |
    | Create pmtable with type varchar, bigint and boolean      | 1              | PMT_Test_QA1 | pmt table 1 created with script | UNO      | UNO       | VARCHAR  | DOS      | DOS       | BIGINT   | TRES     | TRES      | BOOLEAN     |
    | Create pmtable with type varchar, char and date           | 2              | PMT_Test_QA2 | pmt table 2 created with script | UNO      | UNO       | VARCHAR  | DOS      | DOS       | CHAR     | TRES     | TRES      | DATE        |
    | Create pmtable with type varchar, datetime and decimal    | 3              | PMT_Test_QA3 | pmt table 3 created with script | UNO      | UNO       | VARCHAR  | DOS      | DOS       | DATETIME | TRES     | TRES      | DECIMAL     |
    | Create pmtable with type varchar, double and float        | 4              | PMT_Test_QA4 | pmt table 4 created with script | UNO      | UNO       | VARCHAR  | DOS      | DOS       | DOUBLE   | TRES     | TRES      | FLOAT       |
    | Create pmtable with type varchar, integer and longvarchar | 5              | PMT_Test_QA5 | pmt table 5 created with script | UNO      | UNO       | VARCHAR  | DOS      | DOS       | INTEGER  | TRES     | TRES      | LONGVARCHAR |
    | Create pmtable with type varchar, real and smallint       | 6              | PMT_Test_QA6 | pmt table 6 created with script | UNO      | UNO       | VARCHAR  | DOS      | DOS       | REAL     | TRES     | TRES      | SMALLINT    |
    | Create pmtable with type varchar, time and tinyint        | 7              | PMT_Test_QA7 | pmt table 7 created with script | UNO      | UNO       | VARCHAR  | DOS      | DOS       | TIME     | TRES     | TRES      | TINYINT     |


Scenario Outline: Create a new Data of pm table.
    Given POST this data:
    """
    {
        "CAMPO1" : "QA",
        "CAMPO2" : "<CAMPO2>"    
    }
    """
    And I request "pmtable/pmt_uid/data" with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>"  
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    
    Examples:

    | pmt_uid_number | CAMPO2 |
    | 1              | DEV1   |
    | 2              | DEV2   |
    | 3              | DEV3   |
    | 4              | DEV4   |
    | 5              | DEV5   |
    | 6              | DEV6   |
    | 7              | DEV7   |


Scenario Outline: Update a pm table of a project
    Given PUT this data:
    """
    {
    "rep_tab_dsc" : "descripcion de la tabla",
    "fields" : [
        {
            "fld_key" : 1,
            "fld_name" : "<fld_name>",
            "fld_label" : "<fld_label>",
            "fld_type" : "<fld_type>",
            "fld_size" : 200
        }
               ]
    }
    """
    And that I want to update a resource with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>"
    And I request "pmtable"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:

    | Description        | pmt_uid_number | fld_name   | fld_label  |
    | Update a pmtable 1 | 1              | UPDATEUNO  | UPDATEUNO  |
    | Update a pmtable 3 | 3              | UPDATETRES | UPDATETRES |
    | Update a pmtable 6 | 6              | UPDATESEIS | UPDATESEIS |
         

Scenario Outline: Get a single the PMTABLE after update
    Given that I want to get a resource with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>"
    And I request "pmtable"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And that "fld_name" is set to "<fld_name>"
    And that "fld_label" is set to "<fld_label>"
    And that "fld_type" is set to "<fld_type>"

    Examples:

    | pmt_uid_number | fld_name   | fld_label  |
    | 1              | UPDATEUNO  | UPDATEUNO  |
    | 3              | UPDATETRES | UPDATETRES |
    | 6              | UPDATESEIS | UPDATESEIS |



Scenario Outline: Update a a data of pm table
    Given PUT this data:
    """
    {
        "CAMPO1" : "QA",
        "CAMPO2" : "<CAMPO2>"
    }
    """
    And that I want to update "PM Table"
    And I request "pmtable/pmt_uid/data" with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>" 
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:

    | pmt_uid_number | CAMPO2    |
    | 1              | UPDATEQA2 |
    | 4              | UPDATEQA4 |
    | 6              | UPDATEQA6 |


Scenario Outline: Get data of the PMTABLE
    Given I request "pmtable/pmt_uid/data" with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>" 
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And that "CAMPO1" is set to "QA"
    And that "CAMPO2" is set to "CAMPO2"

    Examples:

    | pmt_uid_number | CAMPO2    |
    | 1              | UPDATEQA2 |
    | 4              | UPDATEQA4 |
    | 6              | UPDATEQA6 |


Scenario: Get the PMTABLE List when there are exactly two pmtables in this workspace
    Given I request "pmtable"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 2 records


Scenario Outline: Delete a pm table of a pmtable
    Given that I want to delete a resource with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>"
    And I request "pmtable"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:

    | pmt_uid_number |
    | 1              |
    | 2              |
    | 3              |
    | 4              |
    | 5              |
    | 6              |
    | 7              |


Scenario Outline: Delete a data of a pmtable
    Given that I want to delete a resource with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>"
    And I request "pmtable/<pmt_uid>/data/CAMPO1/QA"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:

    | pmt_uid_number |
    | 1              |


Scenario: Get the PMTABLE List when there are exactly ONE pmtables in this workspace
    Given I request "pmtable"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 records