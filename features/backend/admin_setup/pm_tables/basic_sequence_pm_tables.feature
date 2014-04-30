@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents
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
    

Scenario: Get data of the PMTABLE
    Given I request "pmtable/65193158852cc1a93a5a535084878044/data"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And that "dyn_uid" is set to "1"
    And that "dyn_title" is set to "sample"
    And that "dyn_description" is set to "test"


Scenario Outline: Create new pmtable
    Given POST this data:
    """
    {
    "pmt_tab_name" : "<pmt_tab_name>",
    "pmt_tab_dsc" : "<pmt_tab_dsc>",
    "fields" : [
        {
            "fld_key" : 1,
            "fld_name" : "CAMPO1",
            "fld_label" : "CAMPO1",
            "fld_type" : "VARCHAR",
            "fld_size" : 32
        },{
            "fld_name" : "CAMPO2",
            "fld_label" : "CAMPO2",
            "fld_type" : "VARCHAR",
            "fld_size" : 200
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

    | test_description                   | pmt_uid_number | pmt_tab_name | pmt_tab_dsc                   |
    | Create pmtable with fields defined | 1              | PMT_Test_QA  | pmt table created with script |


Scenario Outline: Create a new Data of pm table.
    Given POST this data:
    """
    {
        "CAMPO1" : "valor1",
        "CAMPO2" : "valor2"    
    }
    """
    And I request "pmtable/pmt_uid/data" with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>" 
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"

    Examples:

    | pmt_uid_number |
    | 1              |


Scenario Outline: Update a pm table of a project
    Given PUT this data:
    """
    {
    "rep_tab_dsc" : "descripcion de la tabla",
    "fields" : [
        {
            "fld_key" : 1,
            "fld_name" : "UPDATECAMPO",
            "fld_label" : "UPDATECAMPO",
            "fld_type" : "VARCHAR",
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

    | pmt_uid_number |
    | 1              |
        

Scenario Outline: Get a single the PMTABLE after update
    Given that I want to get a resource with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>"
    And I request "pmtable"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And that "fld_name" is set to "UPDATECAMPO"
    And that "fld_label" is set to "UPDATECAMPO"
    And that "fld_type" is set to "VARCHAR"

Examples:

    | pmt_uid_number |
    | 1              |


Scenario Outline: Update a a data of pm table
    Given PUT this data:
    """
    {
        "CAMPO1" : "valor1",
        "CAMPO2" : "updatevalor2"
    }
    """
    And that I want to update a "PM Table"
    And I request "pmtable/pmt_uid/data" with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>" 
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:

    | pmt_uid_number |
    | 1              |


Scenario Outline: Get data of the PMTABLE
    Given I request "pmtable/pmt_uid/data" with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>" 
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And that "CAMPO1" is set to "valor1"
    And that "CAMPO2" is set to "updatevalor2"
    
    Examples:

    | pmt_uid_number |
    | 1              |

Scenario: Get the PMTABLE List when there are exactly ONE pmtables in this workspace
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


Scenario Outline: Delete a data of a pmtable
    Given that I want to delete a resource with the key "pmt_uid" stored in session array as variable "pmt_uid_<pmt_uid_number>"
    And I request "pmtable/<pmt_uid>/data/CAMPO1/updatevalor2"
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