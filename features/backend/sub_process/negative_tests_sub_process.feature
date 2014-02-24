@ProcessMakerMichelangelo @RestAPI
Feature: Sub Processs Negative Tests

  Background:
    Given that I have a valid access_token


Scenario Outline: Update a Sub Process ("Sub Process 1") with bad parameters (negative tests)
    Given PUT this data:
    """
    {
        "spr_pro": "52300148451af6f788b0700048230234",
        "spr_tas": "37824599651af6f821d1388066214745",
        "spr_name": "Test Update 1",
        "spr_synchronous": "0",
        "spr_variables_out": {
            "@@First_Name": "@@User_First_Name"
        }
    }

    """
    And I request "project/<project>/subprocess/<subprocess>"
    Then the response status code should be <error_code>
      And the response status message should have the following text "<error_message>"

    Examples:

    | test_description            | project                          | subprocess                       | error_code | error_message |
    | Field required project      |                                  | 98435229151af71504413a4099099648 | 400        | prj_uid       |
    | Invalid required project    | 7726725346465413f92a261016011045 | 98435229151af71504413a4099099648 | 400        | prj_uid       |
    | Invalid required subprocess | 77267253551af713f92a261016011045 | 984666666666f71504413a4099099648 | 400        | tas_uid       |