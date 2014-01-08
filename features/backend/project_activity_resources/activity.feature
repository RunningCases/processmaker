@ProcessMakerMichelangelo @RestAPI
Feature: Project Activity Resources

  Background:
    Given that I have a valid access_token

  Scenario: Get properties & definition of a activity.
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    
  
  Scenario: Get BPMN definition of an activity.
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485?filter=definition"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
  
  Scenario: Get properties data of the ProcessMaker engine of an activity.
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485?filter=properties"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
  
  Scenario: Update definition & properties of an activity
    Given PUT data from file "activity_update_properties.json"
    And I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485"
    Then the response status code should be 200
    
  Scenario: Get properties & definition of an updated activity.
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the response is equivalent to this json file "activity_update_properties.json"
    
  Scenario: Delete a project activity
    Given that I want to delete "activity"
    And I request "project/4224292655297723eb98691001100052/activity/95897010052ccb746c770b6003171032"
    Then the response status code should be 200
    
   Scenario: Get properties & definition of a activity (subprocess)
    Given I request "project/4224292655297723eb98691001100052/activity/68065066252ccb7913b9be6048174072"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
