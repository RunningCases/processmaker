@ProcessMakerMichelangelo @RestAPI
Feature: Web Entry - Remove Assignee and Web Entries
    
    Background:
        Given that I have a valid access_token


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


    Scenario Outline: Delete a Web Entry of a Project
        Given that I want to delete a resource with the key "obj_uid" stored in session array
        And I request "project/28733629952e66a362c4f63066393844/web-entry/<tas_uid>/<dyn_uid>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | tas_uid                          | tas_title | dyn_uid                          | dyn_title     |
        | 44199549652e66ba533bb06088252754 | Task 1    | 60308801852e66b7181ae21045247174 |DynaForm Demo1 |
        | 56118778152e66babcc2103002009439 | Task 2    | 99869771852e66b7dc4b858088901665 |DynaForm Demo2 |
   
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


    Scenario: Get list Web Entries of a Project
        And I request "project/28733629952e66a362c4f63066393844/web-entries"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array