@ProcessMakerMichelangelo @RestAPI
Feature: Project Resource
  Requirements:
    a workspace with the process 40 already loaded aproximatly
    
Background:
    Given that I have a valid access_token

  
Scenario: Get a list of projects
    Given I request "project"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario: Get definition of a project activity for obtent definition
    Given I request "project/4224292655297723eb98691001100052/activity/65496814252977243d57684076211485?filter=definition"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
   

Scenario Outline: Create new Projects
      Given POST this data:
      """
      {        
        "prj_name": "Prueba New Project",    
        "prj_description": "New Project, created of this script",    
        "prj_target_namespace": "sample",    
        "prj_expresion_language": null,    
        "prj_type_language": null,    
        "prj_exporter": null,    
        "prj_exporter_version": null,    
        "prj_create_date": "2014-04-28 11:01:54",    
        "prj_update_date": "2014-04-30 08:46:17",    
        "prj_author": "00000000000000000000000000000001",    
        "prj_author_version": null,    
        "prj_original_source": null,    
        "diagrams": [    
        {    
            "dia_uid": "956446767534fece3179b54016939905",        
            "prj_uid": "655001588534fece2d46f86033751389",        
            "dia_name": "Prueba New Project",        
            "dia_is_closable": 0,        
            "pro_uid": "736054291534fece3342096012897456",        
            "activities": [    
            {    
                "act_uid": "569214945534fecfa8f0835033274864",        
                "act_name": "Task # 1",        
                "act_type": "TASK",        
                "act_is_for_compensation": "0",        
                "act_start_quantity": "1",        
                "act_completion_quantity": "0",        
                "act_task_type": "EMPTY",        
                "act_implementation": "",        
                "act_instantiate": "0",        
                "act_script_type": "",        
                "act_script": "",        
                "act_loop_type": "NONE",        
                "act_test_before": "0",        
                "act_loop_maximum": "0",        
                "act_loop_condition": "0",        
                "act_loop_cardinality": "0",        
                "act_loop_behavior": "0",        
                "act_is_adhoc": "0",        
                "act_is_collapsed": "0",        
                "act_completion_condition": "0",        
                "act_ordering": "0",        
                "act_cancel_remaining_instances": "0",        
                "act_protocol": "0",        
                "act_method": "0",        
                "act_is_global": "0",
                "act_referer": "0",        
                "act_default_flow": "0",        
                "act_master_diagram": "0",        
                "bou_x": "486",        
                "bou_y": "101",        
                "bou_width": "161",        
                "bou_height": "42",        
                "bou_container": "bpmnDiagram"    
            }   
        ],
        "events": [     
            {
                "evn_uid": "259220802534fecfad49854013091940",        
                "evn_name": "Start # 1",        
                "evn_type": "START",        
                "evn_marker": "MESSAGE",        
                "evn_is_interrupting": "1",        
                "evn_cancel_activity": "0",        
                "evn_activity_ref": null,        
                "evn_wait_for_completion": "0",        
                "evn_error_name": null,        
                "evn_error_code": null,        
                "evn_escalation_name": null,        
                "evn_escalation_code": null,        
                "evn_message": "LEAD",        
                "evn_operation_name": null,        
                "evn_operation_implementation_ref": null,        
                "evn_time_date": null,        
                "evn_time_cycle": null,        
                "evn_time_duration": null,        
                "evn_behavior": "CATCH",        
                "bou_x": "517",        
                "bou_y": "19",        
                "bou_width": "33",        
                "bou_height": "33",        
                "bou_container": "bpmnDiagram"        
            },    
            {    
                "evn_uid": "856003291534fecfae5dff7085708495",        
                "evn_name": "End # 1",        
                "evn_type": "END",        
                "evn_marker": "EMPTY",        
                "evn_is_interrupting": "1",        
                "evn_cancel_activity": "0",        
                "evn_activity_ref": null,        
                "evn_wait_for_completion": "0",        
                "evn_error_name": null,        
                "evn_error_code": null,        
                "evn_escalation_name": null,        
                "evn_escalation_code": null,        
                "evn_message": "",        
                "evn_operation_name": null,        
                "evn_operation_implementation_ref": null,        
                "evn_time_date": null,        
                "evn_time_cycle": null,        
                "evn_time_duration": null,        
                "evn_behavior": "THROW",        
                "bou_x": "549",        
                "bou_y": "181",        
                "bou_width": "33",        
                "bou_height": "33",        
                "bou_container": "bpmnDiagram"        
            }    
        ],    
        "gateways": [],    
        "flows": [    
            {    
                "flo_uid": "17092374253551306216a72013534569",        
                "flo_type": "SEQUENCE",        
                "flo_name": null,        
                "flo_element_origin": "569214945534fecfa8f0835033274864",        
                "flo_element_origin_type": "bpmnActivity",        
                "flo_element_dest": "856003291534fecfae5dff7085708495",        
                "flo_element_dest_type": "bpmnEvent",        
                "flo_is_inmediate": "1",        
                "flo_condition": null,        
                "flo_x1": "561",        
                "flo_y1": "193",        
                "flo_x2": "577",        
                "flo_y2": "193",        
                "flo_state": [        
                    {    
                        "x": 566,            
                        "y": 145            
                    },            
                    {            
                        "x": 566,            
                        "y": 171            
                    },            
                    {            
                        "x": 602,            
                        "y": 171            
                    },            
                    {            
                        "x": 602,            
                        "y": 198            
                    },            
                    {            
                        "x": 582,            
                        "y": 198            
                    }            
                ]            
            },            
            {            
                "flo_uid": "304762728534fecfaf3bf88040991913",        
                "flo_type": "SEQUENCE",        
                "flo_name": null,        
                "flo_element_origin": "259220802534fecfad49854013091940",        
                "flo_element_origin_type": "bpmnEvent",        
                "flo_element_dest": "569214945534fecfa8f0835033274864",        
                "flo_element_dest_type": "bpmnActivity",        
                "flo_is_inmediate": "1",        
                "flo_condition": null,        
                "flo_x1": "529",        
                "flo_y1": "95",        
                "flo_x2": "556",        
                "flo_y2": "95",        
                "flo_state": [
                    {            
                        "x": 534,            
                        "y": 52            
                    },            
                    {            
                        "x": 534,            
                        "y": 76            
                    },            
                    {            
                        "x": 561,            
                        "y": 76            
                    },            
                    {            
                        "x": 561,            
                        "y": 100            
                    }            
                ]            
            }            
        ],            
        "artifacts": [],    
        "laneset": [],    
        "lanes": []    
        }    
    ]            
    }         
    """
    And I request "projects"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And store "new_uid" in session array as variable "project_new_uid_<project_new_uid_number>" where an object has "object" equal to "project"
    And store "new_uid" in session array as variable "diagram_new_uid_<project_new_uid_number>" where an object has "object" equal to "diagram"
    And store "new_uid" in session array as variable "activity_new_uid_<project_new_uid_number>" where an object has "object" equal to "activity"
    And store "new_uid" in session array as variable "event_new_uid_<project_new_uid_number>" where an object has "object" equal to "event"
    And store "new_uid" in session array as variable "flow_new_uid_<project_new_uid_number>" where an object has "object" equal to "flow"
    
    Examples:

    | Description          | project_new_uid_number | 
    | Create a new process | 1                      |


Scenario: Get a list of projects
    Given I request "project"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    

Scenario Outline: Update the Projects and then check if the values had changed
    Given PUT this data:
    """
    {        
        "prj_name": "Update Prueba New Project",    
        "prj_description": "Update New Project, created of this script",    
        "prj_target_namespace": "sample",    
        "prj_expresion_language": null,    
        "prj_type_language": null,    
        "prj_exporter": null,    
        "prj_exporter_version": null,    
        "prj_create_date": "2014-04-28 11:01:54",    
        "prj_update_date": "2014-04-30 08:46:17",    
        "prj_author": "00000000000000000000000000000001",    
        "prj_author_version": null,    
        "prj_original_source": null,    
        "diagrams": [    
        {    
            "dia_uid": "956446767534fece3179b54016939905",        
            "prj_uid": "655001588534fece2d46f86033751389",        
            "dia_name": "Update Prueba New Project",        
            "dia_is_closable": 0,        
            "pro_uid": "736054291534fece3342096012897456",        
            "activities": [    
            {    
                "act_uid": "569214945534fecfa8f0835033274864",        
                "act_name": "Task # 1",        
                "act_type": "TASK",        
                "act_is_for_compensation": "0",        
                "act_start_quantity": "1",        
                "act_completion_quantity": "0",        
                "act_task_type": "EMPTY",        
                "act_implementation": "",        
                "act_instantiate": "0",        
                "act_script_type": "",        
                "act_script": "",        
                "act_loop_type": "NONE",        
                "act_test_before": "0",        
                "act_loop_maximum": "0",        
                "act_loop_condition": "0",        
                "act_loop_cardinality": "0",        
                "act_loop_behavior": "0",        
                "act_is_adhoc": "0",        
                "act_is_collapsed": "0",        
                "act_completion_condition": "0",        
                "act_ordering": "0",        
                "act_cancel_remaining_instances": "0",        
                "act_protocol": "0",        
                "act_method": "0",        
                "act_is_global": "0",
                "act_referer": "0",        
                "act_default_flow": "0",        
                "act_master_diagram": "0",        
                "bou_x": "486",        
                "bou_y": "101",        
                "bou_width": "161",        
                "bou_height": "42",        
                "bou_container": "bpmnDiagram"    
            }   
        ],
        "events": [     
            {
                "evn_uid": "259220802534fecfad49854013091940",        
                "evn_name": "Start # 1",        
                "evn_type": "START",        
                "evn_marker": "MESSAGE",        
                "evn_is_interrupting": "1",        
                "evn_cancel_activity": "0",        
                "evn_activity_ref": null,        
                "evn_wait_for_completion": "0",        
                "evn_error_name": null,        
                "evn_error_code": null,        
                "evn_escalation_name": null,        
                "evn_escalation_code": null,        
                "evn_message": "LEAD",        
                "evn_operation_name": null,        
                "evn_operation_implementation_ref": null,        
                "evn_time_date": null,        
                "evn_time_cycle": null,        
                "evn_time_duration": null,        
                "evn_behavior": "CATCH",        
                "bou_x": "517",        
                "bou_y": "19",        
                "bou_width": "33",        
                "bou_height": "33",        
                "bou_container": "bpmnDiagram"        
            },    
            {    
                "evn_uid": "856003291534fecfae5dff7085708495",        
                "evn_name": "End # 1",        
                "evn_type": "END",        
                "evn_marker": "EMPTY",        
                "evn_is_interrupting": "1",        
                "evn_cancel_activity": "0",        
                "evn_activity_ref": null,        
                "evn_wait_for_completion": "0",        
                "evn_error_name": null,        
                "evn_error_code": null,        
                "evn_escalation_name": null,        
                "evn_escalation_code": null,        
                "evn_message": "",        
                "evn_operation_name": null,        
                "evn_operation_implementation_ref": null,        
                "evn_time_date": null,        
                "evn_time_cycle": null,        
                "evn_time_duration": null,        
                "evn_behavior": "THROW",        
                "bou_x": "549",        
                "bou_y": "181",        
                "bou_width": "33",        
                "bou_height": "33",        
                "bou_container": "bpmnDiagram"        
            }    
        ],    
        "gateways": [],    
        "flows": [    
            {    
                "flo_uid": "17092374253551306216a72013534569",        
                "flo_type": "SEQUENCE",        
                "flo_name": null,        
                "flo_element_origin": "569214945534fecfa8f0835033274864",        
                "flo_element_origin_type": "bpmnActivity",        
                "flo_element_dest": "856003291534fecfae5dff7085708495",        
                "flo_element_dest_type": "bpmnEvent",        
                "flo_is_inmediate": "1",        
                "flo_condition": null,        
                "flo_x1": "561",        
                "flo_y1": "193",        
                "flo_x2": "577",        
                "flo_y2": "193",        
                "flo_state": [        
                    {    
                        "x": 566,            
                        "y": 145            
                    },            
                    {            
                        "x": 566,            
                        "y": 171            
                    },            
                    {            
                        "x": 602,            
                        "y": 171            
                    },            
                    {            
                        "x": 602,            
                        "y": 198            
                    },            
                    {            
                        "x": 582,            
                        "y": 198            
                    }            
                ]            
            },            
            {            
                "flo_uid": "304762728534fecfaf3bf88040991913",        
                "flo_type": "SEQUENCE",        
                "flo_name": null,        
                "flo_element_origin": "259220802534fecfad49854013091940",        
                "flo_element_origin_type": "bpmnEvent",        
                "flo_element_dest": "569214945534fecfa8f0835033274864",        
                "flo_element_dest_type": "bpmnActivity",        
                "flo_is_inmediate": "1",        
                "flo_condition": null,        
                "flo_x1": "529",        
                "flo_y1": "95",        
                "flo_x2": "556",        
                "flo_y2": "95",        
                "flo_state": [
                    {            
                        "x": 534,            
                        "y": 52            
                    },            
                    {            
                        "x": 534,            
                        "y": 76            
                    },            
                    {            
                        "x": 561,            
                        "y": 76            
                    },            
                    {            
                        "x": 561,            
                        "y": 100            
                    }            
                ]            
            }            
        ],            
        "artifacts": [],    
        "laneset": [],    
        "lanes": []    
        }    
    ]            
    }         
    """
    And that I want to update a resource with the key "new_uid" stored in session array as variable "project_new_uid_<project_new_uid_number>" in position 0
    And I request "projects"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:

    | test_description             | project_new_uid_number |
    | Update a new project created | 1                      |


Scenario Outline: Get definition of a project
    Given that I want to get a resource with the key "new_uid" stored in session array as variable "project_new_uid_<project_new_uid_number>" in position 0
    And I request "project"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And that "prj_name" is set to "Update Prueba New Project"
    And that "prj_description" is set to "Update New Project, created of this script"
    
    Examples:

    | project_new_uid_number |
    | 1                      |

Scenario Outline: Delete a Project activity created previously in this script
    Given that I want to delete a resource with the key "new_uid" stored in session array as variable "project_new_uid_<project_new_uid_number>" in position 0
    And I request "projects"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:

    | project_new_uid_number |
    | 1                      |


Scenario: Get a list of projects
    Given I request "project"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"