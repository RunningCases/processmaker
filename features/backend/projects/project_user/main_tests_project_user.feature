@ProcessMakerMichelangelo @RestAPI
Feature: Project User Resources Main Tests
  Requirements:
    a workspace with the process 1265557095225ff5c688f46031700471 ("Test Michelangelo") already loaded
    there are two output documents in the process


  Background:
    Given that I have a valid access_token


  Scenario: Get a list of users of a project when there are exactly 52 users
    Given I request "project/1265557095225ff5c688f46031700471/users"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 6 records


  Scenario: Get a list of starting task of a project when there are exactly 2 tasks
  Given I request "project/1265557095225ff5c688f46031700471/starting-tasks"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 2 records

  
  Scenario Outline: Get a list of start task of a specific user
  Given I request "project/1265557095225ff5c688f46031700471/user/<usr_uid>/starting-tasks"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

    Examples:

    | test_description                  | usr_uid                          |
    | Verify that this user admin tasks | 00000000000000000000000000000001 |
    | Verify that this user adam tasks  | 16333273052d567284e6766029512960 |
    | Verify that this user magda tasks | 90909671452d56718417612014706933 |


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

    | test_description                  | act_uid                          | username | password |
    | Properties of user admin - task 1 | 1352844695225ff5fe54de2005407079 | admin    | admin    |
    | Properties of user owen - task 1  | 1352844695225ff5fe54de2005407079 | owen     | sample   |
    | Properties of user admin - task 1 | 1352844695225ff5fe54de2005407079 | alyssa   | sample   | 
    | Properties of user owen - task 2  | 46941969352af5be2ab3f39001216717 | admin    | admin    |     
