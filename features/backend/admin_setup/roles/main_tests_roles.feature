@ProcessMakerMichelangelo @RestAPI
Feature: Roles Main Tests
Requirements:
    a workspace with the three roles created already loaded
          
Background:
    Given that I have a valid access_token

  
Scenario: Get list of Roles
    Given I request "roles"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 3 records


Scenario Outline: Get list of Roles using different filters
    Given I request "roles?filter=<filter>&start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records

    Examples:
    
    | test_description      | filter | start | limit   | records | http_code | type   |
    | lowercase             | admin  |   0   | 1       | 1       |  200      | array  |
    | uppercase             | ADMIN  |   0   | 1       | 1       |  200      | array  |
    | limit=3               | a      |   0   | 3       | 3       |  200      | array  |
    | limit and start       | a      |   1   | 2       | 2       |  200      | array  |
    | high number for start | a      | 1000  | 1       | 0       |  200      | array  |
    | high number for start | a      | 1000  | 0       | 0       |  200      | array  |
    | empty result          | xyz    |   0   | 0       | 0       |  200      | array  |
    | empty string          |        |   0   | 10000   | 3       |  200      | array  |
    | empty string          |        |   1   | 2       | 2       |  200      | array  |
    | invalid start         | a      |   b   | c       | 0       |  400      | string |
    | invalid limit         | a      |   0   | c       | 0       |  400      | string |
    | search 0              | 0      |   0   | 0       | 0       |  200      | array  |
    | search 0              | 0      |   0   | 100     | 0       |  200      | array  |
    | negative numbers      | a      |  -10  | -20     | 0       |  400      | string |
    | real numbers          | a      |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers          | a      |  0.0  | 0.0     | 0       |  400      | string |
    | real numbers          | a      |  0.1  | 1.4599  | 0       |  400      | string |
    | real numbers          | a      |  1.5  | 1.4599  | 0       |  400      | string |


Scenario Outline: Get a single Role created in this script
    Given that I want to get a resource with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Given I request "role/<rol_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And that "rol_code" is set to "<rol_code>"
    And that "rol_name" is set to "<rol_name>"
    And that "rol_status" is set to "<rol_status>"

    Examples:

    | test_description                           | rol_uid                          | rol_code              | rol_name             | rol_status |
    | Review rol PROCESSMAKER_ADMIN              | 00000000000000000000000000000002 | PROCESSMAKER_ADMIN    | System Administrator | ACTIVE     |
    | Review rol PROCESSMAKER_OPERATOR           | 00000000000000000000000000000003 | PROCESSMAKER_OPERATOR | Operator             | ACTIVE     |
    | Review rol PROCESSMAKER_MANAGER            | 00000000000000000000000000000004 | PROCESSMAKER_MANAGER  | Manager              | ACTIVE     |

    
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

    | test_description                             | rol_uid_number | rol_code            | rol_name                                      | rol_status  |
    | Create Role with name short                  | 1              | PROCESSMAKER_UNO    | s                                             | ACTIVE      |
    | Create Role with name large                  | 2              | PROCESSMAKER_DOS    | Esta es una prueba de un rol con nombre largo | ACTIVE      |
    | Create Role with Code Adminsitrator          | 3              | PROCESSMAKER_TRES   | Rol con code administrator                    | ACTIVE      |
    | Create Role with Code Manager                | 4              | PROCESSMAKER_CUATRO | Rol con code manager                          | ACTIVE      |
    | Create Role with Code Adminsitrator/inactive | 5              | PROCESSMAKER_CINCO  | Rol con code administrator/inactive           | INACTIVE    |
    | Create Role with Code Operator/inactive      | 6              | PROCESSMAKER_SEIS   | Rol con code operator/inactive                | INACTIVE    |
    | Create Role with Code Manager/inactive       | 7              | PROCESSMAKER_SIETE  | Rol con code manager/inactive                 | INACTIVE    |
    | Create Role with character special           | 8              | PROCESSMAKER_OCHO   | Rol !@##$%&*()'][' 123                        | ACTIVE      |

    
Scenario: Get list of Roles
    Given I request "roles"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 11 records


#Assign users to role
    
Scenario Outline: List assigned Users to Role & List available Users to assign to Role
    Given I request "role/rol_uid/users" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:

    | rol_uid_number | records |
    | 1              | 0       |
    | 2              | 0       |
    | 3              | 0       |
    | 4              | 0       |
    | 5              | 0       |
    | 6              | 0       |
    | 7              | 0       |
    | 8              | 0       |


Scenario Outline:  List assigned Users to Role & List available Users to assign to Role, using different filters
    Given I request "role/00000000000000000000000000000003/users?filter=<filter>&start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records

    Examples:
    
    | test_description      | filter | start | limit   | records | http_code | type   |
    | lowercase             | amy    |   0   | 1       | 1       |  200      | array  |
    | uppercase             | AMY    |   0   | 1       | 1       |  200      | array  |
    | limit=3               | a      |   0   | 3       | 3       |  200      | array  |
    | limit and start       | a      |   1   | 2       | 2       |  200      | array  |
    | high number for start | a      | 1000  | 1       | 0       |  200      | array  |
    | high number for start | a      | 1000  | 0       | 0       |  200      | array  |
    | empty result          | xyz    |   0   | 0       | 0       |  200      | array  |
    | empty string          |        |   0   | 10000   | 61      |  200      | array  |
    | empty string          |        |   1   | 2       | 2       |  200      | array  |
    | invalid start         | a      |   b   | c       | 0       |  400      | string |
    | invalid limit         | a      |   0   | c       | 0       |  400      | string |
    | search 0              | 0      |   0   | 0       | 0       |  200      | array  |
    | search 0              | 0      |   0   | 100     | 0       |  200      | array  |
    | negative numbers      | a      |  -10  | -20     | 0       |  400      | string |
    | real numbers          | a      |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers          | a      |  0.0  | 0.0     | 0       |  400      | string |
    | real numbers          | a      |  0.1  | 1.4599  | 0       |  400      | string |
    | real numbers          | a      |  1.5  | 1.4599  | 0       |  400      | string |


Scenario Outline:  List assigned Users to Role & List available Users to assign to Role, using different filters
    Given I request "role/00000000000000000000000000000002/available-users?filter=<filter>&start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records

    Examples:
    
    | test_description      | filter | start | limit   | records | http_code | type   |
    | lowercase             | amy    |   0   | 1       | 1       |  200      | array  |
    | uppercase             | AMY    |   0   | 1       | 1       |  200      | array  |
    | limit=3               | a      |   0   | 3       | 3       |  200      | array  |
    | limit and start       | a      |   1   | 2       | 2       |  200      | array  |
    | high number for start | a      | 1000  | 1       | 0       |  200      | array  |
    | high number for start | a      | 1000  | 0       | 0       |  200      | array  |
    | empty result          | xyz    |   0   | 0       | 0       |  200      | array  |
    | empty string          |        |   0   | 10000   | 61      |  200      | array  |
    | empty string          |        |   1   | 2       | 2       |  200      | array  |
    | invalid start         | a      |   b   | c       | 0       |  400      | string |
    | invalid limit         | a      |   0   | c       | 0       |  400      | string |
    | search 0              | 0      |   0   | 0       | 0       |  200      | array  |
    | search 0              | 0      |   0   | 100     | 0       |  200      | array  |
    | negative numbers      | a      |  -10  | -20     | 0       |  400      | string |
    | real numbers          | a      |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers          | a      |  0.0  | 0.0     | 0       |  400      | string |
    | real numbers          | a      |  0.1  | 1.4599  | 0       |  400      | string |
    | real numbers          | a      |  1.5  | 1.4599  | 0       |  400      | string |


Scenario Outline:  Assign User to Role
    Given POST this data:
    """
        {
            "usr_uid": "<usr_uid>"
        }
    """
    And I request "role/rol_uid/user"  with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
 
    Examples:

    | Description           | rol_uid_number | usr_uid                          |
    | Assign user "aaron"   | 1              | 51049032352d56710347233042615067 |
    | Assign user "adam"    | 2              | 44811996752d567110634a1013636964 |
    | Assign user "alexis"  | 3              | 61364466452d56711adb378002702791 |
    | Assign user "amy"     | 4              | 25286582752d56713231082039265791 |
    | Assign user "brianna" | 5              | 86021298852d56716b85f73067566944 |
    | Assign user "carter"  | 6              | 32444503652d5671778fd20059078570 |
    | Assign user "emily"   | 7              | 34289569752d5673d310e82094574281 |
    | Assign user "olivia"  | 8              | 73005191052d56727901138030694610 |


Scenario Outline: List assigned Users to Role & List available Users to assign to Role
    Given I request "role/rol_uid/users" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:

    | rol_uid_number | records |
    | 1              | 1       |
    | 2              | 1       |
    | 3              | 1       |
    | 4              | 1       |
    | 5              | 1       |
    | 6              | 1       |
    | 7              | 1       |
    | 8              | 1       |


Scenario Outline: Unassign User of the Role
    Given that I want to delete a "User from a role" 
    And I request "role/rol_uid/user/<usr_uid>" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:

    | Description             | rol_uid_number | usr_uid                          |
    | Unassign user "aaron"   | 1              | 51049032352d56710347233042615067 |
    | Unassign user "adam"    | 2              | 44811996752d567110634a1013636964 |
    | Unassign user "alexis"  | 3              | 61364466452d56711adb378002702791 |
    | Unassign user "amy"     | 4              | 25286582752d56713231082039265791 |
    | Unassign user "brianna" | 5              | 86021298852d56716b85f73067566944 |
    | Unassign user "carter"  | 6              | 32444503652d5671778fd20059078570 |
    | Unassign user "emily"   | 7              | 34289569752d5673d310e82094574281 |
    | Unassign user "olivia"  | 8              | 73005191052d56727901138030694610 |


Scenario Outline: List assigned Users to Role & List available Users to assign to Role
    Given I request "role/rol_uid/users" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:

    | rol_uid_number | records |
    | 1              | 0       |
    | 2              | 0       |
    | 3              | 0       |
    | 4              | 0       |
    | 5              | 0       |
    | 6              | 0       |
    | 7              | 0       |
    | 8              | 0       |

#Culminacion de los endpoint de asignacion de usuarios

#Role and Permission

Scenario Outline: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/rol_uid/permissions" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:

    | rol_uid_number | records |
    | 1              | 0       |
    | 2              | 0       |
    | 3              | 0       |
    | 4              | 0       |
    | 5              | 0       |
    | 6              | 0       |
    | 7              | 0       |
    | 8              | 0       |


Scenario Outline: List assigned Permissions to Role & List available Permissions to assign to Role, using different filters
    Given I request "role/00000000000000000000000000000004/permissions?filter=<filter>&start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records

    Examples:
    
    | test_description      | filter | start | limit   | records | http_code | type   |
    | lowercase             | cases  |   0   | 1       | 1       |  200      | array  |
    | uppercase             | CASES  |   0   | 1       | 1       |  200      | array  |
    | limit=3               | a      |   0   | 3       | 3       |  200      | array  |
    | limit and start       | a      |   1   | 2       | 2       |  200      | array  |
    | high number for start | a      | 1000  | 1       | 0       |  200      | array  |
    | high number for start | a      | 1000  | 0       | 0       |  200      | array  |
    | empty result          | xyz    |   0   | 0       | 0       |  200      | array  |
    | empty string          |        |   0   | 10000   | 13      |  200      | array  |
    | empty string          |        |   1   | 2       | 2       |  200      | array  |
    | invalid start         | a      |   b   | c       | 0       |  400      | string |
    | invalid limit         | a      |   0   | c       | 0       |  400      | string |
    | search 0              | 0      |   0   | 0       | 0       |  200      | array  |
    | search 0              | 0      |   0   | 100     | 0       |  200      | array  |
    | negative numbers      | a      |  -10  | -20     | 0       |  400      | string |
    | real numbers          | a      |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers          | a      |  0.0  | 0.0     | 0       |  400      | string |
    | real numbers          | a      |  0.1  | 1.4599  | 0       |  400      | string |
    | real numbers          | a      |  1.5  | 1.4599  | 0       |  400      | string |


Scenario Outline: List assigned Permissions to Role & List available Permissions to assign to Role, using different filters
    Given I request "role/00000000000000000000000000000003/available-permissions?filter=<filter>&start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records

    Examples:
    
    | test_description      | filter | start | limit   | records | http_code | type   |
    | lowercase             | add    |   0   | 1       | 1       |  200      | array  |
    | uppercase             | ADD    |   0   | 1       | 1       |  200      | array  |
    | limit=3               | a      |   0   | 3       | 3       |  200      | array  |
    | limit and start       | a      |   1   | 2       | 2       |  200      | array  |
    | high number for start | a      | 1000  | 1       | 0       |  200      | array  |
    | high number for start | a      | 1000  | 0       | 0       |  200      | array  |
    | empty result          | xyz    |   0   | 0       | 0       |  200      | array  |
    | empty string          |        |   0   | 10000   | 14      |  200      | array  |
    | empty string          |        |   1   | 2       | 2       |  200      | array  |
    | invalid start         | a      |   b   | c       | 0       |  400      | string |
    | invalid limit         | a      |   0   | c       | 0       |  400      | string |
    | search 0              | 0      |   0   | 0       | 0       |  200      | array  |
    | search 0              | 0      |   0   | 100     | 0       |  200      | array  |
    | negative numbers      | a      |  -10  | -20     | 0       |  400      | string |
    | real numbers          | a      |  0.0  | 1.0     | 0       |  400      | string |
    | real numbers          | a      |  0.0  | 0.0     | 0       |  400      | string |
    | real numbers          | a      |  0.1  | 1.4599  | 0       |  400      | string |
    | real numbers          | a      |  1.5  | 1.4599  | 0       |  400      | string |


Scenario Outline: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/rol_uid/available-permissions" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    
    Examples:

    | rol_uid_number | records |
    | 1              | 17      |
    | 2              | 17      |
    | 3              | 17      |
    | 4              | 17      |
    | 5              | 17      |
    | 6              | 17      |
    | 7              | 17      |
    | 8              | 17      |


Scenario Outline: Assign Permission "PM_DASHBOARD" to Role
    Given POST this data:
    """
        {
            "per_uid": "<per_uid>"
        }
    """
    And I request "role/rol_uid/permission"  with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"

    Examples:

    | Description                                         | rol_uid_number | per_uid                          |
    | Assign Permissions "PM_LOGIN" to rol 1              | 1              | 00000000000000000000000000000001 |
    | Assign Permissions "PM_SETUP" to rol 1              | 1              | 00000000000000000000000000000002 |
    | Assign Permissions "PM_USERS" to rol 1              | 1              | 00000000000000000000000000000003 |
    | Assign Permissions "PM_FACTORY" to rol 1            | 1              | 00000000000000000000000000000004 |
    | Assign Permissions "PM_CASES" to rol 1              | 1              | 00000000000000000000000000000005 |
    | Assign Permissions "PM_LOGIN" to rol 2              | 2              | 00000000000000000000000000000001 |
    | Assign Permissions "PM_ALLCASES" to rol 2           | 2              | 00000000000000000000000000000006 |
    | Assign Permissions "PM_FOLDERS_VIEW" to rol 2       | 2              | 00000000000000000000000000000015 |
    | Assign Permissions "PM_REASSIGNCASE" to rol 2       | 2              | 00000000000000000000000000000007 |
    | Assign Permissions "PM_SUPERVISOR" to rol 2         | 2              | 00000000000000000000000000000009 |
    | Assign Permissions "PM_SETUP_ADVANCE" to rol 3      | 3              | 00000000000000000000000000000010 |
    | Assign Permissions "PM_DASHBOARD" to rol 4          | 4              | 00000000000000000000000000000011 |
    | Assign Permissions "PM_WEBDAV" to rol 5             | 5              | 00000000000000000000000000000012 |
    | Assign Permissions "PM_LOGIN" to rol 6              | 6              | 00000000000000000000000000000001 |
    | Assign Permissions "PM_EDITPERSONALINFO" to rol 7   | 7              | 00000000000000000000000000000014 |
    | Assign Permissions "PM_FOLDERS_VIEW" to rol 8       | 8              | 00000000000000000000000000000015 |
    | Assign Permissions "PM_FOLDERS_ADD_FOLDER" to rol 8 | 8              | 00000000000000000000000000000016 |
    | Assign Permissions "PM_FOLDERS_ADD_FILE" to rol 8   | 8              | 00000000000000000000000000000017 |
    | Assign Permissions "PM_CANCELCASE" to rol 8         | 8              | 00000000000000000000000000000018 |
    | Assign Permissions "PM_FOLDER_DEL" to rol 8         | 8              | 00000000000000000000000000000019 |


Scenario Outline: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/rol_uid/permissions" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:

    | rol_uid_number | records |
    | 1              | 5       |
    | 2              | 5       |
    | 3              | 1       |
    | 4              | 1       |
    | 5              | 1       |
    | 6              | 1       |
    | 7              | 1       |
    | 8              | 5       |


Scenario Outline: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/rol_uid/available-permissions" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    
    Examples:

    | rol_uid_number | records |
    | 1              | 12      |
    | 2              | 12      |
    | 3              | 16      |
    | 4              | 16      |
    | 5              | 16      |
    | 6              | 16      |
    | 7              | 16      |
    | 8              | 12      |


Scenario Outline: Unassign Permission of the Role
    Given that I want to delete a "Permmission from a role"
    And I request "role/rol_uid/permission/<per_uid>" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:

    | Description                                           | rol_uid_number | per_uid                          |
    | Unassign Permissions "PM_LOGIN" to rol 1              | 1              | 00000000000000000000000000000001 |
    | Unassign Permissions "PM_SETUP" to rol 1              | 1              | 00000000000000000000000000000002 |
    | Unassign Permissions "PM_USERS" to rol 1              | 1              | 00000000000000000000000000000003 |
    | Unassign Permissions "PM_FACTORY" to rol 1            | 1              | 00000000000000000000000000000004 |
    | Unassign Permissions "PM_CASES" to rol 1              | 1              | 00000000000000000000000000000005 |
    | Unassign Permissions "PM_LOGIN" to rol 2              | 2              | 00000000000000000000000000000001 |
    | Unassign Permissions "PM_ALLCASES" to rol 2           | 2              | 00000000000000000000000000000006 |
    | Unassign Permissions "PM_FOLDERS_VIEW" to rol 2       | 2              | 00000000000000000000000000000015 |
    | Unassign Permissions "PM_REASSIGNCASE" to rol 2       | 2              | 00000000000000000000000000000007 |
    | Unassign Permissions "PM_SUPERVISOR" to rol 2         | 2              | 00000000000000000000000000000009 |
    | Unassign Permissions "PM_SETUP_ADVANCE" to rol 3      | 3              | 00000000000000000000000000000010 |
    | Unassign Permissions "PM_DASHBOARD" to rol 4          | 4              | 00000000000000000000000000000011 |
    | Unassign Permissions "PM_WEBDAV" to rol 5             | 5              | 00000000000000000000000000000012 |
    | Unassign Permissions "PM_LOGIN" to rol 6              | 6              | 00000000000000000000000000000001 |
    | Unassign Permissions "PM_EDITPERSONALINFO" to rol 7   | 7              | 00000000000000000000000000000014 |
    | Unassign Permissions "PM_FOLDERS_VIEW" to rol 8       | 8              | 00000000000000000000000000000015 |
    | Unassign Permissions "PM_FOLDERS_ADD_FOLDER" to rol 8 | 8              | 00000000000000000000000000000016 |
    | Unassign Permissions "PM_FOLDERS_ADD_FILE" to rol 8   | 8              | 00000000000000000000000000000017 |
    | Unassign Permissions "PM_CANCELCASE" to rol 8         | 8              | 00000000000000000000000000000018 |
    | Unassign Permissions "PM_FOLDER_DEL" to rol 8         | 8              | 00000000000000000000000000000019 |


Scenario Outline: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/rol_uid/permissions" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:

    | rol_uid_number | records |
    | 1              | 0       |
    | 2              | 0       |
    | 3              | 0       |
    | 4              | 0       |
    | 5              | 0       |
    | 6              | 0       |
    | 7              | 0       |
    | 8              | 0       |


Scenario Outline: List assigned Permissions to Role & List available Permissions to assign to Role
    Given I request "role/rol_uid/available-permissions" with the key "rol_uid" stored in session array as variable "rol_uid_<rol_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    
    Examples:

    | rol_uid_number | records |
    | 1              | 17      |
    | 2              | 17      |
    | 3              | 17      |
    | 4              | 17      |
    | 5              | 17      |
    | 6              | 17      |
    | 7              | 17      |
    | 8              | 17      |
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

    | test_description                           | rol_uid_number | rol_code           | rol_name        | rol_status  |
    | Update name of role created in this script | 1              | PROCESSMAKER_UNO   | update_sample   | INACTIVE    |
    | Update name of role created in this script | 5              | PROCESSMAKER_CINCO | update2         | ACTIVE      |
    | Update name of role created in this script | 8              | PROCESSMAKER_OCHO  | update*'123     | INACTIVE    |


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

    | test_description                           | rol_uid_number | rol_code           | rol_name        | rol_status  |
    | Update name of role created in this script | 1              | PROCESSMAKER_UNO   | update_sample   | INACTIVE    |
    | Update name of role created in this script | 5              | PROCESSMAKER_CINCO | update2         | ACTIVE      |
    | Update name of role created in this script | 8              | PROCESSMAKER_OCHO  | update*'123     | INACTIVE    |


Scenario: Get list of Roles
    Given I request "roles"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 11 records


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
    | 2              |
    | 3              |
    | 4              |
    | 5              |
    | 6              |
    | 7              |
    | 8              |
    

Scenario: Get list of Roles
    Given I request "roles"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 3 records


Scenario Outline:  Assign User to Role "PROCESSMAKER_OPERATOR"
    Given POST this data:
    """
        {
            "usr_uid": "<usr_uid>"
        }
    """
    And I request "role/00000000000000000000000000000003/user"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
 
    Examples:

    | Description           | usr_uid                          |
    | Assign user "aaron"   | 51049032352d56710347233042615067 |
    | Assign user "adam"    | 44811996752d567110634a1013636964 |
    | Assign user "alexis"  | 61364466452d56711adb378002702791 |
    | Assign user "amy"     | 25286582752d56713231082039265791 |
    | Assign user "brianna" | 86021298852d56716b85f73067566944 |
    | Assign user "carter"  | 32444503652d5671778fd20059078570 |
    | Assign user "emily"   | 34289569752d5673d310e82094574281 |
    | Assign user "olivia"  | 73005191052d56727901138030694610 |