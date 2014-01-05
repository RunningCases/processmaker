@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents Resources

  Background:
    Given that I have a valid access_token

  Scenario: Get a List output documents of a project
  Given I request "project/4224292655297723eb98691001100052/output-documents"
  Then the response status code should be 200
  And the content type is "application/json"
  And the type is "array"

  
  Scenario: Get a single output document of a project
  Given I request "project/4224292655297723eb98691001100052/output-document/270088687529c8ace5e5272077582449"
  Then the response status code should be 200
  And the content type is "application/json"
  And the type is "object"
  
  Scenario: Create a new output document for a project
    Given  this scenario is not implemented yet
  
  Scenario: Update a output document for a project
    Given  this scenario is not implemented yet
    
  Scenario: Delete a output document of a project
    Given  this scenario is not implemented yet

