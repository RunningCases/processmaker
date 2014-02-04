@ProcessMakerMichelangelo @RestAPI
Feature: Events Resources Main Tests
  Requirements:
    a workspace with the process 251815090529619a99a2bf4013294414 ("Test(Triggers, Activity") already loaded
    there are zero Events in the process

    Background:
    Given that I have a valid access_token


  Scenario: Get List all the events in the process when there are exactly zero events
        Given I request "project/251815090529619a99a2bf4013294414/events"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record

  Scenario Outline: Create 18 new Event of the project
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
        And I request "project/251815090529619a99a2bf4013294414/event"
        Then the response status code should be 201
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And store "evn_uid" in session array as variable "evn_uid_<evn_uid_number>"
        

        Examples:

        | test_description                                                                       | evn_uid_number | evn_action                  | evn_description               | evn_status | evn_related_to | tas_uid                          | evn_tas_uid_from                 | evn_tas_uid_to                   | evn_tas_estimated_duration | evn_time_unit | evn_when | evn_when_occurs | tri_uid                          | 
        | Create Event Message with single task, duration=days and Execution AFTER_TIME          | 1              | SEND_MESSAGE                | Event Message, Single 1       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Message with single task, duration=hours and Execution AFTER_TIME         | 2              | SEND_MESSAGE                | Event Message, Single 2       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Message with single task, duration=days and Execution TASK_STARTED        | 3              | SEND_MESSAGE                | Event Message, Single 3       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Message with multiple task, duration=days and Execution AFTER_TIME        | 4              | SEND_MESSAGE                | Event Message, Multiple 4     | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Message with multiple task, duration=hours and Execution AFTER_TIME       | 5              | SEND_MESSAGE                | Event Message, Multiple 5     | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Message with multiple task, duration=days and Execution TASK_STARTED      | 6              | SEND_MESSAGE                | Event Message, Multiple 6     | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with single task, duration=days and Execution AFTER_TIME      | 7              | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 1   | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with single task, duration=hours and Execution AFTER_TIME     | 8              | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 2   | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with single task, duration=days and Execution TASK_STARTED    | 9              | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 3   | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with multiple task, duration=days and Execution AFTER_TIME    | 10             | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 4 | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with multiple task, duration=hours and Execution AFTER_TIME   | 11             | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 5 | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with multiple task, duration=days and Execution TASK_STARTED  | 12             | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 6 | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with single task, duration=days and Execution AFTER_TIME            | 13             | EXECUTE_TRIGGER             | Event Timer, Single 1         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with single task, duration=hours and Execution AFTER_TIME           | 14             | EXECUTE_TRIGGER             | Event Timer, Single 2         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with single task, duration=days and Execution TASK_STARTED          | 15             | EXECUTE_TRIGGER             | Event Timer, Single 3         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with multiple task, duration=days and Execution AFTER_TIME          | 16             | EXECUTE_TRIGGER             | Event Timer, Multiple 4       | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with multiple task, duration=hours and Execution AFTER_TIME         | 17             | EXECUTE_TRIGGER             | Event Timer, Multiple 5       | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with multiple task, duration=days and Execution TASK_STARTED        | 18             | EXECUTE_TRIGGER             | Event Timer, Multiple 6       | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
                      

    Scenario: Get List all the events in the process when there are exactly zero events
        Given I request "project/251815090529619a99a2bf4013294414/events"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 18 record

   
    Scenario: Get List all the events in the events Messages when there are exactly six events
        Given I request "project/251815090529619a99a2bf4013294414/events?filter=message"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 6 records

    Scenario: Get List all the events in the events Messages when there are exactly six events
        Given I request "project/251815090529619a99a2bf4013294414/events?filter=conditional"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 6 records

    Scenario: Get List all the events in the events Messages when there are exactly six events
        Given I request "project/251815090529619a99a2bf4013294414/events?filter=multiple"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 6 records


    Scenario Outline: Update the Events and the check if the values had changed
        Given PUT this data:
            """
            {
                "evn_action": "<evn_action>",
                "evn_description": "<evn_description>",
                "evn_status": "<evn_status>",
                "evn_related_to": "<evn_related_to>",
                "evn_tas_uid_from": "<evn_tas_uid_from>",
                "evn_tas_uid_to": "<evn_tas_uid_to>",
                "evn_tas_estimated_duration": <evn_tas_estimated_duration>,
                "evn_time_unit": "<evn_time_unit>",
                "evn_when": <evn_when>,
                "evn_when_occurs": "<evn_when_occurs>",
                "tri_uid": "<tri_uid>"
            }
            """
        And that I want to update a resource with the key "evn_uid" stored in session array as variable "evn_uid_<evn_uid_number>"
        And I request "project/251815090529619a99a2bf4013294414/event"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | test_description                                                                                                              | evn_uid_number | evn_action                  | evn_description                    | evn_status | evn_related_to | evn_tas_uid_from                 | evn_tas_uid_to                   | evn_tas_estimated_duration | evn_time_unit | evn_when | evn_when_occurs | tri_uid                          | 
        | Update evn_description, evn_status, evn_related_to, evn_tas_uid_from, evn_tas_estimated_duration, evn_when, evn_when_occurs 1 | 1              | SEND_MESSAGE                | Update Event Message, Single 1     | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Update evn_description, evn_status, evn_related_to, evn_tas_uid_from, evn_tas_estimated_duration, evn_when, evn_when_occurs 2 | 7              | EXECUTE_CONDITIONAL_TRIGGER | Update Event Conditional, Single 1 | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 3                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Update evn_description, evn_status, evn_related_to, evn_tas_uid_from, evn_tas_estimated_duration, evn_when, evn_when_occurs 3 | 13             | EXECUTE_TRIGGER             | Update Event Timer, Single 1       | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |

    
    Scenario Outline: Get a Single Events and check some properties
        Given that I want to get a resource with the key "evn_uid" stored in session array as variable "evn_uid_<evn_uid_number>"
        And I request "project/251815090529619a99a2bf4013294414/event"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And that "evn_description" is set to "<evn_description>"
        And that "evn_status" is set to "<evn_status>"
        And that "evn_related_to" is set to "<evn_related_to>"
        And that "evn_tas_uid_from" is set to "<evn_tas_uid_from>"
        And that "evn_tas_uid_to" is set to "<evn_tas_uid_to>"
        And that "evn_tas_estimated_duration" is set to "<evn_tas_estimated_duration>"
        And that "evn_time_unit" is set to "<evn_time_unit>"
        And that "evn_when" is set to "<evn_when>"
        And that "evn_when_occurs" is set to "<evn_when_occurs>"

        Examples:
        
        | evn_uid_number | evn_description                    | evn_status | evn_related_to | evn_tas_uid_from                 | evn_tas_uid_to                   | evn_tas_estimated_duration | evn_time_unit | evn_when | evn_when_occurs | tri_uid                          | 
        | 1              | Update Event Message, Single 1     | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | 7              | Update Event Conditional, Single 1 | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 3                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | 13             | Update Event Timer, Single 1       | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |

        
    Scenario Outline: Delete all Events created previously in this script
        Given that I want to delete a resource with the key "evn_uid" stored in session array as variable "evn_uid_<evn_uid_number>"
        And I request "project/251815090529619a99a2bf4013294414/event"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | evn_uid_number | 
        | 1              |
        | 2              |
        | 3              |
        | 4              |
        | 5              |
        | 6              |
        | 7              |
        | 8              |
        | 9              |
        | 10             |
        | 11             |
        | 12             |
        | 13             |
        | 14             |
        | 15             |
        | 16             |
        | 17             |
        | 18             |


    Scenario: Get List all the events in the process when there are exactly zero events
        Given I request "project/251815090529619a99a2bf4013294414/events"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record