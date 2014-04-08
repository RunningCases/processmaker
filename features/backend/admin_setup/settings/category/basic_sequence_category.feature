@ProcessMakerMichelangelo @RestAPI
Feature: Process Category
  Requirements:
    a workspace with the workspace with one process category


Background:
    Given that I have a valid access_token


Scenario: Get list of Categories
    Given I request "categories"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 records


Scenario: Get a Category specific
    Given I request "category/4177095085330818c324501061677193"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
  

Scenario: Create a new Categories
    Given POST this data:
    """
    {
        "cat_name": "Test new Category"    
    }
    """
    And I request "category"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "cat_uid" in session array as variable "cat_uid"


Scenario: Get list of Categories
    Given I request "categories"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 2 records


Scenario: Update the Category created in this script
    Given PUT this data:
      """
      {
        "cat_name": "Name Updated"
      }
      """
      And that I want to update a resource with the key "cat_uid" stored in session array
      And I request "category"
      And the content type is "application/json"
      Then the response status code should be 200
      And the response charset is "UTF-8"


Scenario: Delete the Category created previously in this script
    Given that I want to delete a resource with the key "cat_uid" stored in session array
        And I request "category"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"