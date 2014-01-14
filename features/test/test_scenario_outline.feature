@ProcessMakerMichelangelo @RestAPI
Feature: Project Properties - Assignee Resources

  Background:
    Given that I have a valid access_token

Scenario Outline: Get a list of available users and groups to be assigned to an activity with filter
    Given that I want to make a new "test"
    And his "name" is "<name>"
    And his "lastname" is "<lastname>"
    When I request "test"
    Then the response status code should be 200
    And the response should be JSON
    #And that "id" is set to "<id>"
    And the "id" property equals "<id>"

    Examples:
    | name  | lastname | age | id |
    | erik  | sample1  |  7  | 2  |
    | wendy |  sample2 |  15 | 3  |
    | wendy2 |  sample22 |  15 | 4  |
    | wendy3 |  sample23 |  15 | 5  |

