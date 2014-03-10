@ProcessMakerMichelangelo @RestAPI
Feature: Process Variables Negative Tests


  Background:
    Given that I have a valid access_token

    Scenario Outline: Get all variables of a Grid bad parameters (negative tests)
        Given I request "project/14414793652a5d718b65590036026581/grid/00000000000000000000000000000000/variables"
        And the content type is "application/json"
        Then the response status code should be <status_code>
        And the response charset is "UTF-8"
        And the type is "object"
        And the response status message should have the following text "<status_message>"

        Examples:
        | status_code | status_message |
        | 400         | grid_uid       |        