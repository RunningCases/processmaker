@ProcessMakerMichelangelo @RestAPI
Feature: Calendar Main Tests
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
    And the response has 1 records


  # GET /api/1.0/{workspace}/calendar/<cal_uid>
  #     Get an specific Calendar
  Scenario: Get a single calendar "Default Calendar"
    Given I request "calendar/00000000000000000000000000000001"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the "cal_uid" property equals "00000000000000000000000000000001"
    And the "cal_name" property equals "Default Calendar"
    And the "cal_description" property equals "Default Calendar"


  # POST /api/1.0/{workspace}/calendar
  #      Create a new Calendar
  Scenario Outline: Create new Calendars (one by one)
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
            {"name": "holiday1", "date_start": "2010-01-01", "date_end": "2010-01-10"},
            {"name": "holiday2", "date_start": "2014-04-01", "date_end": "2014-04-04"}
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
    | test_description                       | cal_uid_number | cal_name                                                           | cal_description                    |
    | Create calendar 1                      | 1              | Calendar 1                                                         | Prueba de Creacion de Calendario 1 |
    | Create calendar with short name        | 2              | C                                                                  | Prueba de Creacion de Calendario 2 |
    | Create calendar with name long         | 3              | Creacion de nuevo Calendar con nombre largo para las pruebas behat | Prueba de Creacion de Calendario 3 |
    | Create calendar with special character | 4              | Calendar 4 !@#$%^&                                                 | Prueba de Creacion de Calendario 4 |


  # POST /api/1.0/{workspace}/calendar
  #      Create a new Calendar
  Scenario Outline: Create a new Calendar with parameter cal_work_days diferent
    Given POST this data:
    """
    {
        "cal_name": "Calendar 5",
        "cal_description": "Prueba de Creacion de Calendario 5",
        "cal_work_days": [1, 2, 3, 4, 5, 6, 7],
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
    And I request "calendar"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "cal_uid" in session array as variable "cal_uid_<cal_uid_number>"

  Examples:
    | cal_uid_number |
    | 5              |


  # POST /api/1.0/{workspace}/calendar
  #      Create a new Calendar
  Scenario Outline: Create a new Calendar with parameter cal_work_hour diferent
    Given POST this data:
    """
    {
        "cal_name": "Calendar 6",
        "cal_description": "Prueba de Creacion de Calendario 6",
        "cal_work_days": [3, 4, 5, 6, 7],
        "cal_work_hour": [
            {"day": 3, "hour_start": "08:00", "hour_end": "20:00"},
            {"day": 7, "hour_start": "09:00", "hour_end": "17:00"}
        ],
        "cal_holiday": [
            {"name": "holiday1", "date_start": "2010-01-01", "date_end": "2010-01-10"},
            {"name": "holiday2", "date_start": "2014-04-01", "date_end": "2014-04-04"}
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
    | cal_uid_number |
    | 6              |


  # POST /api/1.0/{workspace}/calendar
  #      Create a new Calendar
  Scenario Outline: Create a new Calendar with parameter cal_holiday diferent
    Given POST this data:
    """
    {
        "cal_name": "Calendar 7",
        "cal_description": "Prueba de Creacion de Calendario 7",
        "cal_work_days": [2, 3, 4, 5, 6],
        "cal_work_hour": [
            {"day": 2, "hour_start": "00:00", "hour_end": "00:00"},
            {"day": 6, "hour_start": "09:00", "hour_end": "17:00"}
        ],
        "cal_holiday": [
            {"name": "holiday2", "date_start": "2010-01-01", "date_end": "2010-02-10"},
            {"name": "holiday5", "date_start": "2014-05-01", "date_end": "2014-10-04"}
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
    | cal_uid_number |
    | 7              |


  # POST /api/1.0/{workspace}/calendar
  #      Create a new Calendar
  Scenario: Create a new Calendar with same name
    Given POST this data:
    """
    {
        "cal_name": "Calendar 7",
        "cal_description": "Prueba de Creacion de Calendario 5",
        "cal_work_days": [2, 3, 4, 5, 6],
        "cal_work_hour": [
            {"day": 2, "hour_start": "00:00", "hour_end": "00:00"},
            {"day": 6, "hour_start": "09:00", "hour_end": "17:00"}
        ],
        "cal_holiday": [
            {"name": "holiday2", "date_start": "2010-01-01", "date_end": "2010-02-10"},
            {"name": "holiday5", "date_start": "2014-05-01", "date_end": "2014-10-04"}
        ]
    }
    """
    And I request "calendar"
    Then the response status code should be 400
    And the response status message should have the following text "already exists"


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
    | test_description  | cal_uid_number | cal_name          | cal_description                           | cal_status |
    | Update calendar 1 | 1              | Update Calendar 1 | Update Prueba de Creacion de Calendario 1 | ACTIVE     |
    | Update calendar 2 | 2              | Update Calendar 2 | Update Prueba de Creacion de Calendario 2 | INACTIVE   |


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


  # GET /api/1.0/{workspace}/calendars
  #     Get Calendar list
  Scenario: List of calendar
    Given I request "calendars"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 8 records


  # DELETE /api/1.0/{workspace}/calendar
  #        Delete an specific calendar
  Scenario Outline: Delete all Calendars created previously in this script
    Given that I want to delete a resource with the key "cal_uid" stored in session array as variable "cal_uid_<cal_uid_number>"
    And I request "calendar"
    And the content type is "application/json"
    Then the response status code should be 200

  Examples:
    | cal_uid_number |
    | 1              |
    | 2              |
    | 3              |
    | 4              |
    | 5              |
    | 6              |
    | 7              |


  # GET /api/1.0/{workspace}/calendars
  #     Get Calendar list
  Scenario: List of calendar
    Given I request "calendars"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 1 records