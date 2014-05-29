@ProcessMakerMichelangelo @RestAPI
Feature: Roles Negative Tests

Background:
    Given that I have a valid access_token

Scenario Outline:  Create new Role (NEGATIVE TESTS)
    Given POST this data:
    """
        {
            "rol_code": "<rol_code>",
            "rol_name": "<rol_name>",
            "rol_status": "<rol_status>"
        }

    """
    And I request "role"
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"

    Examples:

    | test_description                                      | rol_code                    | rol_name                                      | rol_status  | error_code | error_message   |
    | Create Role with same name                            | PROCESSMAKER_OPERATOR       | Operator                                      | ACTIVE      | 400        | already_exists  |
    | Create Role without fill required fields "rol_code"   |                             | sample                                        |             | 400        | required_fields |
    | Create Role without fill required fields "rol_name"   | PROCESSMAKER_ADMINISTRATOR1 |                                               | ACTIVE      | 400        | required_fields |
    | Create Role without fill required fields "rol_status" | PROCESSMAKER_MANAGER1       | Rol con code manager                          |             | 400        | required_fields |
    | Create Role with wrong field rol_status               | PROCESSMAKER_ADMINISTRATOR2 | Rol con code administrator/inactive           | SAMPLE      | 400        | rol_status      |
    

#Scenario Outline:  Assign User to Role (NEGATIVE TESTS)
#    Given POST this data:
#    """
#        {
#            "usr_uid": "<usr_uid>"
#        }
#    """
#    And I request "role/rol_uid/user"
#    Then the response status code should be <error_code>
#    And the response status message should have the following text "<error_message>"
# 
#    Examples:
#
#    | Description             | rol_uid                          | usr_uid                          | error_code | error_message |
#    | Without rol_uid         | 00000000000000000000000000000004 | 51049032352d56710347233042615067 | 400        | rol_uid       |
#    | Without usr_uid         | 00000000000000000000000000000004 |                                  | 400        | usr_uid       |
#    | Assign same user to rol | 00000000000000000000000000000002 | 00000000000000000000000000000001 | 400        | usr_uid       |
   

Scenario Outline: Assign Permission "PM_DASHBOARD" to Role (NEGATIVE TESTS)
    Given POST this data:
    """
        {
            "per_uid": "<per_uid>"
        }
    """
    And I request "role/rol_uid/permission"
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"

    Examples:

    | Description                                           | rol_uid                          | per_uid                          | error_code | error_message |
    | Assign same permissions in rol "PROCESSMAKER_MANAGER" | 00000000000000000000000000000004 | 00000000000000000000000000000001 | 400        | per_uid       | 
    | Create rol without rol_uid                            |                                  | 00000000000000000000000000000002 | 400        | rol_uid       |
    | Create rol without per_uid                            | 00000000000000000000000000000004 |                                  | 400        | per_uid       |