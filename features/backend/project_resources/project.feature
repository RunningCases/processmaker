@ProcessMakerMichelangelo @RestAPI
Feature: Project Resources
 
  Background:
    Given that I have a valid access_token

  Scenario: Get a single project
    
    Given I request "project/4224292655297723eb98691001100052"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


  Scenario: Create a new project
    Given POST data from file "process_template_v1.json"
    And I request "project"
    Then the response status code should be 201
    And the content type is "application/json"
    And the type is "array"
    
  Scenario: Update a project
    Given PUT data from file "process_template_v2.json"
    And I request "project"
    Then the response status code should be 201
    And the content type is "application/json"
    And the type is "array"

  Scenario: Delete a project <project_uid>
    Given I request "project/<project_uid>"
    Then the response status code should be 200
