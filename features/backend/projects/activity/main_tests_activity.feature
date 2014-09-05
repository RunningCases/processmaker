@ProcessMakerMichelangelo @RestAPI
Feature: Activity Resources Main Tests
  Requirements:
    a workspace with the process 59534741653502b6d1820d6012095837 - "Test Activity" already loaded
    and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded
    there are three Activity Resources in the process

Background:
    Given that I have a valid access_token

    
Scenario Outline: Get the Properties and Definition of 3 Activities
    Given I request "project/<project>/activity/<activity>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the "definition" property type is "array"
    And the "properties" property type is "object"
    And the property "tas_title" of "properties" is set to "<tas_title>"

    Examples:

    | Description                          | activity                         | project                          | tas_title         |
    | Get of process Test Activity Task 1  | 28629650453502b70b7f3a8051740006 | 59534741653502b6d1820d6012095837 | Task 1            |
    | Get of process Test Activity Task 2  | 52976670353502b71e2b0a8036043148 | 59534741653502b6d1820d6012095837 | Task 2            |
    | Get of process Test Activity Task 3  | 24689389453502b73597aa5052425148 | 59534741653502b6d1820d6012095837 | Task 3            |
    | Get of process Process Complete BPMN | 6274755055368eed1116388064384542 | 1455892245368ebeb11c1a5001393784 | Dynaform          |
    | Get of process Process Complete BPMN | 4790702485368efad167477011123879 | 1455892245368ebeb11c1a5001393784 | Grids             |
    | Get of process Process Complete BPMN | 2072984565368efc137a394001073529 | 1455892245368ebeb11c1a5001393784 | Dynaform and Grid |
        
        
Scenario Outline: Get the Properties of a Activity are exactly three activity
    Given I request "project/<project>/activity/<activity>?filter=properties"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the response has not a "definition" property

    Examples:

    | Description                          | project                          | activity                         | tas_title         |
    | Get of process Test Activity Task 1  | 59534741653502b6d1820d6012095837 | 28629650453502b70b7f3a8051740006 | Task 1            |
    | Get of process Test Activity Task 2  | 59534741653502b6d1820d6012095837 | 52976670353502b71e2b0a8036043148 | Task 2            |
    | Get of process Test Activity Task 3  | 59534741653502b6d1820d6012095837 | 24689389453502b73597aa5052425148 | Task 3            |
    | Get of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 6274755055368eed1116388064384542 | Dynaform          |
    | Get of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 4790702485368efad167477011123879 | Grids             |
    | Get of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 2072984565368efc137a394001073529 | Dynaform and Grid |
        
    

Scenario Outline: Get the Definition of a Activity are exactly three activity
    Given I request "project/<project>/activity/<activity>?filter=definition"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the response has not a "properties" property

    Examples:

    | Description                          | project                          | activity                         | tas_title         |
    | Get of process Test Activity Task 1  | 59534741653502b6d1820d6012095837 | 28629650453502b70b7f3a8051740006 | Task 1            |
    | Get of process Test Activity Task 2  | 59534741653502b6d1820d6012095837 | 52976670353502b71e2b0a8036043148 | Task 2            |
    | Get of process Test Activity Task 3  | 59534741653502b6d1820d6012095837 | 24689389453502b73597aa5052425148 | Task 3            |
    | Get of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 6274755055368eed1116388064384542 | Dynaform          |
    | Get of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 4790702485368efad167477011123879 | Grids             |
    | Get of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 2072984565368efc137a394001073529 | Dynaform and Grid |
        

Scenario: Get a activity (Verification of initial values) "Task1" of process Test Activity
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


Scenario: Get a activity (Verification of initial values) "Task2" of process Test Activity
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


Scenario: Get a activity (Verification of initial values) "Task3" of process Test Activity
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


Scenario: Get a activity (Verification of initial values) "Task1" of process Process Complete BPMN
    Given I request "project/1455892245368ebeb11c1a5001393784/activity/6274755055368eed1116388064384542"
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
    And the property "tas_title" of "properties" is set to "Dynaform"
    And the property "tas_description" of "properties" is set to ""
    And the property "tas_def_title" of "properties" is set to ""
    And the property "tas_def_description" of "properties" is set to ""
    And the property "tas_calendar" of "properties" is set to ""
    And the property "tas_def_message_type" of "properties" is set to "text"
    And the property "tas_def_message_template" of "properties" is set to "alert_message.html"

Scenario: Update the Definition of a Activity "Task1" of process Test Activity and the check if the values had changed
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
        
    

Scenario: Get a activity (Review of variables after the update) Task1 of process Test Activity
    Given I request "project/59534741653502b6d1820d6012095837/activity/28629650453502b70b7f3a8051740006"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the property "tas_type" of "properties" is set to "ADHOC"
    And the property "tas_timeunit" of "properties" is set to "DAYS"
    And the property "tas_priority_variable" of "properties" is set to "@@PROCESS"    
    And the property "tas_assign_type" of "properties" is set to "BALANCED"    
    And the property "tas_transfer_fly" of "properties" is set to "TRUE"
    And the property "tas_send_last_email" of "properties" is set to "TRUE"
    And the property "tas_derivation_screen_tpl" of "properties" is set to "test activity.html"
    And the property "tas_selfservice_trigger_uid" of "properties" is set to ""
    And the property "tas_title" of "properties" is set to "Task 1 Update Activity"
    And the property "tas_description" of "properties" is set to "Update Description"
    And the property "tas_def_title" of "properties" is set to "Case Title"
    And the property "tas_def_description" of "properties" is set to "Case Description UPDATE"
    And the property "tas_def_message" of "properties" is set to "Esta es una Notificacion - UPDATE"
    And the property "tas_def_subject_message" of "properties" is set to "UPDATE Titulo de Notificacion 1"
    And the property "tas_def_message_type" of "properties" is set to "text"
    
    
Scenario: Update the Definition of a Activity to return to baseline of the task1 of process Test Activity
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
        
    
Scenario: Get a activity (Verification of initial values) Task1 of process Test Activity
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