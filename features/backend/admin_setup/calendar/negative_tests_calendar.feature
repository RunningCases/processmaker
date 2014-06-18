@ProcessMakerMichelangelo @RestAPI
Feature: Calendar Negative Tests


Background:
    Given that I have a valid access_token


Scenario Outline: Create a new Calendars (Negative Tests)
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
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"

    Examples:

    | test_description         | cal_name   | cal_description                    | error_code | error_message |
    | Create whitout cal_name  |            | Prueba de Creacion de Calendario 1 | 400        | cal_name      |
 

Scenario: Create a new Calendars (Wrong cal_work_days)
    Given POST this data:
    """
    {
        "cal_name": "Sample Calendar",
        "cal_description": "Creacion de Calendar 400",
        "cal_work_days": [9,10.30,56],
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
    Then the response status code should be 400
    And the response status message should have the following text "cal_work_days"


Scenario: Create a new Calendars (cal_work_hour . day)
    Given POST this data:
    """
    {
        "cal_name": "Sample Calendar",
        "cal_description": "Creacion de Calendar 400",
        "cal_work_days": [5, 6, 7],
        "cal_work_hour": [
            {"day": 10, "hour_start": "00:00", "hour_end": "00:00"},
            {"day": 72, "hour_start": "09:00", "hour_end": "17:00"}
        ],
        "cal_holiday": [
            {"name": "holiday1", "date_start": "2010-01-01", "date_end": "2010-01-10"},
            {"name": "holiday2", "date_start": "2014-04-01", "date_end": "2014-04-04"}
        ]
    }
    """
    And I request "calendar"
    Then the response status code should be 400
    And the response status message should have the following text "day"


Scenario: Create a new Calendars (without cal_work_days)
    Given POST this data:
    """
    {
        "cal_name": "Sample Calendar",
        "cal_description": "Creacion de Calendar 400",
        
        "cal_work_hour": [
            {"day": 1, "hour_start": "00:00", "hour_end": "00:00"},
            {"day": 2, "hour_start": "09:00", "hour_end": "17:00"}
        ],
        "cal_holiday": [
            {"name": "holiday1", "date_start": "2010-01-01", "date_end": "2010-01-10"},
            {"name": "holiday2", "date_start": "2014-04-01", "date_end": "2014-04-04"}
        ]
    }
    """
    And I request "calendar"
    Then the response status code should be 400
    And the response status message should have the following text "cal_work_days"


Scenario: Create a new Calendars (wrong date_start )
    Given POST this data:
    """
    {
        "cal_name": "Sample Calendar",
        "cal_description": "Creacion de Calendar 400",
        "cal_work_days": [5, 6, 7],
        "cal_work_hour": [
            {"day": 1, "hour_start": "00:00", "hour_end": "00:00"},
            {"day": 2, "hour_start": "09:00", "hour_end": "17:00"}
        ],
        "cal_holiday": [
            {"name": "holiday1", "date_start": "2010-45-100", "date_end": "2010-01-10"},
            {"name": "holiday2", "date_start": "2014-04-01", "date_end": "2014-04-04"}
        ]
    }
    """
    And I request "calendar"
    Then the response status code should be 400
    And the response status message should have the following text "date_start"


Scenario: Create a new Calendars (wrong date_end)
    Given POST this data:
    """
    {
        "cal_name": "Sample Calendar",
        "cal_description": "Creacion de Calendar 400",
        "cal_work_days": [5, 6, 7],
        "cal_work_hour": [
            {"day": 1, "hour_start": "00:00", "hour_end": "00:00"},
            {"day": 2, "hour_start": "09:00", "hour_end": "17:00"}
        ],
        "cal_holiday": [
            {"name": "holiday1", "date_start": "2010-01-01", "date_end": "2010-100-87"},
            {"name": "holiday2", "date_start": "2014-04-01", "date_end": "2014-04-04"}
        ]
    }
    """
    And I request "calendar"
    Then the response status code should be 400
    And the response status message should have the following text "date_end"


Scenario: Create a new Calendars (with work days less than 3)
    Given POST this data:
    """
    {
        "cal_name": "Sample Calendar",
        "cal_description": "Creacion de Calendar 400",
        "cal_work_days": [1,2],
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
    Then the response status code should be 400
    And the response status message should have the following text "least 3 Working Days"



#Scenario: Delete Calendar when it is assigned to a project "Test Process"
#    Given that I want to delete a "Calendar"
#    And I request "calendar/14606161052f50839307899033145440"
#    Then the response status code should be 400
#    And the response status message should have the following text "cannot be deleted" 