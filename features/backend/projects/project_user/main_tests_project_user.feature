@ProcessMakerMichelangelo @RestAPI
Feature: Project User Resources Main Tests
  Requirements:
    a workspace with the process 1265557095225ff5c688f46031700471 ("Test Michelangelo") already loaded
    there are two output documents in the process
    and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded


  Background:
    Given that I have a valid access_token


  Scenario Outline: Get a list of users of a project when there are exactly 52 users
    Given I request "project/<project>/users"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:

    | test_description                                | project                          | records |
    | Get user of the process "Test Michelangelo"     | 1265557095225ff5c688f46031700471 | 6       |
    | Get user of the process "Process Complete BPMN" | 1455892245368ebeb11c1a5001393784 | 1       |
    

  Scenario Outline: Get a list of starting task of a project when there are exactly 2 tasks
    Given I request "project/<project>/starting-tasks"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:

    | test_description                                         | project                          | records |
    | Get user starting of the process "Test Michelangelo"     | 1265557095225ff5c688f46031700471 | 2       |
    | Get user starting of the process "Process Complete BPMN" | 1455892245368ebeb11c1a5001393784 | 3       |

  
  Scenario Outline: Get a list of start task of a specific user
    Given I request "project/<project>/user/<usr_uid>/starting-tasks"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:

    | test_description                       | project                          | usr_uid                          | records |
    | Verify that this user admin tasks .pm  | 1265557095225ff5c688f46031700471 | 00000000000000000000000000000001 | 2       |
    | Verify that this user adam tasks  .pm  | 1265557095225ff5c688f46031700471 | 16333273052d567284e6766029512960 | 1       |
    | Verify that this user magda tasks .pm  | 1265557095225ff5c688f46031700471 | 90909671452d56718417612014706933 | 1       |
    | Verify that this user admin tasks .pmx | 1455892245368ebeb11c1a5001393784 | 00000000000000000000000000000001 | 3       |
    

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
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

    Examples:

    | test_description                       | act_uid                          | username | password   |
    | Properties of user admin - task 1 .pm  | 1352844695225ff5fe54de2005407079 | admin    | sample123* |
    | Properties of user owen - task 1  .pm  | 1352844695225ff5fe54de2005407079 | owen     | sample     |
    | Properties of user admin - task 1 .pm  | 1352844695225ff5fe54de2005407079 | alyssa   | sample     | 
    | Properties of user owen - task 2  .pm  | 46941969352af5be2ab3f39001216717 | admin    | sample123* |