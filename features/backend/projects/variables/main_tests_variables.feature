@ProcessMakerMichelangelo @RestAPI
Feature: Process variables Resources

Requirements:
    a workspace with the process 14414793652a5d718b65590036026581 ("Sample Project #1") already loaded
    there are three activities in the process
    and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded

Background:
    Given that I have a valid access_token


Scenario Outline: Get a List of process variables
    And I request "project/<project>/process-variables"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records

    Examples:
    | test_description              | project                          | records |
    | Get variables of process .pm  | 14414793652a5d718b65590036026581 | 0       |
    | Get variables of process .pmx | 1455892245368ebeb11c1a5001393784 | 0       |
    
   
Scenario Outline: Create variables for a Project (Normal creation of a process variable)
    Given POST this data:
    """
    {
        "var_name": "<var_name>",
        "var_field_type": "<var_field_type>",
        "var_field_size": <var_field_size>,
        "var_label": "<var_label>",
        "var_dbconnection": "<var_dbconnection>",
        "var_sql": "<var_sql>",
        "var_null": <var_null>,
        "var_default": "<var_default>",
        "var_accepted_values": "<var_accepted_values>"
    }
    """
    And I request "project/<project>/process-variable"
    And the content type is "application/json"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the type is "object"
    And store "var_uid" in session array as variable "var_uid_<var_uid_number>"

    Examples:
    | test_description            | var_uid_number | project                          | var_name       | var_field_type | var_field_size | var_label  | var_dbconnection | var_sql                                 | var_null | var_default | var_accepted_values |
    | Create a integer       .pm  | 1              | 14414793652a5d718b65590036026581 | integer1       | integer        | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    | Create a boolean       .pm  | 2              | 14414793652a5d718b65590036026581 | boolean1       | boolean        | 10             | Fecha      |                  |                                         | 0        |             |                     |
    | Create a string        .pm  | 3              | 14414793652a5d718b65590036026581 | string1        | string         | 12             | Dropdown 1 |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |
    | Create a float         .pm  | 4              | 14414793652a5d718b65590036026581 | float1         | float          | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    | Create a datetime      .pm  | 5              | 14414793652a5d718b65590036026581 | datetime1      | datetime       | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    | Create a date_of_birth .pm  | 6              | 14414793652a5d718b65590036026581 | date_of_birth1 | date_of_birth  | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    | Create a integer       .pmx | 7              | 1455892245368ebeb11c1a5001393784 | integer1       | integer        | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    | Create a boolean       .pmx | 8              | 1455892245368ebeb11c1a5001393784 | boolean1       | boolean        | 10             | Fecha      |                  |                                         | 0        |             |                     |
    | Create a string        .pmx | 9              | 1455892245368ebeb11c1a5001393784 | string1        | string         | 12             | Dropdown 1 |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |
    | Create a float         .pmx | 10             | 1455892245368ebeb11c1a5001393784 | float1         | float          | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    | Create a datetime      .pmx | 11             | 1455892245368ebeb11c1a5001393784 | datetime1      | datetime       | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    | Create a date_of_birth .pmx | 12             | 1455892245368ebeb11c1a5001393784 | date_of_birth1 | date_of_birth  | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    

Scenario Outline: Update a process variable
    Given PUT this data:
    """
    {
        "var_field_type": "<var_field_type>",
        "var_field_size": <var_field_size>,
        "var_label": "<var_label>",
        "var_dbconnection": "<var_dbconnection>",
        "var_sql": "<var_sql>",
        "var_null": <var_null>,
        "var_default": "<var_default>",
        "var_accepted_values": "<var_accepted_values>"
    }
    """
    And that I want to update a resource with the key "var_uid" stored in session array as variable "var_uid_<var_uid_number>"
        
    And I request "project/<project>/process-variable"
    And the content type is "application/json"
    Then the response status code should be 200

    Examples:
    | test_description       | var_uid_number | project                          | var_field_type | var_field_size | var_label            | var_dbconnection | var_sql                                 | var_null | var_default | var_accepted_values |
    | Update a text      .pm | 1              | 14414793652a5d718b65590036026581 | string         | 12             | Texto 1 - Updated    |                  |                                         | 0        |             |                     |
    | Update a date      .pm | 2              | 14414793652a5d718b65590036026581 | date           | 10             | Fecha - Updated      |                  |                                         | 0        |             |                     |
    | Update a dropdown  .pm | 3              | 14414793652a5d718b65590036026581 | string         | 12             | Dropdown 1 - Updated |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |
    | Update a text .pmx     | 7              | 1455892245368ebeb11c1a5001393784 | string         | 12             | Texto 1 - Updated    |                  |                                         | 0        |             |                     |
    | Update a date .pmx     | 8              | 1455892245368ebeb11c1a5001393784 | date           | 10             | Fecha - Updated      |                  |                                         | 0        |             |                     |
    | Update a dropdown .pmx | 9              | 1455892245368ebeb11c1a5001393784 | string         | 12             | Dropdown 1 - Updated |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |
    

Scenario Outline: Get a List of process variables
    And I request "project/<project>/process-variables"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has <records> records

    Examples:
    | test_description              | project                          | records |
    | Get variables of process .pm  | 14414793652a5d718b65590036026581 | 6       |
    | Get variables of process .pmx | 1455892245368ebeb11c1a5001393784 | 6       |

    
Scenario Outline: Get a single process variable
    And that I want to get a resource with the key "var_uid" stored in session array as variable "var_uid_<var_uid_number>"
    And I request "project/<project>/process-variable"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
    | test_description         | var_uid_number | project                          | var_name       | var_field_type | var_field_size | var_label            | var_dbconnection | var_sql                                 | var_null | var_default | var_accepted_values |
    | Get after update of .pm  | 1              | 14414793652a5d718b65590036026581 | texto1         | text           | 12             | Texto 1 - Updated    |                  |                                         | 0        |             |                     |
    | Get after update of .pm  | 2              | 14414793652a5d718b65590036026581 | date1          | date           | 10             | Fecha - Updated      |                  |                                         | 0        |             |                     |
    | Get after update of .pm  | 3              | 14414793652a5d718b65590036026581 | dropdown1      | dropdown       | 12             | Dropdown 1 - Updated |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |
    | Get after update of .pmx | 7              | 1455892245368ebeb11c1a5001393784 | date_of_birth1 | string         | 12             | Texto 1 - Updated    |                  |                                         | 0        |             |                     |
    | Get after update of .pmx | 8              | 1455892245368ebeb11c1a5001393784 | integer1       | date           | 10             | Fecha - Updated      |                  |                                         | 0        |             |                     |
    | Get after update of .pmx | 9              | 1455892245368ebeb11c1a5001393784 | boolean1       | string         | 12             | Dropdown 1 - Updated |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |
    

Scenario Outline: Execute query of variables with SQL
    Given POST this data:
    """
    {
        
    }
    """ 
    And I request "project/<project>/process-variable/<var_name>/execute-query"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"

    Examples:
    | test_description  | var_uid_number | project                          | var_name | var_field_type | var_field_size | var_label  | var_dbconnection | var_sql                                 | var_null | var_default | var_accepted_values |
    | Create a dropdown | 3              | 14414793652a5d718b65590036026581 | string1  | string         | 12             | Dropdown 1 |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |
    | Create a dropdown | 9              | 1455892245368ebeb11c1a5001393784 | string1  | string         | 12             | Dropdown 1 |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |

        
Scenario Outline: Delete a previously created process variable
    Given that I want to delete a resource with the key "var_uid" stored in session array as variable "var_uid_<var_uid_number>"
    And I request "project/<project>/process-variable"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:
    | test_description            | var_uid_number | project                          |
    | Create a integer       .pm  | 1              | 14414793652a5d718b65590036026581 |
    | Create a boolean       .pm  | 2              | 14414793652a5d718b65590036026581 |
    | Create a string        .pm  | 3              | 14414793652a5d718b65590036026581 |
    | Create a float         .pm  | 4              | 14414793652a5d718b65590036026581 |
    | Create a datetime      .pm  | 5              | 14414793652a5d718b65590036026581 |
    | Create a date_of_birth .pm  | 6              | 14414793652a5d718b65590036026581 |
    | Create a integer       .pmx | 7              | 1455892245368ebeb11c1a5001393784 |
    | Create a boolean       .pmx | 8              | 1455892245368ebeb11c1a5001393784 |
    | Create a string        .pmx | 9              | 1455892245368ebeb11c1a5001393784 |
    | Create a float         .pmx | 10             | 1455892245368ebeb11c1a5001393784 |
    | Create a datetime      .pmx | 11             | 1455892245368ebeb11c1a5001393784 |
    | Create a date_of_birth .pmx | 12             | 1455892245368ebeb11c1a5001393784 |
    
        
Scenario Outline: Get a List of process variables
    And I request "project/<project>/process-variables"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records

    Examples:
    | test_description              | project                          | records |
    | Get variables of process .pm  | 14414793652a5d718b65590036026581 | 0       |
    | Get variables of process .pmx | 1455892245368ebeb11c1a5001393784 | 0       |