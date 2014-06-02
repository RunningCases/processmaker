@ProcessMakerMichelangelo @RestAPI
Feature: Roles
      
Background:
    Given that I have a valid access_token

  
Scenario: Get list of Roles
    Given I request "role"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 3 records


Scenario: Get a single Role
    Given I request "role/00000000000000000000000000000002"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    

Scenario Outline:  Create new Role
    Given POST this data:
    """
        {
            "rol_code": "<rol_code>",
            "rol_name": "<rol_name>",
            "rol_status": "<rol_status>"
        }

    """
    And I request "role"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "rol_uid" in session array as variable "rol_uid_<rol_uid_number>"

    Examples:

    | test_description            | rol_uid_number | rol_code               | rol_name | rol_status  |
    | Create Role with name short | 1              | PROCESSMAKER_OPERATOR1 | sample   | ACTIVE      |


#Assign users to role

#Scenario: List assigned Users to Role & List available Users to assign to Role
#    Given I request "role/00000000000000000000000000000003/users"
#    Then the response status code should be 200
#    And the response charset is "UTF-8"
#    And the content type is "application/json"
#    And the type is "array"
#    And the response has 61 records
#
#Scenario:  Assign User to Role
#    Given POST this data:
#    """
#        {
#            "usr_uid": "310985970530cbfa4ec0593063369294"
#        }
#    """
#    And I request "role/00000000000000000000000000000003/user"
#    Then the response status code should be 201
#    And the response charset is "UTF-8"
#    And the content type is "application/json"
#    And the type is "object"
#   
#
#Scenario: Get list of Roles
#    Given I request "roles"
#    Then the response status code should be 200
#    And the response charset is "UTF-8"
#    And the content type is "application/json"
#    And the type is "array"
#    And the response has 62 records
#
#
#Scenario: Unassign User of the Role
#    Given that I want to delete a resource with the key "310985970530cbfa4ec0593063369294"
#    And I request "role/00000000000000000000000000000003/user/310985970530cbfa4ec0593063369294"
#    And the content type is "application/json"
#    Then the response status code should be 200
#    And the response charset is "UTF-8"
#
#
#Scenario: Get list of Roles
#    Given I request "roles"
#    Then the response status code should be 200
#    And the response charset is "UTF-8"
#    And the content type is "application/json"
#    And the type is "array"
#    And the response has 61 records
#Culminacion de los endpoint de asignacion de usuarios

#Role and Permission

Scenario: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/00000000000000000000000000000003/permissions"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 3 records

Scenario: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/00000000000000000000000000000003/available-permissions"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 14 recordsuser

Scenario: Assign Permission "PM_DASHBOARD" to Role
    Given POST this data:
    """
        {
            "per_uid": "00000000000000000000000000000011"
        }
    """
    And I request "role/00000000000000000000000000000003/permission"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
   
Scenario: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/00000000000000000000000000000003/permissions"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 4 records

Scenario: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/00000000000000000000000000000003/available-permissions"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 15 records

Scenario: Unassign Permission of the Role
    Given that I want to delete a resource with the key ""
    And I request "role/00000000000000000000000000000003/permission/00000000000000000000000000000011"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

Scenario: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/00000000000000000000000000000003/permissions"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 3 records

Scenario: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/00000000000000000000000000000003/available-permissions"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 14 records
#Culminacion de behat para Role and Permission



Scenario Outline: Update Role
    Given PUT this data:
    """
        {
            "rol_code": "<rol_code>",
            "rol_name": "<rol_name>",
            "rol_status": "<rol_status>"
        }

    """
    And that I want to update a resource with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    And I request "role"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:

    | test_description                           | rol_uid_number | rol_code               | rol_name        | rol_status  |
    | Update name of role created in this script | 1              | PROCESSMAKER_OPERATOR1 | update_sample   | INACTIVE    |


Scenario Outline: Get a single Role created in this script
    Given that I want to get a resource with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Given I request "role"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And that "rol_code" is set to "<rol_code>"
    And that "rol_name" is set to "<rol_name>"
    And that "rol_status" is set to "<rol_status>"

    Examples:

    | test_description                           | rol_uid_number | rol_code               | rol_name        | rol_status  |
    | Update name of role created in this script | 1              | PROCESSMAKER_OPERATOR1 | update_sample   | INACTIVE    |


Scenario Outline: Delete all roles created in this scritp
    Given that I want to delete a resource with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    And I request "role"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:

    | rol_uid_number |
    | 1              |
    

Scenario: Get list of Roles
    Given I request "roles"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 3 records