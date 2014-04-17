@ProcessMakerMichelangelo @RestAPI
Feature: Case Tracker update position Main Tests 
Requirements:
    a workspace with the process 337095208534c2cb794a9b5045424275 ("Case Tracker Ordenamiento") already loaded
    there are two activities and six object in the process

   
   Scenario: List all objects in this process (result 5 objects)
    Given that I have a valid access_token
    And I request "project/337095208534c2cb794a9b5045424275/case-tracker/objects"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 5 records
    And that "cto_position" is set to "3"


  Scenario Outline: List all the objects in this process
    Given that I have a valid access_token
    And I request "project/337095208534c2cb794a9b5045424275/case-tracker/object/<cto_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "cto_uid" is set to "<cto_uid>"
    And that "cto_type_obj" is set to "<cto_type_obj>"
    And that "cto_position" is set to "<cto_position>"
    And that "obj_title" is set to "<obj_title>"


    Examples:

    | test_description     | cto_uid                          | cto_type_obj    | cto_position | obj_title |
    | Description form1    | 596781160534c2e048a51b4023771688 | DYNAFORM        | 1            | form1     |
    | Description form2    | 542952604534c2e084aeeb8075222116 | DYNAFORM        | 2            | form2     |
    | Description input1   | 779426015534c2e0bef94b6074738223 | INPUT_DOCUMENT  | 3            | input1    |
    | Description input2   | 958267629534c2e102d5896033309049 | INPUT_DOCUMENT  | 4            | input2    |
    | Description output1  | 892208902534c2e139f6a01078029070 | OUTPUT_DOCUMENT | 1            | output1   |
  


  Scenario: Change order the object input1 for position one
    Given that I have a valid access_token
    And PUT this data:
    """
    {
        "cto_position": 1
    }
    """
    And I request "project/337095208534c2cb794a9b5045424275/case-tracker/object/779426015534c2e0bef94b6074738223"
    Then the response status code should be 200

  
  Scenario Outline: List all the objects in this process
    Given that I have a valid access_token
    And I request "project/337095208534c2cb794a9b5045424275/case-tracker/object/<cto_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "cto_uid" is set to "<cto_uid>"
    And that "cto_type_obj" is set to "<cto_type_obj>"
    And that "cto_position" is set to "<cto_position>"
    And that "obj_title" is set to "<obj_title>"


    Examples:

    | test_description     | cto_uid                          | cto_type_obj    | cto_position | obj_title |
    | Description form1    | 596781160534c2e048a51b4023771688 | DYNAFORM        | 2            | form1     |
    | Description form2    | 542952604534c2e084aeeb8075222116 | DYNAFORM        | 3            | form2     |
    | Description input1   | 779426015534c2e0bef94b6074738223 | INPUT_DOCUMENT  | 1            | input1    |
    | Description input2   | 958267629534c2e102d5896033309049 | INPUT_DOCUMENT  | 4            | input2    |
    | Description output1  | 892208902534c2e139f6a01078029070 | OUTPUT_DOCUMENT | 5            | output1   |

  
  Scenario: Change order the object input1 for position three
    Given that I have a valid access_token
    And PUT this data:
    """
    {
        "cto_position": 3
    }
    """
    And I request "project/337095208534c2cb794a9b5045424275/case-tracker/object/779426015534c2e0bef94b6074738223"
    Then the response status code should be 200


  Scenario Outline: List all the objects in this process
    Given that I have a valid access_token
    And I request "project/337095208534c2cb794a9b5045424275/case-tracker/object/<cto_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "cto_uid" is set to "<cto_uid>"
    And that "cto_type_obj" is set to "<cto_type_obj>"
    And that "cto_position" is set to "<cto_position>"
    And that "obj_title" is set to "<obj_title>"


    Examples:

    | test_description     | cto_uid                          | cto_type_obj    | cto_position | obj_title |
    | Description form1    | 596781160534c2e048a51b4023771688 | DYNAFORM        | 1            | form1     |
    | Description form2    | 542952604534c2e084aeeb8075222116 | DYNAFORM        | 2            | form2     |
    | Description input1   | 779426015534c2e0bef94b6074738223 | INPUT_DOCUMENT  | 3            | input1    |
    | Description input2   | 958267629534c2e102d5896033309049 | INPUT_DOCUMENT  | 4            | input2    |
    | Description output1  | 892208902534c2e139f6a01078029070 | OUTPUT_DOCUMENT | 5            | output1   |

