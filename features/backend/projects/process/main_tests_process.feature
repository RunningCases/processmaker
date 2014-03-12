@ProcessMakerMichelangelo @RestAPI
Feature: Process of a Project Resources
    Requirements:
        a workspace with the process 79409754952f8f5110c4342001470580 ("Test Process 2") and there are two activities
        and workspace with the process 58773281752f50297d6bf00047802053 ("Test Process 1") and there are two activities, in the process already loaded
        

    Background:
        Given that I have a valid access_token

    Scenario Outline: Get a single Process
        Given that I want to get a resource with the key "obj_uid" stored in session array
        And I request "project/<project>/process"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "pro_title" is set to "<pro_title>"
        And that "pro_description" is set to "<pro_description>"
        And that "pro_parent" is set to "<pro_parent>"
        And that "pro_time" is set to "<pro_time>"
        And that "pro_timeunit" is set to "<pro_timeunit>"
        And that "pro_status" is set to "<pro_status>"
        And that "pro_type_day" is set to "<pro_type_day>"
        And that "pro_type" is set to "<pro_type>"
        And that "pro_assignment" is set to "<pro_assignment>"
        And that "pro_show_map" is set to "<pro_show_map>"
        And that "pro_show_message" is set to "<pro_show_message>"
        And that "pro_subprocess" is set to "<pro_subprocess>"
        And that "pro_tri_deleted" is set to "<pro_tri_deleted>"
        And that "pro_tri_canceled" is set to "<pro_tri_canceled>"
        And that "pro_tri_paused" is set to "<pro_tri_paused>"
        And that "pro_tri_reassigned" is set to "<pro_tri_reassigned>"
        And that "pro_show_delegate" is set to "<pro_show_delegate>"
        And that "pro_show_dynaform" is set to "<pro_show_dynaform>"
        And that "pro_category" is set to "<pro_category>"
        And that "pro_sub_category" is set to "<pro_sub_category>"
        And that "pro_industry" is set to "<pro_industry>"
        And that "pro_update_date" is set to "<pro_update_date>"
        And that "pro_create_date" is set to "<pro_create_date>"
        And that "pro_create_user" is set to "<pro_create_user>"
        And that "pro_debug" is set to "<pro_debug>"
        And that "pro_derivation_screen_tpl" is set to "<pro_derivation_screen_tpl>"
        And that "pro_summary_dynaform" is set to "<pro_summary_dynaform>"
        And that "pro_calendar" is set to "<pro_calendar>"
        

        Examples:
        | project                          | pro_title      | pro_description | pro_parent                       | pro_time | pro_timeunit | pro_status | pro_type_day | pro_type | pro_assignment | pro_show_map | pro_show_message | pro_subprocess | pro_tri_deleted | pro_tri_canceled | pro_tri_paused | pro_tri_reassigned | pro_show_delegate | pro_show_dynaform | pro_category | pro_sub_category | pro_industry | pro_update_date | pro_create_date     | pro_create_user                  | pro_debug | pro_derivation_screen_tpl | pro_summary_dynaform | pro_calendar |
        | 79409754952f8f5110c4342001470580 | Test Process 2 |                 | 79409754952f8f5110c4342001470580 | 1        | DAYS         | ACTIVE     |              | NORMAL   | 0              | 0            | 0                | 0              |                 |                  |                |                    | 0                 | 0                 |              |                  | 0            | null            | 2014-02-10 10:49:37 | 00000000000000000000000000000001 | 0         |                           |                      |              | 
        | 58773281752f50297d6bf00047802053 | Test Process 1 |                 | 58773281752f50297d6bf00047802053 | 1        | DAYS         | ACTIVE     |              | NORMAL   | 0              | 0            | 0                | 0              |                 |                  |                |                    | 0                 | 0                 |              |                  | 0            | null            | 2014-02-07 10:58:15 | 00000000000000000000000000000001 | 0         |                           |                      |              | 


    Scenario Outline: Update Process
        Given PUT this data:
        """
        {
         "pro_title"                : "<pro_title>",
         "pro_description"          : "<pro_description>",
         "pro_parent"               : "<pro_parent>",
         "pro_time"                 : "<pro_time>",
         "pro_timeunit"             : "<pro_timeunit>",
         "pro_status"               : "<pro_status>",
         "pro_type_day"             : "<pro_type_day>",
         "pro_type"                 : "<pro_type>",
         "pro_assignment"           : "<pro_assignment>",
         "pro_show_map"             : "<pro_show_map>",
         "pro_show_message"         : "<pro_show_message>",
         "pro_subprocess"           : "<pro_subprocess>",
         "pro_tri_deleted"          : "<pro_tri_deleted>",
         "pro_tri_canceled"         : "<pro_tri_canceled>",
         "pro_tri_paused"           : "<pro_tri_paused>",
         "pro_tri_reassigned"       : "<pro_tri_reassigned>",
         "pro_show_delegate"        : "<pro_show_delegate>",
         "pro_show_dynaform"        : "<pro_show_dynaform>",
         "pro_category"             : "<pro_category>",
         "pro_sub_category"         : "<pro_sub_category>",
         "pro_industry"             : "<pro_industry>",
         "pro_update_date"          : "<pro_update_date>",
         "pro_create_date"          : "<pro_create_date>",
         "pro_create_user"          : "<pro_create_user>",
         "pro_debug"                : "<pro_debug>",
         "pro_derivation_screen_tpl": "<pro_derivation_screen_tpl>",
         "pro_summary_dynaform"     : "<pro_summary_dynaform>",
         "pro_calendar"             : "<pro_calendar>"
        }
        """
        And I request "project/<project>/process"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | project                          | pro_title             | pro_description      | pro_parent                       | pro_time | pro_timeunit | pro_status | pro_type_day | pro_type | pro_assignment | pro_show_map | pro_show_message | pro_subprocess | pro_tri_deleted                  | pro_tri_canceled                 | pro_tri_paused                   | pro_tri_reassigned               | pro_show_delegate | pro_show_dynaform | pro_category                     | pro_sub_category | pro_industry | pro_update_date     | pro_create_date     | pro_create_user                  | pro_debug | pro_derivation_screen_tpl | pro_summary_dynaform             | pro_calendar                     |
        | 79409754952f8f5110c4342001470580 | Update Test Process 1 | Update Process - PUT | 79409754952f8f5110c4342001470580 | 1        | DAYS         | INACTIVE   |              | NORMAL   | 1              | 0            | 0                | 0              |                                  |                                  |                                  |                                  | 0                 | 0                 |                                  |                  | 0            | 2014-01-10 09:43:36 | 2015-12-09 09:43:36 | 00000000000000000000000000000001 | 0         |                           |                                  |                                  | 
        | 58773281752f50297d6bf00047802053 | Update Test Process 2 | Update Process - PUT | 58773281752f50297d6bf00047802053 | 1        | DAYS         | ACTIVE     |              | NORMAL   | 0              | 1            | 1                | 1              | 69112537052f503b53142c2026229055 | 30169571352f50349539aa7005920345 | 45413889552f5037587e5a4073302257 | 23429991752f5035c3eab21091451118 | 1                 | 0                 | 77488943552f502c3d7f649000082980 |                  | 0            | 2014-01-10 09:43:36 | 2014-02-07 10:58:15 | 51049032352d56710347233042615067 | 1         | tplScreen.html            | 94906672952f5058bf3f0f8012616448 | 99159704252f501c63f8c58025859967 | 


    Scenario Outline: Get a single Process
        Given that I want to get a resource with the key "obj_uid" stored in session array
        And I request "project/<project>/process"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "pro_title" is set to "<pro_title>"
        And that "pro_description" is set to "<pro_description>"
        And that "pro_parent" is set to "<pro_parent>"
        And that "pro_time" is set to "<pro_time>"
        And that "pro_timeunit" is set to "<pro_timeunit>"
        And that "pro_status" is set to "<pro_status>"
        And that "pro_type_day" is set to "<pro_type_day>"
        And that "pro_type" is set to "<pro_type>"
        And that "pro_assignment" is set to "<pro_assignment>"
        And that "pro_show_map" is set to "<pro_show_map>"
        And that "pro_show_message" is set to "<pro_show_message>"
        And that "pro_subprocess" is set to "<pro_subprocess>"
        And that "pro_tri_deleted" is set to "<pro_tri_deleted>"
        And that "pro_tri_canceled" is set to "<pro_tri_canceled>"
        And that "pro_tri_paused" is set to "<pro_tri_paused>"
        And that "pro_tri_reassigned" is set to "<pro_tri_reassigned>"
        And that "pro_show_delegate" is set to "<pro_show_delegate>"
        And that "pro_show_dynaform" is set to "<pro_show_dynaform>"
        And that "pro_category" is set to "<pro_category>"
        And that "pro_sub_category" is set to "<pro_sub_category>"
        And that "pro_industry" is set to "<pro_industry>"
        And that "pro_update_date" is set to "<pro_update_date>"
        And that "pro_create_date" is set to "<pro_create_date>"
        And that "pro_create_user" is set to "<pro_create_user>"
        And that "pro_debug" is set to "<pro_debug>"
        And that "pro_derivation_screen_tpl" is set to "<pro_derivation_screen_tpl>"
        And that "pro_summary_dynaform" is set to "<pro_summary_dynaform>"
        And that "pro_calendar" is set to "<pro_calendar>"


        Examples:
        | project                          | pro_title                | pro_description      | pro_parent                       | pro_time | pro_timeunit | pro_status | pro_type_day | pro_type | pro_assignment | pro_show_map | pro_show_message | pro_subprocess | pro_tri_deleted                  | pro_tri_canceled                 | pro_tri_paused                   | pro_tri_reassigned               | pro_show_delegate | pro_show_dynaform | pro_category                     | pro_sub_category | pro_industry | pro_update_date     | pro_create_date     | pro_create_user                  | pro_debug | pro_derivation_screen_tpl | pro_summary_dynaform             | pro_calendar                     |
        | 79409754952f8f5110c4342001470580 | Update Sample Project #1 | Update Process - PUT | 79409754952f8f5110c4342001470580 | 1        | DAYS         | INACTIVE   |              | NORMAL   | 1              | 0            | 0                | 0              |                                  |                                  |                                  |                                  | 0                 | 0                 |                                  |                  | 0            | 2014-01-10 09:43:36 | 2015-12-09 09:43:36 | 00000000000000000000000000000001 | 0         |                           |                                  |                                  | 
        | 58773281752f50297d6bf00047802053 | Update Test Process      | Update Process - PUT | 58773281752f50297d6bf00047802053 | 1        | DAYS         | ACTIVE     |              | NORMAL   | 0              | 1            | 1                | 1              | 69112537052f503b53142c2026229055 | 30169571352f50349539aa7005920345 | 45413889552f5037587e5a4073302257 | 23429991752f5035c3eab21091451118 | 1                 | 0                 | 77488943552f502c3d7f649000082980 |                  | 0            | 2014-01-10 09:43:36 | 2014-02-07 10:58:15 | 51049032352d56710347233042615067 | 1         | tplScreen.html            | 94906672952f5058bf3f0f8012616448 | 14606161052f50839307899033145440 | 


    Scenario Outline: Update Process
        Given PUT this data:
        """
        {
         "pro_title"                : "<pro_title>",
         "pro_description"          : "<pro_description>",
         "pro_parent"               : "<pro_parent>",
         "pro_time"                 : "<pro_time>",
         "pro_timeunit"             : "<pro_timeunit>",
         "pro_status"               : "<pro_status>",
         "pro_type_day"             : "<pro_type_day>",
         "pro_type"                 : "<pro_type>",
         "pro_assignment"           : "<pro_assignment>",
         "pro_show_map"             : "<pro_show_map>",
         "pro_show_message"         : "<pro_show_message>",
         "pro_subprocess"           : "<pro_subprocess>",
         "pro_tri_deleted"          : "<pro_tri_deleted>",
         "pro_tri_canceled"         : "<pro_tri_canceled>",
         "pro_tri_paused"           : "<pro_tri_paused>",
         "pro_tri_reassigned"       : "<pro_tri_reassigned>",
         "pro_show_delegate"        : "<pro_show_delegate>",
         "pro_show_dynaform"        : "<pro_show_dynaform>",
         "pro_category"             : "<pro_category>",
         "pro_sub_category"         : "<pro_sub_category>",
         "pro_industry"             : "<pro_industry>",
         "pro_update_date"          : "<pro_update_date>",
         "pro_create_date"          : "<pro_create_date>",
         "pro_create_user"          : "<pro_create_user>",
         "pro_debug"                : "<pro_debug>",
         "pro_derivation_screen_tpl": "<pro_derivation_screen_tpl>",
         "pro_summary_dynaform"     : "<pro_summary_dynaform>",
         "pro_calendar"             : "<pro_calendar>"
        }
        """
        And I request "project/<project>/process"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | project                          | pro_title      | pro_description | pro_parent                       | pro_time | pro_timeunit | pro_status | pro_type_day | pro_type | pro_assignment | pro_show_map | pro_show_message | pro_subprocess | pro_tri_deleted | pro_tri_canceled | pro_tri_paused | pro_tri_reassigned | pro_show_delegate | pro_show_dynaform | pro_category | pro_sub_category | pro_industry | pro_update_date     | pro_create_date     | pro_create_user                  | pro_debug | pro_derivation_screen_tpl | pro_summary_dynaform | pro_calendar |
        | 79409754952f8f5110c4342001470580 | Test Process 2 |                 | 79409754952f8f5110c4342001470580 | 1        | DAYS         | ACTIVE     |              | NORMAL   | 0              | 0            | 0                | 0              |                 |                  |                |                    | 0                 | 0                 |              |                  | 0            | 2014-02-10 10:49:37 | 2014-02-10 10:49:37 | 00000000000000000000000000000001 | 0         |                           |                      |              | 
        | 58773281752f50297d6bf00047802053 | Test Process 1 |                 | 58773281752f50297d6bf00047802053 | 1        | DAYS         | ACTIVE     |              | NORMAL   | 0              | 0            | 0                | 0              |                 |                  |                |                    | 0                 | 0                 |              |                  | 0            | 2014-02-10 10:49:37 | 2014-02-07 10:58:15 | 00000000000000000000000000000001 | 0         |                           |                      |              | 


        Scenario Outline: Get a single Process
        Given that I want to get a resource with the key "obj_uid" stored in session array
        And I request "project/<project>/process"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "pro_title" is set to "<pro_title>"
        And that "pro_description" is set to "<pro_description>"
        And that "pro_parent" is set to "<pro_parent>"
        And that "pro_time" is set to "<pro_time>"
        And that "pro_timeunit" is set to "<pro_timeunit>"
        And that "pro_status" is set to "<pro_status>"
        And that "pro_type_day" is set to "<pro_type_day>"
        And that "pro_type" is set to "<pro_type>"
        And that "pro_assignment" is set to "<pro_assignment>"
        And that "pro_show_map" is set to "<pro_show_map>"
        And that "pro_show_message" is set to "<pro_show_message>"
        And that "pro_subprocess" is set to "<pro_subprocess>"
        And that "pro_tri_deleted" is set to "<pro_tri_deleted>"
        And that "pro_tri_canceled" is set to "<pro_tri_canceled>"
        And that "pro_tri_paused" is set to "<pro_tri_paused>"
        And that "pro_tri_reassigned" is set to "<pro_tri_reassigned>"
        And that "pro_show_delegate" is set to "<pro_show_delegate>"
        And that "pro_show_dynaform" is set to "<pro_show_dynaform>"
        And that "pro_category" is set to "<pro_category>"
        And that "pro_sub_category" is set to "<pro_sub_category>"
        And that "pro_industry" is set to "<pro_industry>"
        And that "pro_update_date" is set to "<pro_update_date>"
        And that "pro_create_date" is set to "<pro_create_date>"
        And that "pro_create_user" is set to "<pro_create_user>"
        And that "pro_debug" is set to "<pro_debug>"
        And that "pro_derivation_screen_tpl" is set to "<pro_derivation_screen_tpl>"
        And that "pro_summary_dynaform" is set to "<pro_summary_dynaform>"
        And that "pro_calendar" is set to "<pro_calendar>"
        

        Examples:
        | project                          | pro_title      | pro_description | pro_parent                       | pro_time | pro_timeunit | pro_status | pro_type_day | pro_type | pro_assignment | pro_show_map | pro_show_message | pro_subprocess | pro_tri_deleted | pro_tri_canceled | pro_tri_paused | pro_tri_reassigned | pro_show_delegate | pro_show_dynaform | pro_category | pro_sub_category | pro_industry | pro_update_date | pro_create_date     | pro_create_user                  | pro_debug | pro_derivation_screen_tpl | pro_summary_dynaform | pro_calendar |
        | 79409754952f8f5110c4342001470580 | Test Process 2 |                 | 79409754952f8f5110c4342001470580 | 1        | DAYS         | ACTIVE     |              | NORMAL   | 0              | 0            | 0                | 0              |                 |                  |                |                    | 0                 | 0                 |              |                  | 0            | null            | 2014-02-10 10:49:37 | 00000000000000000000000000000001 | 0         |                           |                      |              | 
        | 58773281752f50297d6bf00047802053 | Test Process 1 |                 | 58773281752f50297d6bf00047802053 | 1        | DAYS         | ACTIVE     |              | NORMAL   | 0              | 0            | 0                | 0              |                 |                  |                |                    | 0                 | 0                 |              |                  | 0            | null            | 2014-02-07 10:58:15 | 00000000000000000000000000000001 | 0         |                           |                      |              |