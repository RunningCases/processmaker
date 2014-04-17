@ProcessMakerMichelangelo @RestAPI
Feature: Assignee Resources
  Requirements:
    a workspace with the process 4224292655297723eb98691001100052 ("Test Users-Step-Properties End Point") already loaded

  Background:
    Given that I have a valid access_token

 Scenario Outline: Get the list of available users and groups to be assigned to an activity
    Check that there are exactly 79 available users for task "Task 1"
    Given I request "project/<project>/activity/<activity>/available-assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "aas_uid" property in row 0 equals "<aas_uid>"
    And the "aas_type" property in row 0 equals "<aas_type>"

    Examples:
    | test_description                                                         | project                          | activity                         | records | aas_uid                          | aas_type |
    | check if the list of possible users and groups to be assigned is correct | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 79      | 54731929352d56741de9d42002704749 | group    |

Scenario Outline: Get the list of available users and groups to be assigned to an activity using filter
    Given I request "project/<project>/activity/<activity>/available-assignee?filter=<filter>&start=<start>&limit=<limit>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "aas_uid" property in row 0 equals "<aas_uid>"
    And the "aas_type" property in row 0 equals "<aas_type>"

    Examples:
    | test_description                                               | project                          | activity                         | filter    | start | limit | records | aas_uid                          | aas_type|
    | Using filter="fin" with no limits should return 2 groups       | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | fin       | 0     | 50    | 2       | 66623507552d56742865613066097298 | group   |
    | Using filter="fin", start="1", limit="1" should return 1 group | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | fin       | 0     | 1     | 1       | 66623507552d56742865613066097298 | group   |
    | Using filter="financial" should return 1 available group       | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | financial | 0     | 1     | 1       | 99025456252d567468f0798036479112 | group   |
    | Using filter="finance"   should return 1 available group       | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | finance   | 0     | 1     | 1       | 66623507552d56742865613066097298 | group   |


  Scenario Outline: Assign 2 users and 2 group to an activity
    Given POST this data:
      """
      {
	  "aas_uid": "<aas_uid>",
	  "aas_type": "<aas_type>"
      }
      """
    And I request "project/<project>/activity/<activity>/assignee"
    Then the response status code should be 201
    And the type is "object"

    Examples:
    | test_description                  | project                          | activity                         | aas_uid                          | aas_type |
    | assign a user  to the first task  | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 73005191052d56727901138030694610 | user     |
    | assign a user  to the first task  | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 25286582752d56713231082039265791 | user     |
    | assign a group to the first task  | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 54731929352d56741de9d42002704749 | group    |
    | assign a group to the second task | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 36775342552d5674146d9c2078497230 | group    |


  Scenario Outline: After assignation - List assignees of each activity
    Given I request "project/<project>/activity/<activity>/assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "aas_uid" property in row 0 equals "<aas_uid>"
    And the "aas_type" property in row 0 equals "<aas_type>"

    Examples:
    | test_description                                           | project                          | activity                         | records | aas_uid                          | aas_type |
    | Verify that the activity has expected quantity of asignees | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 7       | 54731929352d56741de9d42002704749 | group    |
    | Verify that the activity has expected quantity of asignees | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 5       | 36775342552d5674146d9c2078497230 | group    |


Scenario Outline: List assignees of an activity using a filter
    Given I request "project/<project>/activity/<activity>/assignee?filter=<filter>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> record
    And the "aas_uid" property in row 0 equals "<aas_uid>"
    And the "aas_type" property in row 0 equals "<aas_type>"

    Examples:
    | test_description                     | project                          | activity                         | records | aas_uid                          | aas_type | filter |
    | Filtered list should return 1 record | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 1       | 73005191052d56727901138030694610 | user     | oli    |
    | Filtered list should return 1 record | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 1       | 36775342552d5674146d9c2078497230 | group    | emp    |


Scenario Outline: List all assignees of an activity includ user of the groups
    Given I request "project/4224292655297723eb98691001100052/activity/<activity>/assignee/all"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And that "aas_uid" is set to "<aas_uid>"
    And that "aas_name" is set to "<aas_name>"
    And that "aas_lastname" is set to "<aas_lastname>"
    And that "aas_username" is set to "<aas_username>"
    And that "aas_type" is set to "<aas_type>"

    Examples:

    | activity                         | aas_uid                          | aas_name      | aas_lastname | aas_username | aas_type  |
    | 65496814252977243d57684076211485 | 00000000000000000000000000000001 | Administrator |              | admin        | user      |
    | 65496814252977243d57684076211485 | 24166330352d56730cdd525035621101 | Chris         | Rogers       | chris        | user      |
    | 65496814252977243d57684076211485 | 62625000752d5672d6661e6072881167 | Zachary       | Wegner       | zachary      | user      |
    | 65496814252977243d57684076211485 | 33140476452d5671b0abda5073786635 | Gavin         | Brule        | gavin        | user      |
    | 65496814252977243d57684076211485 | 86021298852d56716b85f73067566944 | Brianna       | Ciarleglio   | brianna      | user      |
    | 65496814252977243d57684076211485 | 25286582752d56713231082039265791 | Amy           | Connelly     | amy          | user      |
    | 65496814252977243d57684076211485 | 23085901752d5671483a4c2059274810 | Arlene        | Cleveland    | arlene       | user      |
    | 65496814252977243d57684076211485 | 32444503652d5671778fd20059078570 | Carter        | Ching        | carter       | user      |
    | 65496814252977243d57684076211485 | 33140476452d5671b0abda5073786635 | Gavin         | Brule        | gavin        | user      |
    | 65496814252977243d57684076211485 | 50562411252d5671e788c02016273245 | Jason         | Boria        | jason        | user      |
    | 65496814252977243d57684076211485 | 70952985852d567244ff0c1060856085 | Kevin         | Beam         | kevin        | user      |
    | 65496814252977243d57684076211485 | 66955548552d567318736d1041697217 | Erick         | Blake        | erick        | user      |
    | 65496814252977243d57684076211485 | 82857688652d5673a585726072326208 | Sandra        | Casey        | sandra       | user      |
    

Scenario Outline: Get a single user or group of an activity
    Given I request "project/<project>/activity/<activity>/assignee/<aas_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the "aas_uid" property equals "<aas_uid>"
    And the "aas_name" property equals "<aas_name>"
    And the "aas_lastname" property equals "<aas_lastname>"
    And the "aas_username" property equals "<aas_username>"
    And the "aas_type" property equals "<aas_type>"

    Examples:
    | test_description                               | project                          | activity                         | aas_uid                          | aas_type | aas_name | aas_lastname | aas_username |
    | Obtain details of user assigned to an activity | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 73005191052d56727901138030694610 | user     | Olivia   | Austin       | olivia       |


  Scenario Outline: Remove assignee from an activity
    Given that I want to delete a resource with the key "aas_uid" stored in session array
    And I request "project/<project>/activity/<activity>/assignee/<aas_uid>"
    Then the response status code should be 200

    Examples:
    | test_description                 | project                          | activity                         | aas_uid                          |
    | Remove a user from activity      | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 73005191052d56727901138030694610 |
    | Remove a user from activity      | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 25286582752d56713231082039265791 |
    | Remove a user from activity      | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 54731929352d56741de9d42002704749 |
    | Remove a user from activity      | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 36775342552d5674146d9c2078497230 |


  Scenario: List assignees of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 4 records

