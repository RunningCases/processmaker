@ProcessMakerMichelangelo @RestAPI
Feature: Sub Processs

  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/9821342145305125d48cb88069229840/sub-process/61578996253051263872061082298948"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "spr_name" is set to "Sub-Proces"


  Scenario: Update a Sub Process
    Given that I have a valid access_token
    And PUT this data:
    """
    {
        "spr_pro": "4728335905305113b8880c9007635110",
        "spr_tas": "6238856655305113e127929067843487",
        "spr_name": "test",
        "spr_synchronous": "1",
        "spr_variables_out": {
            "@@APPLICATION": "@@APPLICATION",
            "zzzzz": "asaaaa",
            "aaaa": "ssss"
        }
    }
    """
    And I request "project/9821342145305125d48cb88069229840/sub-process/61578996253051263872061082298948"
    Then the response status code should be 200


  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/9821342145305125d48cb88069229840/sub-process/61578996253051263872061082298948"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "spr_name" is set to "test"


  Scenario: Update a Sub Process
    Given that I have a valid access_token
    And PUT this data:
    """
    {
        "spr_pro": "4728335905305113b8880c9007635110",
        "spr_tas": "6238856655305113e127929067843487",
        "spr_name": "Sub-Proces",
        "spr_synchronous": "1",
        "spr_variables_out": {
            "@@APPLICATION": "@@APPLICATION",
            "zzzzz": "asaaaa",
            "aaaa": "ssss"
        }
    }
    """
    And I request "project/9821342145305125d48cb88069229840/sub-process/61578996253051263872061082298948"
    Then the response status code should be 200


  Scenario: List all the Sub Processs (result 0 Sub Processs)
    Given that I have a valid access_token
    And I request "project/9821342145305125d48cb88069229840/sub-process/61578996253051263872061082298948"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "spr_name" is set to "Sub-Proces"