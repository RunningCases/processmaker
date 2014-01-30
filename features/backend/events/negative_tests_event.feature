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
                "evn_tas_estimated_duration": <evn_tas_estimated_duration>,
                "evn_time_unit": "<evn_time_unit>",
                "evn_when": <evn_when>,
                "evn_when_occurs": "<evn_when_occurs>",
                "tri_uid": "<tri_uid>"
            }
            """
        And I request "project/251815090529619a99a2bf4013294414/event"
        Then the response status code should be 201
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And store "evn_uid" in session array as variable "evn_uid_<evn_uid_number>"


        Examples:

        | test_description                              | evn_uid_number | evn_action                  | evn_description               | evn_status | evn_related_to | tas_uid                          | evn_tas_uid_from                 | evn_tas_estimated_duration | evn_time_unit | evn_when | evn_when_occurs | tri_uid                          | 
        |                                               | 1              | SEND_MESSAGE                | Event Message, Single 1       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 2              | SEND_MESSAGE                | Event Message, Single 2       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 3              | SEND_MESSAGE                | Event Message, Single 3       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        |                                               | 4              | SEND_MESSAGE                | Event Message, Multiple 4     | ACTIVE     | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 5              | SEND_MESSAGE                | Event Message, Multiple 5     | ACTIVE     | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 6              | SEND_MESSAGE                | Event Message, Multiple 6     | ACTIVE     | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        |                                               | 7              | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 1   | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 8              | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 2   | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 9              | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 3   | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        |                                               | 10             | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 4 | ACTIVE     | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 11             | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 5 | ACTIVE     | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 12             | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 6 | ACTIVE     | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        |                                               | 13             | EXECUTE_TRIGGER             | Event Timer, Single 1         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 14             | EXECUTE_TRIGGER             | Event Timer, Single 2         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 15             | EXECUTE_TRIGGER             | Event Timer, Single 3         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        |                                               | 16             | EXECUTE_TRIGGER             | Event Timer, Multiple 4       | ACTIVE     | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 17             | EXECUTE_TRIGGER             | Event Timer, Multiple 5       | ACTIVE     | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        |                                               | 18             | EXECUTE_TRIGGER             | Event Timer, Multiple 6       | ACTIVE     | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
    