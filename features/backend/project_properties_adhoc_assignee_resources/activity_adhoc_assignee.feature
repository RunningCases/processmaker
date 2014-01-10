@ProcessMakerMichelangelo @RestAPI
Feature: Project Properties - Assignee Resources

  Background:
    Given that I have a valid access_token

 Scenario: Get a list of available users and groups to be assigned to an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-available-assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 79 records
    And the "aas_uid" property in row 0 equals "35762872152cda4323207c6035916735"
    And the "aas_type" property in row 0 equals "group"

Scenario: Get a list of available users and groups to be assigned to an activity with filter
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-available-assignee?filter=departa&start=0&limit=50"
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
    "ass_uid": "84643774552cda42dabb732033709262",
    "ass_type": "user"
}
"""
    And I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-assignee"
    Then the response status code should be 201
    And the type is "object"

  Scenario: Assign a user or group to an activity
    Given POST this data:
"""
{
    "ass_uid": "95888918452cda41a2b5d11013819411",
    "ass_type": "user"
}
"""
    And I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-assignee"
    Then the response status code should be 201
    And the type is "object"

  Scenario: Assign a user or group to an activity
    Given POST this data:
"""
{
    "ass_uid": "16698718252cda431814024050455569",
    "ass_type": "group"
}
"""
    And I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-assignee"
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
    And I request "project/4224292655297723eb98691001100052/activity/68911670852a22d93c22c06005808422/adhoc-assignee"
    Then the response status code should be 201
    And the type is "object"

  Scenario: List assignees of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 7 records
    And the "aas_uid" property in row 0 equals "10732248352cda434c43997043577116"
    And the "aas_type" property in row 0 equals "group"

  Scenario: List assignees of an activity with filter
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-assignee?filter=emi"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 record
    And the "aas_uid" property in row 0 equals "84643774552cda42dabb732033709262"
    And the "aas_type" property in row 0 equals "user"

  Scenario: Get a single user or group of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-assignee/95888918452cda41a2b5d11013819411"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the "aas_uid" property equals "95888918452cda41a2b5d11013819411"
    And the "aas_name" property equals "Mike"
    And the "aas_lastname" property equals "Balisi"
    And the "aas_username" property equals "mike"
    And the "aas_type" property equals "user"
  
  Scenario: Remove assignee from an activity
    Given that I want to delete a resource with the key "aas_uid" stored in session array
    And I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-assignee/91968412052cda4097270a3085279286"
    Then the response status code should be 200

  Scenario: List assignees of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/adhoc-assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 6 records
    
  Scenario: List assignees of an activity
    Given I request "project/4224292655297723eb98691001100052/activity/12345/adhoc-assignee"
    Then the response status code should be 400
