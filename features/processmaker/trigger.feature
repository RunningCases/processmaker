@ProcessMakerMichelangelo @RestAPI
Feature: Testing triggers
    Scenario: List triggers of an project
        Given that I have a valid access_token
        And I request "project/534152995521df6bb5986c4062665559/triggers"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"



    Scenario: Create a trigger
        Given that I have a valid access_token
        And POST this data:
            """
            {
                "tri_title": "nuevo triggerr",
                "tri_description": "descripcion"
            }
            """
        And I request "project/534152995521df6bb5986c4062665559/trigger"
        Then the response status code should be 201