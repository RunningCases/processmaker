@ProcessMakerMichelangelo @RestAPI
Feature: Step update position Main Tests
  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/6838258995339cb1474fc94058315158/case-tracker/object/6262239315339cb46efc6e4031561540"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "cto_position" is set to "3"

  Scenario: Change order the step
    Given that I have a valid access_token
    And PUT this data:
    """
    {
        "cto_position": 1
    }
    """
    And I request "project/6838258995339cb1474fc94058315158/case-tracker/object/6262239315339cb46efc6e4031561540"
    Then the response status code should be 200

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/6838258995339cb1474fc94058315158/case-tracker/object/6262239315339cb46efc6e4031561540
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "1"

  Scenario: Change order the step
    Given that I have a valid access_token
    And PUT this data:
    """
    {
        "st_type": "BEFORE",
        "st_position": "3"
    }
    """
    And I request "project/6838258995339cb1474fc94058315158/case-tracker/object/6262239315339cb46efc6e4031561540"
    Then the response status code should be 200

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/6838258995339cb1474fc94058315158/case-tracker/object/6262239315339cb46efc6e4031561540
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "3"
