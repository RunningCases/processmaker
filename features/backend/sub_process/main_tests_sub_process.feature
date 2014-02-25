@ProcessMakerMichelangelo @RestAPI
Feature: Sub Processs Main Tests
 Requirements:
    a workspace with the process 77267253551af713f92a261016011045 ("Main Process") already loaded
    and with the subprocess 52300148451af6f788b0700048230234 ("Sub Process 1") already loaded
    and with the subprocess 51483263751af70c8568228036196106 ("Sub Process 2") already loaded

Background:
    Given that I have a valid access_token


  Scenario: List all properties the Sub Processs of ("Sub Process 1")
    Given I request "project/77267253551af713f92a261016011045/subprocess/98435229151af71504413a4099099648"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "spr_pro" is set to "52300148451af6f788b0700048230234"
    And that "spr_tas" is set to "37824599651af6f821d1388066214745"
    And that "spr_name" is set to "Sub-Process"
    And that "spr_synchronous" is set to "1"


 Scenario: List all properties the Sub Processs of ("Sub Process 2")
    Given I request "project/77267253551af713f92a261016011045/subprocess/71420630951af715408d625020843512"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "spr_pro" is set to "51483263751af70c8568228036196106"
    And that "spr_tas" is set to "44468053351af70d83c9a49040728765"
    And that "spr_name" is set to "Sub-Process"
    And that "spr_synchronous" is set to "0"


  Scenario: Update a Sub Process ("Sub Process 1")
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
    And I request "project/77267253551af713f92a261016011045/subprocess/98435229151af71504413a4099099648"
    Then the response status code should be 200


  Scenario: Verify properties of ("Sub Process 1") after update
    Given I request "project/77267253551af713f92a261016011045/subprocess/98435229151af71504413a4099099648"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "spr_pro" is set to "52300148451af6f788b0700048230234"
    And that "spr_tas" is set to "37824599651af6f821d1388066214745"
    And that "spr_name" is set to "Test Update 1"
    And that "spr_synchronous" is set to "0"
    

Scenario: Update a Sub Process ("Sub Process 1") to return to the original values
    Given PUT this data:
    """
    {
    "spr_pro": "52300148451af6f788b0700048230234",
    "spr_tas": "37824599651af6f821d1388066214745",
    "spr_name": "Sub-Process",
    "spr_synchronous": "1"
    
}

    """
    And I request "project/77267253551af713f92a261016011045/subprocess/98435229151af71504413a4099099648"
    Then the response status code should be 200


Scenario: List all properties the Sub Processs of ("Sub Process 1")
    Given I request "project/77267253551af713f92a261016011045/subprocess/98435229151af71504413a4099099648"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "spr_pro" is set to "52300148451af6f788b0700048230234"
    And that "spr_tas" is set to "37824599651af6f821d1388066214745"
    And that "spr_name" is set to "Sub-Process"
    And that "spr_synchronous" is set to "1"


Scenario: Update a Sub Process ("Sub Process 2")
    Given PUT this data:
    """
    {
        "spr_pro": "51483263751af70c8568228036196106",
        "spr_tas": "44468053351af70d83c9a49040728765",
        "spr_name": "Test Update 2",
        "spr_synchronous": "1",
        "spr_variables_out": {
            "@@First_Name": "@@User_First_Name"
        }
    }

    """
    And I request "project/77267253551af713f92a261016011045/subprocess/71420630951af715408d625020843512"
    Then the response status code should be 200


  Scenario: Verify properties of ("Sub Process 2") after update
    Given I request "project/77267253551af713f92a261016011045/subprocess/71420630951af715408d625020843512"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "spr_pro" is set to "52300148451af6f788b0700048230234"
    And that "spr_tas" is set to "37824599651af6f821d1388066214745"
    And that "spr_name" is set to "Test Update 2"
    And that "spr_synchronous" is set to "1"
    

Scenario: Update a Sub Process ("Sub Process 2") to return to the original values
    Given PUT this data:
    """
    {
    "spr_pro": "51483263751af70c8568228036196106",
    "spr_tas": "44468053351af70d83c9a49040728765",
    "spr_name": "Sub-Process",
    "spr_synchronous": "0",
    "spr_variables_out": {
        "@@First_Name": "@@User_First_Name",
        "@@Last_Name": "@@User_Last_Name"
    }
}

    """
    And I request "project/77267253551af713f92a261016011045/subprocess/71420630951af715408d625020843512"
    Then the response status code should be 200


Scenario: List all properties the Sub Processs of ("Sub Process 2")
    Given I request "project/77267253551af713f92a261016011045/subprocess/71420630951af715408d625020843512"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "spr_pro" is set to "51483263751af70c8568228036196106"
    And that "spr_tas" is set to "44468053351af70d83c9a49040728765"
    And that "spr_name" is set to "Sub-Process"
    And that "spr_synchronous" is set to "0"