@ProcessMakerMichelangelo @RestAPI @adhoc-assignee @negative
Feature: Project Properties -Adhoc Assignee Resources - Negative tests

  Background:
    Given that I have a valid access_token
    
Scenario Outline: List assignees of an activity with bad parameters
    Given I request "project/<project>/activity/<activity>/adhoc-assignee"
    Then the response status code should be 400


  Examples:
    | test_description                             | project                          | activity                         |
    | Use an invalid project ID and empty activity | 4224292655297723eb98691001100052 | 1234556                          |
    | Use an invalid project ID                    | 122134324                        | 65496814252977243d57684076211485 |
    | Use an invalid activity ID                   | 345345345                        | 345345345                        |


  Scenario Outline: Assign a user or group to an activity (Field validation)
    Given POST this data:
    """
    {
	"ass_uid": "<aas_uid>",
	"ass_type": "<aas_type>"
    }
    """
    And I request "project/<project>/activity/<activity>/adhoc-assignee"
    Then the response status code should be 400
    And the type is "object"
    
     
    Examples:
    | test_description                          | project                          | activity                         | aas_uid                          | aas_type      |
    | Asignando un user inexistente             | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 |                                  |               | 
    | Asignando un usuario Con tipo inexistente | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | !@#$%^&*()_+=-[]{};:~,           |  user         |
    | Asignando un usuario como grupo           | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 15746307552d00a66624889076110382 |  group        | 
    | Asignando un usuario con type inexistente | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 69191356252cda41acde328048794164 |  department   |

 
 Scenario Outline: List assignees of an activity using different filters
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-available-assignee?filter=<filter>&start=<start>&limit=<limit>"
    Then the response status code should be <http_code>
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "<type>"
    And the response has <records> records

    Examples:
    | test_description      | filter | start | limit   | records | http_code | type  |
    | lowercase             | admin  |   0   | 1       | 1       |  200      | array |
    | uppercase             | ADMIN  |   0   | 1       | 1       |  200      | array |
    | limit=3               | a      |   0   | 3       | 3       |  200      | array |
    | limit and start       | a      |   1   | 2       | 2       |  200      | array |
    | high number for start | a      | 1000  | 1       | 0       |  200      | array |
    | high number for start | a      | 1000  | 0       | 0       |  200      | array |
    | empty result          | xyz    |   0   | 0       | 0       |  200      | array |
    | empty string          |        |   0   | 10000   | 82      |  200      | array |
    | empty string          |        |   1   | 2       | 2       |  200      | array |
    | invalid start         | a      |   b   | c       | 0       |  400      | string|
    | invalid limit         | a      |   0   | c       | 0       |  400      | string|
    | search 0              | 0      |   0   | 0       | 0       |  200      | array |
    | search 0              | 0      |   0   | 100     | 0       |  200      | array |
    | negative numbers      | a      |  -10  | -20     | 0       |  400      | string|
    | real numbers          | a      |  0.0  | 1.0     | 1       |  200      | string|
    | real numbers          | a      |  0.0  | 0.0     | 0       |  200      | string|
    | real numbers          | a      |  0.1  | 1.4599  | 0       |  400      | string|
    | real numbers          | a      |  1.5  | 1.4599  | 0       |  400      | string|
