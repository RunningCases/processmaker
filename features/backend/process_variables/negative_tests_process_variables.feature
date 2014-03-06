@ProcessMakerMichelangelo @RestAPI
Feature: Process Variables Negative Tests


  Background:
    Given that I have a valid access_token bad parameters (negative tests)


Scenario Outline: Get all variables of a Process
        And I request "project/3306142435318cd22d1eba2015305561/variables"
        And the "var_name" property in row 0 equals "sample"
        Then the response status code should be 400
        And the response status message should have the following text "var_name"
        
