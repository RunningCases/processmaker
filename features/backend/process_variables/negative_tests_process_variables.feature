@ProcessMakerMichelangelo @RestAPI
Feature: Process Variables Negative Tests


  Background:
    Given that I have a valid access_token


    Scenario: Get all variables of a Process bad parameters (negative tests)
        And I request "project/3306142435318cd22d1eba2015305561/variables"
        Then the response status code should be 400


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
        

    Scenario: Get all variables of a Grid bad parameters (negative tests)
        Given I request "project/3306142435318cd22d1eba2015305561/grid/8246998615318cd7cc451d2089449499/variables"
        And that "var_name" is set to "desarrollo"
        Then the response status code should be 400
        And the response status message should have the following text "var_name"
        
        