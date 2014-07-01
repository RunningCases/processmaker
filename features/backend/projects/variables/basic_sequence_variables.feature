@ProcessMakerMichelangelo @RestAPI
Feature: Process variables Resources
    #GET /api/1.0/{workspace}/project/{prj_uid}/process-variables
    #    Get a List of process variables
    Scenario: Get a List of process variables
        Given that I have a valid access_token
        And I request "project/14414793652a5d718b65590036026581/process-variables"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #POST /api/1.0/{workspace}/project/{prj_uid}/process-variable
    #     Create a process variable
    #     Normal creation of a process variable
    Scenario: Create "My variable" for a Project (Normal creation of a process variable)
        Given that I have a valid access_token
        And POST this data:
        """
        {
          "var_name": "My Variable",
          "var_field_type": "text_field",
          "var_field_size": 12,
          "var_label": "Nombre:",
          "var_dbconnection": "",
          "var_sql": "",
          "var_null": 0,
          "var_default": "",
          "var_accepted_values": ""
        }
        """
        And I request "project/14414793652a5d718b65590036026581/process-variable"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "var_uid" in session array as variable "variable1"

    #PUT /api/1.0/{workspace}/project/{prj_uid}/process-variable/{var_uid}
    #    Update a process variable
    Scenario: Update a process variable
        Given that I have a valid access_token
        And PUT this data:
        """
        {
          "var_name": "My Variable Modify",
          "var_field_type": "text_field",
          "var_field_size": 1,
          "var_label": "Nombre modificado:",
          "var_dbconnection": "",
          "var_sql": "",
          "var_null": 0,
          "var_default": "",
          "var_accepted_values": ""
        }
        """
        And that I want to update a resource with the key "variable1" stored in session array
        And I request "project/14414793652a5d718b65590036026581/process-variable"
        And the content type is "application/json"
        Then the response status code should be 200

    #GET /api/1.0/{workspace}/project/{prj_uid}/process-variables
    #    Get a List of process variables
    Scenario: Get a List of process variables
        Given that I have a valid access_token
        And I request "project/14414793652a5d718b65590036026581/process-variables"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"

    #GET /api/1.0/{workspace}/project/{prj_uid}/process-variable/{var_uid}
    #    Get a single process variable
    Scenario: Get a single process variable
        Given that I have a valid access_token
        And that I want to get a resource with the key "variable1" stored in session array
        And I request "project/14414793652a5d718b65590036026581/process-variable"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/process-variable/{var_uid}
    #       Delete a process variable
    Scenario: Delete a previously created process variable
        Given that I have a valid access_token
        And that I want to delete a resource with the key "variable1" stored in session array
        And I request "project/14414793652a5d718b65590036026581/process-variable"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    #GET /api/1.0/{workspace}/project/{prj_uid}/process-variables
    #    Get a List of process variables
    Scenario: Get a List of process variables
        Given that I have a valid access_token
        And I request "project/14414793652a5d718b65590036026581/process-variables"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

