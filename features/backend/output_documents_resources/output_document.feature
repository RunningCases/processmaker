@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents Resources

  Background:
    Given that I have a valid access_token

  Scenario: Get a List output documents of a project
  Given I request "project/4224292655297723eb98691001100052/output-documents"
  Then the response status code should be 200
  And the content type is "application/json"
  And the type is "array"

  
  Scenario: Get a single output document of a project
  Given I request "project/4224292655297723eb98691001100052/output-document/270088687529c8ace5e5272077582449"
  Then the response status code should be 200
  And the content type is "application/json"
  And the type is "object"
  
  Scenario: Create a new output document for a project
      Given POST this data:
      """
      {
      "out_doc_title": "Output doc #1",
      "out_doc_description": "Output doc #1 - Desc",
      "out_doc_filename": "od_generated_1",
      "out_doc_template": "",
      "out_doc_report_generator": "HTML2PDF",
      "out_doc_landscape": 0,
      "out_doc_media": "Letter",
      "out_doc_left_margin": 0,
      "out_doc_right_margin": 0,
      "out_doc_top_margin": 0,
      "out_doc_bottom_margin": 0,
      "out_doc_generate": "BOTH",
      "out_doc_type": "HTML",
      "out_doc_current_revision": 0,
      "out_doc_field_mapping": "",
      "out_doc_versioning": 0,
      "out_doc_destination_path": "",
      "out_doc_tags": "",
      "out_doc_pdf_security_enabled": 0,
      "out_doc_pdf_security_open_password": "",
      "out_doc_pdf_security_owner_password": "",
      "out_doc_pdf_security_permissions": ""
      }
      """
      And I request "project/4224292655297723eb98691001100052/output-document"
      Then the response status code should be 201
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "object"
      And store "out_doc_uid" in session array as variable "out_doc_uid"

  Scenario: Update a output document for a project
    Given PUT this data:
      """
      {
      "out_doc_title": "Output doc #1",
      "out_doc_description": "Output doc #1 - Desc DESCRIPTION Modified",
      "out_doc_filename": "od_generated_1",
      "out_doc_template": "",
      "out_doc_report_generator": "HTML2PDF",
      "out_doc_landscape": 0,
      "out_doc_media": "Letter",
      "out_doc_left_margin": 0,
      "out_doc_right_margin": 0,
      "out_doc_top_margin": 0,
      "out_doc_bottom_margin": 0,
      "out_doc_generate": "BOTH",
      "out_doc_type": "HTML",
      "out_doc_current_revision": 0,
      "out_doc_field_mapping": "",
      "out_doc_versioning": 0,
      "out_doc_destination_path": "",
      "out_doc_tags": "",
      "out_doc_pdf_security_enabled": 0,
      "out_doc_pdf_security_open_password": "",
      "out_doc_pdf_security_owner_password": "",
      "out_doc_pdf_security_permissions": ""
      }
      """
      And that I want to update a resource with the key "out_doc_uid" stored in session array
      And I request "project/4224292655297723eb98691001100052/output-document"
      And the content type is "application/json"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the type is "object"
      And that "out_doc_description": "Output doc #1 - Desc DESCRIPTION Modified"

  Scenario: Delete a output document of a project
    Given that I want to delete a resource with the key "out_doc_uid" stored in session array
        And I request "project/4224292655297723eb98691001100052/output-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"


