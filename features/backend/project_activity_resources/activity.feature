@ProcessMakerMichelangelo @RestAPI
Feature: Activity Resources

    Background:
        Given that I have a valid access_token

    @1: TEST FOR GET PROPERTIES & DEFINITION OF A ACTIVITY /-----------------------------------------------------------------------
    Scenario Outline: Get an activity
        Given I request "project/<project>/activity/<activity>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the "definition" property type is "array"
        And the "properties" property type is "array"
        And that "tas_title" is set to "<tas_title>"

        Examples:

        | project                          | activity                         | tas_title |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095806311 | Task 1    |
        
        
    @2: TEST FOR GET PROPERTIES ACTIVITY /---------------------------------------------------------
    Scenario Outline: Get properties of activity
        Given I request "project/<project>/activity/<activity>?filter=properties"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the response has not a "definition" property

        Examples:

        | project                          | activity                         |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095806311 |
        
    

    @3: TEST FOR GET DEFINITION ACTIVITY /---------------------------------------------------------
    Scenario Outline: Get definition of activity
        Given I request "project/<project>/activity/<activity>?filter=definition"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the response has not a "properties" property

        Examples:

        | project                          | activity                         |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095806311 |
        


    @4: TEST FOR PUT DEFINITION ACTIVITY /---------------------------------------------------------
    Scenario Outline: Put property of activity
        Given PUT this data:
        """
            {

                "definition": {},
                "properties": 
                {
                    "tas_title": "<tas_title>",
                    "tas_description": "<tas_description>",
                    "tas_priority_variable": "@@VAR_PRIORITY",
                    "tas_derivation_screen_tpl": "template.html",
                    "tas_start": <tas_start>,
                    "tas_assign_type" : "<tas_assign_type>",
                    "tas_assign_variable": "@@USER_LOGGED",
                    "tas_group_variable": "@@GROUP_UID",
                    "tas_selfservice_timeout": "<tas_selfservice_timeout>",
                    "tas_selfservice_time": "<tas_selfservice_time>",
                    "tas_selfservice_time_unit" : "<tas_selfservice_time_unit>",
                    "tas_selfservice_trigger_uid" : "3229227245298e1c5191f95009451434",
                    "tas_transfer_fly": <tas_transfer_fly>,
                    "tas_duration" : "<tas_duration>",
                    "tas_timeunit" : "<tas_timeunit>",
                    "tas_type_day": "<tas_type_day>",
                    "tas_calendar": "00000000000000000000000000000001",
                    "tas_type": "<tas_type>",
                    "tas_def_title": "Case Title",
                    "tas_def_description": "Case Descripction",
                    "tas_send_last_email": <tas_send_last_email>,
                    "tas_def_subject_message": "<tas_def_subject_message>",
                    "tas_def_message_type": "template",
                    "tas_def_message": "<tas_def_message>",
                    "tas_def_message_template": "template.html"
                }
            }
        """
        And I request "project/<project>/activity/<activity>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        


        Examples:

        | project                          | activity                         | tas_title       | tas_description    | tas_start | tas_assign_type | tas_selfservice_timeout | tas_selfservice_time | tas_selfservice_time_unit | tas_transfer_fly | tas_duration | tas_timeunit | tas_type_day | tas_type | tas_send_last_email | tas_def_subject_message |  tas_def_message              |
        | 251815090529619a99a2bf4013294414 | 97192372152a5c78f04a794095806311 | update activity | update description | true      | BALANCED        | 0                       | 0                    | DAYS                      | true             | 4            | DAYS         | 1            | NORMAL   | true                | Email desde tarea       |  Contenido del email          |


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
