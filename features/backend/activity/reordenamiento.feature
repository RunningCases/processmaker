@ProcessMakerMichelangelo @RestAPI
Feature: Reorder Steps

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/9821342145305125d48cb88069229840/activity/8251872975307632a765f34007266075/step/285219904530763bb5c4367074373078"
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
    And I request "project/98002714453076320a06cb4009450121/activity/8251872975307632a765f34007266075/step/285219904530763bb5c4367074373078"
    Then the response status code should be 200

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/9821342145305125d48cb88069229840/activity/8251872975307632a765f34007266075/step/285219904530763bb5c4367074373078"
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
    And I request "project/98002714453076320a06cb4009450121/activity/8251872975307632a765f34007266075/step/285219904530763bb5c4367074373078"
    Then the response status code should be 200

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/9821342145305125d48cb88069229840/activity/8251872975307632a765f34007266075/step/285219904530763bb5c4367074373078"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "1"
