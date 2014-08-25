@ProcessMakerMichelangelo @RestAPI
Feature: Input Documents Main Tests
 Requirements:
    a workspace with the process 14414793652a5d718b65590036026581 already loaded
    the process name is "Sample Project #1"
    there are zero input documents in the process

   
   Background:
    Given that I have a valid access_token


    Scenario: Get the Input Documents List when there are exactly zero input documents
        Given I request "project/14414793652a5d718b65590036026581/input-documents"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 records


    Scenario Outline: Create 13 new Input Documents
        Given POST this data:
        """
        {
            "inp_doc_title": "<inp_doc_title>",
            "inp_doc_description": "<inp_doc_description>",
            "inp_doc_form_needed": "<inp_doc_form_needed>",
            "inp_doc_original": "<inp_doc_original>",
            "inp_doc_published": "<inp_doc_published>",
            "inp_doc_versioning": <inp_doc_versioning>,
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

            | test_description                                             | project                          | inp_doc_number | inp_doc_title                         |  inp_doc_description                 | inp_doc_form_needed  | inp_doc_original | inp_doc_published | inp_doc_versioning | inp_doc_destination_path | inp_doc_tags     |  
            | Create with virtual with versioning                          | 14414793652a5d718b65590036026581 | 1              | My InputDocument1                     | My InputDocument2 DESCRIPTION        | VIRTUAL              | ORIGINAL         | PRIVATE           | 1                  |                          | INPUT            |
            | Create with virtual without versioning                       | 14414793652a5d718b65590036026581 | 2              | My InputDocument2                     | My InputDocument2 DESCRIPTION        | VIRTUAL              | ORIGINAL         | PRIVATE           | 0                  |                          | INPUT            |
            | Create with real with versioning                             | 14414793652a5d718b65590036026581 | 3              | My InputDocument3                     | My InputDocument3 DESCRIPTION        | REAL                 | ORIGINAL         | PRIVATE           | 1                  |                          | INPUT            |
            | Create with vreal with versioning                            | 14414793652a5d718b65590036026581 | 4              | My InputDocument4                     | My InputDocument4 DESCRIPTION        | VREAL                | ORIGINAL         | PRIVATE           | 1                  |                          | INPUT            |
            | Create with virtual, copy and versioning                     | 14414793652a5d718b65590036026581 | 5              | My InputDocument5                     | My InputDocument5 DESCRIPTION        | VIRTUAL              | COPY             | PRIVATE           | 1                  |                          | INPUT            |
            | Create with  virtual, original and without versioning        | 14414793652a5d718b65590036026581 | 6              | My InputDocument6                     | My InputDocument6 DESCRIPTION        | VIRTUAL              | ORIGINAL         | PRIVATE           | 0                  |                          | INPUT            |
            | Create with virtual, versioning and destination path         | 14414793652a5d718b65590036026581 | 7              | My InputDocument7                     | My InputDocument7 DESCRIPTION        | VIRTUAL              | ORIGINAL         | PRIVATE           | 1                  | /my/path                 | INPUT            |
            | Create with virtual, without versioning and destination path | 14414793652a5d718b65590036026581 | 8              | My InputDocument8                     | My InputDocument8 DESCRIPTION        | VIRTUAL              | ORIGINAL         | PRIVATE           | 0                  | /my/path                 | INPUT            |
            | Create with real, without versioning and destination path    | 14414793652a5d718b65590036026581 | 9              | My InputDocument9                     | My InputDocument9 DESCRIPTION        | REAL                 | ORIGINAL         | PRIVATE           | 0                  | /my/path                 | INPUT            |
            | Create with special characters in inp doc title              | 14414793652a5d718b65590036026581 | 10             | My InputDocument10  !@#$%^&*€¤½¼‘¾¡²¤³| My InputDocument10 DESCRIPTION       | VIRTUAL              | ORIGINAL         | PRIVATE           | 1                  |                          | INPUT            |
            | Create with special characters in inp doc description        | 14414793652a5d718b65590036026581 | 11             | My InputDocument11                    | My InputDocument11 !@#$%^&*€¤½¼‘¾¡²¤³| REAL                 | ORIGINAL         | PRIVATE           | 1                  |                          | INPUT            |
            | Create with special characters in inp doc destination path   | 14414793652a5d718b65590036026581 | 12             | My InputDocument12                    | My InputDocument12 DESCRIPTION       | VIRTUAL              | ORIGINAL         | PRIVATE           | 1                  | /my#@$#%/path324@$@@     | INPUT            |
            | Create with special characters in inp doc tags               | 14414793652a5d718b65590036026581 | 13             | My InputDocument13                    | My InputDocument13 DESCRIPTION       | REAL                 | ORIGINAL         | PRIVATE           | 1                  |                          | INPU455 @##$$³¤¤ |

 
    Scenario: Create new Input Documents with same name
        Given POST this data:
        """
        {
            "inp_doc_title": "My InputDocument1",
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
            Then the response status code should be 400
            And the response status message should have the following text "already exists"

 
    Scenario: Get the Input Documents list when there are 13 records
        Given I request "project/14414793652a5d718b65590036026581/input-documents"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 13 records



    Scenario Outline: Update the Input Documents and then check if the values had changed
        Given PUT this data:
        """
        {
            "inp_doc_title": "<inp_doc_title>",
            "inp_doc_description": "<inp_doc_description>",
            "inp_doc_form_needed": "<inp_doc_form_needed>",
            "inp_doc_original": "<inp_doc_original>",
            "inp_doc_published": "<inp_doc_published>",
            "inp_doc_versioning": <inp_doc_versioning>,
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

        | test_description                                                      | project                          | inp_doc_number    | inp_doc_title                 |  inp_doc_description                | inp_doc_form_needed  | inp_doc_original    | inp_doc_published    | inp_doc_versioning   | inp_doc_destination_path  | inp_doc_tags            |  
        | Update inp doc title and inp doc description                          | 14414793652a5d718b65590036026581 | 1                 | My InputDocument1 'UPDATE'    | My InputDocument1 DESCRIPTION-update| VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | Update inp doc title, inp doc description and inp doc form needed     | 14414793652a5d718b65590036026581 | 2                 | My InputDocument2 'UPDATE'    | My InputDocument2 DESCRIPTION-update| VREAL                | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   |
        | Update inp doc title, inp doc description and inp doc versioning      | 14414793652a5d718b65590036026581 | 3                 | My InputDocument3 'UPDATE'    | My InputDocument3 DESCRIPTION-update| REAL                 | ORIGINAL            | PRIVATE              | 0                    |                           | INPUT                   |



    Scenario Outline: Get a single Input Document to verify that the were updated correctly in previous step
        Given that I want to get a resource with the key "inp_doc_uid" stored in session array as variable "inp_doc_uid_<inp_doc_number>"
        And I request "project/<project>/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "inp_doc_title" is set to "<inp_doc_title>"
        And that "inp_doc_description" is set to "<inp_doc_description>"
        And that "inp_doc_form_needed" is set to "<inp_doc_form_needed>"
        And that "inp_doc_versioning" is set to <inp_doc_versioning>

         Examples:

        | project                          | inp_doc_number    | inp_doc_title                 |  inp_doc_description                | inp_doc_form_needed  | inp_doc_versioning   |  
        | 14414793652a5d718b65590036026581 | 1                 | My InputDocument1 'UPDATE'    | My InputDocument1 DESCRIPTION-update| VIRTUAL              | 1                    |
        | 14414793652a5d718b65590036026581 | 2                 | My InputDocument2 'UPDATE'    | My InputDocument2 DESCRIPTION-update| VREAL                | 1                    |
        | 14414793652a5d718b65590036026581 | 3                 | My InputDocument3 'UPDATE'    | My InputDocument3 DESCRIPTION-update| REAL                 | 0                    |   


     

    Scenario Outline: Delete all Input documents created previously in this script
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
        | 14414793652a5d718b65590036026581 | 10                |
        | 14414793652a5d718b65590036026581 | 11                |
        | 14414793652a5d718b65590036026581 | 12                |
        | 14414793652a5d718b65590036026581 | 13                | 



    Scenario: Get the Input Documents List when there are exactly zero input documents after of delete all Input documents
        Given I request "project/14414793652a5d718b65590036026581/input-documents"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 records
