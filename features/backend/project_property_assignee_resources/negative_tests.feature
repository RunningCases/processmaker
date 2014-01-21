@ProcessMakerMichelangelo @RestAPI @assignee @negative
Feature: Project Properties -Assignee Resources - Negative tests

  Background:
    Given that I have a valid access_token
    

Scenario Outline: List assignees of an activity with bad parameters
    Given I request "project/<project>/activity/<activity>/assignee"
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
    And I request "project/<project>/activity/<activity>/assignee"
    Then the response status code should be 400
    And the type is "object"
     
      
    Examples:

    | test_description                          | project                          | activity                         | aas_uid                          | aas_type      |
    | Asignando un user inexistente             | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 |                                  |               | 
    | Asignando un usuario Con tipo inexistente | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | !@#$%^&*()_+=-[]{};:~,           |  user         |
    | Asignando un usuario como grupo           | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 69191356252cda41acde328048794164 |  group        | 
    | Asignando un usuario con type inexistente | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 69191356252cda41acde328048794164 |  department   |
    
