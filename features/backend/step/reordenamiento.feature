@ProcessMakerMichelangelo @RestAPI
Feature: Reorder Steps

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/940481651530fa1f3803525098230380/activity/677485918530fa1f80ad463004640434/step/289467030530fa28077e6a2088432104"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "1"

  Scenario: Change order the step
    Given that I have a valid access_token
    And PUT this data:
    """
    {
        "step_position": "3"
    }
    """
    And I request "project/98002714453076320a06cb4009450121/activity/677485918530fa1f80ad463004640434/step/289467030530fa28077e6a2088432104"
    Then the response status code should be 200

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/940481651530fa1f3803525098230380/activity/677485918530fa1f80ad463004640434/step/289467030530fa28077e6a2088432104"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "3"

  Scenario: Change order the step
    Given that I have a valid access_token
    And PUT this data:
    """
    {
        "step_position": "1"
    }
    """
    And I request "project/98002714453076320a06cb4009450121/activity/677485918530fa1f80ad463004640434/step/289467030530fa28077e6a2088432104"
    Then the response status code should be 200

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/940481651530fa1f3803525098230380/activity/677485918530fa1f80ad463004640434/step/289467030530fa28077e6a2088432104"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "1"
