@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents Negative Tests


Background:
    Given that I have a valid access_token

Scenario: Create news pmtable with name same
    Given POST this data:
    """
    {
    "pmt_tab_name" : "DYNAFORM",
    "pmt_tab_dsc" : "",
    "fields" : [
        {
            "fld_key" : 1,
            "fld_name" : "DYN_UID",
            "fld_label" : "Unique Id",
            "fld_type" : "VARCHAR",
            "fld_size" : 32
        },{
            "fld_name" : "DYN_TITLE",
            "fld_label" : "Title",
            "fld_type" : "VARCHAR",
            "fld_size" : 150
        }
            ]
    }
    """
    And I request "pmtable"
    Then the response status code should be 400
    And the response status message should have the following text "already exits"


Scenario Outline: Create news pmtable (Negative Test)
    Given POST this data:
    """
    {
    "pmt_tab_name" : "<pmt_tab_name>",
    "pmt_tab_dsc" : "<pmt_tab_dsc>",
    "fields" : [
        {
            "fld_key" : 1,
            "fld_name" : "<fld_name>",
            "fld_label" : "<fld_label>",
            "fld_type" : "<fld_type>",
            "fld_size" : "<fld_size>"
        },
            ]
    }
    """
    And I request "pmtable"
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"

    Examples:

    | test_description               | pmt_tab_name | pmt_tab_dsc | fld_name | fld_label | fld_type | fld_size | fld_name | fld_label | fld_type | fld_size | fld_name | fld_label | fld_type    | fld_size | error_code | error_message |
    | Required pmt_tab_name          |              | pmt table 1 | UNO      | UNO       | VARCHAR  | 32       | DOS      | DOS       | BIGINT   |          | TRES     | TRES      | BOOLEAN     |          | 400        | pmt_tab_name  |
    | Required pmt_tab_dsc           | PMT_Test_QA2 |             | UNO      | UNO       | VARCHAR  | 32       | DOS      | DOS       | CHAR     |          | TRES     | TRES      | DATE        |          | 400        | pmt_tab_dsc   |
    | Required fld_name              | PMT_Test_QA3 | pmt table 3 |          | UNO       | VARCHAR  | 32       | DOS      | DOS       | DATETIME |          | TRES     | TRES      | DECIMAL     |          | 400        | fld_name      |
    | Required fld_label             | PMT_Test_QA4 | pmt table 4 | UNO      |           | VARCHAR  | 32       | DOS      | DOS       | DOUBLE   |          | TRES     | TRES      | FLOAT       |          | 400        | fld_label     |
    | Required fld_type              | PMT_Test_QA5 | pmt table 5 | UNO      | UNO       | VARCHAR  | 32       | DOS      | DOS       |          |          | TRES     | TRES      | LONGVARCHAR |          | 400        | fld_type      |
    | Invalid fld_size               | PMT_Test_QA6 | pmt table 6 | UNO      | UNO       | VARCHAR  | sample   | DOS      | DOS       | REAL     | samke    | TRES     | TRES      | SMALLINT    | task     | 400        | fld_size      |
    | Invalid fld_type               | PMT_Test_QA7 | pmt table 7 | UNO      | UNO       | 123      | 32       | DOS      | DOS       | 1234     |          | TRES     | TRES      | 457,777     |          | 400        | fld_type      |