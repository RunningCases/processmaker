http://brayan.pmos.colosa.net/api/1.0/cochalo/project/444446641528a7318e16744023753627/activity/1077328655304fcfecdf879070119988
/step/609531574530b7a20dcb7c1053135698/trigger/899405570530ba201363cf9010087072

@ProcessMakerMichelangelo @RestAPI
Feature: Step update position
  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/106912358530c9b14ac15d3001790900/activity/467397212530c9b18435b87094293840/step/693874302530c9ba1734ad0026525748/trigger/659748303530c9b85af4d26007619346/before"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "3"

  Scenario: Change order the step
    Given that I have a valid access_token
    And PUT this data:
    """
    {
        "st_type": "BEFORE",
        "st_position": "1"
    }
    """
    And I request "project/106912358530c9b14ac15d3001790900/activity/467397212530c9b18435b87094293840/step/693874302530c9ba1734ad0026525748/trigger/659748303530c9b85af4d26007619346"
    Then the response status code should be 200

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/106912358530c9b14ac15d3001790900/activity/467397212530c9b18435b87094293840/step/693874302530c9ba1734ad0026525748/trigger/659748303530c9b85af4d26007619346/before"
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
    And I request "project/106912358530c9b14ac15d3001790900/activity/467397212530c9b18435b87094293840/step/693874302530c9ba1734ad0026525748/trigger/659748303530c9b85af4d26007619346"
    Then the response status code should be 200

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/106912358530c9b14ac15d3001790900/activity/467397212530c9b18435b87094293840/step/693874302530c9ba1734ad0026525748/trigger/659748303530c9b85af4d26007619346/before"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "3"
