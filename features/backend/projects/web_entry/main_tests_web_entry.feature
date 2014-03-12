@ProcessMakerMichelangelo @RestAPI
Feature: Web Entry Main Tests
    Requirements:
     a workspace with the process 28733629952e66a362c4f63066393844 ("Sample Project #5 (Web Entry)") already loaded
     there are zero web entry in the process
     Default user "admin" with password "admin"

    Background:
        Given that I have a valid access_token

    #ASSIGNEE USER OF AN ACTIVITY
        
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

    Scenario: Get list Web Entries of a Project when there are exactly zero web entries
        And I request "project/28733629952e66a362c4f63066393844/web-entries"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    
    Scenario Outline: Create a new Web Entry using the method: PHP pages with Web Services
        Given POST this data:
        """
        {
            "tas_uid": "<tas_uid>",
            "dyn_uid": "<dyn_uid>",
            "method": "WS",
            "input_document_access": 1,
            "usr_username": "admin",
            "usr_password": "admin"
        }
        """
        And I request "project/28733629952e66a362c4f63066393844/web-entry"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And the response has a "url" property
        And the "url" property type is "string"
        And store "url" in session array as variable "url_<url_number>"

        Examples:
        | tas_uid                          | tas_title | dyn_uid                          | dyn_title      | url_number |
        | 44199549652e66ba533bb06088252754 | Task 1    | 60308801852e66b7181ae21045247174 | DynaForm Demo1 | 1          |
        | 56118778152e66babcc2103002009439 | Task 2    | 99869771852e66b7dc4b858088901665 | DynaForm Demo2 | 2          |

    
    Scenario Outline: Test webentry URL
    Given I request "url"  with the key "url" stored in session array as variable "url_<url_number>" and url is "absolute"
    And the content type is "text/html"
    Then the response status code should be 200

    Examples:

    | url_number |
    | 1           |
    | 2           |

    Scenario Outline: Create a new Web Entry using the method: Single HTML
        Given POST this data:
        """
        {
            "tas_uid": "<tas_uid>",
            "dyn_uid": "<dyn_uid>",
            "method": "HTML",
            "input_document_access": 1
        }
        """
        And I request "project/28733629952e66a362c4f63066393844/web-entry"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And the response has a "html" property
        And the "html" property type is "string"

        Examples:
        | tas_uid                          | tas_title | dyn_uid                          | dyn_title      |
        | 18096002352e66bc1643af8048493068 | Task 3    | 37977455352e66b892babe6071295002 | DynaForm Demo3 |

    
    Scenario Outline: Get list Web Entries of a Project
        And I request "project/28733629952e66a362c4f63066393844/web-entries"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "tas_uid" property in row <i> equals "<tas_uid>"
        And the "tas_title" property in row <i> equals "<tas_title>"
        And the "dyn_uid" property in row <i> equals "<dyn_uid>"
        And the "dyn_title" property in row <i> equals "<dyn_title>"

        Examples:
        | i | tas_uid                          | tas_title | dyn_uid                          | dyn_title     |
        | 0 | 44199549652e66ba533bb06088252754 | Task 1    | 60308801852e66b7181ae21045247174 |DynaForm Demo1 |
        | 1 | 56118778152e66babcc2103002009439 | Task 2    | 99869771852e66b7dc4b858088901665 |DynaForm Demo2 |

    
    Scenario Outline: Get a single Web Entry of a Project
        Given that I want to get a resource with the key "obj_uid" stored in session array
        And I request "project/28733629952e66a362c4f63066393844/web-entry/<tas_uid>/<dyn_uid>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "tas_uid" is set to "<tas_uid>"
        And that "tas_title" is set to "<tas_title>"
        And that "dyn_uid" is set to "<dyn_uid>"
        And that "dyn_title" is set to "<dyn_title>"

        Examples:
        | tas_uid                          | tas_title | dyn_uid                          | dyn_title     |
        | 44199549652e66ba533bb06088252754 | Task 1    | 60308801852e66b7181ae21045247174 |DynaForm Demo1 |
        | 56118778152e66babcc2103002009439 | Task 2    | 99869771852e66b7dc4b858088901665 |DynaForm Demo2 |

    
    

   