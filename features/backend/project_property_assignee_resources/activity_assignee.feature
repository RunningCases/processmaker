@ProcessMakerMichelangelo @RestAPI
Feature: Project Properties - Assignee Resources

  Background:
    Given that I have a valid access_token

 Scenario Outline: Get a list of available users and groups to be assigned to an activity
    Given I request "project/<project>/activity/<activity>/available-assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "aas_uid" property in row 0 equals "<aas_uid>"
    And the "aas_type" property in row 0 equals "<aas_type>"
    
    Examples:
    | project                          | activity                         | records | aas_uid                          | aas_type |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 82      | 35762872152cda4323207c6035916735 | group    |

Scenario Outline: Get a list of available users and groups to be assigned to an activity with filter
    Given I request "project/<project>/activity/<activity>/available-assignee?filter=<filter>&start=<start>&limit=<limit>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "aas_uid" property in row 0 equals "<aas_uid>"
    And the "aas_type" property in row 0 equals "<aas_type>"
    
    Examples:
    | project                          | activity                         | filter  | start | limit | records | aas_uid                          | aas_type|
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | departa | 0     | 50    | 3       | 90268877852b7b4b9f134b1096735994 | group   |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | departa | 0     | 1     | 1       | 90268877852b7b4b9f134b1096735994 | group   |

  Scenario Outline: Assign a user or group to an activity
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
    | project                          | activity                         | aas_uid                          | aas_type |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 66386662252cda3f9a63226052377198 | user     |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 69191356252cda41acde328048794164 | user     |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 35762872152cda4323207c6035916735 | group    |
    | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 90706007452cda42ed1c326093152317 | group    |




  Scenario Outline: After assignation - List assignees of an activity
    Given I request "project/<project>/activity/<activity>/assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "aas_uid" property in row 0 equals "<aas_uid>"
    And the "aas_type" property in row 0 equals "<aas_type>"
    
    Examples:
    | project                          | activity                         | records | aas_uid                           | aas_type |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 7       | 135762872152cda4323207c6035916735 | group    |
    | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 5       | 90706007452cda42ed1c326093152317  | group    |
    

Scenario Outline: List assignees of an activity with filter
    Given I request "project/<project>/activity/<activity>/assignee?filter=<filter>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> record
    And the "aas_uid" property in row 0 equals "<aas_uid>"
    And the "aas_type" property in row 0 equals "<aas_type>"
    
    Examples:
    | project                          | activity                         | records | aas_uid                          | aas_type | filter |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 1       | 69191356252cda41acde328048794164 | user     | oli    |
    | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 1       | 90706007452cda42ed1c326093152317 | group    | dep    |

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
    | project                          | activity                         | aas_uid                          | aas_type | aas_name | aas_lastname | aas_username |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 69191356252cda41acde328048794164 | user     | Olivia   | Austin       | olivia       |
  
  Scenario Outline: Remove assignee from an activity
    Given that I want to delete a resource with the key "aas_uid" stored in session array
    And I request "project/<project>/activity/<activity>/assignee/<aas_uid>"
    Then the response status code should be 200
    
    Examples:
    | project                          | activity                         | aas_uid                          |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 66386662252cda3f9a63226052377198 |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 69191356252cda41acde328048794164 |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 35762872152cda4323207c6035916735 |
    | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 | 90706007452cda42ed1c326093152317 |

  

  Scenario: List assignees of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 4 records
    
  Scenario Outline: List assignees of an activity with bad parameters
    Given I request "project/<project>/activity/<activity>/assignee"
    Then the response status code should be 400


  Examples:
    | project                          | activity                         |
    | 4224292655297723eb98691001100052 | 1234556                          |
    | 122134324                        | 65496814252977243d57684076211485 |
    | 345345345                        | 345345345                        |  
    |                                  |                                  |


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
    
     # Asignando un user inexistente
     # Asignando un usuario como grupo
      # Asignando un usuario con type inexistente
    Examples:

    | project                          | activity                         | aas_uid                          | aas_type      |
    | 4224292655297723eb98691001100052 | 68911670852a22d93c22c06005808422 |                                  |               | 
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | !@#$%^&*()_+=-[]{};:~,           |  user         |
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 69191356252cda41acde328048794164 |  group        | 
    | 4224292655297723eb98691001100052 | 65496814252977243d57684076211485 | 69191356252cda41acde328048794164 |  department   |
   