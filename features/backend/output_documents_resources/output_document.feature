@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents Resources

  Background:
    Given that I have a valid access_token

  @1: TEST FOR GET OUTPUT DOCUMENT /--------------------------------------------------------------------
  Scenario: Get a List output documents of a project
    Given I request "project/4224292655297723eb98691001100052/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 2 records

  
  @2: TEST FOR GET SINGLE OUTPUT DOCUMENT /--------------------------------------------------------------------
  Scenario: Get a single output document of a project
  Given I request "project/4224292655297723eb98691001100052/output-document/59969646352d6cd3caa0751003892895"
  Then the response status code should be 200
  And the response charset is "UTF-8"
  And the content type is "application/json"
  And the type is "object"
  

  
  @3: TEST FOR POST OUTPUT DOCUMENT /--------------------------------------------------------------------
  Scenario Outline: Create a new output document for a project
      Given POST this data:
      """
      {
      "out_doc_title": "<out_doc_title>",
      "out_doc_description": "<out_doc_description>",
      "out_doc_filename": "<out_doc_filename>",
      "out_doc_template": "<out_doc_template>",
      "out_doc_report_generator": "<out_doc_report_generator>",
      "out_doc_landscape": "<out_doc_landscape>",
      "out_doc_media": "<out_doc_media>",
      "out_doc_left_margin": "<out_doc_left_margin>",
      "out_doc_right_margin": "<out_doc_right_margin>",
      "out_doc_top_margin": "<out_doc_top_margin>",
      "out_doc_bottom_margin": "<out_doc_bottom_margin>",
      "out_doc_generate": "<out_doc_generate>",
      "out_doc_type": "<out_doc_type>",
      "out_doc_current_revision": "<out_doc_current_revision>",
      "out_doc_field_mapping": "<out_doc_field_mapping>",
      "out_doc_versioning": "<out_doc_versioning>",
      "out_doc_destination_path": "<out_doc_destination_path>",
      "out_doc_tags": "<out_doc_tags>",
      "out_doc_pdf_security_enabled": "<out_doc_pdf_security_enabled>",
      "out_doc_pdf_security_open_password": "<out_doc_pdf_security_open_password>",
      "out_doc_pdf_security_owner_password": "<out_doc_pdf_security_owner_password>",
      "out_doc_pdf_security_permissions": "<out_doc_pdf_security_permissions>"
      }
      """
      And I request "project/<project>/output-document"
      Then the response status code should be 201
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "object"
      And store "out_doc_uid" in session array as variable "out_doc_uid_<out_doc_number>"

      Examples:

        | project                          | out_doc_number | out_doc_title                     | out_doc_description                      |out_doc_filename  | out_doc_template | out_doc_report_generator | out_doc_landscape | out_doc_media | out_doc_left_margin | out_doc_right_margin | out_doc_top_margin | out_doc_bottom_margin | out_doc_generate | out_doc_type | out_doc_current_revision | out_doc_field_mapping | out_doc_versioning | out_doc_destination_path | out_doc_tags | out_doc_pdf_security_enabled | out_doc_pdf_security_open_password | out_doc_pdf_security_owner_password | out_doc_pdf_security_permissions |  
        | 4224292655297723eb98691001100052 | 1              | Endpoint Old Version              | Output Document old version - EndPoint   | Output 1         |                  | HTML2PDF                 | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | 2              | Endpoint Old Version PDF SECURITY | Output Document old version PDF SECURITY | Output 2         |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | 4224292655297723eb98691001100052 | 3              | Endpoint Old Version Doc          | Output Document old version solo doc     | Output 3         |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC             | HTML         | 0                        |                       | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | 4              | Endpoint Old Version PDF          | Output Document old version solo pdf     | Output 4         |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
        | 4224292655297723eb98691001100052 | 5              | Endpoint New Version              | Output Document new version - EndPoint   | Output 5         |                  | TCPDF                    | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | 6              | Endpoint New Version PDF SECURITY | Output Document new version PDF SECURITY | Output 6         |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | 4224292655297723eb98691001100052 | 7              | Endpoint New Version Doc          | Output Document new version solo doc     | Output 7         |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC             | HTML         | 0                        |                       | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | 8              | Endpoint New Version PDF          | Output Document new version solo pdf     | Output 8         |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
        | 4224292655297723eb98691001100052 | 9              | test !@#$%^&*€¤¾½²³€¼½¼           | Output Document old version - EndPoint   | Output 9         |                  | HTML2PDF                 | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | 10             | Endpoint Old1                     | test %^&*€¤¾½²³€                         | Output 10        |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | 4224292655297723eb98691001100052 | 11             | Endpoint Old Version Doc 2        | Output Document old version solo doc     | Output @#$%^&*€¤ |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC             | HTML         | 0                        |                       | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | 12             | Endpoint Old Version PDF 3        | Output Document old version solo pdf     | Output 11        | sample @#$%^&*€¤ | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
        | 4224292655297723eb98691001100052 | 13             | Endpoint New Version Doc 14       | Output Document new version solo doc     | Output 12        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC             | HTML         | 0                        | 324#$%%^^@@           | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | 14             | Endpoint New Version  16          | Output Document new version - EndPoint   | Output 13        |                  | TCPDF                    | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  | 23rg@#$%                 |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | 15             | Endpoint New Version PDF SECURI17 | Output Document new version PDF SECURITY | Output 14        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          | vfv23@$@%    | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | 4224292655297723eb98691001100052 | 16             | Endpoint New Version PDF  19      | Output Document new version solo pdf     | Output 15        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample432@$#@$¼€¼½                 | sample                              | print                            |
        | 4224292655297723eb98691001100052 | 17             | Endpoint New Version 20           | Output Document new version - EndPoint   | Output 16        |                  | TCPDF                    | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    | sample432@$#@$¼€¼½                  |                                  |



  @4: TEST FOR GET OUTPUT DOCUMENT /--------------------------------------------------------------------
  Scenario: Get a List output documents of a project
    Given I request "project/4224292655297723eb98691001100052/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 19 records


  @5: TEST FOR PUT UPDATE OUTPUT DOCUMENT /--------------------------------------------------------------------
  Scenario Outline: Update a output document for a project
    Given PUT this data:
      """
      {
        "out_doc_title": "<out_doc_title>",
        "out_doc_description": "<out_doc_description>",
        "out_doc_filename": "<out_doc_filename>",
        "out_doc_template": "<out_doc_template>",
        "out_doc_report_generator": "<out_doc_report_generator>",
        "out_doc_landscape": "<out_doc_landscape>",
        "out_doc_media": "<out_doc_media>",
        "out_doc_left_margin": "<out_doc_left_margin>",
        "out_doc_right_margin": "<out_doc_right_margin>",
        "out_doc_top_margin": "<out_doc_top_margin>",
        "out_doc_bottom_margin": "<out_doc_bottom_margin>",
        "out_doc_generate": "<out_doc_generate>",
        "out_doc_type": "<out_doc_type>",
        "out_doc_current_revision": "<out_doc_current_revision>",
        "out_doc_field_mapping": "<out_doc_field_mapping>",
        "out_doc_versioning": "<out_doc_versioning>",
        "out_doc_destination_path": "<out_doc_destination_path>",
        "out_doc_tags": "<out_doc_tags>",
        "out_doc_pdf_security_enabled": "<out_doc_pdf_security_enabled>",
        "out_doc_pdf_security_open_password": "<out_doc_pdf_security_open_password>",
        "out_doc_pdf_security_owner_password": "<out_doc_pdf_security_owner_password>",
        "out_doc_pdf_security_permissions": "<out_doc_pdf_security_permissions>"
      }
      """
      And that I want to update a resource with the key "out_doc_uid" stored in session array as variable "out_doc_uid_<out_doc_number>"
      And I request "project/<project>/output-document"
      And the content type is "application/json"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the type is "object"
      

      Examples:

        | project                          | out_doc_number | out_doc_title                            | out_doc_description                      |out_doc_filename  | out_doc_template | out_doc_report_generator | out_doc_landscape | out_doc_media | out_doc_left_margin | out_doc_right_margin | out_doc_top_margin | out_doc_bottom_margin | out_doc_generate | out_doc_type | out_doc_current_revision | out_doc_field_mapping | out_doc_versioning | out_doc_destination_path | out_doc_tags | out_doc_pdf_security_enabled | out_doc_pdf_security_open_password | out_doc_pdf_security_owner_password | out_doc_pdf_security_permissions |  
        | 4224292655297723eb98691001100052 | 1              | Endpoint Old Version UPDATE              | Output Document old version - UPDATE     | Output 1         |                  | HTML2PDF                 | 0                 | Letter        | 20                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | 2              | Endpoint Old Version PDF SECURITY UPDATE | Output UPDATE old version PDF SECURITY   | Output 2         |                  | HTML2PDF                 | 1                 | Legal         | 20                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\| modify\|copy              |
        | 4224292655297723eb98691001100052 | 5              | Endpoint New Version UPDATE              | Output UPDATE new version - EndPoint     | Output 5         |                  | TCPDF                    | 0                 | Letter        | 30                  | 20                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | 6              | Endpoint New Version PDF SECURITY UPDATE | Output UPDATE new version PDF SECURITY   | Output 6         |                  | TCPDF                    | 1                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy              |
  
  

  @6: TEST FOR GET OUTPUT DOCUMENT /--------------------------------------------------------------------
    Scenario Outline: Get a output Document

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

        



  @7: TEST FOR DELETE OUTPUT DOCUMENT /--------------------------------------------------------------------      
    Scenario Outline: Delete a output document of a project
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


  @8: TEST FOR GET OUTPUT DOCUMENT /--------------------------------------------------------------------
  Scenario: Get a List output documents of a project
    Given I request "project/4224292655297723eb98691001100052/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 2 records


  
  @9: TEST FOR GET OUTPUT DOCUMENT PRUEBAS NEGATIVAS /---------------------------------------------------  
  Scenario Outline: Create a new output document for a project
    Given POST this data:
      """
      {
      "out_doc_title": "<out_doc_title>",
      "out_doc_description": "<out_doc_description>",
      "out_doc_filename": "<out_doc_filename>",
      "out_doc_template": "<out_doc_template>",
      "out_doc_report_generator": "<out_doc_report_generator>",
      "out_doc_landscape": "<out_doc_landscape>",
      "out_doc_media": "<out_doc_media>",
      "out_doc_left_margin": "<out_doc_left_margin>",
      "out_doc_right_margin": "<out_doc_right_margin>",
      "out_doc_top_margin": "<out_doc_top_margin>",
      "out_doc_bottom_margin": "<out_doc_bottom_margin>",
      "out_doc_generate": "<out_doc_generate>",
      "out_doc_type": "<out_doc_type>",
      "out_doc_current_revision": "<out_doc_current_revision>",
      "out_doc_field_mapping": "<out_doc_field_mapping>",
      "out_doc_versioning": "<out_doc_versioning>",
      "out_doc_destination_path": "<out_doc_destination_path>",
      "out_doc_tags": "<out_doc_tags>",
      "out_doc_pdf_security_enabled": "<out_doc_pdf_security_enabled>",
      "out_doc_pdf_security_open_password": "<out_doc_pdf_security_open_password>",
      "out_doc_pdf_security_owner_password": "<out_doc_pdf_security_owner_password>",
      "out_doc_pdf_security_permissions": "<out_doc_pdf_security_permissions>"
      }
      """
      And I request "project/<project>/output-document"
      Then the response status code should be 400
      

      Examples:

        | project                          | out_doc_title                     | out_doc_description                      |out_doc_filename  | out_doc_template | out_doc_report_generator | out_doc_landscape | out_doc_media | out_doc_left_margin | out_doc_right_margin | out_doc_top_margin | out_doc_bottom_margin | out_doc_generate | out_doc_type | out_doc_current_revision | out_doc_field_mapping | out_doc_versioning | out_doc_destination_path | out_doc_tags | out_doc_pdf_security_enabled | out_doc_pdf_security_open_password | out_doc_pdf_security_owner_password | out_doc_pdf_security_permissions |  
        | 4224292655297723eb98691001100052 | Endpoint New Version 4            | Output Document new version - EndPoint   | Output 5         |                  | @#$%¼¤¾½                 | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | Endpoint New Version PDF SECURIT5 | Output Document new version PDF SECURITY | Output 6         |                  | TCPDF                    | 34                | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | 4224292655297723eb98691001100052 | Endpoint New Version Doc6         | Output Document new version solo doc     | Output 7         |                  | TCPDF                    | 0                 | Legal!@#$$$%^&| 25                  | 25                   | 25                 | 25                    | WORD             | HTML         | 0                        |                       | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | Endpoint New Version PDF7         | Output Document new version solo pdf     | Output 8         |                  | TCPDF                    | 0                 | Legal         | 25,56.98            | 25                   | 25                 | 25                    | PDF              | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
        | 4224292655297723eb98691001100052 | Endpoint New Version8             | Output Document new version - EndPoint   | Output 9         |                  | TCPDF                    | 1                 | Letter        | 30                  | 30,7.98              | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | Endpoint New Version PDF SECURIT9 | Output Document new version PDF SECURITY | Output 10        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25,54.98           | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | 4224292655297723eb98691001100052 | Endpoint New Version Doc10        | Output Document new version solo doc     | Output 11        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25,34.09              | WORD             | HTML         | 0                        |                       | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | Endpoint New Version PDF 11       | Output Document new version solo pdf     | Output 12        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDtest@#$$       | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
        | 4224292655297723eb98691001100052 | Endpoint New Version PDF SECURI13 | Output Document new version PDF SECURITY | Output 14        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 45,988.566               |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | 4224292655297723eb98691001100052 | Endpoint New Version PDF  15      | Output Document new version solo pdf     | Output 16        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 0                        |                       | 1,99.98            |                          |              | 1                            | sample                             | sample                              | print                            |
        | 4224292655297723eb98691001100052 | Endpoint New Version Doc   18     | Output Document new version solo doc     | Output 18        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | WORD             | HTML         | 0                        |                       | 1                  |                          |              | 23454                        |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | Endpoint New Version PDF SECURI22 | Output Document new version PDF SECURITY | Output 21        |                  | TCPDF                    | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modfy\|cop\|for\|aaa      |
        | 4224292655297723eb98691001100052 |                                   | Output Document old version - EndPoint   | Output 22        |                  | HTML2PDF                 | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                        |                       | 0                  |                          |              | 0                            |                                    |                                     |                                  |
        | 4224292655297723eb98691001100052 | Endpoint Old Version PDF SECURITY |                                          | Output 23        |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 0                        |                       | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
        | 4224292655297723eb98691001100052 | Endpoint Old Version Doc          | Output Document old version solo doc     |                  |                  | HTML2PDF                 | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | WORD             | HTML         | 0                        |                       | 1                  |                          |              | 0                            |                                    |                                     |                                  |
        