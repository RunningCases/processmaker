@ProcessMakerMichelangelo @RestAPI
Feature: Projects End Point.
Lists available projects/processes for an specific workspace

  Scenario: Get a list of projects
    Given that I have a valid access_token
    And I request "projects"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


