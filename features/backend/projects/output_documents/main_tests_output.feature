@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents Main Tests
  Requirements:
    a workspace with the process 4224292655297723eb98691001100052 ("Test Users-Step-Properties End Point") already loaded
    there are two output documents in the process
    and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded


  Background:
    Given that I have a valid access_token


  Scenario Outline: Get the Output Documents List both process
    Given I request "project/<project>/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "out_doc_title" property in row 0 equals "<out_doc_title>"
    
    Examples:
    | test_description                                               | project                          | records | out_doc_title               |
    | List Outputs in process "Test Users-Step-Properties End Point" | 4224292655297723eb98691001100052 | 2       | Endpoint Old Version (base) |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 1       | Output Document             |


  Scenario Outline: Get a single output document of a project
    the output document is previously created
    Given I request "project/<project>/output-document/<output-document>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the "out_doc_title" property equals "<out_doc_title>"
    And the "out_doc_filename" property equals "<out_doc_filename>"
    And the "out_doc_report_generator" property equals "<out_doc_report_generator>"

    Examples:
    | test_description                                               | project                          | output-document                  | out_doc_title               | out_doc_filename   | out_doc_report_generator |
    | List Outputs in process "Test Users-Step-Properties End Point" | 4224292655297723eb98691001100052 | 59969646352d6cd3caa0751003892895 | Endpoint New Version (base) | endpointnewversion | TCPDF                    |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 218529141536be955f0b646092366402 | Output Document             | Output Document    | TCPDF                    |


  Scenario Outline: Create 17 new Output Documents in both process
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

      | test_description                                                      .pm  | project                          | out_doc_number | out_doc_title                     | out_doc_description                      |out_doc_filename  | out_doc_template |out_doc_report_generator | out_doc_landscape | out_doc_media | out_doc_left_margin | out_doc_right_margin | out_doc_top_margin | out_doc_bottom_margin | out_doc_generate | out_doc_type | out_doc_versioning | out_doc_destination_path | out_doc_tags | out_doc_pdf_security_enabled | out_doc_pdf_security_open_password | out_doc_pdf_security_owner_password | out_doc_pdf_security_permissions |
      | Create with old version and both                                      .pm  | 4224292655297723eb98691001100052 | 1              | Endpoint Old Version              | Output Document old version - EndPoint   | Output 1         | Example          | HTML2PDF                | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    |                                     |                                  |
      | Craate with old version and pdf security                              .pm  | 4224292655297723eb98691001100052 | 2              | Endpoint Old Version PDF SECURITY | Output Document old version PDF SECURITY | Output 2         | Example          | HTML2PDF                | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
      | Create with old version only doc                                      .pm  | 4224292655297723eb98691001100052 | 3              | Endpoint Old Version Doc          | Output Document old version solo doc     | Output 3         | Example          | HTML2PDF                | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC              | HTML         | 1                  |                          |              | 0                            |                                    |                                     |                                  |
      | Create with old version only pdf                                      .pm  | 4224292655297723eb98691001100052 | 4              | Endpoint Old Version PDF          | Output Document old version solo pdf     | Output 4         | Example          | HTML2PDF                | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
      | Create with new version and  both                                     .pm  | 4224292655297723eb98691001100052 | 5              | Endpoint New Version              | Output Document new version - EndPoint   | Output 5         | Example          | TCPDF                   | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    |                                     |                                  |
      | Create with new version and pdf security                              .pm  | 4224292655297723eb98691001100052 | 6              | Endpoint New Version PDF SECURITY | Output Document new version PDF SECURITY | Output 6         | Example          | TCPDF                   | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
      | Create with new version only doc                                      .pm  | 4224292655297723eb98691001100052 | 7              | Endpoint New Version Doc          | Output Document new version solo doc     | Output 7         | Example          | TCPDF                   | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC              | HTML         | 1                  |                          |              | 0                            |                                    |                                     |                                  |
      | Create with new version only pdf                                      .pm  | 4224292655297723eb98691001100052 | 8              | Endpoint New Version PDF          | Output Document new version solo pdf     | Output 8         | Example          | TCPDF                   | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
      | Create with special characters in out doc title                       .pm  | 4224292655297723eb98691001100052 | 9              | test !@#$%^&*€¤¾½²³€¼½¼           | Output Document old version - EndPoint   | Output 9         | Example          | HTML2PDF                | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    |                                     |                                  |
      | Create with special characters in out doc description                 .pm  | 4224292655297723eb98691001100052 | 10             | Endpoint Old1                     | test %^&*€¤¾½²³€                         | Output 10        | Example          | HTML2PDF                | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
      | Create with special characters in out doc filename                    .pm  | 4224292655297723eb98691001100052 | 11             | Endpoint Old Version Doc 2        | Output Document old version solo doc     | Output @#$%^&*€¤ | Example          | HTML2PDF                | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC              | HTML         | 1                  |                          |              | 0                            |                                    |                                     |                                  |
      | Create with special characters in out doc destination path            .pm  | 4224292655297723eb98691001100052 | 12             | Endpoint New Version  16          | Output Document new version - EndPoint   | Output 13        | Example          | TCPDF                   | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  | 23rg@#$%                 |              | 0                            |                                    |                                     |                                  |
      | Create with special characters in out doc tags                        .pm  | 4224292655297723eb98691001100052 | 13             | Endpoint New Version PDF SECURI17 | Output Document new version PDF SECURITY | Output 14        | Example          | TCPDF                   | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          | vfv23@$@%    | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
      | Create with special characters in out doc pdf security open password  .pm  | 4224292655297723eb98691001100052 | 14             | Endpoint New Version PDF  19      | Output Document new version solo pdf     | Output 15        | Example          | TCPDF                   | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 1                  |                          |              | 1                            | sample432@$#@$¼€¼½                 | sample                              | print                            |
      | Create with special characters in out doc pdf security owner password .pm  | 4224292655297723eb98691001100052 | 15             | Endpoint New Version 20           | Output Document new version - EndPoint   | Output 16        | Example          | TCPDF                   | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    | sample432@$#@$¼€¼½                  |                                  |
      | Create with old version and both                                      .pmx | 1455892245368ebeb11c1a5001393784 | 16             | Endpoint Old Version              | Output Document old version - EndPoint   | Output 1         | Example          | HTML2PDF                | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    |                                     |                                  |
      | Craate with old version and pdf security                              .pmx | 1455892245368ebeb11c1a5001393784 | 17             | Endpoint Old Version PDF SECURITY | Output Document old version PDF SECURITY | Output 2         | Example          | HTML2PDF                | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
      | Create with old version only doc                                      .pmx | 1455892245368ebeb11c1a5001393784 | 18             | Endpoint Old Version Doc          | Output Document old version solo doc     | Output 3         | Example          | HTML2PDF                | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC              | HTML         | 1                  |                          |              | 0                            |                                    |                                     |                                  |
      | Create with old version only pdf                                      .pmx | 1455892245368ebeb11c1a5001393784 | 19             | Endpoint Old Version PDF          | Output Document old version solo pdf     | Output 4         | Example          | HTML2PDF                | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
      | Create with new version and  both                                     .pmx | 1455892245368ebeb11c1a5001393784 | 20             | Endpoint New Version              | Output Document new version - EndPoint   | Output 5         | Example          | TCPDF                   | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    |                                     |                                  |
      | Create with new version and pdf security                              .pmx | 1455892245368ebeb11c1a5001393784 | 21             | Endpoint New Version PDF SECURITY | Output Document new version PDF SECURITY | Output 6         | Example          | TCPDF                   | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
      | Create with new version only doc                                      .pmx | 1455892245368ebeb11c1a5001393784 | 22             | Endpoint New Version Doc          | Output Document new version solo doc     | Output 7         | Example          | TCPDF                   | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC              | HTML         | 1                  |                          |              | 0                            |                                    |                                     |                                  |
      | Create with new version only pdf                                      .pmx | 1455892245368ebeb11c1a5001393784 | 23             | Endpoint New Version PDF          | Output Document new version solo pdf     | Output 8         | Example          | TCPDF                   | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print                            |
      | Create with special characters in out doc title                       .pmx | 1455892245368ebeb11c1a5001393784 | 24             | test !@#$%^&*€¤¾½²³€¼½¼           | Output Document old version - EndPoint   | Output 9         | Example          | HTML2PDF                | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    |                                     |                                  |
      | Create with special characters in out doc description                 .pmx | 1455892245368ebeb11c1a5001393784 | 25             | Endpoint Old1                     | test %^&*€¤¾½²³€                         | Output 10        | Example          | HTML2PDF                | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
      | Create with special characters in out doc filename                    .pmx | 1455892245368ebeb11c1a5001393784 | 26             | Endpoint Old Version Doc 2        | Output Document old version solo doc     | Output @#$%^&*€¤ | Example          | HTML2PDF                | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | DOC              | HTML         | 1                  |                          |              | 0                            |                                    |                                     |                                  |
      | Create with special characters in out doc destination path            .pmx | 1455892245368ebeb11c1a5001393784 | 27             | Endpoint New Version  16          | Output Document new version - EndPoint   | Output 13        | Example          | TCPDF                   | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  | 23rg@#$%                 |              | 0                            |                                    |                                     |                                  |
      | Create with special characters in out doc tags                        .pmx | 1455892245368ebeb11c1a5001393784 | 28             | Endpoint New Version PDF SECURI17 | Output Document new version PDF SECURITY | Output 14        | Example          | TCPDF                   | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          | vfv23@$@%    | 1                            | sample                             | sample                              | print\|modify\|copy\|forms       |
      | Create with special characters in out doc pdf security open password  .pmx | 1455892245368ebeb11c1a5001393784 | 29             | Endpoint New Version PDF  19      | Output Document new version solo pdf     | Output 15        | Example          | TCPDF                   | 0                 | Legal         | 25                  | 25                   | 25                 | 25                    | PDF              | HTML         | 1                  |                          |              | 1                            | sample432@$#@$¼€¼½                 | sample                              | print                            |
      | Create with special characters in out doc pdf security owner password .pmx | 1455892245368ebeb11c1a5001393784 | 30             | Endpoint New Version 20           | Output Document new version - EndPoint   | Output 16        | Example          | TCPDF                   | 1                 | Letter        | 30                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    | sample432@$#@$¼€¼½                  |                                  |

  
  Scenario: Create Output Documents with same name
      Given POST this data:
      """
      {
        "out_doc_title":            "Endpoint Old Version",
        "out_doc_description":      "Output Document old version - EndPoint",
        "out_doc_filename":         "Output 1",
        "out_doc_template":         "Example",
        "out_doc_report_generator": "HTML2PDF",
        "out_doc_landscape":        "1",
        "out_doc_media":            "Letter",
        "out_doc_left_margin":      "30",
        "out_doc_right_margin":     "30",
        "out_doc_top_margin":       "30",
        "out_doc_bottom_margin":    "30",
        "out_doc_generate":         "BOTH",
        "out_doc_type":             "HTML",
        "out_doc_versioning":       "0",
        "out_doc_destination_path": "",
        "out_doc_tags":             "",
        "out_doc_pdf_security_enabled":        "0",
        "out_doc_pdf_security_open_password":  "",
        "out_doc_pdf_security_owner_password": "",
        "out_doc_pdf_security_permissions":    ""
      }
      """
      And I request "project/4224292655297723eb98691001100052/output-document"
      Then the response status code should be 400
      And the response status message should have the following text "same name"


  Scenario Outline: Get the Output Documents list
    Given I request "project/<project>/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records

    Examples:
    | test_description                                               | project                          | records |
    | List Outputs in process "Test Users-Step-Properties End Point" | 4224292655297723eb98691001100052 | 17      |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 16      |


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

      | test_description                               | project                          | out_doc_number | out_doc_title                            | out_doc_description                      | out_doc_filename | out_doc_template | out_doc_report_generator | out_doc_landscape | out_doc_media | out_doc_left_margin | out_doc_right_margin | out_doc_top_margin | out_doc_bottom_margin | out_doc_generate | out_doc_type | out_doc_versioning | out_doc_destination_path | out_doc_tags | out_doc_pdf_security_enabled | out_doc_pdf_security_open_password | out_doc_pdf_security_owner_password | out_doc_pdf_security_permissions |
      | Update out doc title and description      .pm  | 4224292655297723eb98691001100052 | 1              | Endpoint Old Version UPDATE              | Output Document old version - UPDATE     | Output 1         | Example          | HTML2PDF                 | 0                 | Letter        | 20                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    |                                     |                                  |
      | Update out doc title and out doc generate .pm  | 4224292655297723eb98691001100052 | 2              | Endpoint Old Version PDF SECURITY UPDATE | Output UPDATE old version PDF SECURITY   | Output 2         | Example          | TCPDF                    | 1                 | Legal         | 20                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy              |
      | Update out doc title and description      .pm  | 4224292655297723eb98691001100052 | 5              | Endpoint New Version UPDATE              | Output UPDATE new version - EndPoint     | Output 5         | Example          | TCPDF                    | 0                 | Letter        | 30                  | 20                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    |                                     |                                  |
      | Update out doc title and out doc generate .pm  | 4224292655297723eb98691001100052 | 6              | Endpoint New Version PDF SECURITY UPDATE | Output UPDATE new version PDF SECURITY   | Output 6         | Example          | HTML2PDF                 | 1                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy              |
      | Update out doc title and description      .pmx | 1455892245368ebeb11c1a5001393784 | 16             | Endpoint Old Version UPDATE              | Output Document old version - UPDATE     | Output 1         | Example          | HTML2PDF                 | 0                 | Letter        | 20                  | 30                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    |                                     |                                  |
      | Update out doc title and out doc generate .pmx | 1455892245368ebeb11c1a5001393784 | 17             | Endpoint Old Version PDF SECURITY UPDATE | Output UPDATE old version PDF SECURITY   | Output 2         | Example          | TCPDF                    | 1                 | Legal         | 20                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy              |
      | Update out doc title and description      .pmx | 1455892245368ebeb11c1a5001393784 | 20             | Endpoint New Version UPDATE              | Output UPDATE new version - EndPoint     | Output 5         | Example          | TCPDF                    | 0                 | Letter        | 30                  | 20                   | 30                 | 30                    | BOTH             | HTML         | 0                  |                          |              | 0                            |                                    |                                     |                                  |
      | Update out doc title and out doc generate .pmx | 1455892245368ebeb11c1a5001393784 | 21             | Endpoint New Version PDF SECURITY UPDATE | Output UPDATE new version PDF SECURITY   | Output 6         | Example          | HTML2PDF                 | 1                 | Legal         | 25                  | 25                   | 25                 | 25                    | BOTH             | HTML         | 1                  |                          |              | 1                            | sample                             | sample                              | print\|modify\|copy              |


  Scenario Outline: Get a single Output Document and check some properties
    Given that I want to get a resource with the key "out_doc_uid" stored in session array as variable "out_doc_uid_<out_doc_number>"
      And I request "project/<project>/output-document"
      And the content type is "application/json"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the type is "object"
      And that "out_doc_title" is set to "<out_doc_title>"
      And that "out_doc_description" is set to "<out_doc_description>"
      And that "out_doc_filename" is set to "<out_doc_filename>"

      Examples:

      | test_description                               | project                          | out_doc_number | out_doc_title                            | out_doc_description                      |out_doc_filename  |
      | Update out doc title and description      .pm  | 4224292655297723eb98691001100052 | 1              | Endpoint Old Version UPDATE              | Output Document old version - UPDATE     | Output 1         |
      | Update out doc title and out doc generate .pm  | 4224292655297723eb98691001100052 | 2              | Endpoint Old Version PDF SECURITY UPDATE | Output UPDATE old version PDF SECURITY   | Output 2         |
      | Update out doc title and description      .pm  | 4224292655297723eb98691001100052 | 5              | Endpoint New Version UPDATE              | Output UPDATE new version - EndPoint     | Output 5         |
      | Update out doc title and out doc generate .pm  | 4224292655297723eb98691001100052 | 6              | Endpoint New Version PDF SECURITY UPDATE | Output UPDATE new version PDF SECURITY   | Output 6         |
      | Update out doc title and description      .pmx | 1455892245368ebeb11c1a5001393784 | 16             | Endpoint Old Version UPDATE              | Output Document old version - UPDATE     | Output 1         |
      | Update out doc title and out doc generate .pmx | 1455892245368ebeb11c1a5001393784 | 17             | Endpoint Old Version PDF SECURITY UPDATE | Output UPDATE old version PDF SECURITY   | Output 2         |
      | Update out doc title and description      .pmx | 1455892245368ebeb11c1a5001393784 | 20             | Endpoint New Version UPDATE              | Output UPDATE new version - EndPoint     | Output 5         |
      | Update out doc title and out doc generate .pmx | 1455892245368ebeb11c1a5001393784 | 21             | Endpoint New Version PDF SECURITY UPDATE | Output UPDATE new version PDF SECURITY   | Output 6         |


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
      | 1455892245368ebeb11c1a5001393784 | 16             |
      | 1455892245368ebeb11c1a5001393784 | 17             |
      | 1455892245368ebeb11c1a5001393784 | 18             |
      | 1455892245368ebeb11c1a5001393784 | 19             |
      | 1455892245368ebeb11c1a5001393784 | 20             |
      | 1455892245368ebeb11c1a5001393784 | 21             |
      | 1455892245368ebeb11c1a5001393784 | 22             |
      | 1455892245368ebeb11c1a5001393784 | 23             |
      | 1455892245368ebeb11c1a5001393784 | 24             |
      | 1455892245368ebeb11c1a5001393784 | 25             |
      | 1455892245368ebeb11c1a5001393784 | 26             |
      | 1455892245368ebeb11c1a5001393784 | 27             |
      | 1455892245368ebeb11c1a5001393784 | 28             |
      | 1455892245368ebeb11c1a5001393784 | 29             |
      | 1455892245368ebeb11c1a5001393784 | 30             |


  Scenario Outline: Get the Output Documents List both process
    Given I request "project/<project>/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "out_doc_title" property in row 0 equals "<out_doc_title>"
    
    Examples:
    | test_description                                               | project                          | records | out_doc_title               |
    | List Outputs in process "Test Users-Step-Properties End Point" | 4224292655297723eb98691001100052 | 2       | Endpoint Old Version (base) |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 1       | Output Document             |
