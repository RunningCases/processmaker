@ProcessMakerMichelangelo @RestAPI
Feature: ProcessPermissions Resources

    @1: TEST FOR GET PROCESS PERMISSIONS /----------------------------------------------------------------------
    Scenario: List all the process permissions (result 0 process permissions)
        Given that I have a valid access_token
        And I request "project/251815090529619a99a2bf4013294414/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record


    @2: TEST FOR POST PROCESS PERMISSION /----------------------------------------------------------------------
    Scenario: Create a new process permission
        Given that I have a valid access_token
        And POST this data:
            """
            {
                "tas_uid": "",
                "usr_uid": "00000000000000000000000000000001",
                "op_user_relation": "1",
                "op_obj_type": "ANY",
                "op_task_source" : "",
                "op_participate": "0",
                "op_action": "BLOCK",
                "op_case_status": "DRAFT"
            }
            """
        And I request "project/251815090529619a99a2bf4013294414/process-permission"
        Then the response status code should be 201
        And store "op_uid" in session array

    @3: TEST FOR GET PROCESS PERMISSIONS /----------------------------------------------------------------------
    Scenario: List all the process permissions (result 1 process permission)
        Given that I have a valid access_token
        And I request "project/251815090529619a99a2bf4013294414/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 1 record

    @4: TEST FOR PUT PROCESS PERMISSION /-----------------------------------------------------------------------
    Scenario: Update a process permission
        Given that I have a valid access_token
        And PUT this data:
            """
            {
                "tas_uid": "",
                "usr_uid": "00000000000000000000000000000001",
                "op_user_relation": "1",
                "op_obj_type": "ANY",
                "op_task_source" : "",
                "op_participate": "0",
                "op_action": "VIEW",
                "op_case_status": "TO_DO"
            }
            """
        And that I want to update a resource with the key "op_uid" stored in session array
        And I request "project/251815090529619a99a2bf4013294414/process-permission"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    @5: TEST FOR GET PROCESS PERMISSION /-----------------------------------------------------------------------
    Scenario: Get a process permission (with change in "op_action" and "op_case_status")
        Given that I have a valid access_token
        And that I want to get a resource with the key "op_uid" stored in session array
        And I request "project/251815090529619a99a2bf4013294414/process-permission"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "op_action" is set to "VIEW"
        And that "op_case_status" is set to "TO_DO"


    @6: TEST FOR DELETE PROCESS PERMISSION /-----------------------------------------------------------------------
    Scenario: Delete a process permission
        Given that I have a valid access_token
        And that I want to delete a resource with the key "op_uid" stored in session array
        And I request "project/251815090529619a99a2bf4013294414/process-permission"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    @7: TEST FOR GET PROCESS PERMISSIONS /----------------------------------------------------------------------
    Scenario: List all the process permissions (result 0 process permissions)
        Given that I have a valid access_token
        And I request "project/251815090529619a99a2bf4013294414/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record