@ProcessMakerMichelangelo @RestAPI
Feature: Testing activity
    @1: TEST FOR GET ACTIVITY /-----------------------------------------------------------------------
    Scenario: Get a activity
        Given that I have a valid access_token
        And I request "project/251815090529619a99a2bf4013294414/activity/97192372152a5c78f04a794095806311"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the "definition" property type is "array"
        And that "tas_title" is set to "Task 1"


    @2: TEST FOR GET PROPERTIES ACTIVITY /---------------------------------------------------------
    Scenario: Get properties of activity
        Given that I have a valid access_token
        And I request "project/251815090529619a99a2bf4013294414/activity/97192372152a5c78f04a794095806311?filter=properties"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the response not has a "definition" property


    @3: TEST FOR GET DEFINITION ACTIVITY /---------------------------------------------------------
    Scenario: Get definition of activity
        Given that I have a valid access_token
        And I request "project/251815090529619a99a2bf4013294414/activity/97192372152a5c78f04a794095806311?filter=definition"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the response not has a "properties" property


    @4: TEST FOR PUT DEFINITION ACTIVITY /---------------------------------------------------------
    Scenario: Put propertie of activity
        Given that I have a valid access_token
        And PUT this data:
            """
            {
                "definition": [],
                "properties": {
                    "tas_type": "NORMAL",
                    "tas_duration": 1,
                    "tas_type_day": "",
                    "tas_timeunit": "DAYS",
                    "tas_priority_variable": "",
                    "tas_assign_type": "BALANCED",
                    "tas_assign_variable": "@@SYS_NEXT_USER_TO_BE_ASSIGNED",
                    "tas_group_variable": null,
                    "tas_transfer_fly": "FALSE",
                    "tas_send_last_email": "FALSE",
                    "tas_derivation_screen_tpl": "",
                    "tas_selfservice_timeout": 0,
                    "tas_selfservice_time": "",
                    "tas_selfservice_time_unit": "",
                    "tas_selfservice_trigger_uid": "",
                    "tas_title": "Task Edit",
                    "tas_description": "",
                    "tas_def_title": "",
                    "tas_def_description": "",
                    "tas_def_message": "",
                    "tas_def_subject_message": "",
                    "tas_calendar": ""
                }
            }
            """
        And I request "project/251815090529619a99a2bf4013294414/activity/97192372152a5c78f04a794095806311"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"


    @5: TEST FOR GET ACTIVITY /-----------------------------------------------------------------------
    Scenario: Get a activity
        Given that I have a valid access_token
        And I request "project/251815090529619a99a2bf4013294414/activity/97192372152a5c78f04a794095806311"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "tas_title" is set to "Task Edit"


    @6: TEST FOR PUT DEFINITION ACTIVITY /---------------------------------------------------------
    Scenario: Put propertie of activity
        Given that I have a valid access_token
        And PUT this data:
            """
            {
                "definition": [],
                "properties": {
                    "tas_type": "NORMAL",
                    "tas_duration": 1,
                    "tas_type_day": "",
                    "tas_timeunit": "DAYS",
                    "tas_priority_variable": "",
                    "tas_assign_type": "BALANCED",
                    "tas_assign_variable": "@@SYS_NEXT_USER_TO_BE_ASSIGNED",
                    "tas_group_variable": null,
                    "tas_transfer_fly": "FALSE",
                    "tas_send_last_email": "FALSE",
                    "tas_derivation_screen_tpl": "",
                    "tas_selfservice_timeout": 0,
                    "tas_selfservice_time": "",
                    "tas_selfservice_time_unit": "",
                    "tas_selfservice_trigger_uid": "",
                    "tas_title": "Task 1",
                    "tas_description": "",
                    "tas_def_title": "",
                    "tas_def_description": "",
                    "tas_def_message": "",
                    "tas_def_subject_message": "",
                    "tas_calendar": ""
                }
            }
            """
        And I request "project/251815090529619a99a2bf4013294414/activity/97192372152a5c78f04a794095806311"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"