@ProcessMakerMichelangelo @RestAPI
Feature: Projects Negative Tests

Background:
    Given that I have a valid access_token


Scenario Outline: Create new Projects (Negative Tests)
      Given POST this data:
      """
      {        
        "prj_name": "<prj_name>",    
        "prj_description": "<prj_description>",    
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
                "act_name": "<act_name>",        
                "act_type": "<act_type>",        
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
                "evn_name": "<evn_name>",        
                "evn_type": "<evn_type>",        
                "evn_marker": "<evn_marker>",        
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
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"
    
    Examples:

    | Description                    | prj_name                | prj_description | act_name | act_type | evn_name | evn_marker | error_code | error_message   |
    | Field required prj_name        |                         | Prueba          | Task # 1 | TASK     | End # 1  | EMPTY      | 400        | prj_name        |
    | Field required act_name        | Test negative project 2 | Prueba 1        |          | TASK     | End # 1  | EMPTY      | 400        | act_name        |
    | Field required act_type        | Test negative project 3 | Prueba 2        | Task # 1 |          | End # 1  | EMPTY      | 400        | act_type        |
    | Field required evn_marker      | Test negative project 5 | Prueba 4        | Task # 1 | TASK     | End # 1  |            | 400        | evn_marker      |