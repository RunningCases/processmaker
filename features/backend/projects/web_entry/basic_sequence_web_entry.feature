@ProcessMakerMichelangelo @RestAPI
Feature: Web Entry
    Requirements:
        Default user "admin" with password "admin"

    Background:
        Given that I have a valid access_token

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


#POST /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/assignee
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
#GET /api/1.0/{workspace}/project/{prj_uid}/web-entries
    Scenario: Get list Web Entries of a Project
        And I request "project/28733629952e66a362c4f63066393844/web-entries"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 records
        

#POST /api/1.0/{workspace}/project/{prj_uid}/web-entry
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
            "we_input_document_access": 1

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
        And I request "we_data"  with the key "we_data" stored in session array as variable "we_data_<we_number>" and url is "absolute"
        And the content type is "text/html"
        Then the response status code should be 200
     
        Examples:
        | we_number | tas_uid                          | we_title               | dyn_uid                          | we_description             |
        | 1         | 44199549652e66ba533bb06088252754 | Webservice PHP Title 1 | 60308801852e66b7181ae21045247174 | Webservice PHP Description |
        | 2         | 56118778152e66babcc2103002009439 | Webservice PHP Title 2 | 99869771852e66b7dc4b858088901665 | Webservice PHP Description |

    
#POST /api/1.0/{workspace}/project/{prj_uid}/web-entry
    Scenario Outline: Create a new Web Entry using the method: Single HTML
        Given POST this data:
        """
        {
            "tas_uid": "<tas_uid>",
            "dyn_uid": "<dyn_uid>",
            "we_title": "<we_title>",
            "we_description": "<we_description>",
            "we_method": "HTML",
            "we_input_document_access": 1
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
        | we_number | tas_uid                          | we_title                | dyn_uid                          | we_description         |
        | 3         | 18096002352e66bc1643af8048493068 | Webservice HTML Title 2 | 37977455352e66b892babe6071295002 | Webservice description |


#GET /api/1.0/{workspace}/project/{prj_uid}/web-entries
        Scenario: Get list Web Entries of a Project
        And I request "project/28733629952e66a362c4f63066393844/web-entries"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 3 records
        
    
#GET GET /api/1.0/{workspace}/project/{prj_uid}/web-entry/{tas_uid}/{dyn_uid}
        Scenario Outline: Get a single Web Entry PHP of a Project
        Given that I want to get a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
        And I request "project/28733629952e66a362c4f63066393844/web-entry"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "we_title" is set to "<we_title>"
        And that "we_description" is set to "<we_description>"
           
        Examples:
        | we_number | tas_uid                          | dyn_uid                          | we_title                | we_description             |
        | 1         | 44199549652e66ba533bb06088252754 | 60308801852e66b7181ae21045247174 | Webservice PHP Title 1  | Webservice PHP Description |  
        | 2         | 56118778152e66babcc2103002009439 | 99869771852e66b7dc4b858088901665 | Webservice PHP Title 2  | Webservice PHP Description |
        | 3         | 18096002352e66bc1643af8048493068 | 37977455352e66b892babe6071295002 | Webservice HTML Title 2 | Webservice description     |


#PUT /api/1.0/{workspace}/project/{prj_uid}/web-entry/{we_uid}
    Scenario Outline: Update Web Entry
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

        | we_number | tas_uid                          | we_title                      | dyn_uid                          | we_description                    | usr_uid                          | we_method |
        | 1         | 44199549652e66ba533bb06088252754 | Update Webservice PHP Title 1 | 60308801852e66b7181ae21045247174 | Update Webservice PHP Description | 00000000000000000000000000000001 | WS        |
        | 2         | 56118778152e66babcc2103002009439 | Update Webservice PHP Title 2 | 99869771852e66b7dc4b858088901665 | Update Webservice PHP Description | 00000000000000000000000000000001 | WS        |  


#GET GET /api/1.0/{workspace}/project/{prj_uid}/web-entry/{tas_uid}/{dyn_uid}
        Scenario Outline: Get a single Web Entry HTML of a Project
        Given that I want to get a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
        And I request "project/28733629952e66a362c4f63066393844/web-entry"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "we_title" is set to "<we_title>"
        And that "we_description" is set to "<we_description>"
           
        Examples:
        | we_number | tas_uid                          | dyn_uid                          | we_title                       | we_description                    |
        | 1         | 44199549652e66ba533bb06088252754 | 60308801852e66b7181ae21045247174 | Update Webservice PHP Title 1  | Update Webservice PHP Description |  
        | 2         | 56118778152e66babcc2103002009439 | 99869771852e66b7dc4b858088901665 | Update Webservice PHP Title 2  | Update Webservice PHP Description |
        | 3         | 18096002352e66bc1643af8048493068 | 37977455352e66b892babe6071295002 | Update Webservice HTML Title 2 | Update Webservice description     |


#DELETE /api/1.0/{workspace}/project/{prj_uid}/web-entry/{tas_uid}/{dyn_uid}
    Scenario Outline: Delete a Web Entry of a Project
        Given that I want to delete a resource with the key "we_uid" stored in session array as variable "we_uid_<we_number>"
        And I request "project/28733629952e66a362c4f63066393844/web-entry"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | we_number |
        | 1         |
        | 2         |
        | 3         |

   
#GET /api/1.0/{workspace}/project/{prj_uid}/web-entries
        Scenario: Get list Web Entries of a Project
        And I request "project/28733629952e66a362c4f63066393844/web-entries"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 records


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