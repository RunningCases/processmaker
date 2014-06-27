@ProcessMakerMichelangelo @RestAPI
Feature: Process Category Negative Tests


Background:
    Given that I have a valid access_token


Scenario Outline: Create a new Categories (Negative Test)
    Given POST this data:
    """
    {
        "cat_name": "<cat_name>"    
    }
    """
    And I request "project/category"
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"

    Examples:

    | test_description   | cat_name       | error_code | error_message |
    | without name       |                | 400        | cat_name      | 



Scenario: Delete the Category when it is assigned to a project "Category Cases Lists"
    Given that I want to delete a "Category"
    And I request "project/category/4177095085330818c324501061677193"
    Then the response status code should be 400
    And the response status message should have the following text "cannot be deleted" 