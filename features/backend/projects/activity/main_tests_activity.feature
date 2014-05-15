@ProcessMakerMichelangelo @RestAPI
Feature: Activity Resources Main Tests
  Requirements:
    a workspace with the process 59534741653502b6d1820d6012095837 already loaded
    the process name is "Test Activity"
    there are three Activity Resources in the process

Background:
    Given that I have a valid access_token

    
Scenario Outline: Get the Properties and Definition of 3 Activities
    Given I request "project/59534741653502b6d1820d6012095837/activity/<activity>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the "definition" property type is "array"
    And the "properties" property type is "object"
    And the property "tas_title" of "properties" is set to "<tas_title>"

    Examples:

    | activity                         | tas_title |
    | 28629650453502b70b7f3a8051740006 | Task 1    |
    | 52976670353502b71e2b0a8036043148 | Task 2    |
    | 24689389453502b73597aa5052425148 | Task 3    |
        
        
Scenario Outline: Get the Properties of a Activity are exactly three activity
    Given I request "project/59534741653502b6d1820d6012095837/activity/<activity>?filter=properties"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the response has not a "definition" property

    Examples:

    | activity                         | tas_title |
    | 28629650453502b70b7f3a8051740006 | Task 1    |
    | 52976670353502b71e2b0a8036043148 | Task 2    |
    | 24689389453502b73597aa5052425148 | Task 3    |
        
    

Scenario Outline: Get the Definition of a Activity are exactly three activity
    Given I request "project/59534741653502b6d1820d6012095837/activity/<activity>?filter=definition"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the response has not a "properties" property

    Examples:

    | activity                         | tas_title |
    | 28629650453502b70b7f3a8051740006 | Task 1    |
    | 52976670353502b71e2b0a8036043148 | Task 2    |
    | 24689389453502b73597aa5052425148 | Task 3    |
        

Scenario: Get a activity (Verification of initial values) "Task1"
    Given I request "project/59534741653502b6d1820d6012095837/activity/28629650453502b70b7f3a8051740006"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the property "tas_type" of "properties" is set to "NORMAL"
    And the property "tas_duration" of "properties" is set to "1"
    And the property "tas_type_day" of "properties" is set to ""
    And the property "tas_timeunit" of "properties" is set to "DAYS"
    And the property "tas_priority_variable" of "properties" is set to ""    
    And the property "tas_assign_type" of "properties" is set to "BALANCED"    
    And the property "tas_assign_variable" of "properties" is set to "@@SYS_NEXT_USER_TO_BE_ASSIGNED"    
    And the property "tas_group_variable" of "properties" is set to ""
    And the property "tas_transfer_fly" of "properties" is set to "FALSE"
    And the property "tas_derivation_screen_tpl" of "properties" is set to ""
    And the property "tas_selfservice_timeout" of "properties" is set to "0"
    And the property "tas_selfservice_time" of "properties" is set to ""
    And the property "tas_selfservice_time_unit" of "properties" is set to ""
    And the property "tas_selfservice_trigger_uid" of "properties" is set to ""
    And the property "tas_title" of "properties" is set to "Task 1"
    And the property "tas_description" of "properties" is set to ""
    And the property "tas_def_title" of "properties" is set to ""
    And the property "tas_def_description" of "properties" is set to ""
    And the property "tas_calendar" of "properties" is set to ""
    And the property "tas_def_message_type" of "properties" is set to "text"
    And the property "tas_def_message_template" of "properties" is set to "alert_message.html"


Scenario: Get a activity (Verification of initial values) "Task2"
    Given I request "project/59534741653502b6d1820d6012095837/activity/52976670353502b71e2b0a8036043148"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the property "tas_type" of "properties" is set to "NORMAL"
    And the property "tas_duration" of "properties" is set to "1"
    And the property "tas_type_day" of "properties" is set to ""
    And the property "tas_timeunit" of "properties" is set to "DAYS"
    And the property "tas_priority_variable" of "properties" is set to ""    
    And the property "tas_assign_type" of "properties" is set to "BALANCED"    
    And the property "tas_assign_variable" of "properties" is set to "@@SYS_NEXT_USER_TO_BE_ASSIGNED"    
    And the property "tas_transfer_fly" of "properties" is set to "FALSE"
    And the property "tas_derivation_screen_tpl" of "properties" is set to ""
    And the property "tas_selfservice_timeout" of "properties" is set to "0"
    And the property "tas_selfservice_time" of "properties" is set to ""
    And the property "tas_selfservice_time_unit" of "properties" is set to ""
    And the property "tas_selfservice_trigger_uid" of "properties" is set to ""
    And the property "tas_title" of "properties" is set to "Task 2"
    And the property "tas_description" of "properties" is set to ""
    And the property "tas_def_title" of "properties" is set to ""
    And the property "tas_def_description" of "properties" is set to ""
    And the property "tas_calendar" of "properties" is set to ""
    And the property "tas_def_message_type" of "properties" is set to "text"
    And the property "tas_def_message_template" of "properties" is set to "alert_message.html"


Scenario: Get a activity (Verification of initial values) "Task3"
    Given I request "project/59534741653502b6d1820d6012095837/activity/24689389453502b73597aa5052425148"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the property "tas_type" of "properties" is set to "NORMAL"
    And the property "tas_duration" of "properties" is set to "1"
    And the property "tas_type_day" of "properties" is set to ""
    And the property "tas_timeunit" of "properties" is set to "DAYS"
    And the property "tas_priority_variable" of "properties" is set to ""    
    And the property "tas_assign_type" of "properties" is set to "BALANCED"    
    And the property "tas_assign_variable" of "properties" is set to "@@SYS_NEXT_USER_TO_BE_ASSIGNED"    
    And the property "tas_transfer_fly" of "properties" is set to "FALSE"
    And the property "tas_derivation_screen_tpl" of "properties" is set to ""
    And the property "tas_selfservice_timeout" of "properties" is set to "0"
    And the property "tas_selfservice_time" of "properties" is set to ""
    And the property "tas_selfservice_time_unit" of "properties" is set to ""
    And the property "tas_selfservice_trigger_uid" of "properties" is set to ""
    And the property "tas_title" of "properties" is set to "Task 3"
    And the property "tas_description" of "properties" is set to ""
    And the property "tas_def_title" of "properties" is set to ""
    And the property "tas_def_description" of "properties" is set to ""
    And the property "tas_calendar" of "properties" is set to ""
    And the property "tas_def_message_type" of "properties" is set to "text"
    And the property "tas_def_message_template" of "properties" is set to "alert_message.html"


Scenario: Update the Definition of a Activity "Task1" and the check if the values had changed
    Given PUT this data:
    """
    {
          "definition": {},
          "properties": 
        {
            "tas_type": "ADHOC",
            "tas_duration": 2,
            "tas_type_day": "DAYS",
            "tas_timeunit": "DAYS",
            "tas_priority_variable": "@@PROCESS",
            "tas_assign_type": "BALANCED",
            "tas_assign_variable": "@@USER_LOGGED",
            "tas_group_variable": "@@USER_LOGGED",
            "tas_transfer_fly": "TRUE",
            "tas_send_last_email": "TRUE",         
            "tas_derivation_screen_tpl": "test activity.html",
            "tas_selfservice_timeout": 1,
            "tas_selfservice_time": 1,
            "tas_selfservice_time_unit": "HOURS",
            "tas_selfservice_trigger_uid": "",
            "tas_title": "Task 1 Update Activity",
            "tas_description": "Update Description",
            "tas_def_title": "Case Title",
            "tas_def_description": "Case Description UPDATE",
            "tas_def_message": "Esta es una Notificacion - UPDATE ",
            "tas_def_subject_message": "UPDATE Titulo de Notificacion 1",
            "tas_calendar": "14606161052f50839307899033145440",
            "tas_def_message_type": "text",
            "tas_def_message_template": "test activity.html"
        }
    }
    """
    And I request "project/59534741653502b6d1820d6012095837/activity/28629650453502b70b7f3a8051740006"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
        
    

Scenario: Get a activity (Review of variables after the update) Task1
    Given I request "project/59534741653502b6d1820d6012095837/activity/28629650453502b70b7f3a8051740006"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the property "tas_type" of "properties" is set to "ADHOC"
    And the property "tas_duration" of "properties" is set to 2
    And the property "tas_type_day" of "properties" is set to "DAYS"
    And the property "tas_timeunit" of "properties" is set to "DAYS"
    And the property "tas_priority_variable" of "properties" is set to "@@PROCESS"    
    And the property "tas_assign_type" of "properties" is set to "BALANCED"    
    And the property "tas_assign_variable" of "properties" is set to "@@USER_LOGGED"    
    And the property "tas_group_variable" of "properties" is set to "@@USER_LOGGED"
    And the property "tas_transfer_fly" of "properties" is set to "TRUE"
    And the property "tas_send_last_email" of "properties" is set to "TRUE"
    And the property "tas_derivation_screen_tpl" of "properties" is set to "test activity.html"
    And the property "tas_selfservice_timeout" of "properties" is set to 1
    And the property "tas_selfservice_time" of "properties" is set to 1
    And the property "tas_selfservice_time_unit" of "properties" is set to "HOURS"
    And the property "tas_selfservice_trigger_uid" of "properties" is set to ""
    And the property "tas_title" of "properties" is set to "Task 1 Update Activity"
    And the property "tas_description" of "properties" is set to "Update Description"
    And the property "tas_def_title" of "properties" is set to "Case Title"
    And the property "tas_def_description" of "properties" is set to "Case Description UPDATE"
    And the property "tas_def_message" of "properties" is set to "Esta es una Notificacion - UPDATE"
    And the property "tas_def_subject_message" of "properties" is set to "UPDATE Titulo de Notificacion 1"
    And the property "tas_calendar" of "properties" is set to "14606161052f50839307899033145440"
    And the property "tas_def_message_type" of "properties" is set to "text"
    And the property "tas_def_message_template" of "properties" is set to "test activity.html"
    
    
Scenario: Update the Definition of a Activity to return to baseline of the task1
    Given PUT this data:
    """
        {
          "definition": {},
          "properties": 
        {
            "tas_type": "NORMAL",
            "tas_duration": 1,
            "tas_type_day": "",
            "tas_timeunit": "DAYS",
            "tas_priority_variable": "",
            "tas_assign_type": "BALANCED",
            "tas_assign_variable": "@@SYS_NEXT_USER_TO_BE_ASSIGNED",
            "tas_group_variable": "",
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
            "tas_calendar": "",
            "tas_def_message_type": "text",
            "tas_def_message_template": "alert_message.html"
        }

    }
        """
    And I request "project/59534741653502b6d1820d6012095837/activity/28629650453502b70b7f3a8051740006"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
        
    
Scenario: Get a activity (Verification of initial values) Task1
    Given I request "project/59534741653502b6d1820d6012095837/activity/28629650453502b70b7f3a8051740006"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the property "tas_type" of "properties" is set to "NORMAL"
    And the property "tas_duration" of "properties" is set to "1"
    And the property "tas_type_day" of "properties" is set to ""
    And the property "tas_timeunit" of "properties" is set to "DAYS"
    And the property "tas_priority_variable" of "properties" is set to ""    
    And the property "tas_assign_type" of "properties" is set to "BALANCED"    
    And the property "tas_assign_variable" of "properties" is set to "@@SYS_NEXT_USER_TO_BE_ASSIGNED"    
    And the property "tas_group_variable" of "properties" is set to ""
    And the property "tas_transfer_fly" of "properties" is set to "FALSE"
    And the property "tas_send_last_email" of "properties" is set to "FALSE"
    And the property "tas_derivation_screen_tpl" of "properties" is set to ""
    And the property "tas_selfservice_timeout" of "properties" is set to "0"
    And the property "tas_selfservice_time" of "properties" is set to ""
    And the property "tas_selfservice_time_unit" of "properties" is set to ""
    And the property "tas_selfservice_trigger_uid" of "properties" is set to ""
    And the property "tas_title" of "properties" is set to "Task 1"
    And the property "tas_description" of "properties" is set to ""
    And the property "tas_def_title" of "properties" is set to ""
    And the property "tas_def_description" of "properties" is set to ""
    And the property "tas_calendar" of "properties" is set to ""
    And the property "tas_def_message_type" of "properties" is set to "text"
    And the property "tas_def_message_template" of "properties" is set to "alert_message.html"