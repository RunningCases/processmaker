@ProcessMakerMichelangelo @RestAPI @assignee
Feature: Project Properties -Adhoc Assignee Resources

  Background:
    Given that I have a valid access_token

 Scenario Outline: Get a list of available adhoc users and groups to be assigned to an activity
    Given I request "project/<project>/activity/<activity>/adhoc-available-assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "ada_uid" property in row 0 equals "<ada_uid>"
    And the "ada_type" property in row 0 equals "<ada_type>"

    Examples:
    | test_description                                                         | project                          | activity                         | records | ada_uid                          | ada_type |
    | check if the list of possible users and groups to be assigned is correct | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 84      | 54731929352d56741de9d42002704749 | group    |

 Scenario Outline: Get a list of available adhoc users and groups to be assigned to an activity with filter
    Given I request "project/<project>/activity/<activity>/adhoc-available-assignee?filter=<filter>&start=<start>&limit=<limit>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "ada_uid" property in row 0 equals "<ada_uid>"
    And the "ada_type" property in row 0 equals "<ada_type>"

    Examples:
    | test_description                                          | project                          | activity                          | filter  | start | limit | records | ada_uid                          | ada_type |
    | Using filter get available users that match with "fin"   | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485  | fin | 0     | 50    | 2       | 66623507552d56742865613066097298 | group    |
    | Using filter get 1 available user that match with "fin"  | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485  | fin | 0     | 1     | 1       | 66623507552d56742865613066097298 | group    |

  Scenario Outline: Assign a adhoc user or group to an activity
    Given POST this data:
    """
    {
	"ada_uid": "<ada_uid>",
	"ada_type": "<ada_type>"
    }
    """
    And I request "project/<project>/activity/<activity>/adhoc-assignee"
    Then the response status code should be 201
    And the type is "object"

   Examples:
    | test_description                  | project                          | activity                         | ada_uid                          | ada_type |
    | assign a user  to the first task  | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 73005191052d56727901138030694610 | user     |
    | assign a user  to the first task  | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 25286582752d56713231082039265791 | user     |
    | assign a group to the first task  | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 54731929352d56741de9d42002704749 | group    |
    | assign a group to the second task | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 36775342552d5674146d9c2078497230 | group    |



  Scenario Outline: List adhoc assignees of an activity
    Given I request "project/<project>/activity/<activity>/adhoc-assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "ada_uid" property in row 0 equals "<ada_uid>"
    And the "ada_type" property in row 0 equals "<ada_type>"

    Examples:
    | test_description                                           | project                          | activity                         | records | ada_uid                           | ada_type |
    | Verify that the activity has expected quantity of asignees | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 4       | 54731929352d56741de9d42002704749  | group    |
    | Verify that the activity has expected quantity of asignees | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 1       | 36775342552d5674146d9c2078497230  | group    |


  Scenario Outline: After assignation - List adhoc assignees of an activity with filter
    Given I request "project/<project>/activity/<activity>/adhoc-assignee?filter=<filter>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 record
    And the "ada_uid" property in row 0 equals "<ada_uid>"
    And the "ada_type" property in row 0 equals "<ada_type>"

    Examples:
    | test_description                     | project                          | activity                         | records | ada_uid                          | ada_type | filter |
    | Filtered list should return 1 record | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 1       | 73005191052d56727901138030694610 | user     | oli    |
    | Filtered list should return 1 record | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 1       | 36775342552d5674146d9c2078497230 | group    | emp    |

  Scenario Outline: Get a single adhoc user or group of an activity
    Given I request "project/<project>/activity/<activity>/adhoc-assignee/<ada_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the "ada_uid" property equals "<ada_uid>"
    And the "ada_name" property equals "<ada_name>"
    And the "ada_lastname" property equals "<ada_lastname>"
    And the "ada_username" property equals "<ada_username>"
    And the "ada_type" property equals "user"

    Examples:
    | test_description                               | project                          | activity                         | ada_uid                          | ada_type | ada_name | ada_lastname | ada_username |
    | Obtain details of user assigend to an activity | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 73005191052d56727901138030694610 | user     | Olivia   | Austin       | olivia       |
  
  Scenario Outline: Remove adhoc assignee from an activity
    Given that I want to delete a resource with the key "ada_uid" stored in session array
    And I request "project/<project>/activity/<activity>/adhoc-assignee/<ada_uid>"
    Then the response status code should be 200

    Examples:
    | test_description                 | project                          | activity                         | ada_uid                          |
    | Remove a user from activity      | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 73005191052d56727901138030694610 |
    | Remove a user from activity      | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 25286582752d56713231082039265791 | 
    | Remove a user from activity      | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 54731929352d56741de9d42002704749 | 
    | Remove a user from activity      | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 36775342552d5674146d9c2078497230 | 

  Scenario: List assignees of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 records
    
  

  