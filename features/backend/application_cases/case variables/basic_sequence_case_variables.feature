@ProcessMakerMichelangelo @RestAPI
Feature: Cases Variables
Requirements:
    a workspace with five cases of the process "Test micheangelo" and "Test Users-Step-Properties End Point"

Background:
    Given that I have a valid access_token


Scenario: Returns the variables can be system variables and/or case variables.
    Given I request "case/{uid}/get-variables"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"


Scenario: Sends variables to a case
        Given PUT this data:
            """
            {
                


            }
            """
        And that I want to update a resource with the key "" stored in session array
        And I request "case/{uid}/send-variables"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"