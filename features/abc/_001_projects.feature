@ProcessMakerMichelangelo @RestAPI
Feature: Testing Oauth
  Scenario: GET projects list
    Given that I have a valid access_token
    And I request "projects"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the "prj_uid" property in row 0 equals "31034744752a5d007364d93044509065"
    And the "prj_name" property in row 0 equals "Sample Project #1"
    And the "prj_create_date" property in row 0 equals "2013-12-09 09:13:27"
    #And the "diagrams" property in row 0 equals "diagrams"

  Scenario: GET project Sample Project #1 process...
    Given that I have a valid access_token
    And I request "project/31034744752a5d007364d93044509065"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the response has a "diagrams" property
    And the "diagrams" property type is "array"
    And the "dia_uid" property in row 0 of property "diagrams" equals "23643663952a5d0073a6133086723956"
    And the "prj_uid" property in row 0 of property "diagrams" equals "31034744752a5d007364d93044509065"
    And the "dia_name" property in row 0 of property "diagrams" equals "Sample Project #1"
    And the "dia_is_closable" property in row 0 of property "diagrams" equals "0"

    And the "lanesets" property in row 0 of property "diagrams" is "array"
    And the "lanes" property in row 0 of property "diagrams" is "array"
    And the "activities" property in row 0 of property "diagrams" is "array"
    And the "events" property in row 0 of property "diagrams" is "array"
    And the "gateways" property in row 0 of property "diagrams" is "array"
    And the "flows" property in row 0 of property "diagrams" is "array"
    And the "artifacts" property in row 0 of property "diagrams" is "array"

  Scenario: get an activity from process Sample Project #1
    Given that I have a valid access_token
    And I request "project/31034744752a5d007364d93044509065/activity/72845150252a5d718be4df5092655350"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the response is equivalent to this json file "task_72845150252a5d718be4df5092655350.json"

  Scenario: get an activity from process Sample Project #1
    Given that I have a valid access_token
    And I request "project/31034744752a5d007364d93044509065/activity/13508932952a5d718ef56f6044945775"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the response is equivalent to this json file "task_13508932952a5d718ef56f6044945775.json"

  Scenario: get an activity from process Sample Project #1
    Given that I have a valid access_token
    And I request "project/31034744752a5d007364d93044509065/activity/58163453252a5d719253ff9069893664"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the response is equivalent to this json file "task_58163453252a5d719253ff9069893664.json"

  Scenario: get a list of input documents
    Given that I have a valid access_token
    And I request "project/31034744752a5d007364d93044509065/input-documents"
    Then the response status code should be 200
    And the content type is "application/json"
    And the response is equivalent to this json file "task_58163453252a5d719253ff9069893664.json"

#  Scenario: post an activity
#    Given that I send "data"
#    And I request "project/31034744752a5d007364d93044509065/activity"
#    Then the response status code should be 200
#    And the response charset is "UTF-8"

#  Scenario: Creating new Activity with API
#    Given that I want to make a new "Process" with:
#      | name          | followers |
#      | everzet       | 147       |
#      | avalanche123  | 142       |
#      | kriswallsmith | 274       |
#      | fabpot        | 962       |
#    And I want to Insert a new "Process" with:
#    """
#    {
#        "id" : 123,
#        "name" : "john",
#        "age" : 12
#    }
#    """
##    And "prj_name" is "my test process"
##    And "prj_description" is "test for gizzle"
##    And the request is sent as JSON
##    When I request "project"
#    Then the response status code should be 201
##    And the response should be JSON
##    And the response has a "prj_id" property
