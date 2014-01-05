@ProcessMakerMichelangelo @RestAPI
Feature: Getting started with Behat tests
  Scenario: GET projects list
    Given that I have a valid access_token
    And I request "projects"
    Then the response status code should be 200
    And the response time should at least be 100 milliseconds
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the "prj_uid" property in row 0 equals "31034744752a5d007364d93044509065"
    And the "prj_name" property in row 0 equals "Sample Project #1"
    And the "prj_create_date" property in row 0 equals "2013-12-09 09:13:27"

  Scenario: GET project Sample Project #1 process...
    Given that I have a valid access_token
    And I request "project/14414793652a5d718b65590036026581"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And the response has a "diagrams" property
    And the "lanesets" property in row 0 of property "diagrams" is "array"
    And the "lanes" property in row 0 of property "diagrams" is "array"
    And the "activities" property in row 0 of property "diagrams" is "array"
    And the "events" property in row 0 of property "diagrams" is "array"
    And the "gateways" property in row 0 of property "diagrams" is "array"
    And the "flows" property in row 0 of property "diagrams" is "array"
    And the "artifacts" property in row 0 of property "diagrams" is "array"

  Scenario: get an activity from some process
    Given that I have a valid access_token
    And I request "project/31034744752a5d007364d93044509065/activity/72845150252a5d718be4df5092655350"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the response is equivalent to this json file "task_72845150252a5d718be4df5092655350.json"

  Scenario: get an activity from some process
    Given that I have a valid access_token
    And I request "project/31034744752a5d007364d93044509065/activity/13508932952a5d718ef56f6044945775"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the response is equivalent to this json file "task_13508932952a5d718ef56f6044945775.json"

  Scenario: get an activity from some process
    Given that I have a valid access_token
    And I request "project/31034744752a5d007364d93044509065/activity/58163453252a5d719253ff9069893664"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the response is equivalent to this json file "task_58163453252a5d719253ff9069893664.json"

  Scenario: get a list of input documents
    Given that I have a valid access_token
    And I request "project/14414793652a5d718b65590036026581/input-documents"
    Then the response status code should be 200
    And the content type is "application/json"
    And the json data is an empty array

#there is an error in :
#message": "Bad Request:  [wrapped: Cannot fetch TableMap for undefined table: CONTENT]"
#source": "InputDocument.php:51 at call stage",
#  Scenario: post a new input documents
#    Given that I have a valid access_token
#    And POST this data:
#"""
#{
#    "inp_doc_title": "Input Doc #1",
#    "inp_doc_description":  "Input Doc #1 - Desc",
#    "out_doc_form_needed":  "VIRTUAL",
#    "inp_doc_original":     "ORIGINAL",
#    "inp_doc_published":    "PRIVATE",
#    "inp_doc_versionning":  0,
#    "inp_doc_destination_path": "",
#    "inp_doc_tags":             "INPUT"
#}
#"""
#    And I request "project/14414793652a5d718b65590036026581/input-document"
#    Then the response status code should be 201

#  Scenario: modify an input document
#    Given that I have a valid access_token
#    And PUT this data:
#"""
#{
#    "inp_doc_title": "Input Doc #1",
#    "inp_doc_description":  "Input Doc #1 - Desc",
#    "out_doc_form_needed":  "VIRTUAL",
#    "inp_doc_original":     "ORIGINAL",
#    "inp_doc_published":    "PRIVATE",
#    "inp_doc_versionning":  0,
#    "inp_doc_destination_path": "",
#    "inp_doc_tags":             "INPUT"
#}
#"""
#    And I request "project/14414793652a5d718b65590036026581/input-document/44915038352b08f590a4105021431900"
#    Then the response status code should be 200

  Scenario: get an empty list of triggers
    Given that I have a valid access_token
    And I request "project/14414793652a5d718b65590036026581/triggers"
    Then the response status code should be 200
    And the content type is "application/json"
    And the json data is an empty array


  Scenario: post a new trigger
    Given that I have a valid access_token
    And POST this data:
"""
{
    "tri_title": "Trigger #1",
    "tri_description": "Trigger #1 - Desc",
    "tri_type": "SCRIPT",
    "tri_webbot": "print 'hello world!!'; ",
    "tri_param": "PRIVATE"
}
"""
    And I request "project/14414793652a5d718b65590036026581/trigger"
    Then the response status code should be 201
    And the content type is "application/json"
    And the type is "object"
    And that "tri_param" is set to "PRIVATE"
    And store "tri_uid" in session array

  Scenario: re-post a new trigger, to get an error
    Given that I have a valid access_token
    And POST this data:
"""
{
    "tri_title": "Trigger #1",
    "tri_description": "Trigger #1 - Desc",
    "tri_type": "SCRIPT",
    "tri_webbot": "print 'hello world!!'; ",
    "tri_param": "PRIVATE"
}
"""
    And I request "project/14414793652a5d718b65590036026581/trigger"
    Then the response status code should be 400
    And the content type is "application/json"
    And the type is "object"
    #message: "Bad Request: There is a triggers with the same name in this process"

  Scenario: modify a Trigger
    Given that I have a valid access_token
    And PUT this data:
"""
{
    "tri_title": "Trigger #1-modified",
    "tri_description": "Trigger #1 - -modified",
    "tri_type": "SCRIPT",
    "tri_webbot": "print 'hello modified world!!'; ",
    "tri_param": "PRIVATE"
}
"""
    And that I want to update a resource with the key "tri_uid" stored in session array
    And I request "project/14414793652a5d718b65590036026581/trigger"
    Then the response status code should be 200
    And the content type is "application/json"
    And the type is "object"
    And that "tri_title" is set to "Trigger #1-modified"

  Scenario: delete a previously created trigger
    Given that I have a valid access_token
    And that I want to delete a resource with the key "tri_uid" stored in session array
    And I request "project/14414793652a5d718b65590036026581/trigger"
    Then the response status code should be 200
    And the content type is "application/json"

  Scenario: get the empty list of triggers again!
    Given that I have a valid access_token
    And I request "project/14414793652a5d718b65590036026581/triggers"
    Then the response status code should be 200
    And the json data is an empty array

