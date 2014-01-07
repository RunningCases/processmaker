@ProcessMakerMichelangelo @RestAPI
Feature: Input Documents Resources
    #GET /api/1.0/{workspace}/project/{prj_uid}/input-documents
    #    Get a List Input Documents of a Project
    Scenario: Get a List Input Documents of a Project
        Given that I have a valid access_token
        And I request "project/14414793652a5d718b65590036026581/input-documents"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    #POST /api/1.0/{workspace}/project/{prj_uid}/input-document
    #     Create a new Input Document for a Project
    Scenario: Create "My InputDocument1" for a Project
        Given that I have a valid access_token
        And POST this data:
        """
        {
            "inp_doc_title": "My InputDocument1",
            "inp_doc_description": "My InputDocument1 DESCRIPTION",
            "inp_doc_form_needed": "VIRTUAL",
            "inp_doc_original": "ORIGINAL",
            "inp_doc_published": "PRIVATE",
            "inp_doc_versioning": 1,
            "inp_doc_destination_path": "",
            "inp_doc_tags": "INPUT"
        }
        """
        And I request "project/14414793652a5d718b65590036026581/input-document"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "inp_doc_uid" in session array as variable "indoc1"

    #POST /api/1.0/{workspace}/project/{prj_uid}/input-document
    #     Create a new Input Document for a Project
    Scenario: Create "My InputDocument2" for a Project
        Given that I have a valid access_token
        And POST this data:
        """
        {
            "inp_doc_title": "My InputDocument2",
            "inp_doc_description": "My InputDocument2 DESCRIPTION",
            "inp_doc_form_needed": "VIRTUAL",
            "inp_doc_original": "ORIGINAL",
            "inp_doc_published": "PRIVATE",
            "inp_doc_versioning": 1,
            "inp_doc_destination_path": "",
            "inp_doc_tags": "INPUT"
        }
        """
        And I request "project/14414793652a5d718b65590036026581/input-document"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "inp_doc_uid" in session array as variable "indoc2"

    #PUT /api/1.0/{workspace}/project/{prj_uid}/input-document/{inp_doc_uid}
    #    Update an Input Document for a Project
    Scenario: Update an Input Document for a Project
        Given that I have a valid access_token
        And PUT this data:
        """
        {
            "inp_doc_title": "My InputDocument1 Modified",
            "inp_doc_description": "My InputDocument1 DESCRIPTION Modified",
            "inp_doc_form_needed": "VIRTUAL",
            "inp_doc_original": "ORIGINAL",
            "inp_doc_published": "PRIVATE"
        }
        """
        And that I want to update a resource with the key "indoc1" stored in session array
        And I request "project/14414793652a5d718b65590036026581/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "inp_doc_title" is set to "My InputDocument1 Modified"

    #GET /api/1.0/{workspace}/project/{prj_uid}/input-documents
    #    Get a List Input Documents of a Project
    Scenario: Get a List Input Documents of a Project
        Given that I have a valid access_token
        And I request "project/14414793652a5d718b65590036026581/input-documents"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "inp_doc_title" property in row 0 equals "My InputDocument1 Modified"
        And the "inp_doc_description" property in row 0 equals "My InputDocument1 DESCRIPTION Modified"

    #GET /api/1.0/{workspace}/project/{prj_uid}/input-document/{inp_doc_uid}
    #    Get a single Input Document of a Project
    Scenario: Get a single Input Document of a Project
        Given that I have a valid access_token
        And that I want to get a resource with the key "indoc1" stored in session array
        And I request "project/14414793652a5d718b65590036026581/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "inp_doc_title" is set to "My InputDocument1 Modified"
        And that "inp_doc_description" is set to "My InputDocument1 DESCRIPTION Modified"

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/input-document/{inp_doc_uid}
    #       Delete an Input Document of a Project
    Scenario: Delete a previously created Input Document
        Given that I have a valid access_token
        And that I want to delete a resource with the key "indoc1" stored in session array
        And I request "project/14414793652a5d718b65590036026581/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/input-document/{inp_doc_uid}
    #       Delete an Input Document of a Project
    Scenario: Delete a previously created Input Document
        Given that I have a valid access_token
        And that I want to delete a resource with the key "indoc2" stored in session array
        And I request "project/14414793652a5d718b65590036026581/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    #GET /api/1.0/{workspace}/project/{prj_uid}/input-documents
    #    Get a List Input Documents of a Project
    Scenario: Get a List Input Documents of a Project
        Given that I have a valid access_token
        And I request "project/14414793652a5d718b65590036026581/input-documents"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

