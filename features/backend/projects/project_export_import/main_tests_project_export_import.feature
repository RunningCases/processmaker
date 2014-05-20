@ProcessMakerMichelangelo @RestAPI
Feature: Import/Export Process Main Tests
  Requirements:
    a workspace without the project 1455892245368ebeb11c1a5001393784 ("Process Complete BPMN", "Export process empty") already loaded
    there are many activities, steps, triggers, pmtables, asignee, process supervisor, process permissions, etc. in the process

Background:
    Given that I have a valid access_token


#Verificar cantidad de dynaform, output, inputs, triggers, asignacion de usuarios, etc. del proyecto "Process Complete BPMN"

Scenario: Get a List DynaForms of a Project Process Complete BPMN   
    Given I request "project/1455892245368ebeb11c1a5001393784/dynaforms"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 26 records

 Scenario: Get the Input Documents List when there are exactly zero input documents
    Given I request "project/1455892245368ebeb11c1a5001393784/input-documents"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 1 records
   
Scenario: Get the Output Documents List when there are exactly two output documents
    Given I request "project/1455892245368ebeb11c1a5001393784/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And that "out_doc_template" is set to "Ejemplo de Output Document"
    And the response has 1 records
    
Scenario: Get the Triggers List when there are exactly two triggers
    Given I request "project/1455892245368ebeb11c1a5001393784/triggers"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 3 records
        
Scenario Outline: List assignees of each activity
    Given I request "project/1455892245368ebeb11c1a5001393784/activity/<activity>/assignee"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "aas_uid" property in row 0 equals "<aas_uid>"
    And the "aas_type" property in row 0 equals "<aas_type>"

    Examples:
    | test_description                                           | project                          | activity                         | records | aas_uid                          | aas_type |
    | Verify that the activity has expected quantity of asignees | 1455892245368ebeb11c1a5001393784 | 6274755055368eed1116388064384542 | 1       | 70084316152d56749e0f393054862525 | group    |
    | Verify that the activity has expected quantity of asignees | 1455892245368ebeb11c1a5001393784 | 4790702485368efad167477011123879 | 1       | 70084316152d56749e0f393054862525 | group    |

Scenario: Get a List of current process supervisors of a project
    Given I request "project/1455892245368ebeb11c1a5001393784/process-supervisors"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 records
      
Scenario: Get a List of current Process Permissions of a project
    Given I request "project/1455892245368ebeb11c1a5001393784/process-permissions"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has 1 record

Scenario: Get a list templates folder of process files manager
    Given I request "project/1455892245368ebeb11c1a5001393784/file-manager?path=templates"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 records

Scenario: Verify that there are report tables
    Given I request "project/1455892245368ebeb11c1a5001393784/report-tables"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has 1 record

Scenario: Get the Case Trackers Objects of a Project
        And I request "project/1455892245368ebeb11c1a5001393784/case-tracker/objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 1 record

Scenario: Get a single Process
    Given that I want to get a resource with the key "obj_uid" stored in session array
    And I request "project/1455892245368ebeb11c1a5001393784/process"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "pro_title" is set to "Process Complete BPMN"
    And that "pro_description" is set to ""
    And that "pro_parent" is set to "1455892245368ebeb11c1a5001393784"
    And that "pro_time" is set to 1
    And that "pro_timeunit" is set to "DAYS"
    And that "pro_status" is set to "ACTIVE"
    And that "pro_type_day" is set to ""
    And that "pro_type" is set to "NORMAL"
    And that "pro_assignment" is set to 0
    And that "pro_show_map" is set to 0
    And that "pro_show_message" is set to 1
    And that "pro_subprocess" is set to 0
    And that "pro_tri_deleted" is set to "712197294536bea56a8b4d0014148679"
    And that "pro_tri_canceled" is set to "950769923536bea6a39c833033416052"
    And that "pro_tri_paused" is set to "350949312536bea73c53791057971272"
    And that "pro_tri_reassigned" is set to "712197294536bea56a8b4d0014148679"
    And that "pro_show_delegate" is set to 0
    And that "pro_show_dynaform" is set to 0
    And that "pro_category" is set to "4177095085330818c324501061677193"
    And that "pro_sub_category" is set to ""
    And that "pro_industry" is set to 0
    And that "pro_update_date" is set to "null"
    And that "pro_create_date" is set to "2014-05-06 10:04:27"
    And that "pro_create_user" is set to "00000000000000000000000000000001"
    And that "pro_debug" is set to 0
    And that "pro_derivation_screen_tpl" is set to ""
    And that "pro_summary_dynaform" is set to "898822326536be3a12addb0034537553"
    And that "pro_calendar" is set to "14606161052f50839307899033145440"

#Export Process

Scenario: Get for Export Project
    Given I request "project/1455892245368ebeb11c1a5001393784/export"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/xml"
    And save exported process to "/home/wendy/uploadfiles/" as "Process_Complete_BPMN"


Scenario: Delete a Project created previously in this script
    Given that I want to delete a resource with the key "prj_uid" stored in session array
    And I request "projects/1455892245368ebeb11c1a5001393784"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

#Import Process

Scenario Outline: Import a process
 	Given POST upload a project file "<project_file>" to "project/import?option=<import_option>"
 	Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "prj_uid" in session array as variable "prj_uid_<prj_uid_number>"


 	Examples:
 	| project_file                                       | import_option | prj_uid_number |
 	| /home/wendy/uploadfiles/Process_NewCreate_BPMN.pmx | create        | 1              |
    | /home/wendy/uploadfiles/Process_Complete_BPMN.pmx  | create        | 2              |
    | /home/wendy/uploadfiles/Process_Complete_BPMN.pmx  | overwrite     | 3              |
    | /home/wendy/uploadfiles/Process_Complete_BPMN.pmx  | disable       | 4              |
    | /home/wendy/uploadfiles/Process_Complete_BPMN.pmx  | keep          | 5              |
    

#Verificar cantidad de dynaform, output, inputs, triggers, asignacion de usuarios, etc.

Scenario Outline: Get a List DynaForms of a Project Process Complete BPMN   
    Given I request "project/prj_uid/dynaforms" with the key "prj_uid" stored in session array as variable "prj_uid_<prj_uid_number>"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records
    
    Examples:
    | import_option | prj_uid_number | prj_uid                          | records | 
    | create        | 1              | 601816709536cfeae7d7cd9079578104 | 4       |
    | create        | 2              | 1455892245368ebeb11c1a5001393784 | 26      |
    | overwrite     | 3              | 1455892245368ebeb11c1a5001393784 | 26      |
    | disable       | 4              | 1455892245368ebeb11c1a5001393784 | 26      |
    | keep          | 5              | 1455892245368ebeb11c1a5001393784 | 26      |

  
Scenario Outline: Get the Input Documents List when there are exactly zero input documents
    Given I request "project/prj_uid/input-documents" with the key "prj_uid" stored in session array as variable "prj_uid_<prj_uid_number>"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records

    Examples:
    | import_option | prj_uid_number | prj_uid                          | records |
    | create        | 1              | 601816709536cfeae7d7cd9079578104 | 0       |
    | create        | 2              | 1455892245368ebeb11c1a5001393784 | 1       |
    | overwrite     | 3              | 1455892245368ebeb11c1a5001393784 | 1       |
    | disable       | 4              | 1455892245368ebeb11c1a5001393784 | 1       |
    | keep          | 5              | 1455892245368ebeb11c1a5001393784 | 1       |
   

Scenario Outline: Get the Output Documents List when there are exactly two output documents
    Given I request "project/prj_uid/output-documents" with the key "prj_uid" stored in session array as variable "prj_uid_<prj_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    | import_option | prj_uid_number | prj_uid                          | records |
    | create        | 1              | 601816709536cfeae7d7cd9079578104 | 0       |
    | create        | 2              | 1455892245368ebeb11c1a5001393784 | 1       |
    | overwrite     | 3              | 1455892245368ebeb11c1a5001393784 | 1       |
    | disable       | 4              | 1455892245368ebeb11c1a5001393784 | 1       |
    | keep          | 5              | 1455892245368ebeb11c1a5001393784 | 1       |

    
Scenario Outline: Get the Triggers List when there are exactly two triggers
    Given I request "project/prj_uid/triggers" with the key "prj_uid" stored in session array as variable "prj_uid_<prj_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    | import_option | prj_uid_number | prj_uid                          | records |
    | create        | 1              | 601816709536cfeae7d7cd9079578104 | 0       |
    | create        | 2              | 1455892245368ebeb11c1a5001393784 | 3       |
    | overwrite     | 3              | 1455892245368ebeb11c1a5001393784 | 3       |
    | disable       | 4              | 1455892245368ebeb11c1a5001393784 | 3       |
    | keep          | 5              | 1455892245368ebeb11c1a5001393784 | 3       |

        
Scenario Outline: List assignees of each activity
    Given I request "project/prj_uid/activity/<activity>/assignee" with the key "prj_uid" stored in session array as variable "prj_uid_<prj_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "aas_uid" property in row 0 equals "<aas_uid>"
    And the "aas_type" property in row 0 equals "<aas_type>"

    Examples:
    | import_option | prj_uid_number | prj_uid                          | records | project                          | activity                         | records | aas_uid                          | aas_type |
    | create        | 1              | 601816709536cfeae7d7cd9079578104 | 0       | 601816709536cfeae7d7cd9079578104 | 771350954536cfec446fab9019867857 | 1       | 70084316152d56749e0f393054862525 | group    |
    | create        | 2              | 1455892245368ebeb11c1a5001393784 | 26      | 1455892245368ebeb11c1a5001393784 | 6274755055368eed1116388064384542 | 1       | 70084316152d56749e0f393054862525 | group    |
    | overwrite     | 3              | 1455892245368ebeb11c1a5001393784 | 26      | 1455892245368ebeb11c1a5001393784 | 6274755055368eed1116388064384542 | 1       | 70084316152d56749e0f393054862525 | group    |
    | disable       | 4              | 1455892245368ebeb11c1a5001393784 | 26      | 1455892245368ebeb11c1a5001393784 | 4790702485368efad167477011123879 | 1       | 70084316152d56749e0f393054862525 | group    |
    | keep          | 5              | 1455892245368ebeb11c1a5001393784 | 26      | 1455892245368ebeb11c1a5001393784 | 2072984565368efc137a394001073529 | 1       | 70084316152d56749e0f393054862525 | group    |


Scenario Outline: Get a List of current process supervisors of a project
    Given I request "project/prj_uid/process-supervisors" with the key "prj_uid" stored in session array as variable "prj_uid_<prj_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    | import_option | prj_uid_number | prj_uid                          | records |
    | create        | 1              | 601816709536cfeae7d7cd9079578104 | 0       |
    | create        | 2              | 1455892245368ebeb11c1a5001393784 | 1       |
    | overwrite     | 3              | 1455892245368ebeb11c1a5001393784 | 1       |
    | disable       | 4              | 1455892245368ebeb11c1a5001393784 | 1       |
    | keep          | 5              | 1455892245368ebeb11c1a5001393784 | 1       |

      
Scenario Outline: Get a List of current Process Permissions of a project
    Given I request "project/prj_uid/process-permissions" with the key "prj_uid" stored in session array as variable "prj_uid_<prj_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has <records> records

    Examples:
    | import_option | prj_uid_number | prj_uid                          | records |
    | create        | 1              | 601816709536cfeae7d7cd9079578104 | 0       |
    | create        | 2              | 1455892245368ebeb11c1a5001393784 | 1       |
    | overwrite     | 3              | 1455892245368ebeb11c1a5001393784 | 1       |
    | disable       | 4              | 1455892245368ebeb11c1a5001393784 | 1       |
    | keep          | 5              | 1455892245368ebeb11c1a5001393784 | 1       |


Scenario Outline: Get a list templates folder of process files manager
    Given I request "project/prj_uid/file-manager?path=templates" with the key "prj_uid" stored in session array as variable "prj_uid_<prj_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    | import_option | prj_uid_number | prj_uid                          | records |
    | create        | 1              | 601816709536cfeae7d7cd9079578104 | 0       |
    | create        | 2              | 1455892245368ebeb11c1a5001393784 | 1       |
    | overwrite     | 3              | 1455892245368ebeb11c1a5001393784 | 1       |
    | disable       | 4              | 1455892245368ebeb11c1a5001393784 | 1       |
    | keep          | 5              | 1455892245368ebeb11c1a5001393784 | 1       |


Scenario: Get a single Process process "Process Complete BPMN"
    Given that I want to get a resource with the key "obj_uid" stored in session array
    And I request "project/1455892245368ebeb11c1a5001393784/process"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "pro_title" is set to "Process Complete BPMN"
    And that "pro_description" is set to ""
    And that "pro_parent" is set to "1455892245368ebeb11c1a5001393784"
    And that "pro_time" is set to 1
    And that "pro_timeunit" is set to "DAYS"
    And that "pro_status" is set to "ACTIVE"
    And that "pro_type_day" is set to ""
    And that "pro_type" is set to "NORMAL"
    And that "pro_assignment" is set to 0
    And that "pro_show_map" is set to 0
    And that "pro_show_message" is set to 1
    And that "pro_subprocess" is set to 0
    And that "pro_tri_deleted" is set to "712197294536bea56a8b4d0014148679"
    And that "pro_tri_canceled" is set to "950769923536bea6a39c833033416052"
    And that "pro_tri_paused" is set to "350949312536bea73c53791057971272"
    And that "pro_tri_reassigned" is set to "712197294536bea56a8b4d0014148679"
    And that "pro_show_delegate" is set to 0
    And that "pro_show_dynaform" is set to 0
    And that "pro_category" is set to "4177095085330818c324501061677193"
    And that "pro_sub_category" is set to ""
    And that "pro_industry" is set to 0
    And that "pro_update_date" is set to "null"
    And that "pro_create_date" is set to "2014-05-06 10:04:27"
    And that "pro_create_user" is set to "00000000000000000000000000000001"
    And that "pro_debug" is set to 0
    And that "pro_derivation_screen_tpl" is set to ""
    And that "pro_summary_dynaform" is set to "898822326536be3a12addb0034537553"
    And that "pro_calendar" is set to "14606161052f50839307899033145440"


Scenario: Get a single Process process "Process NewCreate BPMN"
    Given that I want to get a resource with the key "obj_uid" stored in session array
    And I request "project/601816709536cfeae7d7cd9079578104/process"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "pro_title" is set to "Process NewCreate BPMN"
    And that "pro_description" is set to ""
    And that "pro_parent" is set to "601816709536cfeae7d7cd9079578104"
    And that "pro_time" is set to 1
    And that "pro_timeunit" is set to "DAYS"
    And that "pro_status" is set to "ACTIVE"
    And that "pro_type_day" is set to ""
    And that "pro_type" is set to "NORMAL"
    And that "pro_assignment" is set to 0
    And that "pro_show_map" is set to 0
    And that "pro_show_message" is set to 0
    And that "pro_subprocess" is set to 0
    And that "pro_tri_deleted" is set to ""
    And that "pro_tri_canceled" is set to ""
    And that "pro_tri_paused" is set to ""
    And that "pro_tri_reassigned" is set to ""
    And that "pro_show_delegate" is set to 0
    And that "pro_show_dynaform" is set to 0
    And that "pro_category" is set to ""
    And that "pro_sub_category" is set to ""
    And that "pro_industry" is set to 0
    And that "pro_update_date" is set to "null"
    And that "pro_create_date" is set to "2014-05-12 09:10:23"
    And that "pro_create_user" is set to "00000000000000000000000000000001"
    And that "pro_debug" is set to 0
    And that "pro_derivation_screen_tpl" is set to ""
    And that "pro_summary_dynaform" is set to ""
    And that "pro_calendar" is set to ""


Scenario Outline: Delete a Project created previously in this script
    Given that I want to delete a resource with the key "prj_uid" stored in session array as variable "prj_uid_<prj_uid_number>"
    And I request "projects"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:

    | prj_uid_number |
    | 1              |
    | 2              |
    | 4              |
    | 5              |
    

Scenario: Get a list of projects
    Given I request "project"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"


Scenario Outline: Import a process
    Given POST upload a project file "<project_file>" to "project/import?option=<import_option>"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    
    Examples:
    | project_file                                       | import_option |
    | /home/wendy/uploadfiles/Process_Complete_BPMN.pmx  | create        |


#For example, to export a empty process

Scenario: Get for Export Project "Export process empty"
    Given I request "project/5195971265375127fce82f4015927137/export"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/xml"
    And save exported process to "/home/wendy/uploadfiles/" as "Export process empty"

Scenario: Delete a Project created previously in this script "Export process empty"
    Given that I want to delete a resource with the key "prj_uid" stored in session array
    And I request "projects/5195971265375127fce82f4015927137" 
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    
Scenario: Import a process "Export process empty"
    Given POST upload a project file "/home/wendy/uploadfiles/Export_process_empty.pmx" to "project/import?option=create"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"

Scenario: Get a List DynaForms of a Project "Export process empty"  
    Given I request "project/5195971265375127fce82f4015927137/dynaforms" with the key "prj_uid" stored in session array
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 0 records
    
Scenario: Get the Input Documents List when there are exactly zero input documents
    Given I request "project/5195971265375127fce82f4015927137/input-documents" with the key "prj_uid" stored in session array
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 0 records

Scenario: Get the Output Documents List when there are exactly zero output documents
    Given I request "project/5195971265375127fce82f4015927137/output-documents" with the key "prj_uid" stored in session array
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 0 records

Scenario: Verify that there are report tables
    Given I request "project/5195971265375127fce82f4015927137/report-tables"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has 0 record

Scenario: Get the Case Trackers Objects of a Project
        And I request "project/5195971265375127fce82f4015927137/case-tracker/objects"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 record