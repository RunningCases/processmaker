@ProcessMakerMichelangelo @RestAPI
Feature: Process supervisor Resources

    Background:
    Given that I have a valid access_token

    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisors
    @1: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
        Scenario: Get a List of process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisors"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has 1 records


   #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/{pu_uid}
    @2: TEST FOR GET A SPECIFIC PROCESS SUPERVISOR /--------------------------------------------------------------------
        Scenario: Get a specific process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/70662784652cef0878516f7085532841"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"


    #GET /api/1.0/{workspace}/project/{prj_uid}/available-process-supervisors
    @3: TEST FOR GET USERS AND GROUP SUPERVISOR /--------------------------------------------------------------------
        Scenario: Get a List of available process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/available-process-supervisors"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has 86 records


    #GET /api/1.0/{workspace}/project/{prj_uid}/available-process-supervisors
    @4: TEST FOR GET GROUP SUPERVISOR /--------------------------------------------------------------------    
        Scenario: Get a List of available groups process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/available-process-supervisors?obj_type=group"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has 23 records

    
    #GET /api/1.0/{workspace}/project/{prj_uid}/available-process-supervisors
    @5: TEST FOR GET USERS SUPERVISOR /--------------------------------------------------------------------
        Scenario: Get a List of available users process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/available-process-supervisors?obj_type=user"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has 63 records

   
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/dynaforms
    @6: TEST FOR DYNAFORM PROCESS SUPERVISOR /--------------------------------------------------------------------    
       Scenario: Get a List of dynaforms process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/dynaforms"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has 3 records

    
    @7: TEST FOR GET A SPECIFIC PROCESS SUPERVISOR /--------------------------------------------------------------------
     Scenario: Get a specific process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/dynaform/78069721352ceef1fd61878075214306"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"

    
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/available-dynaforms
    @8: TEST FOR GET AVAILABLE DYNAFORM PROCESS SUPERVISOR /-------------------------------------------------------------------- 
     Scenario: Get a List of available dynaforms process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/available-dynaforms"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has 2 records

    
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/input-documents
    @9: TEST FOR GET INPUT DOCUMENT PROCESS SUPERVISOR /--------------------------------------------------------------------
    Scenario: Get a List of input-documents process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/input-documents"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has 1 records

    
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/available-input-documents
    @10: TEST FOR GET LIST INPUT DOCUMENTS - PROCESS SUPERVISOR /--------------------------------------------------------------------
    Scenario: Get a List of input-documents process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/available-input-documents"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has 2 records

    
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/input-document/{pu_uid}
    @11: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    Scenario: Get a specific process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/input-document/37709187452ceef4f601dd3045365506"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"

    
    #POST /api/1.0/{workspace}/project/{prj_uid}/process-supervisor
    @12: TEST FOR POST PROCESS SUPERVISOR /--------------------------------------------------------------------
    Scenario Outline: Assign a user and group process supervisor of a project
        Given that I POST this data:
        """
       {
           "pu_type": "pu_type",
           "usr_uid": "usr_uid"
       }
       """
       And I request "project/<project>/process-supervisor"
       Then the response status code should be 201
       And the response charset is "UTF-8"
       And the content type is "application/json"
       And the type is "object"
       And store "pu_uid" in session array as variable "pug_uid_<ps_number>"

       Examples:
       | project                          | ps_number        | pu_type                         | usr_uid                          |   
       | 85794888452ceeef3675164057928956 | 1                | GROUP_SUPERVISOR                | 46138556052cda43a051110007756836 |
       | 85794888452ceeef3675164057928956 | 2                | SUPERVISOR                      | 00000000000000000000000000000001 |
    
        
    
    #POST /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/dynaform
    @13: TEST FOR POST DYNAFORM PROCESS SUPERVISOR /--------------------------------------------------------------------
    Scenario: Assign a dynaform process supervisor of a project
        Given that I POST this data:
        """
       {
            "dyn_uid": "78212661352ceef2dc4e987081647602"
       }
       """
       And I request "project/85794888452ceeef3675164057928956/process-supervisor/dynaform"
       Then the response status code should be 201
       And the response charset is "UTF-8"
       And the content type is "application/json"
       And the type is "object"
       And store "pud_uid" in session array as variable "pud_uid_<dps_number>"


       Examples:
       | project                          | dps_number       | dyn_uid                          |  
       | 85794888452ceeef3675164057928956 | 1                | 78212661352ceef2dc4e987081647602 |
       | 85794888452ceeef3675164057928956 | 1                | 

    
    #POST /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/input-document
    @14: TEST FOR POST INPUT DOCUMENT - PROCESS SUPERVISOR /--------------------------------------------------------------------
    Scenario: Assign a dynaform process supervisor of a project
        Given that I POST this data:
        """
       {
            "inp_doc_uid": "<inp_doc_uid>"
       }
       """
       And I request "project/85794888452ceeef3675164057928956/process-supervisor/input-document"
       Then the response status code should be 201
       And the response charset is "UTF-8"
       And the content type is "application/json"
       And the type is "object"
       And store "pui_uid" in session array as variable "pui_inpdoc_uid_<>"

      
      Examples:
       | project                          | dps_number       | inp_doc_uid                      | 
       | 85794888452ceeef3675164057928956 | 1                | 25205290452ceef570741c3067266323 |  


    
    #DELETE /api/1.0/{workspace}/project/{prj_uid}/process-supervisor
    @15: TEST FOR DELETE PROCESS SUPERVISOR /--------------------------------------------------------------------
    Scenario: Delete a user process supervisor of a project
        Given that I want to delete a resource with the key "pug_uid" stored in session array
        And I request "project/85794888452ceeef3675164057928956/process-supervisor"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

        Examples:
        | project                          | ps_number |
        | 85794888452ceeef3675164057928956 | 1         |
        | 85794888452ceeef3675164057928956 | 2         |


    
    #DELETE /api/1.0/{workspace}/project/{prj_uid}/process-supervisor
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    Scenario: Delete a user process supervisor of a project
        Given that I want to delete a resource with the key "puu_uid" stored in session array
        And I request "project/85794888452ceeef3675164057928956/process-supervisor"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    
    #DELETE /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/dynaform
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    Scenario: Delete a dynaform process supervisor of a project
        Given that I want to delete a resource with the key "pud_uid" stored in session array
        And I request "project/85794888452ceeef3675164057928956/process-supervisor/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
    
    
    #DELETE /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/input-document
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    Scenario: Delete a input-document process supervisor of a project
        Given that want to delete a resource with the key "pui_uid" stored in session array
        And I request "project/85794888452ceeef3675164057928956/process-supervisor/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"