@ProcessMakerMichelangelo @RestAPI
Feature: Dynaform Negative Tests


  Background:
    Given that I have a valid access_token


Scenario Outline: Normal Dynaform creation for a project with bad parameters (negative tests)
        Given POST this data:
        """
        {
            "dyn_title": "<dyn_title>",
            "dyn_description": "<dyn_description>",
            "dyn_type": "<dyn_type>"
        }
        """
        And I request "project/<project>/dynaform"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:
        | test_description           | project                          | dyn_title         | dyn_description | dyn_type | error_code | error_message |
        | Field required project     |                                  | Dynaform - Normal | dyn normal P1   | xmlform  | 400        | prj_uid       |
        | Field required dyn_title   | 14414793652a5d718b65590036026581 |                   | dyn grid P1     | grid     | 400        | dyn_title     |
        | Field required dyn_type    | 42445320652cd534acb3824056962285 | Dynaform - Normal | dyn normal P2   |          | 400        | dyn_type      |
        | Invalid dyn_type           | 42445320652cd534acb3824056962285 | Dynaform - Grid   | dyn grid P2     | graad    | 400        | dyn_type      |


    Scenario Outline: Create a Dynaform using the Copy/Import method for a project with bad parameters (negative tests)
        Given POST this data:
        """
        {
            "dyn_title": "<dyn_title>",
            "dyn_description": "<dyn_description>",
            "dyn_type": "<dyn_type>",
            "copy_import":
            {
                "prj_uid": "<copy_prj_uid>",
                "dyn_uid": "<copy_dyn_uid>"
            }
        }
        """
        And I request "project/<project>/dynaform"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:

        | test_description       | project                          | dyn_title         | dyn_description | dyn_type | copy_prj_uid                     | copy_dyn_uid                     | error_code | error_message |
        | invalid copy_prj_uid   | 14414793652a5d718b65590036026581 | Dynaform - Copy 1 | dyn copy        | xmlform  | 42445320652cd0000000000000000085 | 70070685552cd53605650f7062918505 | 400        | copy_import.prj_uid  |
        | invalid copy_dyn_uid   | 42445320652cd534acb3824056962285 | Dynaform - Copy 2 | dyn copy        | xmlform  | 14414793652a5d718b65590036026581 | 70070685500000000000000000000000 | 400        | copy_import.dyn_uid  |

    
    Scenario Outline: Create dynaform based on a PMTable for a project with bad parameters (negative tests)
        Given POST this data:
        """
        {
            "dyn_title": "<dyn_title>",
            "dyn_description": "<dyn_description>",
            "dyn_type": "<dyn_type>",
            "pmtable":
            {
                "tab_uid": "<tab_uid>",
                "fields": [
                      {
                          "fld_name": "<fld_name_01>",
                          "pro_variable": "<pro_variable_01>"
                      },
                      {
                          "fld_name": "<fld_name_02>",
                          "pro_variable": "<pro_variable_02>"
                      },
                      {
                          "fld_name": "<fld_name_03",
                          "pro_variable": "<pro_variable_03>"
                      }
                 ]
            }
        }
        """
        And I request "project/<project>/dynaform"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:

        | test_description    | project                          | dyn_title            | dyn_description  | dyn_type | tab_uid                          | fld_name_01  | pro_variable_01 | fld_name_02  | pro_variable_02 | fld_name_03     | pro_variable_03 |  error_code | error_message |
        | invalid tab_uid     | 14414793652a5d718b65590036026581 | Dynaform - pmtable 1 | dyn from pmtable | xmlform  | 65193158852cc1a00000000000000000 | DYN_UID      | @#APPLICATION   | DYN_TITLE    | @#TITLE         | DYN_DESCRIPTION | @#DESCRIPTION   | 400         | tab_uid       | 
        | invalid fld_name_01 | 42445320652cd534acb3824056962285 | Dynaform - pmtable 2 | dyn from pmtable | xmlform  | 65193158852cc1a93a5a535084878044 | DYN_INPUT    | @#APPLICATION   | DYN_TITLE    | @#TITLE         | DYN_DESCRIPTION | @#DESCRIPTION   | 400         | fld_name   |
