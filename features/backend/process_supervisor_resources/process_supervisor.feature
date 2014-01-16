@ProcessMakerMichelangelo @RestAPI
Feature: Process supervisor Resources

    Background:
    Given that I have a valid access_token

    @1: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisors
    Scenario: Get a List of process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisors"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has 1 records


    @2: TEST FOR GET A SPECIFIC PROCESS SUPERVISOR /--------------------------------------------------------------------
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/{pu_uid}
    Scenario: Get a specific process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/70662784652cef0878516f7085532841"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"


    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #GET /api/1.0/{workspace}/project/{prj_uid}/available-process-supervisors
    Scenario: Get a List of available process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/available-process-supervisors"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------    
    #GET /api/1.0/{workspace}/project/{prj_uid}/available-process-supervisors
    Scenario: Get a List of available groups process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/available-process-supervisors?obj_type=group"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #GET /api/1.0/{workspace}/project/{prj_uid}/available-process-supervisors
    Scenario: Get a List of available users process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/available-process-supervisors?obj_type=user"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------    
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/dynaforms
    Scenario: Get a List of dynaforms process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/dynaforms"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/dynaform/{pu_uid}
    Scenario: Get a specific process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/dynaform/78069721352ceef1fd61878075214306"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"

    
     @3: TEST FOR GET PROCESS SUPERVISOR /-------------------------------------------------------------------- 
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/available-dynaforms
    Scenario: Get a List of available dynaforms process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/available-dynaforms"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/input-documents
    Scenario: Get a List of input-documents process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/input-documents"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/available-input-documents
    Scenario: Get a List of input-documents process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/available-input-documents"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/input-document/{pu_uid}
    Scenario: Get a specific process supervisor of a project
        Given that I request "project/85794888452ceeef3675164057928956/process-supervisor/input-document/37709187452ceef4f601dd3045365506"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #POST /api/1.0/{workspace}/project/{prj_uid}/process-supervisor
    Scenario: Assign a group process supervisor of a project
        Given that I POST this data:
        """
       {
           "pu_type": "GROUP_SUPERVISOR",
           "usr_uid": "46138556052cda43a051110007756836"
       }
       """
       And I request "project/85794888452ceeef3675164057928956/process-supervisor"
       Then the response status code should be 201
       And the response charset is "UTF-8"
       And the content type is "application/json"
       And the type is "object"
       And store "pu_uid" in session array as variable "pug_uid"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #POST /api/1.0/{workspace}/project/{prj_uid}/process-supervisor
    Scenario: Assign a user process supervisor of a project
        Given that I POST this data:
        """
       {
           "pu_type": "SUPERVISOR",
           "usr_uid": "00000000000000000000000000000001"
       }
       """
       And I request "project/85794888452ceeef3675164057928956/process-supervisor"
       Then the response status code should be 201
       And the response charset is "UTF-8"
       And the content type is "application/json"
       And the type is "object"
       And store "pu_uid" in session array as variable "puu_uid"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #POST /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/dynaform
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
       And store "pud_uid" in session array as variable "pud_uid"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #POST /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/input-document
    Scenario: Assign a dynaform process supervisor of a project
        Given that I POST this data:
        """
       {
            "inp_doc_uid": "25205290452ceef570741c3067266323"
       }
       """
       And I request "project/85794888452ceeef3675164057928956/process-supervisor/input-document"
       Then the response status code should be 201
       And the response charset is "UTF-8"
       And the content type is "application/json"
       And the type is "object"
       And store "pui_uid" in session array as variable "pui_uid"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #DELETE /api/1.0/{workspace}/project/{prj_uid}/process-supervisor
    Scenario: Delete a user process supervisor of a project
        Given that I want to delete a resource with the key "pug_uid" stored in session array
        And I request "project/85794888452ceeef3675164057928956/process-supervisor"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #DELETE /api/1.0/{workspace}/project/{prj_uid}/process-supervisor
    Scenario: Delete a user process supervisor of a project
        Given that I want to delete a resource with the key "puu_uid" stored in session array
        And I request "project/85794888452ceeef3675164057928956/process-supervisor"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #DELETE /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/dynaform
    Scenario: Delete a dynaform process supervisor of a project
        Given that I want to delete a resource with the key "pud_uid" stored in session array
        And I request "project/85794888452ceeef3675164057928956/process-supervisor/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
    
    
    @3: TEST FOR GET PROCESS SUPERVISOR /--------------------------------------------------------------------
    #DELETE /api/1.0/{workspace}/project/{prj_uid}/process-supervisor/input-document
    Scenario: Delete a input-document process supervisor of a project
        Given that want to delete a resource with the key "pui_uid" stored in session array
        And I request "project/85794888452ceeef3675164057928956/process-supervisor/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"