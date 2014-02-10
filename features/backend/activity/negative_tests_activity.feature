@ProcessMakerMichelangelo @RestAPI
Feature: Activity Resources Main Tests
  Requirements:
    a workspace with the process 251815090529619a99a2bf4013294414 already loaded
    the process name is "Test (Triggers, Activity)"
    there are three Activity Resources in the process


    Background:
        Given that I have a valid access_token

    

@6: TEST FOR GET PROPERTIES & DEFINITION OF A ACTIVITY /-----------------------------------------------------------------------
    Scenario Outline: Get an activity
        Given I request "project/<project>/activity/<activity>"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"
        
        
        Examples:

        | project                          | activity                         | tas_title | error_code | error_message |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095845000 | Task 1    | 400        | act_uid       |
        | 251815090529619a99a2bf4013294414 |                                  | Task 1    | 404        | Not Found     |
        |                                  | 97192372152a5c78f04a794095806311 | Task 1    | 400        | prj_uid       |
  

@7: TEST FOR GET PROPERTIES ACTIVITY /---------------------------------------------------------
    Scenario Outline: Get properties of activity
        Given I request "project/<project>/activity/<activity>?filter=properties"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"
        
        Examples:

        | project                          | activity                         | error_code | error_message |
        | 251815090529619a99a2bf4013294414 |                                  | 404        | Not Found     |
        |                                  | 97192372152a5c78f04a794095806311 | 400        | prj_uid   |

@8: TEST FOR GET DEFINITION ACTIVITY /---------------------------------------------------------
    Scenario Outline: Get definition of activity
        Given I request "project/<project>/activity/<activity>"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"
        
        Examples:

        | project                          | activity                         | error_code | error_message |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095200000 | 400        | act_uid       |
        | 251815090529619a99a2bf4013294414 |                                  | 404        | Not Found     |
        |                                  | 97192372152a5c78f04a794095806311 | 400        | prj_uid       |


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
            "tas_send_last_email": "<tas_send_last_email>",
            "tas_def_subject_message": "<tas_def_subject_message>",
            "tas_def_message_type": "<tas_def_message_type>",
            "tas_def_message": "<tas_def_message>",
            "tas_def_message_template": "<tas_def_message_template>"
            }
        }
        """
        And I request "project/251815090529619a99a2bf4013294414/activity/<activity>"
        Then the response status code should be 400
        And the response status message should have the following text "<error_message>"


        Examples:

        | test_description                 | activity                         | tas_title | tas_description  | tas_priority_variable          | tas_derivation_screen_tpl | tas_start | tas_assign_type | tas_assign_variable| tas_group_variable | tas_selfservice_timeout | tas_selfservice_time | tas_selfservice_time_unit | tas_selfservice_trigger_uid | tas_transfer_fly | tas_duration | tas_timeunit | tas_type_day | tas_calendar | tas_type | tas_def_title | tas_def_description | tas_send_last_email | tas_def_subject_message | tas_def_message_type | tas_def_message          | tas_def_message_template | error_message             |
        | Invalid tas_assign_type          | 97192372152a5c78f04a794095806311 | Task1     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | INPUT           | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            | tas_assign_type           |
        | Invalid tas_selfservice_timeout  | 95655319552a5c790b69a04054667879 | Task2     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 5                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            | tas_selfservice_timeout   |
        | Invalid tas_selfservice_time     | 63843886052a5cc066e4c04056414372 | Task3     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 5.67,76              | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            | tas_selfservice_time      |
        | Invalid tas_selfservice_time_unit| 97192372152a5c78f04a794095806311 | Task1     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | YEAR                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            | tas_selfservice_time_unit |
        | Invalid tas_transfer_fly         | 95655319552a5c790b69a04054667879 | Task2     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | INPUT            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            | tas_transfer_fly          |
        | Invalid tas_duration             | 63843886052a5cc066e4c04056414372 | Task3     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2,54.98      | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            | tas_duration              |
        | Invalid tas_timeunit             | 97192372152a5c78f04a794095806311 | Task1     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | YEAR         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            | tas_timeunit              |
        | Invalid tas_type_day             | 95655319552a5c790b69a04054667879 | Task2     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 6            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            | tas_type_day              |
        | Invalid tas_type                 | 63843886052a5cc066e4c04056414372 | Task3     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | INPUT    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            | tas_type                  |
        | Invalid tas_send_last_email      | 97192372152a5c78f04a794095806311 | Task1     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | INPUT               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            | tas_send_last_email       |
        | Invalid tas_def_message_type     | 95655319552a5c790b69a04054667879 | Task2     | Case Description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| INPUT                | Esta es una Notificacion | template.html            | tas_def_message_type      |  