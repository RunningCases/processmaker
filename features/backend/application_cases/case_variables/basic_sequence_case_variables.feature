@ProcessMakerMichelangelo @RestAPI
Feature: Cases Variables
Requirements:
    a workspace with five cases of the process "Test Case Variables" and with one case in workspace 

Background:
    Given that I have a valid access_token


Scenario: Returns the variables can be system variables and/or case variables.
    Given I request "cases/95124734553388becc0e332080057699/variables"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"


Scenario: Sends variables to a case
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
                        "percentage1": "424.00 %"
                        }
                    }
            }
            """
        And I request "cases/95124734553388becc0e332080057699/variable"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"


Scenario: Return variables to a case
        Given PUT this data:
            """
            {
                "nameany": "sample",
                "namealphabetic": "juan",
                "namealphanumeric": "sample123",
                "nameinteger": "12345",
                "namerealnumber": "12344.56",
                "nameemail": "qa@colosa.com",
                "namelogin": "sample",
                "valorreal": "12,344,556,778.00",
                "valorinteger": "1,223,445",
                "porcentagereal": "122.44 %",
                "porcentageinteger": "123 %",
                "observaciones": "Observaciones",
                "grid": {
                    "1": {
                        "sample": "jose",
                        "currency1": "12,334,444.00",
                        "percentage1": "333.00 %"
                        }
                    }
            }
            """
        And I request "cases/95124734553388becc0e332080057699/variable"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"


Scenario: Returns the variables can be system variables and/or case variables.
    Given I request "cases/95124734553388becc0e332080057699/variables"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"


#guardar el caso 128-->volver a ejecutar otro caso