@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (cancelCase, pauseCase, unpaseCase, executeCase, executeCase, executeTrigger and Delete Case)
Requirements:
    a workspace with five cases of the process "Test micheangelo" and "Test Users-Step-Properties End Point"

Background:
    Given that I have a valid access_token


Scenario: Cancel Case
        Given PUT this data:
            """
            {
                


            }
            """
        And that I want to update a resource with the key "" stored in session array
        And I request "case/{uid}/cancel-case"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"


Scenario: Pause Case
        Given PUT this data:
            """
            {
                


            }
            """
        And that I want to update a resource with the key "" stored in session array
        And I request "case/{uid}/pause-case"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"


Scenario: Unpause Case
        Given PUT this data:
            """
            {
                


            }
            """
        And that I want to update a resource with the key "" stored in session array
        And I request "case/{uid}/unpause-case"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"



Scenario: Executes a ProcessMaker trigger for a case
        Given POST this data:
            """
            {

                

            }
            """
        And I request "case{uid}/execute-trigger"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "" in session array


Scenario: Delete case of workspace
        Given that I want to delete a resource with the key "" stored in session array
        And I request "case/{uid}/unpause-case"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
