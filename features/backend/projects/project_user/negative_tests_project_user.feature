@ProcessMakerMichelangelo @RestAPI
Feature:  Project User Resources Negative Tests

  Background:
    Given that I have a valid access_token


  # GET /api/1.0/{workspace}/project/<project-uid>/user/<user-uid>/starting-tasks
  #     Get started tasks list by a user
  Scenario: Get a list of start task of a specific user
    Given I request "project/1265557095225ff5c688f46031700471/user/23348978752d567259ea6f3004731611/starting-tasks"
    Then the response status code should be 400
    And the response charset is "UTF-8"
    And the content type is "application/json"


  # POST /api/1.0/{workspace}/project/<project-uid>/ws/user/can-start-task
  #      Start a task with bad credentials
  Scenario Outline: Verify if a user can start a task with bad parameters (negative tests)
    Given POST this data:
    """
      {
      "act_uid": "<act_uid>",
      "username": "<username>",
      "password": "<password>"
      }
      """
    And I request "project/<project>/ws/user/can-start-task"
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"

  Examples:
    | test_description       | project                          | act_uid                          | username | password   | error_code | error_message        |
    | Invalid act_uid        | 1265557095225ff5c688f46031700471 | 00000000009999f5fe54de2005407079 | admin    | sample123* | 400        | act_uid              |
    | Invalid username       | 1265557095225ff5c688f46031700471 | 1546168275225ff617b6a34046164891 | ain      | admin      | 400        | User not registered! |
    | Invalid password       | 1265557095225ff5c688f46031700471 | 1546168275225ff617b6a34046164891 | erick    | sle        | 400        | Wrong password       |
    | Field requered project |                                  | 46941969352af5be2ab3f39001216717 | admin    | admin      | 400        | prj_uid              |
    | Field requered act_uid | 1265557095225ff5c688f46031700471 |                                  | admin    | admin      | 400        | act_uid              |
