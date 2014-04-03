@ProcessMakerMichelangelo @RestAPI
Feature: Cases Variables
Requirements:
    a workspace with five cases of the process "Test Case Variables" and with one case in workspace 

Background:
    Given that I have a valid access_token

#case 128
Scenario: Returns the variables can be system variables and/or case variables.
    Given I request "cases/95124734553388becc0e332080057699/variables"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the "nameany" property equals "sample"
    And the "namealphabetic" property equals "juan"
    And the "namealphanumeric" property equals "sample123"
    And the "nameinteger" property equals "12345"
    And the "namerealnumber" property equals "12344.56"
    And the "nameemail" property equals "qa@colosa.com"
    And the "namelogin" property equals "sample"
    And the "valorreal" property equals "12,344,556,778.00"
    And the "valorinteger" property equals "1,223,445"
    And the "porcentagereal" property equals "122.44 %"
    And the "porcentageinteger" property equals "123 %"
    And the "observaciones" property equals "Observaciones"
    And the "areascolosa" property equals "desarrollo"
    And the "areascolosa_label" property equals "Desarrollo"
    And the "COUNTRY" property equals "BO"
    And the "COUNTRY_label" property equals "Bolivia"
    And the "STATE" property equals "H"
    And the "STATE_label" property equals "Chuquisaca"
    And the "LOCATION" property equals "SRE"
    And the "LOCATION_label" property equals "Sucre"
    And the "aprobado" property equals "1"
    And the "aprobadohint" property equals "0"
    And the "checkbox1" property equals "On"
    And the "checkbox2" property equals "Off"
    And the "checkbox3" property equals "Off"
    And the "radiogroup1" property equals "primero"
    And the "radiogroup1_label" property equals "Primero"
    And the "radiogroup2" property equals "quinto"
    And the "radiogroup2_label" property equals "Quinto"    
    And the "date1" property equals "2013-11-07"
    And the "date2" property equals "2013-11-08"
    And the "date3" property equals "2014-03-03"
    And the "date4" property equals "2014-03-01"
    And the "suggest2" property equals "51049032352d56710347233042615067"
    And the "suggest2_label" property equals "aaron"   
    And the "sample" property in row 1 of property "grid" equals "jose"
    And the "currency1" property equals "12,334,444.00"
    And the "percentage1" property equals "333.00 %"
    And the "suggest1_label" property equals "gavin"
    And the "suggest1" property equals "33140476452d5671b0abda5073786635"
    And the "textarea1" property equals "ninguno"
    And the "dropdown1" property equals "dos"
    And the "dropdown1_label" property equals "dos"
    And the "yesno1" property equals "1"
    And the "checkbox1" property equals "On"
    And the "date1" property equals "2014-03-13"
    And the "link1" property equals "http://www.google.com/"
    And the "link1_label" property equals "link1"
    And the "sample" property equals "maria"
    And the "currency1" property equals "132,424.00"
    And the "percentage1" property equals "344.00 %"
    And the "suggest1_label" property equals "emily"
    And the "suggest1" property equals "34289569752d5673d310e82094574281"
    And the "textarea1" property equals "sample"
    And the "dropdown1" property equals "uno"
    And the "dropdown1_label" property equals "uno"
    And the "yesno1" property equals "0"
    And the "checkbox1" property equals "Off"
    And the "date1" property equals "2014-03-29"
    And the "link1" property equals "http://www.google.com/"
    And the "link1_label" property equals "link1"
    
    
Scenario: Sends variables to a case
        Given PUT this data:
            """
            {
                "nameany": "sample-put",
                "namealphabetic": "juanput",
                "namealphanumeric": "sample123567",
                "nameinteger": "1313200000",
                "namerealnumber": "4324325000567.12",
                "nameemail": "qa@colosaput.com",
                "namelogin": "sampleput",
                "valorreal": "32,142,424.00",
                "porcentagereal": "424.00 %",
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


Scenario: Returns the variables can be system variables and/or case variables.
    Given I request "cases/95124734553388becc0e332080057699/variables"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And the "nameany" property equals "sample-put"
    And the "namealphabetic" property equals "juan-put"
    And the "namealphanumeric" property equals "sample123567"
    And the "nameinteger" property equals "1313200000"
    And the "namerealnumber" property equals "4324325000567.12"
    And the "nameemail" property equals "qa@colosaput.com"
    And the "namelogin" property equals "sampleput"
    And the "valorreal" property equals "32,142,424.00"
    And the "valorinteger" property equals "1,223,445"
    And the "porcentagereal" property equals "424.00 %"
    And the "porcentageinteger" property equals "123 %"
    And the "observaciones" property equals "ningunaput"
    And the "areascolosa" property equals "desarrollo"
    And the "areascolosa_label" property equals "Desarrollo"
    And the "COUNTRY" property equals "BO"
    And the "COUNTRY_label" property equals "Bolivia"
    And the "STATE" property equals "H"
    And the "STATE_label" property equals "Chuquisaca"
    And the "LOCATION" property equals "SRE"
    And the "LOCATION_label" property equals "Sucre"
    And the "aprobado" property equals "1"
    And the "aprobadohint" property equals "0"
    And the "checkbox1" property equals "On"
    And the "checkbox2" property equals "Off"
    And the "checkbox3" property equals "Off"
    And the "radiogroup1" property equals "primero"
    And the "radiogroup1_label" property equals "Primero"
    And the "radiogroup2" property equals "quinto"
    And the "radiogroup2_label" property equals "Quinto"    
    And the "date1" property equals "2013-11-07"
    And the "date2" property equals "2013-11-08"
    And the "date3" property equals "2014-03-03"
    And the "date4" property equals "2014-03-01"
    And the "suggest2" property equals "51049032352d56710347233042615067"
    And the "suggest2_label" property equals "aaron"
    And the "sample" property equals "unoput"
    And the "currency1" property equals "133,000.00"
    And the "percentage1" property equals "424.00 %"
    And the "suggest1_label" property equals "gavin"
    And the "suggest1" property equals "33140476452d5671b0abda5073786635"
    And the "textarea1" property equals "ninguno"
    And the "dropdown1" property equals "dos"
    And the "dropdown1_label" property equals "dos"
    And the "yesno1" property equals "1"
    And the "checkbox1" property equals "On"
    And the "date1" property equals "2014-03-13"
    And the "link1" property equals "http://www.google.com/"
    And the "link1_label" property equals "link1"
    And the "sample" property equals "maria"
    And the "currency1" property equals "132,424.00"
    And the "percentage1" property equals "344.00 %"
    And the "suggest1_label" property equals "emily"
    And the "suggest1" property equals "34289569752d5673d310e82094574281"
    And the "textarea1" property equals "sample"
    And the "dropdown1" property equals "uno"
    And the "dropdown1_label" property equals "uno"
    And the "yesno1" property equals "0"
    And the "checkbox1" property equals "Off"
    And the "date1" property equals "2014-03-29"
    And the "link1" property equals "http://www.google.com/"
    And the "link1_label" property equals "link1"


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
                "porcentagereal": "122.44 %",
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
    And the "nameany" property equals "sample"
    And the "namealphabetic" property equals "juan"
    And the "namealphanumeric" property equals "sample123"
    And the "nameinteger" property equals "12345"
    And the "namerealnumber" property equals "12344.56"
    And the "nameemail" property equals "qa@colosa.com"
    And the "namelogin" property equals "sample"
    And the "valorreal" property equals "12,344,556,778.00"
    And the "valorinteger" property equals "1,223,445"
    And the "porcentagereal" property equals "122.44 %"
    And the "porcentageinteger" property equals "123 %"
    And the "observaciones" property equals "Observaciones"
    And the "areascolosa" property equals "desarrollo"
    And the "areascolosa_label" property equals "Desarrollo"
    And the "COUNTRY" property equals "BO"
    And the "COUNTRY_label" property equals "Bolivia"
    And the "STATE" property equals "H"
    And the "STATE_label" property equals "Chuquisaca"
    And the "LOCATION" property equals "SRE"
    And the "LOCATION_label" property equals "Sucre"
    And the "aprobado" property equals "1"
    And the "aprobadohint" property equals "0"
    And the "checkbox1" property equals "On"
    And the "checkbox2" property equals "Off"
    And the "checkbox3" property equals "Off"
    And the "radiogroup1" property equals "primero"
    And the "radiogroup1_label" property equals "Primero"
    And the "radiogroup2" property equals "quinto"
    And the "radiogroup2_label" property equals "Quinto"    
    And the "date1" property equals "2013-11-07"
    And the "date2" property equals "2013-11-08"
    And the "date3" property equals "2014-03-03"
    And the "date4" property equals "2014-03-01"
    And the "suggest2" property equals "51049032352d56710347233042615067"
    And the "suggest2_label" property equals "aaron"
    And the "sample" property equals "jose"
    And the "currency1" property equals "12,334,444.00"
    And the "percentage1" property equals "333.00 %"
    And the "suggest1_label" property equals "gavin"
    And the "suggest1" property equals "33140476452d5671b0abda5073786635"
    And the "textarea1" property equals "ninguno"
    And the "dropdown1" property equals "dos"
    And the "dropdown1_label" property equals "dos"
    And the "yesno1" property equals "1"
    And the "checkbox1" property equals "On"
    And the "date1" property equals "2014-03-13"
    And the "link1" property equals "http://www.google.com/"
    And the "link1_label" property equals "link1"
    And the "sample" property equals "maria"
    And the "currency1" property equals "132,424.00"
    And the "percentage1" property equals "344.00 %"
    And the "suggest1_label" property equals "emily"
    And the "suggest1" property equals "34289569752d5673d310e82094574281"
    And the "textarea1" property equals "sample"
    And the "dropdown1" property equals "uno"
    And the "dropdown1_label" property equals "uno"
    And the "yesno1" property equals "0"
    And the "checkbox1" property equals "Off"
    And the "date1" property equals "2014-03-29"
    And the "link1" property equals "http://www.google.com/"
    And the "link1_label" property equals "link1"