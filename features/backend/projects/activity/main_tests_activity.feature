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
        

Scenario Outline: Get a activity (Verification of initial values)
    Given I request "project/59534741653502b6d1820d6012095837/activity/<activity>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the property "tas_type" of "properties" is set to "<tas_type>"
    And the property "tas_duration" of "properties" is set to "<tas_duration>"
    And the property "tas_type_day" of "properties" is set to "<tas_type_day>"
    And the property "tas_timeunit" of "properties" is set to "<tas_timeunit>"
    And the property "tas_priority_variable" of "properties" is set to "<tas_priority_variable>"
    And the property "tas_assign_type" of "properties" is set to "<tas_assign_type>"
    And the property "tas_assign_variable" of "properties" is set to "<tas_assign_variable>"
    And the property "tas_transfer_fly" of "properties" is set to "<tas_transfer_fly>"
    And the property "tas_send_last_email" of "properties" is set to "<tas_send_last_email>"
    And the property "tas_derivation_screen_tpl" of "properties" is set to "<tas_derivation_screen_tpl>"
    And the property "tas_selfservice_timeout" of "properties" is set to "<tas_selfservice_timeout>"
    And the property "tas_selfservice_time" of "properties" is set to "<tas_selfservice_time>"
    And the property "tas_selfservice_time_unit" of "properties" is set to "<tas_selfservice_time_unit>"
    And the property "tas_selfservice_trigger_uid" of "properties" is set to "<tas_selfservice_trigger_uid>"
    And the property "tas_title" of "properties" is set to "<tas_title>"
    And the property "tas_description" of "properties" is set to "<tas_description>"
    And the property "tas_def_title" of "properties" is set to "<tas_def_title>"
    And the property "tas_def_description" of "properties" is set to "<tas_def_description>"
    And the property "tas_def_message" of "properties" is set to "<tas_def_message>"
    And the property "tas_def_subject_message" of "properties" is set to "<tas_def_subject_message>"
    And the property "tas_calendar" of "properties" is set to "<tas_calendar>"
    And the property "tas_def_message_type" of "properties" is set to "<tas_def_message_type>"
    And the property "tas_def_message_template" of "properties" is set to "<tas_def_message_template>"
        
    Examples:

    | activity                         | tas_type | tas_duration | tas_type_day | tas_timeunit | tas_priority_variable | tas_assign_type | tas_assign_variable            | tas_transfer_fly | tas_send_last_email | tas_derivation_screen_tpl | tas_selfservice_timeout | tas_selfservice_time | tas_selfservice_time_unit | tas_selfservice_trigger_uid | tas_title | tas_description | tas_def_title | tas_def_description | tas_def_message | tas_def_subject_message | tas_calendar | tas_def_message_type | tas_def_message_template |
    | 28629650453502b70b7f3a8051740006 | NORMAL   | 1            |              | DAYS         |                       | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            | FALSE               |                           | 0                       |                      |                           |                             | Task 1    |                 |               |                     |                 |                         |              | text                 | alert_message.html       |
    | 52976670353502b71e2b0a8036043148 | NORMAL   | 1            |              | DAYS         |                       | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            | FALSE               |                           | 0                       |                      |                           |                             | Task 2    |                 |               |                     |                 |                         |              | text                 | alert_message.html       |
    | 24689389453502b73597aa5052425148 | NORMAL   | 1            |              | DAYS         |                       | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            | FALSE               |                           | 0                       |                      |                           |                             | Task 3    |                 |               |                     |                 |                         |              | text                 | alert_message.html       |


Scenario Outline: Update the Definition of a Activity and the check if the values had changed
    Given PUT this data:
    """
    {
          "definition": {},
          "properties": 
        {
            "tas_type": "<tas_type>",
            "tas_duration": "<tas_duration>",
            "tas_timeunit": "<tas_timeunit>",
            "tas_priority_variable": "<tas_priority_variable>",
            "tas_assign_type": "<tas_assign_type>",
            "tas_assign_variable": "<tas_assign_variable>",
            "tas_transfer_fly": "<tas_transfer_fly>",         
            "tas_derivation_screen_tpl": "<tas_derivation_screen_tpl>",
            "tas_selfservice_time_unit": "<tas_selfservice_time_unit>",
            "tas_selfservice_timeout": "<tas_selfservice_timeout>",
            "tas_selfservice_trigger_uid": "<tas_selfservice_trigger_uid>",
            "tas_title": "<tas_title>",
            "tas_description": "<tas_description>",
            "tas_def_title": "<tas_def_title>",
            "tas_def_description": "<tas_def_description>",
            "tas_def_message": "<tas_def_message>",
            "tas_def_subject_message": "<tas_def_subject_message>",
            "tas_calendar": "<tas_calendar>",
            "tas_def_message_type": "<tas_def_message_type>",
            "tas_def_message_template": "<tas_def_message_template>"
        }
    }
    """
    And I request "project/59534741653502b6d1820d6012095837/activity/<activity>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
        
    Examples:

    | test_description | activity                         | tas_type | tas_duration | tas_timeunit | tas_priority_variable | tas_assign_type | tas_assign_variable            | tas_transfer_fly | tas_derivation_screen_tpl | tas_selfservice_time_unit | tas_selfservice_timeout | tas_selfservice_trigger_uid | tas_title              | tas_description    | tas_def_title | tas_def_description     | tas_def_message                   | tas_def_subject_message         | tas_calendar                     | tas_def_message_type | tas_def_message_template |
    | Update Task1     | 28629650453502b70b7f3a8051740006 | ADHOC    | 2            | DAYS         | @@PROCESS             | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            | test activity.html        | HOURS                     | 1                       |                             | Task 1 Update Activity | Update Description | Case Title    | Case Description UPDATE | Esta es una Notificacion - UPDATE | UPDATE Titulo de Notificacion 1 | 14606161052f50839307899033145440 | text                 | test activity.html       |
    | Update Task2     | 52976670353502b71e2b0a8036043148 | NORMAL   | 3            | HOURS        | @@PROCESS             | BALANCED        | @@USER_LOGGED                  | TRUE             | test activity.html        | HOURS                     | 1                       |                             | Task 2 Update Activity | Update Description | Case Title    | Case Description UPDATE | Esta es una Notificacion - UPDATE | UPDATE Titulo de Notificacion 1 | 14606161052f50839307899033145440 | text                 | test activity.html       |
    | Update Task3     | 24689389453502b73597aa5052425148 | NORMAL   | 2            | HOURS        | @@PROCESS             | BALANCED        | @@USER_LOGGED                  | FALSE            | test activity.html        | HOURS                     | 1                       |                             | Task 3 Update Activity | Update Description | Case Title    | Case Description UPDATE | Esta es una Notificacion - UPDATE | UPDATE Titulo de Notificacion 1 | 14606161052f50839307899033145440 | text                 | test activity.html       |


Scenario Outline: Get a activity (Review of variables after the update)
    Given I request "project/59534741653502b6d1820d6012095837/activity/<activity>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the property "tas_type" of "properties" is set to "<tas_type>"
    And the property "tas_duration" of "properties" is set to "<tas_duration>"
    And the property "tas_timeunit" of "properties" is set to "<tas_timeunit>"
    And the property "tas_priority_variable" of "properties" is set to "<tas_priority_variable>"
    And the property "tas_assign_type" of "properties" is set to "<tas_assign_type>"
    And the property "tas_assign_variable" of "properties" is set to "<tas_assign_variable>"
    And the property "tas_transfer_fly" of "properties" is set to "<tas_transfer_fly>"
    And the property "tas_derivation_screen_tpl" of "properties" is set to "<tas_derivation_screen_tpl>"
    And the property "tas_selfservice_time_unit" of "properties" is set to "<tas_selfservice_time_unit>"
    And the property "tas_selfservice_timeout" of "properties" is set to "<tas_selfservice_timeout>"
    And the property "tas_selfservice_trigger_uid" of "properties" is set to "<tas_selfservice_trigger_uid>"
    And the property "tas_title" of "properties" is set to "<tas_title>"
    And the property "tas_description" of "properties" is set to "<tas_description>"
    And the property "tas_def_title" of "properties" is set to "<tas_def_title>"
    And the property "tas_def_description" of "properties" is set to "<tas_def_description>"
    And the property "tas_def_message" of "properties" is set to "<tas_def_message>"
    And the property "tas_def_subject_message" of "properties" is set to "<tas_def_subject_message>"
    And the property "tas_calendar" of "properties" is set to "<tas_calendar>"
    And the property "tas_def_message_type" of "properties" is set to "<tas_def_message_type>"
    And the property "tas_def_message_template" of "properties" is set to "<tas_def_message_template>"
    
    Examples:

    | activity                         | tas_type | tas_duration | tas_timeunit | tas_priority_variable | tas_assign_type | tas_assign_variable            | tas_transfer_fly | tas_derivation_screen_tpl | tas_selfservice_time_unit | tas_selfservice_timeout | tas_selfservice_trigger_uid | tas_title              | tas_description    | tas_def_title | tas_def_description     | tas_def_message                   | tas_def_subject_message         | tas_calendar                     | tas_def_message_type | tas_def_message_template |
    | 28629650453502b70b7f3a8051740006 | ADHOC    | 2            | DAYS         | @@PROCESS             | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            | test activity.html        | HOURS                     | 1                       |                             | Task 1 Update Activity | Update Description | Case Title    | Case Description UPDATE | Esta es una Notificacion - UPDATE | UPDATE Titulo de Notificacion 1 | 14606161052f50839307899033145440 | text                 | test activity.html       |
    | 52976670353502b71e2b0a8036043148 | NORMAL   | 3            | HOURS        | @@PROCESS             | BALANCED        | @@USER_LOGGED                  | TRUE             | test activity.html        | HOURS                     | 1                       |                             | Task 2 Update Activity | Update Description | Case Title    | Case Description UPDATE | Esta es una Notificacion - UPDATE | UPDATE Titulo de Notificacion 1 | 14606161052f50839307899033145440 | text                 | test activity.html       |
    | 24689389453502b73597aa5052425148 | NORMAL   | 2            | HOURS        | @@PROCESS             | BALANCED        | @@USER_LOGGED                  | FALSE            | test activity.html        | HOURS                     | 1                       |                             | Task 3 Update Activity | Update Description | Case Title    | Case Description UPDATE | Esta es una Notificacion - UPDATE | UPDATE Titulo de Notificacion 1 | 14606161052f50839307899033145440 | text                 | test activity.html       |


Scenario Outline: Update the Definition of a Activity to return to baseline
    Given PUT this data:
    """
        {
          "definition": {},
          "properties": 
        {
            "tas_type": "<tas_type>",
            "tas_duration": "<tas_duration>",
            "tas_timeunit": "<tas_timeunit>",
            "tas_priority_variable": "<tas_priority_variable>",
            "tas_assign_type": "<tas_assign_type>",
            "tas_assign_variable": "<tas_assign_variable>",
            "tas_transfer_fly": "<tas_transfer_fly>",         
            "tas_derivation_screen_tpl": "<tas_derivation_screen_tpl>",
            "tas_selfservice_time_unit": "<tas_selfservice_time_unit>",
            "tas_selfservice_timeout": "<tas_selfservice_timeout>",
            "tas_selfservice_trigger_uid": "<tas_selfservice_trigger_uid>",
            "tas_title": "<tas_title>",
            "tas_description": "<tas_description>",
            "tas_def_title": "<tas_def_title>",
            "tas_def_description": "<tas_def_description>",
            "tas_def_message": "<tas_def_message>",
            "tas_def_subject_message": "<tas_def_subject_message>",
            "tas_calendar": "<tas_calendar>",
            "tas_def_message_type": "<tas_def_message_type>",
            "tas_def_message_template": "<tas_def_message_template>"
        }
    }
        """
    And I request "project/59534741653502b6d1820d6012095837/activity/<activity>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
        
    Examples:

    | activity                         | tas_type | tas_duration | tas_timeunit | tas_priority_variable | tas_assign_type | tas_assign_variable            | tas_transfer_fly | tas_derivation_screen_tpl | tas_selfservice_timeout | tas_selfservice_time | tas_selfservice_time_unit | tas_selfservice_trigger_uid | tas_title | tas_description | tas_def_title | tas_def_description | tas_def_message | tas_def_subject_message | tas_calendar | tas_def_message_type | tas_def_message_template |
    | 28629650453502b70b7f3a8051740006 | NORMAL   | 1            | DAYS         |                       | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            |                           | 0                       |                      |                           |                             | Task 1    |                 |               |                     |                 |                         |              | text                 | alert_message.html       |
    | 52976670353502b71e2b0a8036043148 | NORMAL   | 1            | DAYS         |                       | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            |                           | 0                       |                      |                           |                             | Task 2    |                 |               |                     |                 |                         |              | text                 | alert_message.html       |
    | 24689389453502b73597aa5052425148 | NORMAL   | 1            | DAYS         |                       | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            |                           | 0                       |                      |                           |                             | Task 3    |                 |               |                     |                 |                         |              | text                 | alert_message.html       |


Scenario Outline: Get a activity (Verification of initial values)
    Given I request "project/59534741653502b6d1820d6012095837/activity/<activity>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the property "tas_type" of "properties" is set to "<tas_type>"
    And the property "tas_duration" of "properties" is set to "<tas_duration>"
    And the property "tas_type_day" of "properties" is set to "<tas_type_day>"
    And the property "tas_timeunit" of "properties" is set to "<tas_timeunit>"
    And the property "tas_priority_variable" of "properties" is set to "<tas_priority_variable>"
    And the property "tas_assign_type" of "properties" is set to "<tas_assign_type>"
    And the property "tas_assign_variable" of "properties" is set to "<tas_assign_variable>"
    And the property "tas_transfer_fly" of "properties" is set to "<tas_transfer_fly>"
    And the property "tas_send_last_email" of "properties" is set to "<tas_send_last_email>"
    And the property "tas_derivation_screen_tpl" of "properties" is set to "<tas_derivation_screen_tpl>"
    And the property "tas_selfservice_timeout" of "properties" is set to "<tas_selfservice_timeout>"
    And the property "tas_selfservice_time" of "properties" is set to "<tas_selfservice_time>"
    And the property "tas_selfservice_time_unit" of "properties" is set to "<tas_selfservice_time_unit>"
    And the property "tas_selfservice_trigger_uid" of "properties" is set to "<tas_selfservice_trigger_uid>"
    And the property "tas_title" of "properties" is set to "<tas_title>"
    And the property "tas_description" of "properties" is set to "<tas_description>"
    And the property "tas_def_title" of "properties" is set to "<tas_def_title>"
    And the property "tas_def_description" of "properties" is set to "<tas_def_description>"
    And the property "tas_def_message" of "properties" is set to "<tas_def_message>"
    And the property "tas_def_subject_message" of "properties" is set to "<tas_def_subject_message>"
    And the property "tas_calendar" of "properties" is set to "<tas_calendar>"
    And the property "tas_def_message_type" of "properties" is set to "<tas_def_message_type>"
    And the property "tas_def_message_template" of "properties" is set to "<tas_def_message_template>"
        
    Examples:

    | activity                         | tas_type | tas_duration | tas_type_day | tas_timeunit | tas_priority_variable | tas_assign_type | tas_assign_variable            | tas_transfer_fly | tas_send_last_email | tas_derivation_screen_tpl | tas_selfservice_timeout | tas_selfservice_time | tas_selfservice_time_unit | tas_selfservice_trigger_uid | tas_title | tas_description | tas_def_title | tas_def_description | tas_def_message | tas_def_subject_message | tas_calendar | tas_def_message_type | tas_def_message_template |
    | 28629650453502b70b7f3a8051740006 | NORMAL   | 1            |              | DAYS         |                       | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            | FALSE               |                           | 0                       |                      |                           |                             | Task 1    |                 |               |                     |                 |                         |              | text                 | alert_message.html       |
    | 52976670353502b71e2b0a8036043148 | NORMAL   | 1            |              | DAYS         |                       | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            | FALSE               |                           | 0                       |                      |                           |                             | Task 2    |                 |               |                     |                 |                         |              | text                 | alert_message.html       |
    | 24689389453502b73597aa5052425148 | NORMAL   | 1            |              | DAYS         |                       | BALANCED        | @@SYS_NEXT_USER_TO_BE_ASSIGNED | FALSE            | FALSE               |                           | 0                       |                      |                           |                             | Task 3    |                 |               |                     |                 |                         |              | text                 | alert_message.html       |