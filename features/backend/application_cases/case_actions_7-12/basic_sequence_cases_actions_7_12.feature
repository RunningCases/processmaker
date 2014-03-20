@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (getCaseInfo, taskCase, newCase, newCaseImpersonate, reassignCase and routeCase)
Requirements:
    a workspace with five cases of the process "Test micheangelo" and "Test Users-Step-Properties End Point"

Background:
    Given that I have a valid access_token


Scenario: Returns information about a given case
    Given I request "case"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the "ca_prj" property equals "ca_prj"
    And the "ca_title" property equals "ca_title"
    And the "ca_number" property equals "ca_number"
    And the "ca_status" property equals "ca_status"
    And the "ca_uid" property equals "ca_uid"
    And the "ca_creator" property equals "ca_creator"
    And the "ca_date" property equals "ca_date"
    And the "ca_update" property equals "ca_update"
    And the "ca_lastupdate" property equals "ca_lastupdate"
    And the "ca_description" property equals "ca_description"
    And the "ca_task" property equals "ca_task"
    And the "ca_current_user" property equals "ca_current_user"
    And the "ca_delegate" property equals "ca_delegate"
    And the "ca_init_date" property equals "ca_init_date"
    And the "ca_due_date" property equals "ca_duo_date"
    And the "ca_finish_date" property equals "ca_finish_date"


Scenario: Returns the current task for a given case
    Given I request "case/uid/current-task"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the "ca_prj" property equals "ca_prj"


Scenario: Create a new case in workspace with process Test Micheangelo
        Given POST this data:
            """
            {

                

            }
            """
        And I request "case"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "" in session array


Scenario: Create a new case Impersonate in workspace with process Test Micheangelo
        Given POST this data:
            """
            {

                

            }
            """
        And I request "case/impersonate"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "" in session array


Scenario: Reassigns a case to a different user
        Given PUT this data:
            """
            {
                


            }
            """
        And that I want to update a resource with the key "" stored in session array
        And I request "case/{uid}/reassign-case"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"


Scenario: Autoderivate a case to the next task in the process
        Given PUT this data:
            """
            {
                


            }
            """
        And that I want to update a resource with the key "" stored in session array
        And I request "case/{uid}/route-case"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"