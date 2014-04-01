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

        

        18130826553359171798e40060879912

    "nameany": "wendy344%",
    "namealphabetic": "nestor123",
    "namealphanumeric": "rad1233$%",
    "nameinteger": "342432,7",
    "namerealnumber": "35353",
    "nameemail": "wendycolosacom",
    "namelogin": "sample",
    "valorreal": "242343253,253.00",
    "valorinteger": "346436363",
    "porcentagereal": "64600",
    "porcentageinteger": "464",
    "observaciones": "ninguna",
    "areascolosa": "sample",
    "areascolosa_label": "sample",
    "COUNTRY": "BOA",
    "COUNTRY_label": "Bolivia",
    "STATE": "aH",
    "STATE_label": "Chuquisaca",
    "LOCATION": "SRE",
    "LOCATION_label": "Sucre",
    "aprobado": "20",
    "aprobadohint": "0",
    "checkbox1": "sample",
    "checkbox2": "oki",
    "checkbox3": "Off",
    "radiogroup1": "hola",
    "radiogroup1_label": "Primero",
    "date1": "hola",
    "date2": "2013-11-08",
    "date3": "2014-03-09",
    "date4": "2014-03-02",
    "suggest2": "51049032352d56710347233042615067",
    "suggest2_label": "sample",
    "grid": {
        "1": {
            "sample": "hugo",
            "currency1": "2,424,234.00",
            "percentage1": "354.00 %",
            "suggest1_label": "dorothy",
            "suggest1": "81205219852d56719a97fc3086456770",
            "textarea1": "ninguno",
            "dropdown1": "uno",
            "yesno1": "0",
            "checkbox1": "On",
            "date1": "2014-03-20",
            "link1": "http://www.google.com/",
            "link1_label": "link1",
            "file1": "Tuesday.docx",
            "dropdown1_label": "uno"