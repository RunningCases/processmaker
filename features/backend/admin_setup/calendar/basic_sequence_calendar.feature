@ProcessMakerMichelangelo @RestAPI
Feature: Calendar
  Requirements:
  a workspace with one calendar in this workspace "Default Calendar"

  Background:
    Given that I have a valid access_token


  # GET /api/1.0/{workspace}/calendars
  #     Get Calendar list
  Scenario: List of calendar
    Given I request "calendars"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 2 records


  # POST /api/1.0/{workspace}/calendar
  #      Create a new Calendar
  Scenario Outline: Create a new Calendars
    Given POST this data:
    """
    {
        "cal_name": "<cal_name>",
        "cal_description": "<cal_description>",
        "cal_work_days": [5, 6, 7],
        "cal_work_hour": [
            {"day": 0, "hour_start": "00:00", "hour_end": "00:00"},
            {"day": 7, "hour_start": "09:00", "hour_end": "17:00"}
        ],
        "cal_holiday": [
            {"name": "holiday1", "date_start": "2015-01-01", "date_end": "2015-01-10"},
            {"name": "holiday2", "date_start": "2015-04-01", "date_end": "2015-04-04"}
        ]
    }
    """
    And I request "calendar"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "cal_uid" in session array as variable "cal_uid_<cal_uid_number>"

  Examples:
    | cal_uid_number | cal_name   | cal_description                    |
    | 1              | Calendar 1 | Prueba de Creacion de Calendario 1 |
    | 2              | Calendar 2 | Prueba de Creacion de Calendario 2 |


  # PUT /api/1.0/{workspace}/calendar
  #     Update a Calendar
  Scenario Outline: Update the calendars and then check if the values had changed
    Given PUT this data:
    """
    {
        "cal_name": "<cal_name>",
        "cal_description": "<cal_description>",
        "cal_work_days": [5, 6, 7],
        "cal_status": "<cal_status>",
        "cal_work_hour": [
            {"day": 0, "hour_start": "00:00", "hour_end": "00:00"},
            {"day": 7, "hour_start": "09:00", "hour_end": "17:00"}
        ],
        "cal_holiday": [
            {"name": "holiday1", "date_start": "2010-01-01", "date_end": "2010-01-10"},
            {"name": "holiday2", "date_start": "2014-04-01", "date_end": "2014-04-04"}
        ]
    }
      """
    And that I want to update a resource with the key "cal_uid" stored in session array as variable "cal_uid_<cal_uid_number>"
    And I request "calendar"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

  Examples:
    | cal_uid_number | cal_name          | cal_description                           | cal_status |
    | 1              | Update Calendar 1 | Update Prueba de Creacion de Calendario 1 | ACTIVE     |
    | 2              | Update Calendar 2 | Update Prueba de Creacion de Calendario 2 | INACTIVE   |


  # GET /api/1.0/{workspace}/calendar
  #     Get an specific Calendar
  Scenario Outline: Get a single calendar
    Given that I want to get a resource with the key "cal_uid" stored in session array as variable "cal_uid_<cal_uid_number>"
    And I request "calendar"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the "cal_name" property equals "<cal_name>"
    And the "cal_description" property equals "<cal_description>"
    And the "cal_status" property equals "<cal_status>"

  Examples:
    | cal_uid_number | cal_name          | cal_description                           | cal_status |
    | 1              | Update Calendar 1 | Update Prueba de Creacion de Calendario 1 | ACTIVE     |
    | 2              | Update Calendar 2 | Update Prueba de Creacion de Calendario 2 | INACTIVE   |


  # DELETE /api/1.0/{workspace}/calendar
  #        Delete an specific calendar
  Scenario Outline: Delete all Calendars created previously in this script (one by one )
    Given that I want to delete a resource with the key "cal_uid" stored in session array as variable "cal_uid_<cal_uid_number>"
    And I request "calendar"
    And the content type is "application/json"
    Then the response status code should be 200

  Examples:
    | cal_uid_number |
    | 1              |
    | 2              |


  # GET /api/1.0/{workspace}/calendars
  #     Get Calendar list
  Scenario: List of calendar
    Given I request "calendars"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 2 records