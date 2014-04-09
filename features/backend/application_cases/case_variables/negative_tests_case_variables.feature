@ProcessMakerMichelangelo @RestAPI
Feature: Cases Variables Negative Tests

Background:
    Given that I have a valid access_token


Scenario: Returns the variables can be system variables and/or case variables (negative tests).
    Given I request "cases/95124734553388becc0e332080057699/variable"
    Then the response status code should be 404
    And the response status message should have the following text "Not Found"
