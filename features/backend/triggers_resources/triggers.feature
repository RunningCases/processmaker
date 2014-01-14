@ProcessMakerMichelangelo @RestAPI
Feature: Testing triggers

    Background:
        Given that I have a valid access_token

    @1: TEST FOR POST TRIGGER /--------------------------------------------------------------------
    Scenario Outline: Get a list of triggers of a project
        Given I request "project/<prj_uid>/triggers"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has <records> records
        
        Examples:
        | project                          |  records |
        | 251815090529619a99a2bf4013294414 | 0        | 
    
    @2: TEST FOR POST TRIGGER /--------------------------------------------------------------------
    Scenario Outline: Create a trigger
        
        Given POST this data:
            """
            {
                "tri_title": "<tri_title>",
                "tri_description": "<tri_description>",
                "tri_type": "<tri_type>",
                "tri_webbot": "<tri_webbot>",
                "tri_param": "PRIVATE"
            }
            """
        And I request "project/<project>/trigger"
        Then the response status code should be 201
        And store "tri_uid" in session array as variable "tri_uid_<tri_number>"

        Examples:

        | project                          | tri_number | tri_title                    | tri_description                                          |tri_type  | tri_webbot |
        | 251815090529619a99a2bf4013294414 | 1          | nuevo trigger 2              | descripcion                                              |SCRIPT    | @@user1 = @@USER_LOGGED; \n $x = rand();|
        | 251815090529619a99a2bf4013294414 | 2          | otro trigger 2               | descripcion de otro trigger                              |SCRIPT    | //Trigger with comments |
        

    @3: TEST FOR PUT TRIGGER /-----------------------------------------------------------------------
    Scenario Outline: Update a trigger
        
        Given PUT this data:
            """
            {
                "tri_title": "<tri_title>",
                "tri_description": "<tri_description>"
            }
            """
        And that I want to update a resource with the key "tri_uid" stored in session array as variable "tri_uid_<tri_number>"
        And I request "project/<project>/trigger"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | project                          | tri_number  | tri_title              | tri_description                     |
        | 251815090529619a99a2bf4013294414 | 1           | trigger editado 2      | descripcion editada                 |
        | 251815090529619a99a2bf4013294414 | 2           | otro trigger editado 2 | descripcion de otro trigger editado |


    @4: TEST FOR GET TRIGGER /-----------------------------------------------------------------------
    Scenario Outline: Get a trigger
        
        Given that I want to get a resource with the key "tri_uid" stored in session array as variable "tri_uid_<tri_number>"
        And I request "project/<project>/trigger"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "tri_title" is set to "<tri_title>"
        And that "tri_description" is set to "<tri_description>"

        Examples:

        | project                          | tri_number  | tri_title                | tri_description                     |
        | 251815090529619a99a2bf4013294414 | 1           | trigger editado 2        | descripcion editada                 |
        | 251815090529619a99a2bf4013294414 | 2           | otro trigger editado 2   | descripcion de otro trigger editado |



    @5: TEST FOR DELETE TRIGGER /-----------------------------------------------------------------------
    Scenario Outline: Get a trigger
        
        Given that I want to delete a resource with the key "tri_uid" stored in session array as variable "tri_uid_<tri_number>"
        And I request "project/<project>/trigger"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | project                          | tri_number  |
        | 251815090529619a99a2bf4013294414 | 1           |
        | 251815090529619a99a2bf4013294414 | 2           |

    


    @6: TEST FOR POST TRIGGER /--------------------------------------------------------------------
    Scenario Outline: Create a trigger
        
        Given POST this data:
            """
            {
                "tri_title": "<tri_title>",
                "tri_description": "<tri_description>"
                "tri_type": "<tri_type>",
                "tri_webbot": "",
                "tri_param": "PRIVATE"
            }
            """
        And I request "project/<project>/trigger"
        Then the response status code should be 400
        
        Examples:

        | project                          | tri_title                    | tri_description                                          |tri_type  |
        | 251815090529619a99a2bf4013294414 | Especial !@#$%^&*(){[/½‘€¤@  | Trigger con caracteres especiales                        |SCRIPT    |
        | 251815090529619a99a2bf4013294414 |                              | Trigger con nombre en blanco                             |SCRIPT    |
        | 251815090529619a99a2bf4013294414 | Trigger 3                    | Descripcion con caracteres especiales !@#$%^&*(){[/½‘€¤@ |SCRIPT    |
        | 251815090529619a99a2bf4013294414 | Trigger 4                    | descripcion                                              |SCRI123%@$|