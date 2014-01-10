@ProcessMakerMichelangelo @RestAPI
Feature: Triggers Resources
    
    @1: TEST FOR GET TRIGGERS /--------------------------------------------------------------------
    Scenario: List all the events (result 1 trigger)
        Given that I have a valid access_token
        And I request "project/251815090529619a99a2bf4013294414/triggers"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 1 record


    @2: TEST FOR POST TRIGGER /--------------------------------------------------------------------
    Scenario: Create a trigger
        Given that I have a valid access_token
        And POST this data:
            """
            {
                "tri_title": "nuevo trigger",
                "tri_description": "descripcion"
            }
            """
        And I request "project/251815090529619a99a2bf4013294414/trigger"
        Then the response status code should be 201
        And store "tri_uid" in session array


    @3: TEST FOR PUT TRIGGER /---------------------------------------------------------------------
    Scenario: Update a trigger
        Given that I have a valid access_token
        And PUT this data:
            """
            {
                "tri_title": "trigger editado",
                "tri_description": "descripcion editada"
            }
            """
        And that I want to update a resource with the key "tri_uid" stored in session array
        And I request "project/251815090529619a99a2bf4013294414/trigger"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"


    @4: TEST FOR GET TRIGGER /---------------------------------------------------------------------
    Scenario: Get a trigger
        Given that I have a valid access_token
        And that I want to get a resource with the key "tri_uid" stored in session array
        And I request "project/251815090529619a99a2bf4013294414/trigger"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "tri_title" is set to "trigger editado"
        And that "tri_description" is set to "descripcion editada"


    @5: TEST FOR GET TRIGGERS /--------------------------------------------------------------------
    Scenario: List all the events (result 2 trigger)
        Given that I have a valid access_token
        And I request "project/251815090529619a99a2bf4013294414/triggers"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 2 record


    @6: TEST FOR DELETE TRIGGER /-----------------------------------------------------------------------
    Scenario: Get a trigger
        Given that I have a valid access_token
        And that I want to delete a resource with the key "tri_uid" stored in session array
        And I request "project/251815090529619a99a2bf4013294414/trigger"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"


    @7: TEST FOR GET TRIGGERS /--------------------------------------------------------------------
    Scenario: List all the events (result 1 trigger)
        Given that I have a valid access_token
        And I request "project/251815090529619a99a2bf4013294414/triggers"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 1 record