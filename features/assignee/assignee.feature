@ProcessMakerMichelangelo @RestAPI
Feature: Project Properties Assignee Resources

 Background:
    Given that I have a valid acess_token

  Scenario: Get a list of available users and groups to be assigned to an activity
    Given that I have a valid access_token
    And I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485/available-assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

  Scenario: List assignees of an activity
    Given that I have a valid access_token
    And I request "project/14414793652a5d718b65590036026581/activity/13508932952a5d718ef56f6044945775/assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the "aas_uid" property in row 0 equals "00000000000000000000000000000001"
    And the "aas_name" property in row 0 equals "Administrator"
    And the "aas_lastname" property in row 0 equals "Admin"
    And the "aas_username" property in row 0 equals "admin"
    And the "aas_type" property in row 0 equals "user"

  
 
  Scenario: Get a single user or group of an activity
    Given that I have a valid access_token
    And I request "project/14414793652a5d718b65590036026581/activity/13508932952a5d718ef56f6044945775/assignee/00000000000000000000000000000001"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the "aas_uid" property equals "00000000000000000000000000000001"
    And the "aas_name" property equals "Administrator"
    And the "aas_lastname" property equals "Admin"
    And the "aas_username" property equals "admin"
    And the "aas_type" property equals "user"

  

 # Scenario: Unassign a user or group to an activity
 #  Given that I have a valid access_token
 #   And that I want to delete a resource with the key "aas_uid" stored in session array
 #   And I request "project/14414793652a5d718b65590036026581/activity/13508932952a5d718ef56f6044945775/assignee/42713393551f2a8aae1fde2096962777"
 #   Then the response status code should be 200
 #   And the content type is "application/json"

   Scenario: List assignees of an activity with filter
    Given that I have a valid access_token
    And I request "project/14414793652a5d718b65590036026581/activity/13508932952a5d718ef56f6044945775/assignee?filter=adm"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the "aas_uid" property in row 0 equals "42713393551f2a8aae1fde2096962777"
    And the "aas_name" property in row 0 equals "AdministratorOnly (0 Users)"
    And the "aas_lastname" property in row 0 equals ""
    And the "aas_username" property in row 0 equals ""
    And the "aas_type" property in row 0 equals "group"
