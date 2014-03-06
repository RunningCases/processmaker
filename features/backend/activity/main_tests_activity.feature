@ProcessMakerMichelangelo @RestAPI
Feature: Activity Resources Main Tests
  Requirements:
    a workspace with the process 251815090529619a99a2bf4013294414 already loaded
    the process name is "Test (Triggers, Activity)"
    there are three Activity Resources in the process


    Background:
        Given that I have a valid access_token

    
    Scenario Outline: Get the Properties and Definition of 3 Activities
      Given I request "project/251815090529619a99a2bf4013294414/activity/<activity>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the "definition" property type is "array"
        And the "properties" property type is "object"
        And the property "tas_title" of "properties" is set to "<tas_title>"


        Examples:

        | activity                         | tas_title |
        | 97192372152a5c78f04a794095806311 | Task1     |
        | 95655319552a5c790b69a04054667879 | Task2     |
        | 63843886052a5cc066e4c04056414372 | Task3     |
        
        
    Scenario Outline: Get the Properties of a Activity are exactly three activity
      Given I request "project/251815090529619a99a2bf4013294414/activity/<activity>?filter=properties"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the response has not a "definition" property

        Examples:

        | activity                         | tas_title |
        | 97192372152a5c78f04a794095806311 | Task1     |
        | 95655319552a5c790b69a04054667879 | Task2     |
        | 63843886052a5cc066e4c04056414372 | Task3     |
        
    

    Scenario Outline: Get the Definition of a Activity are exactly three activity
      Given I request "project/251815090529619a99a2bf4013294414/activity/<activity>?filter=definition"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the response has not a "properties" property

        Examples:

        | activity                         | tas_title |
        | 97192372152a5c78f04a794095806311 | Task1     |
        | 95655319552a5c790b69a04054667879 | Task2     |
        | 63843886052a5cc066e4c04056414372 | Task3     |
        


    Scenario Outline: Update the Definition of a Activity and the check if the values had changed
      Given PUT this data:
        """
        {
          "definition": {},
          "properties": 
            {
            "tas_title": "<tas_title>",
            "tas_description": "<tas_description>",
            "tas_priority_variable": "<tas_priority_variable>",
            "tas_derivation_screen_tpl": "<tas_derivation_screen_tpl>",
            "tas_assign_type" : "<tas_assign_type>",
            "tas_assign_variable": "<tas_assign_variable>",
            "tas_group_variable": "<tas_group_variable>",
            "tas_selfservice_timeout": "<tas_selfservice_timeout>",
            "tas_selfservice_time": "<tas_selfservice_time>",
            "tas_selfservice_time_unit" : "<tas_selfservice_time_unit>",
            "tas_selfservice_trigger_uid" : "<tas_selfservice_trigger_uid>",
            "tas_transfer_fly": "<tas_transfer_fly>",
            "tas_duration" : "<tas_duration>",
            "tas_timeunit" : "<tas_timeunit>",
            "tas_type_day": "<tas_type_day>",
            "tas_calendar": "<tas_calendar>",
            "tas_type": "<tas_type>",
            "tas_def_title": "<tas_def_title>",
            "tas_def_description": "<tas_def_description>",
            "tas_def_subject_message": "<tas_def_subject_message>",
            "tas_def_message_type": "<tas_def_message_type>",
            "tas_def_message": "<tas_def_message>",
            "tas_def_message_template": "<tas_def_message_template>"
            }
        }
        """
        And I request "project/251815090529619a99a2bf4013294414/activity/<activity>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        


        Examples:

        | test_description | activity                         | tas_title               | tas_description    | tas_priority_variable | tas_derivation_screen_tpl | tas_assign_type | tas_assign_variable| tas_group_variable | tas_selfservice_timeout | tas_selfservice_time | tas_selfservice_time_unit | tas_selfservice_trigger_uid | tas_transfer_fly | tas_duration | tas_timeunit | tas_type_day | tas_calendar | tas_type | tas_def_title | tas_def_description     | tas_def_subject_message         | tas_def_message_type | tas_def_message                   | tas_def_message_template |
        | Update Task1     | 97192372152a5c78f04a794095806311 | update activity task1   | update description | @@PROCESS             | template.html             | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 0                       | 4                    | HOURS                     |                             | TRUE             | 4            | HOURS        | 1            |              | NORMAL   | Case Title    | Case Description UPDATE | UPDATE Titulo de Notificacion 1 | text                 | Esta es una Notificacion - UPDATE | template.html            |
        | Update Task2     | 95655319552a5c790b69a04054667879 | update activity task2   | update description | @@PROCESS             | template.html             | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 0                       | 4                    | HOURS                     |                             | TRUE             | 4            | HOURS        | 1            |              | NORMAL   | Case Title    | Case Description UPDATE | UPDATE Titulo de Notificacion 1 | text                 | Esta es una Notificacion - UPDATE | template.html            |
        | Update Task3     | 63843886052a5cc066e4c04056414372 | update activity task3   | update description | @@PROCESS             | template.html             | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 0                       | 6                    | HOURS                     |                             | TRUE             | 4            | HOURS        | 1            |              | NORMAL   | Case Title    | Case Description UPDATE | UPDATE Titulo de Notificacion 1 | text                 | Esta es una Notificacion - UPDATE | template.html            |


    @5: TEST FOR GET ACTIVITY /-----------------------------------------------------------------------
    Scenario Outline: Get a activity
        Given I request "project/251815090529619a99a2bf4013294414/activity/<activity>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the property "tas_title" of "properties" is set to "<tas_title>"
        And the property "tas_description" of "properties" is set to "<tas_description>"
        And the property "tas_priority_variable" of "properties" is set to "<tas_priority_variable>"
        And the property "tas_derivation_screen_tpl" of "properties" is set to "<tas_derivation_screen_tpl>"
        And the property "tas_assign_type" of "properties" is set to "<tas_assign_type>"
        And the property "tas_assign_variable" of "properties" is set to "<tas_assign_variable>"
        And the property "tas_group_variable" of "properties" is set to "<tas_group_variable>"
        And the property "tas_selfservice_timeout" of "properties" is set to <tas_selfservice_timeout>
        And the property "tas_selfservice_time" of "properties" is set to "<tas_selfservice_time>"
        And the property "tas_selfservice_time_unit" of "properties" is set to "<tas_selfservice_time_unit>"
        And the property "tas_transfer_fly" of "properties" is set to "<tas_transfer_fly>"
        And the property "tas_duration" of "properties" is set to "<tas_duration>"
        And the property "tas_timeunit" of "properties" is set to "<tas_timeunit>"
        And the property "tas_type_day" of "properties" is set to "<tas_type_day>"
        And the property "tas_calendar" of "properties" is set to "<tas_calendar>"
        And the property "tas_type" of "properties" is set to "<tas_type>"
        And the property "tas_def_title" of "properties" is set to "<tas_def_title>"
        And the property "tas_def_description" of "properties" is set to "<tas_def_description>"
        And the property "tas_def_subject_message" of "properties" is set to "<tas_def_subject_message>"
        And the property "tas_def_message_type" of "properties" is set to "<tas_def_message_type>"
        And the property "tas_def_message" of "properties" is set to "<tas_def_message>"
    

        Examples:

        | activity                         | tas_title               | tas_description    | tas_priority_variable | tas_derivation_screen_tpl | tas_assign_type | tas_assign_variable| tas_group_variable | tas_selfservice_timeout | tas_selfservice_time | tas_selfservice_time_unit | tas_transfer_fly | tas_duration | tas_timeunit | tas_type_day | tas_calendar | tas_type | tas_def_title | tas_def_description     | tas_def_subject_message         | tas_def_message_type | tas_def_message                   |
        | 97192372152a5c78f04a794095806311 | update activity task1   | update description | @@PROCESS             | template.html             | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 0                       | 4                    | HOURS                     | TRUE             | 4            | HOURS        | 1            |              | NORMAL   | Case Title    | Case Description UPDATE | UPDATE Titulo de Notificacion 1 | text                 | Esta es una Notificacion - UPDATE |
        | 95655319552a5c790b69a04054667879 | update activity task2   | update description | @@PROCESS             | template.html             | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 0                       | 4                    | HOURS                     | TRUE             | 4            | HOURS        | 1            |              | NORMAL   | Case Title    | Case Description UPDATE | UPDATE Titulo de Notificacion 1 | text                 | Esta es una Notificacion - UPDATE |
        | 63843886052a5cc066e4c04056414372 | update activity task3   | update description | @@PROCESS             | template.html             | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 0                       | 6                    | HOURS                     | TRUE             | 4            | HOURS        | 1            |              | NORMAL   | Case Title    | Case Description UPDATE | UPDATE Titulo de Notificacion 1 | text                 | Esta es una Notificacion - UPDATE |


Scenario Outline: Update the Definition of a Activity and the check if the values had changed
      Given PUT this data:
        """
        {
          "definition": {},
          "properties": 
            {
            "tas_title": "<tas_title>",
            "tas_description": "<tas_description>",
            "tas_priority_variable": "<tas_priority_variable>",
            "tas_derivation_screen_tpl": "<tas_derivation_screen_tpl>",
            "tas_assign_type" : "<tas_assign_type>",
            "tas_assign_variable": "<tas_assign_variable>",
            "tas_group_variable": "<tas_group_variable>",
            "tas_selfservice_timeout": "<tas_selfservice_timeout>",
            "tas_selfservice_time": "<tas_selfservice_time>",
            "tas_selfservice_time_unit" : "<tas_selfservice_time_unit>",
            "tas_selfservice_trigger_uid" : "<tas_selfservice_trigger_uid>",
            "tas_transfer_fly": "<tas_transfer_fly>",
            "tas_duration" : "<tas_duration>",
            "tas_timeunit" : "<tas_timeunit>",
            "tas_type_day": "<tas_type_day>",
            "tas_calendar": "<tas_calendar>",
            "tas_type": "<tas_type>",
            "tas_def_title": "<tas_def_title>",
            "tas_def_description": "<tas_def_description>",
            "tas_def_subject_message": "<tas_def_subject_message>",
            "tas_def_message_type": "<tas_def_message_type>",
            "tas_def_message": "<tas_def_message>",
            "tas_def_message_template": "<tas_def_message_template>"
            }
        }
        """
        And I request "project/251815090529619a99a2bf4013294414/activity/<activity>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        


        Examples:

        | activity                         | tas_title | tas_description  | tas_priority_variable          | tas_derivation_screen_tpl | tas_assign_type | tas_assign_variable| tas_group_variable | tas_selfservice_timeout | tas_selfservice_time | tas_selfservice_time_unit | tas_selfservice_trigger_uid | tas_transfer_fly | tas_duration | tas_timeunit | tas_type_day | tas_calendar | tas_type | tas_def_title | tas_def_description | tas_def_subject_message | tas_def_message_type | tas_def_message          | tas_def_message_template |
        | 97192372152a5c78f04a794095806311 | Task1     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            |
        | 95655319552a5c790b69a04054667879 | Task2     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            |
        | 63843886052a5cc066e4c04056414372 | Task3     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            |