@ProcessMakerMichelangelo @RestAPI
Feature: Process variables Resources

Requirements:
    a workspace with the process 14414793652a5d718b65590036026581 ("Sample Project #1") already loaded
    there are three activities in the process

Background:
    Given that I have a valid access_token


Scenario: Get a List of process variables
    And I request "project/14414793652a5d718b65590036026581/process-variables"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the json data is an empty array

   
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
    And I request "project/14414793652a5d718b65590036026581/process-variable"
    And the content type is "application/json"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the type is "object"
    And store "var_uid" in session array as variable "var_uid_<var_uid_number>"

    Examples:
    | test_description       | var_uid_number | var_name       | var_field_type | var_field_size | var_label  | var_dbconnection | var_sql                                 | var_null | var_default | var_accepted_values |
    | Create a integer       | 1              | integer1       | integer        | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    | Create a boolean       | 2              | boolean1       | boolean        | 10             | Fecha      |                  |                                         | 0        |             |                     |
    | Create a string        | 3              | string1        | string         | 12             | Dropdown 1 |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |
    | Create a float         | 4              | float1         | float          | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    | Create a datetime      | 5              | datetime1      | datetime       | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    | Create a date_of_birth | 6              | date_of_birth1 | date_of_birth  | 12             | Texto 1    |                  |                                         | 0        |             |                     |
    

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
        
    And I request "project/14414793652a5d718b65590036026581/process-variable"
    And the content type is "application/json"
    Then the response status code should be 200

    Examples:
    | test_description  | var_uid_number | var_field_type | var_field_size | var_label            | var_dbconnection | var_sql                                 | var_null | var_default | var_accepted_values |
    | Update a text     | 1              | text           | 12             | Texto 1 - Updated    |                  |                                         | 0        |             |                     |
    | Update a date     | 2              | date           | 10             | Fecha - Updated      |                  |                                         | 0        |             |                     |
    | Update a dropdown | 3              | dropdown       | 12             | Dropdown 1 - Updated |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |
 

    
Scenario: Get a List of process variables
    And I request "project/14414793652a5d718b65590036026581/process-variables"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"

    
Scenario Outline: Get a single process variable
    And that I want to get a resource with the key "var_uid" stored in session array as variable "var_uid_<var_uid_number>"
    And I request "project/14414793652a5d718b65590036026581/process-variable"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
    | test_description  | var_uid_number | var_name  | var_field_type | var_field_size | var_label            | var_dbconnection | var_sql                                 | var_null | var_default | var_accepted_values |
    | Update a text     | 1              | texto1    | text           | 12             | Texto 1 - Updated    |                  |                                         | 0        |             |                     |
    | Update a date     | 2              | date1     | date           | 10             | Fecha - Updated      |                  |                                         | 0        |             |                     |
    | Update a dropdown | 3              | dropdown1 | dropdown       | 12             | Dropdown 1 - Updated |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |
 

Scenario Outline: Execute query of variables with SQL
    Given POST this data:
    """
    {
        
    }
    """ 
    And I request "project/14414793652a5d718b65590036026581/process-variable/<var_name>/execute-query"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"

    Examples:
    | test_description  | var_uid_number | var_name | var_field_type | var_field_size | var_label  | var_dbconnection | var_sql                                 | var_null | var_default | var_accepted_values |
    | Create a dropdown | 3              | string1  | string         | 12             | Dropdown 1 |                  | SELECT IC_UID, IC_NAME FROM ISO_COUNTRY | 0        |             |                     |

        
Scenario Outline: Delete a previously created process variable
    Given that I want to delete a resource with the key "var_uid" stored in session array as variable "var_uid_<var_uid_number>"
    And I request "project/14414793652a5d718b65590036026581/process-variable"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:
    | test_description       | var_uid_number |
    | Create a integer       | 1              |
    | Create a boolean       | 2              |
    | Create a string        | 3              |
    | Create a float         | 4              |
    | Create a datetime      | 5              |
    | Create a date_of_birth | 6              |
    
        
Scenario: Get a List of process variables
    And I request "project/14414793652a5d718b65590036026581/process-variables"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the json data is an empty array