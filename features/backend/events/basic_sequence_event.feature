@ProcessMakerMichelangelo @RestAPI
Feature: Events Resources

    @1: TEST FOR GET EVENTS /----------------------------------------------------------------------
    Scenario: List all the events (result 0 events)
        Given that I have a valid access_token
        And I request "project/74621721252eab0a6383731089824705/events"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record


    @2: TEST FOR POST EVENT /----------------------------------------------------------------------
    Scenario: Create a new event
        Given that I have a valid access_token
        And POST this data:
            """
            {
                "evn_description": "DE BEHAT",
                "evn_status": "ACTIVE",
                "evn_action": "SEND_MESSAGE",
                "evn_related_to": "SINGLE",
                "tas_uid": "46337349752eab0df6e7b61093784394",
                "evn_tas_estimated_duration": 1,
                "evn_time_unit": "DAYS",
                "evn_when": 1,
                "evn_when_occurs": "AFTER_TIME",
                "tri_uid": "95002116552eab0b786a743075022999"
            }
            """
        And I request "project/74621721252eab0a6383731089824705/event"
        Then the response status code should be 201
        And store "evn_uid" in session array

    @3: TEST FOR GET EVENTS /----------------------------------------------------------------------
    Scenario: List all the events (result 1 event)
        Given that I have a valid access_token
        And I request "project/74621721252eab0a6383731089824705/events"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 1 record

    @4: TEST FOR PUT EVENT /-----------------------------------------------------------------------
    Scenario: Update a event
        Given that I have a valid access_token
        And PUT this data:
            """
            {
                "evn_description": "change description",
                "evn_status": "ACTIVE",
                "evn_action": "SEND_MESSAGE",
                "evn_related_to": "MULTIPLE",
                "evn_tas_uid_from": "46337349752eab0df6e7b61093784394",
                "evn_tas_uid_to": "86505074952eab0e1007790039372925",
                "evn_tas_estimated_duration": 1,
                "evn_time_unit": "DAYS",
                "evn_when": 1,
                "evn_when_occurs": "AFTER_TIME",
                "tri_uid": "95002116552eab0b786a743075022999"
            }
            """
        And that I want to update a resource with the key "evn_uid" stored in session array
        And I request "project/74621721252eab0a6383731089824705/event"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"


    @5: TEST FOR GET EVENT /-----------------------------------------------------------------------
    Scenario: Get a event (with change in "evn_description")
        Given that I have a valid access_token
        And that I want to get a resource with the key "evn_uid" stored in session array
        And I request "project/74621721252eab0a6383731089824705/event"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "evn_description" is set to "change description"


    @6: TEST FOR DELETE EVENT /-----------------------------------------------------------------------
    Scenario: Delete a event
        Given that I have a valid access_token
        And that I want to delete a resource with the key "evn_uid" stored in session array
        And I request "project/74621721252eab0a6383731089824705/event"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

    @7: TEST FOR GET EVENTS /----------------------------------------------------------------------
    Scenario: List all the events (result 0 events)
        Given that I have a valid access_token
        And I request "project/74621721252eab0a6383731089824705/events"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record