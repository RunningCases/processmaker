@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents cases Main Tests
  Requirements:
  a workspace with one case of the process "Test Output Document Case"
  and there are six Output Documents in the process

  Background:
    Given that I have a valid access_token


  Scenario Outline: Generate or regenerates an output documents for a given case
    Given POST this data:
    """
            {
                "out_doc_uid": "<out_doc_uid>"
            }
            """
    And I request "cases/33125846153383cecdf64f1079330191/output-document"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And store "app_doc_uid" in session array as variable "app_doc_uid_<app_doc_uid_number>"


  Examples:

    | test_description                             | app_doc_uid_number | out_doc_uid                      |
    | Generate "output document only doc"          | 1                  | 2087233055331ef4127d238097105696 |
    | Generate "output document with versioning"   | 2                  | 5961108155331efc976cee7011445347 |
    | Generate "output document only pdf"          | 3                  | 7074907425331ef837aa8b2055964905 |
    | Generate "output document old version"       | 4                  | 7385645355331ee70ea6a87029841722 |
    | Generate "output document with pdf security" | 5                  | 8594478445331eff2d30767061922215 |


  Scenario: Returns a list of the generated documents for a given cases
    Given I request "cases/33125846153383cecdf64f1079330191/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 6 records

  Scenario Outline: Returns an generated document for a given case
    Given I request "cases/33125846153383cecdf64f1079330191/output-document/app_doc_uid"  with the key "app_doc_uid" stored in session array as variable "app_doc_uid_<app_doc_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the "app_doc_filename" property equals "<app_doc_filename>"
    And the "doc_uid" property equals "<doc_uid>"
    And the "app_doc_create_user" property equals "<app_doc_create_user>"
    And the "app_doc_type" property equals "<app_doc_type>"

  Examples:

    | test_description                                   | app_doc_uid_number | app_doc_filename                     | doc_uid                          |  app_doc_create_user    | app_doc_type |
    | Get Output "output document only doc.doc"          | 1                  | output document only doc.doc         | 2087233055331ef4127d238097105696 | , Administrator (admin) | OUTPUT DOC   |
    | Get Output "output document with versioning.pdf"   | 2                  | output document with versioning.pdf  | 5961108155331efc976cee7011445347 | , Administrator (admin) | OUTPUT BOTH  |
    | Get Output "output document only pdf.pdf"          | 3                  | output document only pdf.pdf         | 7074907425331ef837aa8b2055964905 | , Administrator (admin) | OUTPUT PDF   |
    | Get Output "output document old version.pdf"       | 4                  | output document old version.pdf      | 7385645355331ee70ea6a87029841722 | , Administrator (admin) | OUTPUT BOTH  |
    | Get Output "output document with pdf security.pdf" | 5                  | output document with pdf security.pdf| 8594478445331eff2d30767061922215 | , Administrator (admin) | OUTPUT BOTH  |


  Scenario Outline: Delete an uploaded or generated document from a case.
    Given that I want to delete a resource with the key "app_doc_uid" stored in session array as variable "app_doc_uid_<app_doc_uid_number>"
    And I request "cases/33125846153383cecdf64f1079330191/output-document"
    And the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object"

  Examples:

    | app_doc_uid_number |
    | 1                  |
    | 2                  |
    | 3                  |
    | 4                  |
    | 5                  |
