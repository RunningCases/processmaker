@ProcessMakerMichelangelo @RestAPI
Feature: Process Category
  Requirements:
  a workspace with the workspace with one process category


  Background:
    Given that I have a valid access_token


  # GET /api/1.0/{workspace}/project/categories
  #     Get Category list
  Scenario: Get list of Categories
    Given I request "project/categories"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 records


  # POST /api/1.0/{workspace}/project/category
  #      Create a new Category
  Scenario: Create a new Category
    Given POST this data:
    """
    {
        "cat_name": "Test new Category"    
    }
    """
    And I request "project/category"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "cat_uid" in session array as variable "cat_uid"


  # GET /api/1.0/{workspace}/project/categories
  #     Get Category list
  Scenario: Get list of Categories
    Given I request "project/categories"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 2 records


  # PUT /api/1.0/{workspace}/project/categories
  #     Update an specific Category
  Scenario: Update the Category created in this script
    Given PUT this data:
    """
      {
        "cat_name": "Name Updated"
      }
      """
    And that I want to update a resource with the key "cat_uid" stored in session array
    And I request "project/category"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"


  # GET /api/1.0/{workspace}/project/category/<category-id>
  #     Get an specific Category
  Scenario: Get an specific Category
    Given that I want to get a resource with the key "cat_uid" stored in session array
    And I request "project/category"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


  # DELETE /api/1.0/{workspace}/project/category
  #        Delete an specific Category
  Scenario: Delete the Category created previously in this script
    Given that I want to delete a resource with the key "cat_uid" stored in session array
    And I request "project/category"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"


  # GET /api/1.0/{workspace}/project/categories
  #     Get Category list
  Scenario: Get list of Categories
    Given I request "project/categories"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 records