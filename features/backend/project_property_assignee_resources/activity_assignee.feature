@ProcessMakerMichelangelo @RestAPI
Feature: Project Properties - Assignee Resources

  Background:
    Given that I have a valid access_token

 Scenario: Get a list of available users and groups to be assigned to an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/available-assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 79 records
    And the "aas_uid" property in row 0 equals "35762872152cda4323207c6035916735"
    And the "aas_type" property in row 0 equals "group"

Scenario: Get a list of available users and groups to be assigned to an activity with filter
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/available-assignee?filter=departa&start=0&limit=50"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 3 records
    And the "aas_uid" property in row 0 equals "90268877852b7b4b9f134b1096735994"
    And the "aas_type" property in row 0 equals "group"

  Scenario: Assign a user or group to an activity
    Given POST this data:
"""
{
    "ass_uid": "66386662252cda3f9a63226052377198",
    "ass_type": "user"
}
"""
    And I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/assignee"
    Then the response status code should be 201
    And the type is "object"

  Scenario: Assign a user or group to an activity
    Given POST this data:
"""
{
    "ass_uid": "69191356252cda41acde328048794164",
    "ass_type": "user"
}
"""
    And I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/assignee"
    Then the response status code should be 201
    And the type is "object"

  Scenario: Assign a user or group to an activity
    Given POST this data:
"""
{
    "ass_uid": "35762872152cda4323207c6035916735",
    "ass_type": "group"
}
"""
    And I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/assignee"
    Then the response status code should be 201
    And the type is "object"

  Scenario: Assign a user or group to an activity
    Given POST this data:
"""
{
    "ass_uid": "90706007452cda42ed1c326093152317",
    "ass_type": "group"
}
"""
    And I request "project/4224292655297723eb98691001100052/activity/68911670852a22d93c22c06005808422/assignee"
    Then the response status code should be 201
    And the type is "object"

  Scenario: List assignees of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 7 records
    And the "aas_uid" property in row 0 equals "35762872152cda4323207c6035916735"
    And the "aas_type" property in row 0 equals "group"

Scenario: List assignees of an activity with filter
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/assignee?filter=oli"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 record
    And the "aas_uid" property in row 0 equals "69191356252cda41acde328048794164"
    And the "aas_type" property in row 0 equals "user"

  Scenario: Get a single user or group of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/assignee/69191356252cda41acde328048794164"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the "aas_uid" property equals "69191356252cda41acde328048794164"
    And the "aas_name" property equals "Olivia"
    And the "aas_lastname" property equals "Austin"
    And the "aas_username" property equals "olivia"
    And the "aas_type" property equals "user"
  
  Scenario: Remove assignee from an activity
    Given that I want to delete a resource with the key "aas_uid" stored in session array
    And I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/assignee/69191356252cda41acde328048794164"
    Then the response status code should be 200

  Scenario: List assignees of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 6 records
    
  Scenario: List assignees of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/12345/assignee"
    Then the response status code should be 400


    

