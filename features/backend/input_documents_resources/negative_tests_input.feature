@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents Negative Tests


  Background:
    Given that I have a valid access_token

  Scenario Outline: Create a new input document for a project with bad parameters (negative tests)
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
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"
        

        Examples:

        | test_description                                                    | project                          | inp_doc_title                        |  inp_doc_description                | inp_doc_form_needed  | inp_doc_original    | inp_doc_published    | inp_doc_versioning   | inp_doc_destination_path  | inp_doc_tags            | error_code | error_message       |  
        | Create with project invalid                                         | 14414793652a5d718b65590036       | My InputDocument1                    | My InputDocument1 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   | 400        | project             |
        | Invalid inp doc from needed                                         | 14414793652a5d718b65590036026581 | My InputDocument4                    | My InputDocument4 DESCRIPTION       | VRESAMPLE12334$%#@   | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   | 400        | inp_doc_form_needed |
        | Invalid inp doc original                                            | 14414793652a5d718b65590036026581 | My InputDocument5                    | My InputDocument5 DESCRIPTION       | VIRTUAL              | COORIGI 123@#$%$%   | PRIVATE              | 1                    |                           | INPUT                   | 400        | inp_doc_original    |
        | Invalid inp doc published                                           | 14414793652a5d718b65590036026581 | My InputDocument6                    | My InputDocument6 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIV123234@##$$%%    | 0                    |                           | INPUT                   | 400        | inp_doc_published   |
        | Invalid inp doc versioning                                          | 14414793652a5d718b65590036026581 | My InputDocument7                    | My InputDocument7 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 87                   |                           | INPUT                   | 400        | inp_doc_versioning  |
        | Create without id of project                                        |                                  | My InputDocument10                   | My InputDocument10 DESCRIPTION      | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   | 400        | project             |
        | Create without inp doc form needed                                  | 14414793652a5d718b65590036026581 | My InputDocument11                   | My InputDocument11 DESCRIPTION      |                      | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   | 400        | inp_doc_form_needed |
        | Create without inp doc original                                     | 14414793652a5d718b65590036026581 | My InputDocument12                   | My InputDocument12 DESCRIPTION      | REAL                 |                     | PRIVATE              | 1                    |                           | INPUT                   | 400        | inp_doc_original    |     
        | Create without inp doc published                                    | 14414793652a5d718b65590036026581 | My InputDocument13                   | My InputDocument13 DESCRIPTION      | VREAL                | ORIGINAL            |                      | 1                    |                           | INPUT                   | 400        | inp_doc_published   |
        | Create without inp doc title                                        | 14414793652a5d718b65590036026581 |                                      | My InputDocument1 DESCRIPTION       | VIRTUAL              | ORIGINAL            | PRIVATE              | 1                    |                           | INPUT                   | 400        | inp_doc_title       |