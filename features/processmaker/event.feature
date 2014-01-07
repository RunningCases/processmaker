@ProcessMakerMichelangelo @RestAPI
Feature: Testing triggers

    @1: TEST FOR POST EVENT /----------------------------------------------------------------------
    Scenario: Create a event
        Given that I have a valid access_token
        And POST this data:
            """
            {
                "evn_description": "DE BEHAT",
                "evn_status": "ACTIVE",
                "evn_action": "SEND_MESSAGE",
                "evn_related_to": "SINGLE",
                "tas_uid": "97192372152a5c78f04a794095806311",
                "evn_tas_uid_from": "97192372152a5c78f04a794095806311",
                "evn_tas_estimated_duration": 1,
                "evn_time_unit": "DAYS",
                "evn_when": 1,
                "evn_when_occurs": "AFTER_TIME",
                "tri_uid": "95325847552af0c07792c15098680510"
            }
            """
        And I request "project/251815090529619a99a2bf4013294414/event"
        Then the response status code should be 201
        And store "evn_uid" in session array

    @2: TEST FOR PUT EVENT /-----------------------------------------------------------------------
    Scenario: Update a event
        Given that I have a valid access_token
        And PUT this data:
            """
            {
                "evn_description": "change description",
                "evn_status": "ACTIVE",
                "evn_action": "SEND_MESSAGE",
                "evn_related_to": "SINGLE",
                "tas_uid": "97192372152a5c78f04a794095806311",
                "evn_tas_uid_from": "97192372152a5c78f04a794095806311",
                "evn_tas_estimated_duration": 1,
                "evn_time_unit": "DAYS",
                "evn_when": 1,
                "evn_when_occurs": "AFTER_TIME",
                "tri_uid": "95325847552af0c07792c15098680510"
            }
            """
        And that I want to update a resource with the key "evn_uid" stored in session array
        And I request "project/251815090529619a99a2bf4013294414/event"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"


    @3: TEST FOR GET EVENT /-----------------------------------------------------------------------
    Scenario: Get a event
        Given that I have a valid access_token
        And that I want to find a resource with the key "evn_uid" stored in session array
        And I request "project/251815090529619a99a2bf4013294414/event"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "evn_description" is set to "change description"