@ProcessMakerMichelangelo @RestAPI
Feature: Project Properties - Step Resources
    #STEPS OF A ACTIVITY

    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/steps
    #    List assigned Steps to an Activity
    Scenario: List assigned Steps to "Task1"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/steps"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #POST /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step
    #     Assign a Step to an Activity
    Scenario: Assign "DynaForm Demo1" to "Task1"
        Given that I have a valid access_token
        And POST this data:
        """
        {
            "step_type_obj": "DYNAFORM",
            "step_uid_obj": "50332332752cd9b9a7cc989003652905",
            "step_condition": "",
            "step_position": 1,
            "step_mode": "EDIT"
        }
        """
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/step"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "step_uid" in session array as variable "step1"

    #POST /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step
    #     Assign a Step to an Activity
    Scenario: Assign "InputDocument Demo" to "Task1"
        Given that I have a valid access_token
        And POST this data:
        """
        {
            "step_type_obj": "INPUT_DOCUMENT",
            "step_uid_obj": "83199959452cd62589576c1018679557",
            "step_condition": "",
            "step_position": 2,
            "step_mode": "EDIT"
        }
        """
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/step"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "step_uid" in session array as variable "step2"

    #PUT /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}
    #    Update a Step assignation of an Activity
    Scenario: Update Step "DynaForm Demo1" assigned to "Task1"
        Given that I have a valid access_token
        And PUT this data:
        """
        {
            "step_condition": "@@FIELD1 == 1",
            "step_mode": "VIEW"
        }
        """
        And that I want to update a resource with the key "step1" stored in session array
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/step"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "step_condition" is set to "@@FIELD1 == 1"

    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/steps
    #    List assigned Steps to an Activity
    Scenario: List assigned Steps to "Task1"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/steps"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "step_type_obj" property in row 1 equals "INPUT_DOCUMENT"
        And the "step_uid_obj" property in row 1 equals "83199959452cd62589576c1018679557"
        And the "step_condition" property in row 1 equals ""
        And the "step_position" property in row 1 equals "2"
        And the "step_mode" property in row 1 equals "EDIT"
        And the "obj_title" property in row 1 equals "InputDocument Demo"
        And the "obj_description" property in row 1 equals "Description"

    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/available-steps
    #    List available Steps to assign to an Activity
    Scenario: List available Steps to assign to "Task1"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/available-steps"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "obj_uid" property in row 1 equals "32743823452cd63105006e1076595203"
        And the "obj_title" property in row 1 equals "OutputDocument Demo"
        And the "obj_description" property in row 1 equals "Description"
        And the "obj_type" property in row 1 equals "OUTPUT_DOCUMENT"

    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}
    #    Get a single Step assigned to an Activity
    Scenario: Get a single Step "DynaForm Demo1" assigned to "Task1"
        Given that I have a valid access_token
        And that I want to get a resource with the key "step1" stored in session array
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/step"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "step_type_obj" is set to "DYNAFORM"
        And that "step_uid_obj" is set to "50332332752cd9b9a7cc989003652905"
        And that "step_condition" is set to "@@FIELD1 == 1"
        And that "step_position" is set to "1"
        And that "step_mode" is set to "VIEW"
        And that "obj_title" is set to "DynaForm Demo1"
        And that "obj_description" is set to "Description"

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}
    #       Unassign a Step from an Activity
    Scenario: Unassign "DynaForm Demo1" from "Task1"
        Given that I have a valid access_token
        And that I want to delete a resource with the key "step1" stored in session array
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/step"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}
    #       Unassign a Step from an Activity
    Scenario: Unassign "InputDocument Demo" from "Task1"
        Given that I have a valid access_token
        And that I want to delete a resource with the key "step2" stored in session array
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/step"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/steps
    #    List assigned Steps to an Activity
    Scenario: List assigned Steps to "Task1"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/steps"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #TRIGGERS OF A STEP

    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/triggers
    #    List assigned Triggers to a Step
    Scenario: List Triggers assigned to first Step of "Task2"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #POST /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/trigger
    #     Assign a Trigger to a Step
    Scenario: Assign "Trigger Demo1" to first Step of "Task2"
        Given that I have a valid access_token
        And POST this data:
        """
        {
            "tri_uid": "81919273152cd636c665080083928728",
            "st_type": "BEFORE",
            "st_condition": "",
            "st_position": 1
        }
        """
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/trigger"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"

    #POST /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/trigger
    #     Assign a Trigger to a Step
    Scenario: Assign "Trigger Demo2" to first Step of "Task2"
        Given that I have a valid access_token
        And POST this data:
        """
        {
            "tri_uid": "56359776552cd6378b38e47080912028",
            "st_type": "BEFORE",
            "st_condition": "",
            "st_position": 2
        }
        """
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/trigger"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"

    #PUT /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/trigger/{tri_uid}
    #    Update a Trigger assignation of a Step
    Scenario: Update "Trigger Demo1" assigned to first Step of "Task2"
        Given that I have a valid access_token
        And PUT this data:
        """
        {
            "st_type": "BEFORE",
            "st_condition": "@@FIELD2 == 2"
        }
        """
        And that I want to update a resource with the key "tgr1" stored in session array
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/trigger/81919273152cd636c665080083928728"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "st_condition" is set to "@@FIELD2 == 2"

    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/triggers
    #    List assigned Triggers to a Step
    Scenario: List Triggers assigned to first Step of "Task2"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "tri_uid" property in row 1 equals "56359776552cd6378b38e47080912028"
        And the "tri_title" property in row 1 equals "Trigger Demo2"
        And the "tri_description" property in row 1 equals "Description"
        And the "st_type" property in row 1 equals "BEFORE"
        And the "st_condition" property in row 1 equals ""
        And the "st_position" property in row 1 equals "2"

    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/available-triggers/{type}
    #    List available Triggers to assign to a Step
    Scenario: List available Triggers to assign to first Step of "Task2"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/available-triggers/before"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "tri_uid" property in row 0 equals "57401970252cd6393531551040242546"
        And the "tri_title" property in row 0 equals "Trigger Demo3"
        And the "tri_description" property in row 0 equals "Description"
        And the "tri_type" property in row 0 equals "SCRIPT"
        And the "tri_webbot" property in row 0 equals ""
        And the "tri_param" property in row 0 equals ""

    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/trigger/{tri_uid}/{type}
    #    Get a single Trigger assigned to a Step
    Scenario: Get a single Trigger "Trigger Demo1" assigned to first Step of "Task2"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/trigger/81919273152cd636c665080083928728/before"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "tri_uid" is set to "81919273152cd636c665080083928728"
        And that "tri_title" is set to "Trigger Demo1"
        And that "tri_description" is set to "Description"
        And that "st_type" is set to "BEFORE"
        And that "st_condition" is set to "@@FIELD2 == 2"
        And that "st_position" is set to "1"

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/trigger/{tri_uid}/{type}
    #       Remove a Trigger assignation of a Step
    Scenario: Remove "Trigger Demo1" assigned to first Step of "Task2"
        Given that I have a valid access_token
        And that I want to delete a resource with the key "tgr1" stored in session array
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/trigger/81919273152cd636c665080083928728/before"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/trigger/{tri_uid}/{type}
    #       Remove a Trigger assignation of a Step
    Scenario: Remove "Trigger Demo2" assigned to first Step of "Task2"
        Given that I have a valid access_token
        And that I want to delete a resource with the key "tgr2" stored in session array
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/trigger/56359776552cd6378b38e47080912028/before"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/triggers
    #    List assigned Triggers to a Step
    Scenario: List Triggers assigned to first Step of "Task2"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

