@ProcessMakerMichelangelo @RestAPI
Feature: Cases Variables Negative Tests

Background:
    Given that I have a valid access_token


Scenario: Returns the variables can be system variables and/or case variables (negative tests).
    Given I request "cases/95124734553388becc0e332080057699/variable"
    Then the response status code should be 400
    And the response status message should have the following text "Not Found"


Scenario: Sends variables to a case (negative tests)
        Given PUT this data:
            """
            {
                "nameany": "sample-put",
                "namealphabetic": "juanput",
                "namealphanumeric": "sample123put",
                "nameinteger": "1313200000",
                "namerealnumber": "4324325000put",
                "nameemail": "qa@colosaput.com",
                "namelogin": "sampleput",
                "valorreal": "32,142,424.00",
                "valorinteger": "4,242,424",
                "porcentagereal": "424.00 %",
                "porcentageinteger": "424 %",
                "observaciones": "ningunaput",
                "grid": {
                    "1": {
                        "sample": "unoput",
                        "currency1": "133,000.00",
                        "percentage1": "424.00 %",
            }
            """
        And I request "cases/95124734553388becc0e332080057699/variable"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"