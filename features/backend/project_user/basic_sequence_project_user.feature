@ProcessMakerMichelangelo @RestAPI
Feature: Project User Resources

  Background:
    Given that I have a valid access_token

  Scenario: Get a list of users of a project
    Given I request "project/1265557095225ff5c688f46031700471/users"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

  Scenario: Get a list of starting task of a project
  Given I request "project/1265557095225ff5c688f46031700471/starting-tasks"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
  
  Scenario: Get a list of start task of a user
  Given I request "project/1265557095225ff5c688f46031700471/user/00000000000000000000000000000001/starting-tasks"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

  Scenario Outline: Verify if a user can start a task
      Given POST this data:
      """
      {
      "act_uid": "<act_uid>",
      "username": "<username>",
      "password": "<password>"
      }
      """
      And I request "project/1265557095225ff5c688f46031700471/ws/user/can-start-task"
      Then the response status code should be <http_code>
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "<type>"

    Examples:
    | test_description      | act_uid                          | username | password | http_code | type   |
    | error username        | 1352844695225ff5fe54de2005407079 | adm      | admin    | 400       | string |
    | error password        | 1352844695225ff5fe54de2005407079 | admin    | adm      | 400       | string |
    | short activity        | 1352844695225ff5fe54de20         | admin    | admin    | 400       | string |
    | error activity        | 225ff5fe54de20054070791352844695 | admin    | admin    | 400       | string |
    | all ok                | 1352844695225ff5fe54de2005407079 | admin    | admin    | 200       | array  |
