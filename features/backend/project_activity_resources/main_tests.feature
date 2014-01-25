@ProcessMakerMichelangelo @RestAPI
Feature: Activity Resources Main Tests
  Requirements:
    a workspace with the process 251815090529619a99a2bf4013294414 already loaded
    the process name is "Test (Triggers, Activity)"
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
        And that "tas_title" is set to "<tas_title>"

        Examples:

        | project                          | activity                         | tas_title |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095806311 | Task 1    |
        | 251815090529619a99a2bf4013294414 | 95655319552a5c790b69a04054667879 | Task 2    |
        | 251815090529619a99a2bf4013294414 | 63843886052a5cc066e4c04056414372 | Task 3    |
        
        
    Scenario Outline: Get the Properties of a Activity are exactly three activity
      Given I request "project/<project>/activity/<activity>?filter=properties"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the response has not a "definition" property

        Examples:

        | project                          | activity                         | tas_title |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095806311 | Task 1    |
        | 251815090529619a99a2bf4013294414 | 95655319552a5c790b69a04054667879 | Task 2    |
        | 251815090529619a99a2bf4013294414 | 63843886052a5cc066e4c04056414372 | Task 3    |
        
    

    Scenario Outline: Get the Definition of a Activity are exactly three activity
      Given I request "project/<project>/activity/<activity>?filter=definition"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the response has not a "properties" property

        Examples:

        | project                          | activity                         | tas_title |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095806311 | Task 1    |
        | 251815090529619a99a2bf4013294414 | 95655319552a5c790b69a04054667879 | Task 2    |
        | 251815090529619a99a2bf4013294414 | 63843886052a5cc066e4c04056414372 | Task 3    |
        


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
            "tas_start": "<tas_start>",
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
        And I request "project/<project>/activity/<activity>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        


        Examples:

        | test_description                                                                                                                     | project                          | activity                         | tas_title               | tas_description    | tas_priority_variable          | tas_derivation_screen_tpl | tas_start | tas_assign_type | tas_assign_variable| tas_group_variable | tas_selfservice_timeout | tas_selfservice_time | tas_selfservice_time_unit | tas_selfservice_trigger_uid | tas_transfer_fly | tas_duration | tas_timeunit | tas_type_day | tas_calendar | tas_type | tas_def_title | tas_def_description | tas_send_last_email | tas_def_subject_message | tas_def_message_type | tas_def_message          | tas_def_message_template |
        | Update (tas_title, tas_description), tas_assign_type=BALANCED, tas_selfservice_timeout=1, tas_selfservice_time_unit=DAYS             | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095806311 | update activity task1   | update description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            |
        |                                                                                                                                      | 251815090529619a99a2bf4013294414 | 95655319552a5c790b69a04054667879 | update activity task2   | update description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            |
        |                                                                                                                                      | 251815090529619a99a2bf4013294414 | 63843886052a5cc066e4c04056414372 | update activity task3   | update description | @@SYS_NEXT_USER_TO_BE_ASSIGNED | template.html             | TRUE      | BALANCED        | @@USER_LOGGED      | @@GROUP_UID        | 1                       | 2                    | DAYS                      |                             | FALSE            | 2            | DAYS         | 2            |              | ADHOC    | Case Title    | Case Description    | FALSE               | Titulo de Notificacion 1| template             | Esta es una Notificacion | template.html            |


    @5: TEST FOR GET ACTIVITY /-----------------------------------------------------------------------
    Scenario Outline: Get a activity
        Given I request "project/<project>/activity/<activity>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "tas_title" is set to "<tas_title>"

        Examples:

        | project                          | activity                         | tas_title |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095806311 | Task Edit |

@6: TEST FOR GET PROPERTIES & DEFINITION OF A ACTIVITY /-----------------------------------------------------------------------
    Scenario Outline: Get an activity
        Given I request "project/<project>/activity/<activity>"
        Then the response status code should be <error_code>
        And the response charset is "UTF-8"
        And the type is "object"
        
        
        Examples:

        | project                          | activity                         | tas_title | error_code |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095845000 | Task 1    | 400 |
        | 251815090529619a99a2bf4013294414 |                                  | Task 1    | 404 |
        |                                  | 97192372152a5c78f04a794095806311 | Task 1    | 400 |
  

@7: TEST FOR GET PROPERTIES ACTIVITY /---------------------------------------------------------
    Scenario Outline: Get properties of activity
        Given I request "project/<project>/activity/<activity>?filter=properties"
        Then the response status code should be <error_code>
        And the response charset is "UTF-8"
        And the type is "object"
        
        Examples:

        | project                          | activity                         | error_code |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095801000 | 400 |
        | 251815090529619a99a2bf4013294414 |                                  | 404 |
        |                                  | 97192372152a5c78f04a794095806311 | 400 |

@8: TEST FOR GET DEFINITION ACTIVITY /---------------------------------------------------------
    Scenario Outline: Get definition of activity
        Given I request "project/<project>/activity/<activity>?filter=definition"
        Then the response status code should be <error_code>
        And the response charset is "UTF-8"
        And the type is "object"
        
        Examples:

        | project                          | activity                         | error_code |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095200000 | 400 |
        | 251815090529619a99a2bf4013294414 |                                  | 404 |
        |                                  | 97192372152a5c78f04a794095806311 | 400 |
