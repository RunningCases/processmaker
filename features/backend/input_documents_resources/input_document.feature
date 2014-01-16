@ProcessMakerMichelangelo @RestAPI
Feature: Input Documents Resources

   Background:
    Given that I have a valid access_token

    #GET /api/1.0/{workspace}/project/{prj_uid}/input-documents
    #    Get a List Input Documents of a Project
    Scenario: Get a List Input Documents of a Project
        And I request "project/14414793652a5d718b65590036026581/input-documents"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 records


    #POST /api/1.0/{workspace}/project/{prj_uid}/input-document
    #Create a new Input Document for a Project
    Scenario Outline: Create a new Inputs Documents for a Project
        Given POST this data:
        """
        {
            "inp_doc_title": "<inp_doc_title>",
            "inp_doc_description": "<inp_doc_description>",
            "inp_doc_form_needed": "<inp_doc_form_needed>",
            "inp_doc_original": "<inp_doc_original>",
            "inp_doc_published": "<inp_doc_published>",
            "inp_doc_versioning": "<inp_doc_versioning>",
            "inp_doc_destination_path": "<inp_doc_destination_path>",
            "inp_doc_tags": "<inp_doc_tags>"
        }
        """
        And I request "project/<project>/input-document"
        Then the response status code should be 201
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        And store "inp_doc_uid" in session array as variable "inp_doc_uid_<inp_doc_number>"

        Examples:

        | project                          | inp_doc_number    | inp_doc_title         |  inp_doc_description                | inp_doc_form_needed  | inp_doc_original    | inp_doc_published    | inp_doc_versioning   | inp_doc_destination_path  | inp_doc_tags": "INPUT   |  
        | 14414793652a5d718b65590036026581 | 1                 | My InputDocument1     | My InputDocument1 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | 2                 | My InputDocument2     | My InputDocument2 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | 3                 | My InputDocument3     | My InputDocument3 DESCRIPTION       | REAL                 | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | 4                 | My InputDocument4     | My InputDocument4 DESCRIPTION       | VREAL                | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | 5                 | My InputDocument5     | My InputDocument5 DESCRIPTION       | VIRTUAL              | COPY                | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | 6                 | My InputDocument6     | My InputDocument6 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 0                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | 7                 | My InputDocument7     | My InputDocument7 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    | /my/path                  | INPUT                   |
        | 14414793652a5d718b65590036026581 | 8                 | My InputDocument8     | My InputDocument8 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | 9                 | My InputDocument9     | My InputDocument9 DESCRIPTION       | REAL                 | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |

 

 
    #GET /api/1.0/{workspace}/project/{prj_uid}/input-documents
    #    Get a List Input Documents of a Project
    Scenario: Get a List Input Documents of a Project
        And I request "project/14414793652a5d718b65590036026581/input-documents"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 9 records



    #PUT /api/1.0/{workspace}/project/{prj_uid}/input-document/{inp_doc_uid}
    #    Update an Input Document for a Project
    Scenario Outline: Update an Input Document for a Project
        Given PUT this data:
        """
        {
            "inp_doc_title": "<inp_doc_title>",
            "inp_doc_description": "<inp_doc_description>",
            "inp_doc_form_needed": "<inp_doc_form_needed>",
            "inp_doc_original": "<inp_doc_original>",
            "inp_doc_published": "<inp_doc_published>",
            "inp_doc_versioning": "<inp_doc_versioning>",
            "inp_doc_destination_path": "<inp_doc_destination_path>",
            "inp_doc_tags": "<inp_doc_tags>"
        }
        """
        And that I want to update a resource with the key "inp_doc_uid" stored in session array as variable "inp_doc_uid_<inp_doc_number>"
       And I request "project/<project>/input-document"
      And the content type is "application/json"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the type is "object"


        Examples:

        | project                          | inp_doc_number    | inp_doc_title                 |  inp_doc_description                | inp_doc_form_needed  | inp_doc_original    | inp_doc_published    | inp_doc_versioning   | inp_doc_destination_path  | inp_doc_tags            |  
        | 14414793652a5d718b65590036026581 | 1                 | My InputDocument1 'UPDATE'    | My InputDocument1 DESCRIPTION-update| VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | 2                 | My InputDocument2 'UPDATE'    | My InputDocument2 DESCRIPTION-update| VREAL                | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | 3                 | My InputDocument3 'UPDATE'    | My InputDocument3 DESCRIPTION-update| REAL                 | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |



    Scenario Outline: Get a input Document

    Given that I want to get a resource with the key "inp_doc_uid" stored in session array as variable "inp_doc_uid_<inp_doc_number>"
        And I request "project/<project>/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "inp_doc_title" is set to "<inp_doc_title>"
        And that "inp_doc_description" is set to "<inp_doc_description>"
        And that "inp_doc_form_needed" is set to "<inp_doc_form_needed>"

         Examples:

        | project                          | inp_doc_number    | inp_doc_title                 |  inp_doc_description                | inp_doc_form_needed  |  
        | 14414793652a5d718b65590036026581 | 1                 | My InputDocument1 'UPDATE'    | My InputDocument1 DESCRIPTION-update| VIRTUAL              |
        | 14414793652a5d718b65590036026581 | 2                 | My InputDocument2 'UPDATE'    | My InputDocument2 DESCRIPTION-update| VREAL                |
        | 14414793652a5d718b65590036026581 | 3                 | My InputDocument3 'UPDATE'    | My InputDocument3 DESCRIPTION-update| REAL                 |   


    #GET /api/1.0/{workspace}/project/{prj_uid}/input-document/{inp_doc_uid}
    #    Get a single Input Document of a Project
    Scenario: Get a single Input Document of a Project
        Given that I want to get a resource with the key "inp_doc_uid_1" stored in session array
        And I request "project/14414793652a5d718b65590036026581/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "inp_doc_title" is set to "My InputDocument1 Modified"
        And that "inp_doc_description" is set to "My InputDocument1 DESCRIPTION Modified"

    

    #DELETE /api/1.0/{workspace}/project/{prj_uid}/input-document/{inp_doc_uid}
    #       Delete an Input Document of a Project
    Scenario Outline: Delete a previously created Input Document
        Given that I want to delete a resource with the key "inp_out_uid" stored in session array as variable "inp_doc_uid_<inp_doc_number>"
        And I request "project/<project>/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | project                          | inp_doc_number    |
        | 14414793652a5d718b65590036026581 | 1                 |
        | 14414793652a5d718b65590036026581 | 2                 |
        | 14414793652a5d718b65590036026581 | 3                 |
        | 14414793652a5d718b65590036026581 | 4                 |
        | 14414793652a5d718b65590036026581 | 5                 |
        | 14414793652a5d718b65590036026581 | 6                 |
        | 14414793652a5d718b65590036026581 | 7                 |
        | 14414793652a5d718b65590036026581 | 8                 |
        | 14414793652a5d718b65590036026581 | 9                 | 



    #GET /api/1.0/{workspace}/project/{prj_uid}/input-documents
    #    Get a List Input Documents of a Project
    Scenario: Get a List Input Documents of a Project
        And I request "project/14414793652a5d718b65590036026581/input-documents"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 records




   #POST /api/1.0/{workspace}/project/{prj_uid}/input-document
    #Create a new Input Document for a Project
    Scenario Outline: Create a new Inputs Documents for a Project
        Given POST this data:
        """
        {
            "inp_doc_title": "<inp_doc_title>",
            "inp_doc_description": "<inp_doc_description>",
            "inp_doc_form_needed": "<inp_doc_form_needed>",
            "inp_doc_original": "<inp_doc_original>",
            "inp_doc_published": "<inp_doc_published>",
            "inp_doc_versioning": "<inp_doc_versioning>",
            "inp_doc_destination_path": "<inp_doc_destination_path>",
            "inp_doc_tags": "<inp_doc_tags>"
        }
        """
        And I request "project/<project>/input-document"
        Then the response status code should be 400
        

        Examples:

        | project                          | inp_doc_title                        |  inp_doc_description                | inp_doc_form_needed  | inp_doc_original    | inp_doc_published    | inp_doc_versioning   | inp_doc_destination_path  | inp_doc_tags            |  
        | 14414793652a5d718b65590036       | My InputDocument1                    | My InputDocument1 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument2 !@#$%^&*€¤½¼‘¾¡²¤³ | My InputDocument2 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument3                    | My InputDocument3 !@#$%^&*€¤½¼‘¾¡²¤³| REAL                 | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument4                    | My InputDocument4 DESCRIPTION       | VRESAMPLE12334$%#@   | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument5                    | My InputDocument5 DESCRIPTION       | VIRTUAL              | COORIGI 123@#$%$%   | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument6                    | My InputDocument6 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIV123234@##$$%%    | 0                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument7                    | My InputDocument7 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 87                   |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument8                    | My InputDocument8 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    | /my#@$#%/path324@$@@      | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument9                    | My InputDocument9 DESCRIPTION       | REAL                 | ORIGINAL            | PRIVATE              | 1                    |                           | INPU455 @##$$³¤¤        | 
        |                                  | My InputDocument10                   | My InputDocument10 DESCRIPTION      | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument11                   | My InputDocument11 DESCRIPTION      |                      | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument12                   | My InputDocument12 DESCRIPTION      | REAL                 |                     | PRIVATE              | 1                    |                           | INPUT                   |
        | 14414793652a5d718b65590036026581 | My InputDocument13                   | My InputDocument13 DESCRIPTION      | VREAL                | ORIGINAL            |                      | 1                    |                           | INPUT                   |
        
