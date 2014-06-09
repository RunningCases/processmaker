@ProcessMakerMichelangelo @RestAPI
Feature: Events Resources Negative Tests


  Background:
    Given that I have a valid access_token

  Scenario Outline: Create a new event for a project with bad parameters (negative tests)
       Given POST this data:
            """
            {
                "evn_action": "<evn_action>",
                "evn_description": "<evn_description>",
                "evn_status": "<evn_status>",
                "evn_related_to": "<evn_related_to>",
                "tas_uid": "<tas_uid>",
                "evn_tas_uid_from": "<evn_tas_uid_from>",
                "evn_tas_uid_to": "<evn_tas_uid_to>",
                "evn_tas_estimated_duration": <evn_tas_estimated_duration>,
                "evn_time_unit": "<evn_time_unit>",
                "evn_when": <evn_when>,
                "evn_when_occurs": "<evn_when_occurs>",
                "tri_uid": "<tri_uid>"
            }
            """
        And I request "project/<project>/event"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:

        | test_description                                   | project                          | evn_action                  | evn_description               | evn_status | evn_related_to | tas_uid                          | evn_tas_uid_from                 | evn_tas_uid_to                   | evn_tas_estimated_duration | evn_time_unit | evn_when | evn_when_occurs | tri_uid                          | error_code | error_message              |
        | Field required evn_description                     | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                |                               | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 | 400        | evn_description            | 
        | Field required evn_status                          | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Single 2       |            | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 | 400        | evn_status                 |
        | Field required evn_action                          | 251815090529619a99a2bf4013294414 |                             | Event Message, Single 3       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 | 400        | evn_action                 |
        | Field required evn_related_to                      | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Multiple 4     | ACTIVE     |                |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 | 400        | evn_related_to             |
        | Field required evn_tas_uid_from                    | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Multiple 5     | ACTIVE     | MULTIPLE       |                                  |                                  | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 | 400        | evn_tas_uid_from           |
        | Field required evn_tas_uid_to                      | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Multiple 6     | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 | 400        | evn_tas_uid_to             |
        | Field required tas_uid                             | 251815090529619a99a2bf4013294414 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 1   | ACTIVE     | SINGLE         |                                  |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 | 400        | tas_uid                    |
        | Field required evn_time_unit                       | 251815090529619a99a2bf4013294414 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 3   | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          |               | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 | 400        | evn_time_unit              |
        | Field required evn_when_occurs                     | 251815090529619a99a2bf4013294414 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 5 | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        |                 | 75916963152cc6ab085a704081670580 | 400        | evn_when_occurs            |
        | Field required tri_uid                             | 251815090529619a99a2bf4013294414 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 6 | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    |                                  | 400        | tri_uid                    |
        | Field required prj_uid                             |                                  | EXECUTE_TRIGGER             | Event Timer, Single 1         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 | 400        | prj_uid                    |
        | Invalid evn_status                                 | 251815090529619a99a2bf4013294414 | EXECUTE_TRIGGER             | Event Timer, Single 2         | SAMPLE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 | 400        | evn_status                 |
        | Invalid evn_action                                 | 251815090529619a99a2bf4013294414 | INPUT DOCUMENT              | Event Timer, Single 3         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 | 400        | evn_action                 |
        | Invalid evn_related_to                             | 251815090529619a99a2bf4013294414 | EXECUTE_TRIGGER             | Event Timer, Multiple 4       | ACTIVE     | SAMPLE         |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 | 400        | evn_related_to             |
        | Invalid evn_tas_uid_from                           | 251815090529619a99a2bf4013294414 | EXECUTE_TRIGGER             | Event Timer, Multiple 5       | ACTIVE     | MULTIPLE       |                                  | 0000000000000c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 | 400        | task                       |
        | Invalid evn_tas_uid_to                             | 251815090529619a99a2bf4013294414 | EXECUTE_TRIGGER             | Event Timer, Multiple 6       | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 00000000000000c066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 | 400        | task                       |
        | Invalid tas_uid                                    | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Single 1       | ACTIVE     | SINGLE         | 0000000000000c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 | 400        | task                       |
        | Invalid evn_time_unit                              | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Single 3       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | YEAR          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 | 400        | evn_time_unit              |
        | Invalid evn_when_occurs                            | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Multiple 5     | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TASK      | 75916963152cc6ab085a704081670580 | 400        | evn_when_occurs            |
        | Invalid tri_uid                                    | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Multiple 6     | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 00000000000006ab085a704081670580 | 400        | tri_uid                    |
        


    Scenario: Create a new event for a project with bad parameters (negative tests) for variable evn_tas_estimated_duration
        Given POST this data:
            """
            {
                "evn_description": "DE BEHAT",
                "evn_status": "ACTIVE",
                "evn_action": "SEND_MESSAGE",
                "evn_related_to": "SINGLE",
                "tas_uid": "97192372152a5c78f04a794095806311",
                "evn_tas_estimated_duration": "",
                "evn_time_unit": "DAYS",
                "evn_when": 1,
                "evn_when_occurs": "AFTER_TIME",
                "tri_uid": "75916963152cc6ab085a704081670580"
            }
            """
        And I request "project/251815090529619a99a2bf4013294414/event"
        Then the response status code should be 400
        And the response status message should have the following text "evn_tas_estimated_duration"
   

   Scenario: Create a new event for a project with bad parameters (negative tests) for variable evn_when
        Given POST this data:
            """
            {
                "evn_description": "DE BEHAT",
                "evn_status": "ACTIVE",
                "evn_action": "SEND_MESSAGE",
                "evn_related_to": "SINGLE",
                "tas_uid": "97192372152a5c78f04a794095806311",
                "evn_tas_estimated_duration": "1",
                "evn_time_unit": "DAYS",
                "evn_when": "",
                "evn_when_occurs": "AFTER_TIME",
                "tri_uid": "75916963152cc6ab085a704081670580"
            }
            """
        And I request "project/251815090529619a99a2bf4013294414/event"
        Then the response status code should be 400
        And the response status message should have the following text "evn_when"