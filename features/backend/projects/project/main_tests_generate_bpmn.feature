@ProcessMakerMichelangelo @RestAPI
Feature: Generate BPMN of process Main Tests
  Requirements:
    a workspace without the project test_generate_bpmn already loaded
    
Background:
    Given that I have a valid access_token



 Scenario Outline: Generate BPMN of process imported - "test generate bpmn"
    Given POST this data:
    """
    {
        "pro_uid": "22872259053dff3046d8db7020830606"
    }
    """
    And I request "project/generate-bpmn"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "pro_uid" in session array as variable "pro_uid_<pro_uid_number>"
    And store "prj_uid" in session array as variable "prj_uid_<pro_uid_number>"
  
    Examples:

    | test_description                    | pro_uid_number |
    | Generate process imported to a bpmn | 1              |


#Verify different objects 


Scenario Outline: Get a List DynaForms of a Project Process Complete BPMN  
    Given I request "project/prj_uid/dynaforms" with the key "prj_uid" stored in session array as variable "prj_uid_<pro_uid_number>"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 15 records

    Examples:

    | test_description             | pro_uid_number |
    | List of Dynaform the project | 1              |

 Scenario Outline: Get the Input Documents List when there are exactly zero input documents
    Given I request "project/prj_uid/input-documents" with the key "prj_uid" stored in session array as variable "prj_uid_<pro_uid_number>"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 2 records

    Examples:

    | test_description                   | pro_uid_number |
    | List of Input Document the project | 1              |
   

Scenario Outline: Get the Output Documents List when there are exactly two output documents " BUG-14907, No se visualiza los cambios en el editor tiny de OutputDocuments"
    Given I request "project/prj_uid/output-documents" with the key "prj_uid" stored in session array as variable "prj_uid_<pro_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And that "out_doc_template" is set to "Ejemplo de Output Document"
    And the response has 5 records
    
    Examples:

    | test_description                    | pro_uid_number |
    | List of Output Document the project | 1              |

    
Scenario Outline: Get the Triggers List when there are exactly two triggers
    Given I request "project/prj_uid/triggers" with the key "prj_uid" stored in session array as variable "prj_uid_<pro_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 7 records

    Examples:
 
    | test_description             | pro_uid_number |
    | List of Triggers the project | 1              |
        

Scenario Outline: Get a List of current process supervisors of a project
    Given I request "project/prj_uid/process-supervisors" with the key "prj_uid" stored in session array as variable "prj_uid_<pro_uid_number>" 
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 records

    Examples:

    | test_description                       | pro_uid_number |
    | List of Process Supervisor the project | 1              |

         
Scenario Outline: Get a List of current Process Permissions of a project
    Given I request "project/prj_uid/process-permissions" with the key "prj_uid" stored in session array as variable "prj_uid_<pro_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the response has 1 records

    Examples:

    | test_description                        | pro_uid_number |
    | List of Process Permissions the project | 1              |

    
Scenario Outline: Get a single Process
    Given I request "project/prj_uid/process" with the key "prj_uid" stored in session array as variable "prj_uid_<pro_uid_number>"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "pro_title" is set to "test generate bpmn"
    And that "pro_description" is set to ""
    And that "pro_parent" is set to "1455892245368ebeb11c1a5001393784"
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
    And that "pro_create_date" is set to "2014-08-04 16:54:28"
    And that "pro_create_user" is set to "00000000000000000000000000000001"
    And that "pro_debug" is set to 0
    And that "pro_derivation_screen_tpl" is set to ""
    And that "pro_summary_dynaform" is set to ""
    And that "pro_calendar" is set to ""

    Examples:

    | test_description           | pro_uid_number |
    | process imported to a bpmn | 1              |


Scenario Outline: Delete a Project activity created previously in this script
    Given that I want to delete a resource with the key "prj_uid" stored in session array as variable "prj_uid_<pro_uid_number>"
    And I request "projects"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:

    | pro_uid_number |
    | 1              |

#Negative test, to import an invalid project

Scenario: Generate BPMN with wrong project
    Given POST this data:
    """
    {
        "pro_uid": "22872259050000000000db7020830606"
    }
    """
    And I request "project/generate-bpmn"
    Then the response status code should be 400
    And the response status message should have the following text "exist"