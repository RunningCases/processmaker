@ProcessMakerMichelangelo @RestAPI
Feature: Export Process Main Tests
  Requirements:
    a workspace with the project 1455892245368ebeb11c1a5001393784 ("Process Complete BPMN") already loaded
    there are many activities, steps, triggers, pmtables, asignee, events, etc.  in the process

Background:
    Given that I have a valid access_token


Scenario: Get for Export Project
    Given I request "project/1455892245368ebeb11c1a5001393784/export"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

