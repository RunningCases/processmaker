@ProcessMakerMichelangelo @RestAPI
Feature: DynaForms Resources
    #GET /api/1.0/{workspace}/project/{prj_uid}/dynaforms
    #    Get a List DynaForms of a Project
    Scenario: Get a List DynaForms of a Project
        Given that I have a valid access_token
        And I request "project/14414793652a5d718b65590036026581/dynaforms"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #POST /api/1.0/{workspace}/project/{prj_uid}/dynaform
    #     Create a new DynaForm for a Project
    #     Creation normal of a DynaForm
    Scenario: Create "My DynaForm1" for a Project (Creation normal of a DynaForm)
        Given that I have a valid access_token
        And POST this data:
        """
        {
            "dyn_title": "My DynaForm1",
            "dyn_description": "My DynaForm1 DESCRIPTION",
            "dyn_type": "xmlform"
        }
        """
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "dyn_uid" in session array as variable "dynaForm1"

    #POST /api/1.0/{workspace}/project/{prj_uid}/dynaform
    #     Create a new DynaForm for a Project
    #     Copy/Import a DynaForm
    Scenario: Create "DynaForm Demo" for a Project (Copy/Import a DynaForm)
        Given that I have a valid access_token
        And POST this data:
        """
        {
            "dyn_title": "DynaForm Demo",
            "dyn_description": "Description",
            "dyn_type": "xmlform",
            "copy_import":
            {
                "prj_uid": "48207364252cc27894ca354020290166",
                "dyn_uid": "68268455252cc27cb463d76013645722"
            }
        }
        """
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "dyn_uid" in session array as variable "dynaForm2"

    #POST /api/1.0/{workspace}/project/{prj_uid}/dynaform
    #     Create a new DynaForm for a Project
    #     Creation of a DynaForm based in a PMTable
    Scenario: Create "My DynaForm3" for a Project (Creation of a DynaForm based in a PMTable)
        Given that I have a valid access_token
        And POST this data:
        """
        {
            "dyn_title": "My DynaForm3",
            "dyn_description": "My DynaForm3 DESCRIPTION",
            "dyn_type": "xmlform",
            "pmtable":
            {
                "tab_uid": "65193158852cc1a93a5a535084878044",
                "fields": [
                      {
                          "fld_name": "DYN_UID",
                          "pro_variable": "@#APPLICATION"
                      }
                 ]
            }
        }
        """
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "dyn_uid" in session array as variable "dynaForm3"

    #PUT /api/1.0/{workspace}/project/{prj_uid}/dynaform/{dyn_uid}
    #    Update a DynaForm for a Project
    Scenario: Update a DynaForm for a Project
        Given that I have a valid access_token
        And PUT this data:
        """
        {
            "dyn_title": "My DynaForm1 Modified",
            "dyn_description": "My DynaForm1 DESCRIPTION Modified",
            "dyn_type": "xmlform"
        }
        """
        And that I want to update a resource with the key "dynaForm1" stored in session array
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "dyn_title" is set to "My DynaForm1 Modified"

    #GET /api/1.0/{workspace}/project/{prj_uid}/dynaforms
    #    Get a List DynaForms of a Project
    Scenario: Get a List DynaForms of a Project
        Given that I have a valid access_token
        And I request "project/14414793652a5d718b65590036026581/dynaforms"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "dyn_title" property in row 0 equals "DynaForm Demo"
        And the "dyn_description" property in row 0 equals "Description"
        And the "dyn_type" property in row 0 equals "xmlform"

    #GET /api/1.0/{workspace}/project/{prj_uid}/dynaform/{dyn_uid}
    #    Get a single DynaForm of a Project
    Scenario: Get a single DynaForm of a Project
        Given that I have a valid access_token
        And that I want to get a resource with the key "dynaForm3" stored in session array
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "dyn_title" is set to "My DynaForm3"
        And that "dyn_description" is set to "My DynaForm3 DESCRIPTION"
        And that "dyn_type" is set to "xmlform"

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/dynaform/{dyn_uid}
    #       Delete a DynaForm of a Project
    Scenario: Delete a previously created DynaForms
        Given that I have a valid access_token
        And that I want to delete a resource with the key "dynaForm1" stored in session array
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/dynaform/{dyn_uid}
    #       Delete a DynaForm of a Project
    Scenario: Delete a previously created DynaForms
        Given that I have a valid access_token
        And that I want to delete a resource with the key "dynaForm2" stored in session array
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/dynaform/{dyn_uid}
    #       Delete a DynaForm of a Project
    Scenario: Delete a previously created DynaForms
        Given that I have a valid access_token
        And that I want to delete a resource with the key "dynaForm3" stored in session array
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    #GET /api/1.0/{workspace}/project/{prj_uid}/dynaforms
    #    Get a List DynaForms of a Project
    Scenario: Get a List DynaForms of a Project
        Given that I have a valid access_token
        And I request "project/14414793652a5d718b65590036026581/dynaforms"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

