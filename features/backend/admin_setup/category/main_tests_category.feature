@ProcessMakerMichelangelo @RestAPI
Feature: Process Category Main Test
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
    And the response has 0 records


  # POST /api/1.0/{workspace}/project/category
  #      Create a new Category
  Scenario Outline: Create new Categories
    Given POST this data:
    """
    {
        "cat_name": "<cat_name>"    
    }
    """
    And I request "project/category"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "cat_uid" in session array as variable "cat_uid_<cat_uid_number>"

  Examples:
    | test_description                                      | cat_uid_number | cat_name                                               |
    | Create new Category with character special            | 1              | sample!@#$%^^&                                         |
    | Create new Category with only character numeric       | 2              | 32425325                                               |
    | Create new Category with only character special       | 3              | @$@$#@%                                                |
    | Create new Category with normal character             | 4              | sample                                                 |
    | Create new Category with short name                   | 5              | s                                                      |
    | Create new Category with long name                    | 6              | Prueba de Creacion de nuevo categoria con nombre largo |
    | Create new Category with long name                    | 7              | Right Category                                         |


  # GET /api/1.0/{workspace}/project/categories
  #     Get Category list
  Scenario: Get list of Categories
    Given I request "project/categories"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 7 records


  # POST /api/1.0/{workspace}/project/category
  #      Create a new Category
  Scenario: Create Category with same name twice
    Given POST this data:
    """
      {
        "cat_name": "sample"    
      }
      """
    And I request "project/category"
    Then the response status code should be 400
    And the response status message should have the following text "exist"


  # PUT /api/1.0/{workspace}/project/categories
  #     Update an specific Category
  Scenario Outline: Update the Categories created in this script
    Given PUT this data:
    """
      {
        "cat_name": "<cat_name>"
      }
      """
    And I request "project/category/cat_uid"  with the key "cat_uid" stored in session array as variable "cat_uid_<cat_uid_number>"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

  Examples:
    | test_description  | cat_uid_number | cat_name              |
    | Update Category   | 1              | UPDATE sample!@#$%^^& |
    | Update Category   | 2              | UPDATE 32425325       |


  # PUT /api/1.0/{workspace}/project/categories
  #     Update an specific Category
  Scenario Outline: Update the Category putting the same name
    Given PUT this data:
    """
    {
        "cat_name": "<cat_name>"
    }
    """
    And I request "project/category/cat_uid"  with the key "cat_uid" stored in session array as variable "cat_uid_<cat_uid_number>"
    Then the response status code should be 400
    And the response status message should have the following text "exist"

  Examples:
    | test_description | cat_uid_number | cat_name |
    | Update Category  | 5              | sample   |


  # GET /api/1.0/{workspace}/project/categories
  #     Get an specific Category
  Scenario Outline: Get an specific Category
    Given I request "project/category/cat_uid"  with the key "cat_uid" stored in session array as variable "cat_uid_<cat_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the "cat_name" property equals "<cat_name>"
    And the "cat_total_processes" property equals "<cat_total_processes>"

  Examples:

    | cat_uid_number | cat_name              | cat_total_processes |
    | 1              | UPDATE sample!@#$%^^& | 0                   |
    | 2              | UPDATE 32425325       | 0                   |


  # DELETE /api/1.0/{workspace}/project/category
  #        Delete an specific Category
  Scenario Outline: Delete the Category created previously in this script
    Given that I want to delete a resource with the key "cat_uid" stored in session array as variable "cat_uid_<cat_uid_number>"
    And I request "project/category"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

  Examples:
    | cat_uid_number |
    | 1              |
    | 2              |
    | 3              |
    | 4              |
    | 5              |
    | 6              |
    | 7              |


  # GET /api/1.0/{workspace}/project/categories
  #     Get Category list
  Scenario: Get list of Categories
    Given I request "project/categories"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 0 records