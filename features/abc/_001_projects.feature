@ProcessMakerMichelangelo @RestAPI
Feature: Testing Oauth
  Scenario: GET projects list
    Given that I have a valid access_token
    And I request "projects"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the "prj_uid" property in row 0 equals "8061532405176da5242da84006421863"
    And the "prj_name" property in row 0 equals "Sample Project"
    And the "prj_description" property in row 0 equals "description"
    And the "diagrams" property in row 0 equals "diagrams"

  Scenario: GET project SLA Process Entire process...
    Given that I have a valid access_token
    And I request "project/725511159507f70b01942b3002493938"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the response has a "diagrams" property
    And the "diagrams" property type is "array"
    #And the "lanesets" property in row 0 of property "diagrams" equals "xx"
    And the "lanesets" property in row 0 of property "diagrams" is "array"
    And the "lanes" property in row 0 of property "diagrams" is "array"
    And the "activities" property in row 0 of property "diagrams" is "array"
    And the "events" property in row 0 of property "diagrams" is "array"
    And the "gateways" property in row 0 of property "diagrams" is "array"
    And the "flows" property in row 0 of property "diagrams" is "array"
    And the "artifacts" property in row 0 of property "diagrams" is "array"

  Scenario: Creating new Project with API
    Given that I want to make a new "Process" with:
      | name          | followers |
      | everzet       | 147       |
      | avalanche123  | 142       |
      | kriswallsmith | 274       |
      | fabpot        | 962       |
    And I want to Insert a new "Process" with:
    """
    { 
        "id" : 123,
        "name" : "john",
        "age" : 12
    }
    """      
#    And "prj_name" is "my test process"
#    And "prj_description" is "test for gizzle"
#    And the request is sent as JSON
#    When I request "project"
    Then the response status code should be 201
#    And the response should be JSON
#    And the response has a "prj_id" property
