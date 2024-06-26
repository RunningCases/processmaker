@ProcessMakerMichelangelo @RestAPI
Feature: Dynaform Main Tests
  Requirements:
    a workspace with the process 14414793652a5d718b65590036026581 ("Sample Project #1") already loaded
    there are three activities in the process
    and a workspace with the process 42445320652cd534acb3824056962285 ("Sample Project #2 (DynaForms Resources)") already loaded, this process will be used for the import of Dynaform
    there are one dynaform in the process 
    and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded

Background:
        Given that I have a valid access_token


    Scenario Outline: Get a List DynaForms of a Project    
        Given I request "project/<project>/dynaforms"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

    Examples:

        | Description                                                  | project                          | records |
        | Get for the amount dynaform of process Sample Project #1     | 14414793652a5d718b65590036026581 | 0       |
        | Get for the amount dynaform of process Sample Project #2     | 42445320652cd534acb3824056962285 | 1       |
        | Get for the amount dynaform of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 26      |


    Scenario Outline: Normal Dynaform creation
        Given POST a dynaform:
        """
        {
            "dyn_title": "<dyn_title>",
            "dyn_description": "<dyn_description>",
            "dyn_type": "<dyn_type>",
            "dyn_content": "<dyn_content>",
            "dyn_version": 1

        }
        """
        And I request "project/<project>/dynaform"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "dyn_uid" in session array as variable "dyn_uid_<dyn_uid_number>"

        Examples:
        | test_description                                            | project                          | dyn_title         | dyn_description | dyn_type | dyn_content      | dyn_uid_number |
        | Create dynaform xmlform P1 of process Sample Project #1     | 14414793652a5d718b65590036026581 | Dynaform - Normal | dyn normal P1   | xmlform  | sample content 1 | 1              |
        | Create dynaform grid P1 of process Sample Project #1        | 14414793652a5d718b65590036026581 | Dynaform - Grid   | dyn grid P1     | grid     | sample content 2 | 2              |
        | Create dynaform xmlform P2 of process Sample Project #2     | 42445320652cd534acb3824056962285 | Dynaform - Normal | dyn normal P2   | xmlform  |                  | 3              | 
        | Create dynaform grid P2 of process Sample Project #2        | 42445320652cd534acb3824056962285 | Dynaform - Grid   | dyn grid P2     | grid     | sample content 4 | 4              | 
        | Create dynaform xmlform P1 of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | Dynaform - Normal | dyn normal P1   | xmlform  | sample content 1 | 9              |
        | Create dynaform grid P1 of process Process Complete BPMN    | 1455892245368ebeb11c1a5001393784 | Dynaform - Grid   | dyn grid P1     | grid     | sample content 2 | 10             |
        

    Scenario Outline: Get a single dynaform and check some properties
        Given that I want to get a resource with the key "dyn_uid" stored in session array as variable "dyn_uid_<dyn_uid_number>"
        And I request "project/<project>/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "dyn_title" is set to "<dyn_title>"
        And that "dyn_description" is set to "<dyn_description>"
        And that "dyn_type" is set to "<dyn_type>"
        And that "dyn_content" is set to "<dyn_content>"
        And that "dyn_version" is set to 1

        Examples:
        | test_description                                            | project                          | dyn_title         | dyn_description | dyn_type | dyn_content      | dyn_uid_number |
        | Create dynaform xmlform P1 of process Sample Project #1     | 14414793652a5d718b65590036026581 | Dynaform - Normal | dyn normal P1   | xmlform  | sample content 1 | 1              |
        | Create dynaform grid P1 of process Sample Project #1        | 14414793652a5d718b65590036026581 | Dynaform - Grid   | dyn grid P1     | grid     | sample content 2 | 2              |
        | Create dynaform xmlform P2 of process Sample Project #2     | 42445320652cd534acb3824056962285 | Dynaform - Normal | dyn normal P2   | xmlform  |                  | 3              | 
        | Create dynaform grid P2 of process Sample Project #2        | 42445320652cd534acb3824056962285 | Dynaform - Grid   | dyn grid P2     | grid     | sample content 4 | 4              | 
        | Create dynaform xmlform P1 of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | Dynaform - Normal | dyn normal P1   | xmlform  | sample content 1 | 9              |
        | Create dynaform grid P1 of process Process Complete BPMN    | 1455892245368ebeb11c1a5001393784 | Dynaform - Grid   | dyn grid P1     | grid     | sample content 2 | 10             |
        

    Scenario: Create dynaform with same name
        Given POST a dynaform:
        """
        {
            "dyn_title": "Dynaform - Normal",
            "dyn_description": "dyn normal P1",
            "dyn_type": "xmlform",
            "dyn_content": "sample content 1",
            "dyn_version": 1
        }
        """
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        Then the response status code should be 400
        And the response status message should have the following text "already exists"
        

    Scenario Outline: Create a Dynaform using the Copy/Import method
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
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "dyn_uid" in session array as variable "dyn_uid_<dyn_uid_number>"

        Examples:

        | test_description                                        | project                          | dyn_title         | dyn_description | dyn_type | dyn_uid_number | copy_prj_uid                     | copy_dyn_uid                     |
        | create dynaform copy 1 of process Sample Project #1     | 14414793652a5d718b65590036026581 | Dynaform - Copy 1 | dyn copy        | xmlform  | 5              | 42445320652cd534acb3824056962285 | 70070685552cd53605650f7062918506 |
        | create dynaform copy 2 of process Sample Project #2     | 42445320652cd534acb3824056962285 | Dynaform - Copy 2 | dyn copy        | xmlform  | 6              | 42445320652cd534acb3824056962285 | 70070685552cd53605650f7062918506 |
        | create dynaform copy 1 of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | Dynaform - Copy 1 | dyn copy        | xmlform  | 11             | 42445320652cd534acb3824056962285 | 70070685552cd53605650f7062918506 |
        

     Scenario: Create a Dynaform using the Copy/Import with same name
        Given POST this data:
        """
        {
            "dyn_title": "Dynaform - Copy 1",
            "dyn_description": "dyn copy",
            "dyn_type": "xmlform",
            "copy_import":
            {
                "prj_uid": "42445320652cd534acb3824056962285",
                "dyn_uid": "70070685552cd53605650f7062918506"
            }
        }
        """
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        Then the response status code should be 400
        And the response status message should have the following text "already exists"


    Scenario Outline: Create dynaform based on a PMTable
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
                      }
                 ]
            }
        }
        """
        And I request "project/<project>/dynaform"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "dyn_uid" in session array as variable "dyn_uid_<dyn_uid_number>"

        Examples:

        | test_description                                           | project                          | dyn_title            | dyn_description   | dyn_type | dyn_uid_number | tab_uid                          | fld_name_01  | pro_variable_01 |
        | create dynaform pmtable 1 of process Sample Project #1     | 14414793652a5d718b65590036026581 | Dynaform - pmtable 1 | dyn from pmtable1 | xmlform  | 7              | 65193158852cc1a93a5a535084878044 | DYN_UID      | @#APPLICATION   |
        | create dynaform pmtable 2 of process Sample Project #2     | 42445320652cd534acb3824056962285 | Dynaform - pmtable2  | dyn from pmtable2 | xmlform  | 8              | 65193158852cc1a93a5a535084878044 | DYN_UID      | @#APPLICATION   |
        | create dynaform pmtable 1 of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | Dynaform - pmtable 1 | dyn from pmtable1 | xmlform  | 12             | 65193158852cc1a93a5a535084878044 | DYN_UID      | @#APPLICATION   |
        

     Scenario: Create dynaform based on a PMTable with same name
        Given POST this data:
        """
        {
            "dyn_title": "Dynaform - pmtable 1",
            "dyn_description": "dyn from pmtable1",
            "dyn_type": "xmlform",
            "pmtable":
            {
                "tab_uid": "65193158852cc1a93a5a535084878044",
                "fields": [
                      {
                          "fld_name": "DYN_UID",
                          "pro_variable": "@#APPLICATION "
                      }
                 ]
            }
        }
        """
        And I request "project/14414793652a5d718b65590036026581/dynaform"
        And the content type is "application/json"
        Then the response status code should be 400
        And the response status message should have the following text "already exists"


    Scenario Outline: Get a List DynaForms of a Project list when there are 9 records, total in both projects
        Given I request "project/<project>/dynaforms"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

     Examples:

        | project                          | records |
        | 14414793652a5d718b65590036026581 | 4       |
        | 42445320652cd534acb3824056962285 | 5       |


    Scenario Outline: Update the Dynaform and then check if the values had changed
        Given PUT a dynaform:
        """
        {
            "dyn_title": "<dyn_title>",
            "dyn_description": "<dyn_description>",
            "dyn_type": "<dyn_type>",
            "dyn_content": "<dyn_content>",
            "dyn_version": 1

        }
        """
        And that I want to update a resource with the key "dyn_uid" stored in session array as variable "dyn_uid_<dyn_uid_number>"
        And I request "project/<project>/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        

        Examples:
        | test_description                        | project                          | dyn_title                  | dyn_description                    | dyn_type | dyn_content             | dyn_uid_number |
        | Update dynaform xmlform P1              | 14414793652a5d718b65590036026581 | My DynaForm1 Modified      | My DynaForm1 DESCRIPTION Modified  | grid     | update sample content 1 | 1              |
        | Update dynaform grid P1                 | 14414793652a5d718b65590036026581 | Dynaform - Grid Modified   | dyn grid P1 DESCRIPTION Modified   | xmlform  | update sample content 2 | 2              |
        | Update dynaform xmlform P2              | 42445320652cd534acb3824056962285 | Dynaform - Normal Modified | dyn normal P2 DESCRIPTION Modified | grid     | sample                  | 3              |
        | Update dynaform grid P2                 | 42445320652cd534acb3824056962285 | Dynaform - Grid Modified   | dyn grid P2 DESCRIPTION Modified   | xmlform  | sample content 4        | 4              |
        | Update of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | Dynaform - Normal Modified | dyn normal P1 DESCRIPTION Modified | xmlform  | update sample content 1 | 9              |
        

    Scenario Outline: Get a single dynaform and check some properties
        Given that I want to get a resource with the key "dyn_uid" stored in session array as variable "dyn_uid_<dyn_uid_number>"
        And I request "project/<project>/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "dyn_title" is set to "<dyn_title>"
        And that "dyn_description" is set to "<dyn_description>"
        And that "dyn_type" is set to "<dyn_type>"
        And that "dyn_content" is set to "<dyn_content>"
        And that "dyn_version" is set to 1

        Examples:
        | test_description                        | project                          | dyn_title                  | dyn_description                    | dyn_type | dyn_content             | dyn_uid_number |
        | Update dynaform xmlform P1              | 14414793652a5d718b65590036026581 | My DynaForm1 Modified      | My DynaForm1 DESCRIPTION Modified  | grid     | update sample content 1 | 1              |
        | Update dynaform grid P1                 | 14414793652a5d718b65590036026581 | Dynaform - Grid Modified   | dyn grid P1 DESCRIPTION Modified   | xmlform  | update sample content 2 | 2              |
        | Update dynaform xmlform P2              | 42445320652cd534acb3824056962285 | Dynaform - Normal Modified | dyn normal P2 DESCRIPTION Modified | grid     | sample                  | 3              |
        | Update dynaform grid P2                 | 42445320652cd534acb3824056962285 | Dynaform - Grid Modified   | dyn grid P2 DESCRIPTION Modified   | xmlform  | sample content 4        | 4              |
        | Update of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | Dynaform - Normal Modified | dyn normal P1 DESCRIPTION Modified | xmlform  | update sample content 1 | 9              |
        

    Scenario Outline: Delete all Dynaform created previously in this script
        Given that I want to delete a resource with the key "dyn_uid" stored in session array as variable "dyn_uid_<dyn_uid_number>"
        And I request "project/<project>/dynaform"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"


    Examples:

        | project                          | dyn_uid_number |
        | 14414793652a5d718b65590036026581 | 1              |
        | 14414793652a5d718b65590036026581 | 2              |
        | 42445320652cd534acb3824056962285 | 3              |
        | 42445320652cd534acb3824056962285 | 4              |
        | 14414793652a5d718b65590036026581 | 5              |
        | 42445320652cd534acb3824056962285 | 6              |
        | 14414793652a5d718b65590036026581 | 7              |
        | 42445320652cd534acb3824056962285 | 8              |
        | 1455892245368ebeb11c1a5001393784 | 9              |
        | 1455892245368ebeb11c1a5001393784 | 10             |
        | 1455892245368ebeb11c1a5001393784 | 11             |
        | 1455892245368ebeb11c1a5001393784 | 12             |
    
    
    Scenario Outline: Get a List DynaForms of a Project   
        Given I request "project/<project>/dynaforms"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

     Examples:

        | project                          | records |
        | 14414793652a5d718b65590036026581 | 0       |
        | 42445320652cd534acb3824056962285 | 1       |
        | 1455892245368ebeb11c1a5001393784 | 26      |
