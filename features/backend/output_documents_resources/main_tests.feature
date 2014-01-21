@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents Main Tests
  Requirements:
    a workspace with the process 4224292655297723eb98691001100052 already loaded
    the process name is "Test Users-Step-Properties End Point"
    there are two output documents in the process


  Background:
    Given that I have a valid access_token


  Scenario: Get the Output Documents List when there are exactly two output documents
    Given I request "project/4224292655297723eb98691001100052/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 2 records
    And the "out_doc_title" property in row 0 equals "Endpoint Old Version (base)"
    And the "out_doc_title" property in row 1 equals "Endpoint New Version (base)"


  Scenario: Get a single output document of a project
    the output document is previously created
    Given I request "project/4224292655297723eb98691001100052/output-document/59969646352d6cd3caa0751003892895"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the "out_doc_title" property equals "Endpoint New Version (base)"
    And the "out_doc_filename" property equals "endpointnewversion"
    And the "out_doc_report_generator" property equals "TCPDF"


  Scenario Outline: Create 17 new Output Documents
      Given POST this data:
      """
      {
        "out_doc_title":            "<out_doc_title>",
        "out_doc_description":      "<out_doc_description>",
        "out_doc_filename":         "<out_doc_filename>",
        "out_doc_template":         "<out_doc_template>",
        "out_doc_report_generator": "<out_doc_report_generator>",
        "out_doc_landscape":        "<out_doc_landscape>",
        "out_doc_media":            "<out_doc_media>",
        "out_doc_left_margin":      "<out_doc_left_margin>",
        "out_doc_right_margin":     "<out_doc_right_margin>",
        "out_doc_top_margin":       "<out_doc_top_margin>",
        "out_doc_bottom_margin":    "<out_doc_bottom_margin>",
        "out_doc_generate":         "<out_doc_generate>",
        "out_doc_type":             "<out_doc_type>",
        "out_doc_current_revision": "<out_doc_current_revision>",
        "out_doc_field_mapping":    "<out_doc_field_mapping>",
        "out_doc_versioning":       "<out_doc_versioning>",
        "out_doc_destination_path": "<out_doc_destination_path>",
        "out_doc_tags":             "<out_doc_tags>",
        "out_doc_pdf_security_enabled":        "<out_doc_pdf_security_enabled>",
        "out_doc_pdf_security_open_password":  "<out_doc_pdf_security_open_password>",
        "out_doc_pdf_security_owner_password": "<out_doc_pdf_security_owner_password>",
        "out_doc_pdf_security_permissions":    "<out_doc_pdf_security_permissions>"
      }
      """
      And I request "project/<project>/output-document"
      Then the response status code should be 201
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "object"
      And store "out_doc_uid" in session array as variable "out_doc_uid_<out_doc_number>"

      Examples:

        | test_description                                                      | project                          | out_doc_number | out_doc_title                     | out_doc_description                      |out_doc_filename  | out_doc_template | out_doc_report_generator | out_doc_landscape | out_doc_media | out_doc_left_margin | out_doc_right_margin | out_doc_top_margin | out_doc_bottom_margin | out_doc_generate | out_doc_type | out_doc_current_revision | out_doc_field_mapping | out_doc_versioning | out_doc_destination_path | out_doc_tags | out_doc_pdf_security_enabled | out_doc_pdf_security_open_password | out_doc_pdf_security_owner_password | out_doc_pdf_security_permissions |
        | Create with old version and both                                      | 4224292655297723eb98691001100052 | 1              | Endpoint Old Version              | Output Document old version - EndPoint   | Output 1         |                  | HTML2PDF                 | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | Craate with old version and pdf security                              | 4224292655297723eb98691001100052 | 2              | Endpoint Old Version PDF SECURITY | Output Document old version PDF SECURITY | Output 2         |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | Create with old version only doc                                      | 4224292655297723eb98691001100052 | 3              | Endpoint Old Version Doc          | Output Document old version solo doc     | Output 3         |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC              | HTML         | 0                        |                       | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        | Create with old version only pdf                                      | 4224292655297723eb98691001100052 | 4              | Endpoint Old Version PDF          | Output Document old version solo pdf     | Output 4         |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
        | Create with new version and  both                                     | 4224292655297723eb98691001100052 | 5              | Endpoint New Version              | Output Document new version - EndPoint   | Output 5         |                  | TCPDF                    | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | Create with new version and pdf security                              | 4224292655297723eb98691001100052 | 6              | Endpoint New Version PDF SECURITY | Output Document new version PDF SECURITY | Output 6         |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | Create with new version only doc                                      | 4224292655297723eb98691001100052 | 7              | Endpoint New Version Doc          | Output Document new version solo doc     | Output 7         |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC              | HTML         | 0                        |                       | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        | Create with new version only pdf                                      | 4224292655297723eb98691001100052 | 8              | Endpoint New Version PDF          | Output Document new version solo pdf     | Output 8         |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
        | Create with special characters in out doc title                       | 4224292655297723eb98691001100052 | 9              | test !@#$%^&*€¤¾½²³€¼½¼           | Output Document old version - EndPoint   | Output 9         |                  | HTML2PDF                 | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | Create with special characters in out doc description                 | 4224292655297723eb98691001100052 | 10             | Endpoint Old1                     | test %^&*€¤¾½²³€                         | Output 10        |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | Create with special characters in out doc filename                    | 4224292655297723eb98691001100052 | 11             | Endpoint Old Version Doc 2        | Output Document old version solo doc     | Output @#$%^&*€¤ |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC              | HTML         | 0                        |                       | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        | Create with special characters in out doc template                    | 4224292655297723eb98691001100052 | 12             | Endpoint Old Version PDF 3        | Output Document old version solo pdf     | Output 11        | sample @#$%^&*€¤ | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
        | Create with special characters in out doc field mapping               | 4224292655297723eb98691001100052 | 13             | Endpoint New Version Doc 14       | Output Document new version solo doc     | Output 12        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC              | HTML         | 0                        | 324#$%%^^@@           | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        | Create with special characters in out doc destination path            | 4224292655297723eb98691001100052 | 14             | Endpoint New Version  16          | Output Document new version - EndPoint   | Output 13        |                  | TCPDF                    | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  | 23rg@#$%                 |              | 0                            |                                    |                                     |                                  |
        | Create with special characters in out doc tags                        | 4224292655297723eb98691001100052 | 15             | Endpoint New Version PDF SECURI17 | Output Document new version PDF SECURITY | Output 14        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          | vfv23@$@%    | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | Create with special characters in out doc pdf security open password  | 4224292655297723eb98691001100052 | 16             | Endpoint New Version PDF  19      | Output Document new version solo pdf     | Output 15        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample432@$#@$¼€¼½                 | sample                              | print                            |
        | Create with special characters in out doc pdf security owner password | 4224292655297723eb98691001100052 | 17             | Endpoint New Version 20           | Output Document new version - EndPoint   | Output 16        |                  | TCPDF                    | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    | sample432@$#@$¼€¼½                  |                                  |


  Scenario: Get the Output Documents list when there are 19 records
    Given I request "project/4224292655297723eb98691001100052/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 19 records


  Scenario Outline: Update the Output Documents and then check if the values had changed
    Given PUT this data:
      """
      {
        "out_doc_title":            "<out_doc_title>",
        "out_doc_description":      "<out_doc_description>",
        "out_doc_filename":         "<out_doc_filename>",
        "out_doc_template":         "<out_doc_template>",
        "out_doc_report_generator": "<out_doc_report_generator>",
        "out_doc_landscape":        "<out_doc_landscape>",
        "out_doc_media":            "<out_doc_media>",
        "out_doc_left_margin":      "<out_doc_left_margin>",
        "out_doc_right_margin":     "<out_doc_right_margin>",
        "out_doc_top_margin":       "<out_doc_top_margin>",
        "out_doc_bottom_margin":    "<out_doc_bottom_margin>",
        "out_doc_generate":         "<out_doc_generate>",
        "out_doc_type":             "<out_doc_type>",
        "out_doc_current_revision": "<out_doc_current_revision>",
        "out_doc_field_mapping":    "<out_doc_field_mapping>",
        "out_doc_versioning":       "<out_doc_versioning>",
        "out_doc_destination_path": "<out_doc_destination_path>",
        "out_doc_tags":             "<out_doc_tags>",
        "out_doc_pdf_security_enabled":        "<out_doc_pdf_security_enabled>",
        "out_doc_pdf_security_open_password":  "<out_doc_pdf_security_open_password>",
        "out_doc_pdf_security_owner_password": "<out_doc_pdf_security_owner_password>",
        "out_doc_pdf_security_permissions":    "<out_doc_pdf_security_permissions>"
      }
      """
      And that I want to update a resource with the key "out_doc_uid" stored in session array as variable "out_doc_uid_<out_doc_number>"
      And I request "project/<project>/output-document"
      And the content type is "application/json"
      Then the response status code should be 200
      And the response charset is "UTF-8"

      Examples:

        | test_description                                                      | project                          | out_doc_number | out_doc_title                            | out_doc_description                      |out_doc_filename  | out_doc_template | out_doc_report_generator | out_doc_landscape | out_doc_media | out_doc_left_margin | out_doc_right_margin | out_doc_top_margin | out_doc_bottom_margin | out_doc_generate | out_doc_type | out_doc_current_revision | out_doc_field_mapping | out_doc_versioning | out_doc_destination_path | out_doc_tags | out_doc_pdf_security_enabled | out_doc_pdf_security_open_password | out_doc_pdf_security_owner_password | out_doc_pdf_security_permissions |
        | Update out doc title and description                                  | 4224292655297723eb98691001100052 | 1              | Endpoint Old Version UPDATE              | Output Document old version - UPDATE     | Output 1         |                  | HTML2PDF                 | 0                 | Letter        | 20                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | Update out doc title and out doc generate                             | 4224292655297723eb98691001100052 | 2              | Endpoint Old Version PDF SECURITY UPDATE | Output UPDATE old version PDF SECURITY   | Output 2         |                  | TCPDF                    | 1                 | Legal         | 20                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy              |
        | Update out doc title and description                                  | 4224292655297723eb98691001100052 | 5              | Endpoint New Version UPDATE              | Output UPDATE new version - EndPoint     | Output 5         |                  | TCPDF                    | 0                 | Letter        | 30                  | 20                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | Update out doc title and out doc generate                             | 4224292655297723eb98691001100052 | 6              | Endpoint New Version PDF SECURITY UPDATE | Output UPDATE new version PDF SECURITY   | Output 6         |                  | HTML2PDF                 | 1                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy              |


    Scenario Outline: Get a single Output Document and check some properties
      Given that I want to get a resource with the key "out_doc_uid" stored in session array as variable "out_doc_uid_<out_doc_number>"
        And I request "project/<project>/output-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And that "out_doc_title" is set to "<out_doc_title>"
        And that "out_doc_description" is set to "<out_doc_description>"
        And that "out_doc_filename" is set to "<out_doc_filename>"

      Examples:

        | project                          | out_doc_number | out_doc_title                            | out_doc_description                      |out_doc_filename  |
        | 4224292655297723eb98691001100052 | 1              | Endpoint Old Version UPDATE              | Output Document old version - UPDATE     | Output 1         |
        | 4224292655297723eb98691001100052 | 2              | Endpoint Old Version PDF SECURITY UPDATE | Output UPDATE old version PDF SECURITY   | Output 2         |
        | 4224292655297723eb98691001100052 | 5              | Endpoint New Version UPDATE              | Output UPDATE new version - EndPoint     | Output 5         |
        | 4224292655297723eb98691001100052 | 6              | Endpoint New Version PDF SECURITY UPDATE | Output UPDATE new version PDF SECURITY   | Output 6         |


    Scenario Outline: Delete all Output documents created previously in this script
    Given that I want to delete a resource with the key "out_doc_uid" stored in session array as variable "out_doc_uid_<out_doc_number>"
        And I request "project/<project>/output-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | project                          | out_doc_number |
        | 4224292655297723eb98691001100052 | 1              |
        | 4224292655297723eb98691001100052 | 2              |
        | 4224292655297723eb98691001100052 | 3              |
        | 4224292655297723eb98691001100052 | 4              |
        | 4224292655297723eb98691001100052 | 5              |
        | 4224292655297723eb98691001100052 | 6              |
        | 4224292655297723eb98691001100052 | 7              |
        | 4224292655297723eb98691001100052 | 8              |
        | 4224292655297723eb98691001100052 | 9              |
        | 4224292655297723eb98691001100052 | 10             |
        | 4224292655297723eb98691001100052 | 11             |
        | 4224292655297723eb98691001100052 | 12             |
        | 4224292655297723eb98691001100052 | 13             |
        | 4224292655297723eb98691001100052 | 14             |
        | 4224292655297723eb98691001100052 | 15             |
        | 4224292655297723eb98691001100052 | 16             |
        | 4224292655297723eb98691001100052 | 17             |


  Scenario: Get the Output Documents List when should be exactly two output documents
    Given I request "project/4224292655297723eb98691001100052/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 2 records
    And the "out_doc_title" property in row 0 equals "Endpoint Old Version (base)"
    And the "out_doc_title" property in row 1 equals "Endpoint New Version (base)"

