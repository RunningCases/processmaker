@ProcessMakerMichelangelo @RestAPI
Feature: Web Entry Main Tests
Requirements:
a workspace with the process 28733629952e66a362c4f63066393844 ("Sample Project #5 (Web Entry)") already loaded
there are zero web entry in the process
and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded
     
Background:
    Given that I have a valid access_token

#ASSIGNEE USER OF AN ACTIVITY
        
Scenario Outline: List assignees of an activity 
    Given I request "project/<project>/activity/<tas_uid>/assignee"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records

    Examples:

    | Description                       | project                          | tas_uid                          | tas_title | records |
    | Get list activity of process .pm  | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | Task 1    | 0       |
    | Get list activity of process .pm  | 28733629952e66a362c4f63066393844 | 56118778152e66babcc2103002009439 | Task 2    | 0       |
    | Get list activity of process .pm  | 28733629952e66a362c4f63066393844 | 18096002352e66bc1643af8048493068 | Task 3    | 0       |
    | Get list activity of process .pmx | 1455892245368ebeb11c1a5001393784 | 6274755055368eed1116388064384542 | Task 1    | 1       |
    | Get list activity of process .pmx | 1455892245368ebeb11c1a5001393784 | 4790702485368efad167477011123879 | Task 2    | 1       |
    | Get list activity of process .pmx | 1455892245368ebeb11c1a5001393784 | 2072984565368efc137a394001073529 | Task 3    | 1       |


Scenario Outline: Assign a user or group to an activity
    Given POST this data:
    """
    {
        "aas_uid": "<aas_uid>",
        "aas_type": "user"
    }
    """
    And I request "project/28733629952e66a362c4f63066393844/activity/<tas_uid>/assignee"
    And the content type is "application/json"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:

    | tas_uid                          | tas_title | aas_uid                          |
    | 44199549652e66ba533bb06088252754 | Task 1    | 00000000000000000000000000000001 |
    | 56118778152e66babcc2103002009439 | Task 2    | 00000000000000000000000000000001 |
    | 18096002352e66bc1643af8048493068 | Task 3    | 00000000000000000000000000000001 |

#WEB ENTRY

Scenario Outline: Get list Web Entries of a Project when there are exactly zero web entries
    And I request "project/<project>/web-entries"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records

    Examples:

    | Description                        | project                          | records |
    | Get list web entry of process .pm  | 28733629952e66a362c4f63066393844 | 0       |
    | Get list web entry of process .pmx | 1455892245368ebeb11c1a5001393784 | 1       |
   
    
Scenario Outline: Create a new Web Entry using the method: PHP pages with Web Services
    Given POST this data:
    """
    {
        "tas_uid": "<tas_uid>",
        "dyn_uid": "<dyn_uid>",
        "usr_uid": "00000000000000000000000000000001",
        "we_title": "<we_title>",
        "we_description": "<we_description>",
        "we_method": "WS",
        "we_input_document_access": <we_input_document_access>
    }
    """
    And I request "project/<project>/web-entry"
    And the content type is "application/json"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the type is "object"
    And the response has a "we_data" property
    And the "we_data" property type is "string"
    And store "we_data" in session array as variable "we_data_<we_number>"
    And store "we_uid" in session array as variable "we_uid_<we_number>"
    And I request "we_data"  with the key "we_data" stored in session array as variable "we_data_<we_number>" and url is "absolute"
    And the content type is "text/html"
    Then the response status code should be 200

    Examples:

    | Description                                                   | we_number | project                          | tas_uid                          | we_title                 | dyn_uid                          | we_description               | we_input_document_access |
    | Web entry PHP task 1, with values normal                 .pm  | 1         | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | Web Entry PHP 1          | 60308801852e66b7181ae21045247174 | Webservice PHP Description 1 | 1                        |
    | Web entry PHP task 2, with character special             .pm  | 2         | 28733629952e66a362c4f63066393844 | 56118778152e66babcc2103002009439 | Web Entry PHP 2 @#$%&*   | 99869771852e66b7dc4b858088901665 | Webservice PHP Description 2 | 1                        |
    | Web entry PHP task 3, with we_input_document_access in 0 .pm  | 3         | 28733629952e66a362c4f63066393844 | 18096002352e66bc1643af8048493068 | Web Entry PHP 3          | 37977455352e66b892babe6071295002 | Webservice PHP Description 3 | 0                        |
    | Web entry PHP task 1, with values normal                 .pmx | 7         | 1455892245368ebeb11c1a5001393784 | 6274755055368eed1116388064384542 | x Web Entry PHP 1        | 898822326536be3a12addb0034537553 | Webservice PHP Description 1 | 1                        |
    | Web entry PHP task 2, with character special             .pmx | 8         | 1455892245368ebeb11c1a5001393784 | 4790702485368efad167477011123879 | x Web Entry PHP 2 @#$%&* | 318701641536be67467eb84048959982 | Webservice PHP Description 2 | 1                        |
    | Web entry PHP task 3, with we_input_document_access in 0 .pmx | 9         | 1455892245368ebeb11c1a5001393784 | 2072984565368efc137a394001073529 | x Web Entry PHP 3        | 923528807536be661d3d421038593630 | Webservice PHP Description 3 | 0                        |


Scenario Outline: Get list Web Entries of a Project after created in this scritp web entries PHP
    And I request "project/<project>/web-entries"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records

    Examples:

    | Description                        | project                          | records |
    | Get list web entry of process .pm  | 28733629952e66a362c4f63066393844 | 3       |
    | Get list web entry of process .pmx | 1455892245368ebeb11c1a5001393784 | 4       |


Scenario Outline: Get a single Web Entry PHP of a Project
    Given that I want to get a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
    And I request "project/<project>/web-entry"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "we_title" is set to "<we_title>"
    And that "we_description" is set to "<we_description>"
    And that "we_input_document_access" is set to "<we_input_document_access>"
           
    Examples:

    | we_number | project                          | tas_uid                          | we_title               | dyn_uid                          | we_description               | we_input_document_access |
    | 1         | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | Web Entry PHP 1        | 60308801852e66b7181ae21045247174 | Webservice PHP Description 1 | 1                        |
    | 2         | 28733629952e66a362c4f63066393844 | 56118778152e66babcc2103002009439 | Web Entry PHP 2 @#$%&* | 99869771852e66b7dc4b858088901665 | Webservice PHP Description 2 | 1                        |
    | 3         | 28733629952e66a362c4f63066393844 | 18096002352e66bc1643af8048493068 | Web Entry PHP 3        | 37977455352e66b892babe6071295002 | Webservice PHP Description 3 | 0                        |
    | 7         | 1455892245368ebeb11c1a5001393784 | 6274755055368eed1116388064384542 | xWeb Entry PHP 1        | 898822326536be3a12addb0034537553 | Webservice PHP Description 1 | 1                        |
    | 8         | 1455892245368ebeb11c1a5001393784 | 4790702485368efad167477011123879 | xWeb Entry PHP 2 @#$%&* | 318701641536be67467eb84048959982 | Webservice PHP Description 2 | 1                        |
    | 9         | 1455892245368ebeb11c1a5001393784 | 2072984565368efc137a394001073529 | xWeb Entry PHP 3        | 923528807536be661d3d421038593630 | Webservice PHP Description 3 | 0                        |


Scenario Outline: Update Web Entry PHP
    Given PUT this data:
    """
    {
        "tas_uid": "<tas_uid>",
        "dyn_uid": "<dyn_uid>",
        "we_title": "<we_title>",
        "we_description": "<we_description>",
        "we_method": "WS",
        "we_input_document_access": 1
    }
    """
    And that I want to update a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
    And I request "project/<project>/web-entry"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:

    | we_number | project                          | tas_uid                          | we_title                       | dyn_uid                          | we_description                      | we_input_document_access |
    | 1         | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | Update Web Entry PHP 1         | 60308801852e66b7181ae21045247174 | Update Webservice PHP Description 1 | 1                        |
    | 2         | 28733629952e66a362c4f63066393844 | 56118778152e66babcc2103002009439 | Update Web Entry PHP 2 @#$%&*  | 99869771852e66b7dc4b858088901665 | Update Webservice PHP Description 2 | 1                        | 
    | 7         | 1455892245368ebeb11c1a5001393784 | 6274755055368eed1116388064384542 | xUpdate Web Entry PHP 1        | 898822326536be3a12addb0034537553 | Upadte Webservice PHP Description 1 | 1                        |
    | 8         | 1455892245368ebeb11c1a5001393784 | 4790702485368efad167477011123879 | xUpdate Web Entry PHP 2 @#$%&* | 318701641536be67467eb84048959982 | Upadte Webservice PHP Description 2 | 1                        |
    

Scenario Outline: Get a single Web Entry PHP of a Project
    Given that I want to get a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
    And I request "project/<project>/web-entry"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "we_title" is set to "<we_title>"
    And that "we_description" is set to "<we_description>"
    And that "we_input_document_access" is set to "<we_input_document_access>"
           
    Examples:

    | we_number | project                          | tas_uid                          | we_title                      | dyn_uid                          | we_description                      | usr_uid                          | we_method |
    | 1         | 28733629952e66a362c4f63066393844 | 44199549652e66ba533bb06088252754 | Update Web Entry PHP 1        | 60308801852e66b7181ae21045247174 | Update Webservice PHP Description 1 | 00000000000000000000000000000001 | WS        |
    | 2         | 28733629952e66a362c4f63066393844 | 56118778152e66babcc2103002009439 | Update Web Entry PHP 2 @#$%&* | 99869771852e66b7dc4b858088901665 | Update Webservice PHP Description 2 | 00000000000000000000000000000001 | WS        |  
    | 3         | 28733629952e66a362c4f63066393844 | 18096002352e66bc1643af8048493068 | Web Entry PHP 3               | 37977455352e66b892babe6071295002 | Webservice PHP Description 3        | 00000000000000000000000000000001 | WS        |
    | 7         | 1455892245368ebeb11c1a5001393784 | 6274755055368eed1116388064384542 | xUpdate Web Entry PHP 1        | 898822326536be3a12addb0034537553 | Upadte Webservice PHP Description 1 | 00000000000000000000000000000001 | WS        |
    | 8         | 1455892245368ebeb11c1a5001393784 | 4790702485368efad167477011123879 | xUpdate Web Entry PHP 2 @#$%&* | 318701641536be67467eb84048959982 | Upadte Webservice PHP Description 2 | 00000000000000000000000000000001 | WS        |
    | 9         | 1455892245368ebeb11c1a5001393784 | 2072984565368efc137a394001073529 | xWeb Entry PHP 3               | 923528807536be661d3d421038593630 | Webservice PHP Description 3        | 00000000000000000000000000000001 | WS        |


Scenario Outline: Update Web Entry, when we_method is change a other method of the register 3
    Given PUT this data:
    """
    {
        "tas_uid": "18096002352e66bc1643af8048493068",
        "dyn_uid": "37977455352e66b892babe6071295002",
        "usr_uid": "00000000000000000000000000000001",
        "we_title": "Web Entry PHP 3 change method",
        "we_description": "Webservice PHP Description 3",
        "we_method": "HTML",
        "we_input_document_access": 1
    }
    """
    And that I want to update a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
    And I request "project/28733629952e66a362c4f63066393844/web-entry"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:

    | we_number | tas_uid                          | we_title                      | dyn_uid                          | we_description                      | usr_uid                          | we_method |
    | 3         | 18096002352e66bc1643af8048493068 | Web Entry PHP 3               | 37977455352e66b892babe6071295002 | Webservice PHP Description 3        | 00000000000000000000000000000001 | WS        |


Scenario Outline: Create a new Web Entry using the method: Single HTML
    Given POST this data:
    """
    {
        "tas_uid": "<tas_uid>",
        "dyn_uid": "<dyn_uid>",
        "we_title": "<we_title>",
        "we_description": "<we_description>",
        "we_method": "HTML",
        "we_input_document_access": <we_input_document_access>
    }
    """
    And I request "project/28733629952e66a362c4f63066393844/web-entry"
    And the content type is "application/json"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the type is "object"
    And the response has a "we_data" property
    And the "we_data" property type is "string"
    And store "we_data" in session array as variable "we_data_<we_number>"
    And store "we_uid" in session array as variable "we_uid_<we_number>"

    Examples:

    | Description                                               | we_number | tas_uid                          | we_title                | dyn_uid                          | we_description                | we_input_document_access |
    | Web entry HTML task 3, with values normal                 | 4         | 18096002352e66bc1643af8048493068 | Web Entry HTML 1        | 37977455352e66b892babe6071295002 | Webservice HTML description 1 | 1                        |
    | Web entry HTML task 1, with we_input_document_access in 0 | 5         | 44199549652e66ba533bb06088252754 | Web Entry HTML 2        | 60308801852e66b7181ae21045247174 | Webservice HTML Description 2 | 1                        |
    | Web entry HTML task 2, with character special             | 6         | 56118778152e66babcc2103002009439 | Web Entry HTML 3 @#$%&* | 99869771852e66b7dc4b858088901665 | Webservice HTML Description 3 | 1                        |
    
    
Scenario: Get list Web Entries of a Project after created in this scritp web entries HTML
    And I request "project/28733629952e66a362c4f63066393844/web-entries"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 6 records


Scenario Outline: Get a single Web Entry HTML of a Project
    Given that I want to get a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
    And I request "project/28733629952e66a362c4f63066393844/web-entry"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "we_title" is set to "<we_title>"
    And that "we_description" is set to "<we_description>"
    And that "we_input_document_access" is set to "<we_input_document_access>"
           
    Examples:

    | we_number | tas_uid                          | we_title                | dyn_uid                          | we_description                | we_input_document_access |
    | 4         | 18096002352e66bc1643af8048493068 | Web Entry HTML 1        | 37977455352e66b892babe6071295002 | Webservice HTML description 1 | 1                        |
    | 5         | 44199549652e66ba533bb06088252754 | Web Entry HTML 2        | 60308801852e66b7181ae21045247174 | Webservice HTML Description 2 | 1                        |
    | 6         | 56118778152e66babcc2103002009439 | Web Entry HTML 3 @#$%&* | 99869771852e66b7dc4b858088901665 | Webservice HTML Description 3 | 1                        |
    

Scenario Outline: Update Web Entry HTML
    Given PUT this data:
    """
    {
        "tas_uid": "<tas_uid>",
        "dyn_uid": "<dyn_uid>",
        "usr_uid": "<usr_uid>",
        "we_title": "<we_title>",
        "we_description": "<we_description>",
        "we_method": "<we_method>",
        "we_input_document_access": 1
    }
    """
    And that I want to update a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
    And I request "project/28733629952e66a362c4f63066393844/web-entry"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"

    Examples:

    | we_number | tas_uid                          | we_title                       | dyn_uid                          | we_description                       | usr_uid                          | we_method |
    | 4         | 18096002352e66bc1643af8048493068 | Update Web Entry HTML 1        | 37977455352e66b892babe6071295002 | Update Webservice HTML Description 1 | 00000000000000000000000000000001 | HTML      |
    | 5         | 44199549652e66ba533bb06088252754 | Update Web Entry HTML 2 @#$%&* | 60308801852e66b7181ae21045247174 | Update Webservice HTML Description 2 | 00000000000000000000000000000001 | HTML      |  


Scenario Outline: Get a single Web Entry HTML of a Project
    Given that I want to get a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
    And I request "project/28733629952e66a362c4f63066393844/web-entry"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "we_title" is set to "<we_title>"
    And that "we_description" is set to "<we_description>"
    And that "we_input_document_access" is set to "<we_input_document_access>"
           
    Examples:

    | we_number | tas_uid                          | we_title                       | dyn_uid                          | we_description                       | usr_uid                          | we_method |
    | 4         | 18096002352e66bc1643af8048493068 | Update Web Entry HTML 1        | 37977455352e66b892babe6071295002 | Update Webservice HTML Description 1 | 00000000000000000000000000000001 | HTML      |
    | 5         | 44199549652e66ba533bb06088252754 | Update Web Entry HTML 2 @#$%&* | 60308801852e66b7181ae21045247174 | Update Webservice HTML Description 2 | 00000000000000000000000000000001 | HTML      |  
    | 6         | 56118778152e66babcc2103002009439 | Web Entry HTML 3 @#$%&*        | 99869771852e66b7dc4b858088901665 | Webservice HTML Description 3        | 00000000000000000000000000000001 | HTML      |
    

Scenario Outline: Delete a Web Entry of a Project
    Given that I want to delete a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
    And I request "project/<project>/web-entry"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:

    | project                          | we_number |
    | 28733629952e66a362c4f63066393844 | 1         |
    | 28733629952e66a362c4f63066393844 | 2         |
    | 28733629952e66a362c4f63066393844 | 3         |
    | 28733629952e66a362c4f63066393844 | 4         |
    | 28733629952e66a362c4f63066393844 | 5         |
    | 28733629952e66a362c4f63066393844 | 6         |
    | 1455892245368ebeb11c1a5001393784 | 7         |
    | 1455892245368ebeb11c1a5001393784 | 8         |
    | 1455892245368ebeb11c1a5001393784 | 9         |


Scenario Outline: Get list Web Entries of a Project
    And I request "project/<project>/web-entries"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records

    Examples:

    | Description                        | project                          | records |
    | Get list web entry of process .pm  | 28733629952e66a362c4f63066393844 | 0       |
    | Get list web entry of process .pmx | 1455892245368ebeb11c1a5001393784 | 1       |


#WEB ENTRY - END


#DELETE /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/assignee/{aas_uid}
    Scenario Outline: Remove an assignee of an activity
    Given that I want to delete a resource with the key "obj_uid" stored in session array
    And I request "project/28733629952e66a362c4f63066393844/activity/<tas_uid>/assignee/<aas_uid>"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:

    | tas_uid                          | tas_title | aas_uid                          |
    | 44199549652e66ba533bb06088252754 | Task 1    | 00000000000000000000000000000001 |
    | 56118778152e66babcc2103002009439 | Task 2    | 00000000000000000000000000000001 |
    | 18096002352e66bc1643af8048493068 | Task 3    | 00000000000000000000000000000001 |


#GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/assignee?filter=john&start=0&limit=50
    Scenario Outline: List assignees of an activity
    Given I request "project/28733629952e66a362c4f63066393844/activity/<tas_uid>/assignee"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the json data is an empty array

    Examples:

    | tas_uid                          | tas_title |
    | 44199549652e66ba533bb06088252754 | Task 1    |
    | 56118778152e66babcc2103002009439 | Task 2    |
    | 18096002352e66bc1643af8048493068 | Task 3    |