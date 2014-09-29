@ProcessMakerMichelangelo @RestAPI
Feature: Events Resources Main Tests
  Requirements:
    a workspace with the process 251815090529619a99a2bf4013294414 ("Test(Triggers, Activity") already loaded
    there are zero Events in the process
    and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded

    Background:
    Given that I have a valid access_token


    Scenario Outline: Get List all the events in the process when there are exactly zero events
        Given I request "project/<project>/events"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <record> record

        Examples:

        | test_description                                      | project                          | record |
        | Get list event of the process Test(Triggers, Activity | 251815090529619a99a2bf4013294414 | 0      |
        | Get list event of the process Process Complete BPMN   | 1455892245368ebeb11c1a5001393784 | 0      |


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
        And I request "project/<project>/event"
        Then the response status code should be 201
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And store "evn_uid" in session array as variable "evn_uid_<evn_uid_number>"
        

        Examples:

        | test_description                                                                         | evn_uid_number | project                          | evn_action                  | evn_description               | evn_status | evn_related_to | tas_uid                          | evn_tas_uid_from                 | evn_tas_uid_to                   | evn_tas_estimated_duration | evn_time_unit | evn_when | evn_when_occurs | tri_uid                          | 
        | Create Event Message with single task, duration=days and Execution AFTER_TIME       .pm  | 1              | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Single 1       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Message with single task, duration=hours and Execution AFTER_TIME      .pm  | 2              | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Single 2       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Message with single task, duration=days and Execution TASK_STARTED     .pm  | 3              | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Single 3       | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Message with multiple task, duration=days and Execution AFTER_TIME     .pm  | 4              | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Multiple 4     | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Message with multiple task, duration=hours and Execution AFTER_TIME    .pm  | 5              | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Multiple 5     | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Message with multiple task, duration=days and Execution TASK_STARTED   .pm  | 6              | 251815090529619a99a2bf4013294414 | SEND_MESSAGE                | Event Message, Multiple 6     | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with single task, duration=days and Execution AFTER_TIME   .pm  | 7              | 251815090529619a99a2bf4013294414 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 1   | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with single task, duration=hours and Execution AFTER_TIME  .pm  | 8              | 251815090529619a99a2bf4013294414 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 2   | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with single task, duration=days and Execution TASK_STARTED .pm  | 9              | 251815090529619a99a2bf4013294414 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 3   | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with multiple task, duration=days and Execution AFTER_TIME .pm  | 10             | 251815090529619a99a2bf4013294414 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 4 | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional with multiple task, duration=hours and Execution AFTER_TIME.pm  | 11             | 251815090529619a99a2bf4013294414 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 5 | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Conditional multiple task, duration=days and Execution TASK_STARTED    .pm  | 12             | 251815090529619a99a2bf4013294414 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 6 | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with single task, duration=days and Execution AFTER_TIME         .pm  | 13             | 251815090529619a99a2bf4013294414 | EXECUTE_TRIGGER             | Event Timer, Single 1         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with single task, duration=hours and Execution AFTER_TIME        .pm  | 14             | 251815090529619a99a2bf4013294414 | EXECUTE_TRIGGER             | Event Timer, Single 2         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with single task, duration=days and Execution TASK_STARTED       .pm  | 15             | 251815090529619a99a2bf4013294414 | EXECUTE_TRIGGER             | Event Timer, Single 3         | ACTIVE     | SINGLE         | 97192372152a5c78f04a794095806311 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with multiple task, duration=days and Execution AFTER_TIME       .pm  | 16             | 251815090529619a99a2bf4013294414 | EXECUTE_TRIGGER             | Event Timer, Multiple 4       | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with multiple task, duration=hours and Execution AFTER_TIME      .pm  | 17             | 251815090529619a99a2bf4013294414 | EXECUTE_TRIGGER             | Event Timer, Multiple 5       | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | HOURS         | 1        | AFTER_TIME      | 75916963152cc6ab085a704081670580 |
        | Create Event Timer with multiple task, duration=days and Execution TASK_STARTED     .pm  | 18             | 251815090529619a99a2bf4013294414 | EXECUTE_TRIGGER             | Event Timer, Multiple 6       | ACTIVE     | MULTIPLE       |                                  | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 1                          | DAYS          | 1        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Create Event Message with single task, duration=days and Execution AFTER_TIME       .pmx | 19             | 1455892245368ebeb11c1a5001393784 | SEND_MESSAGE                | Event Message, Single 1       | ACTIVE     | SINGLE         | 6274755055368eed1116388064384542 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Message with single task, duration=hours and Execution AFTER_TIME      .pmx | 20             | 1455892245368ebeb11c1a5001393784 | SEND_MESSAGE                | Event Message, Single 2       | ACTIVE     | SINGLE         | 6274755055368eed1116388064384542 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Message with single task, duration=days and Execution TASK_STARTED     .pmx | 21             | 1455892245368ebeb11c1a5001393784 | SEND_MESSAGE                | Event Message, Single 3       | ACTIVE     | SINGLE         | 6274755055368eed1116388064384542 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |
        | Create Event Message with multiple task, duration=days and Execution AFTER_TIME     .pmx | 22             | 1455892245368ebeb11c1a5001393784 | SEND_MESSAGE                | Event Message, Multiple 4     | ACTIVE     | MULTIPLE       |                                  | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 1                          | DAYS          | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Message with multiple task, duration=hours and Execution AFTER_TIME    .pmx | 23             | 1455892245368ebeb11c1a5001393784 | SEND_MESSAGE                | Event Message, Multiple 5     | ACTIVE     | MULTIPLE       |                                  | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 2                          | HOURS         | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Message with multiple task, duration=days and Execution TASK_STARTED   .pmx | 24             | 1455892245368ebeb11c1a5001393784 | SEND_MESSAGE                | Event Message, Multiple 6     | ACTIVE     | MULTIPLE       |                                  | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 1                          | DAYS          | 1        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |
        | Create Event Conditional with single task, duration=days and Execution AFTER_TIME   .pmx | 25             | 1455892245368ebeb11c1a5001393784 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 1   | ACTIVE     | SINGLE         | 6274755055368eed1116388064384542 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Conditional with single task, duration=hours and Execution AFTER_TIME  .pmx | 26             | 1455892245368ebeb11c1a5001393784 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 2   | ACTIVE     | SINGLE         | 6274755055368eed1116388064384542 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Conditional with single task, duration=days and Execution TASK_STARTED .pmx | 27             | 1455892245368ebeb11c1a5001393784 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Single 3   | ACTIVE     | SINGLE         | 6274755055368eed1116388064384542 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |
        | Create Event Conditional with multiple task, duration=days and Execution AFTER_TIME .pmx | 28             | 1455892245368ebeb11c1a5001393784 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 4 | ACTIVE     | MULTIPLE       |                                  | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 1                          | DAYS          | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Conditional with multiple task, duration=hours and Execution AFTER_TIME.pmx | 29             | 1455892245368ebeb11c1a5001393784 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 5 | ACTIVE     | MULTIPLE       |                                  | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 2                          | HOURS         | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Conditional multiple task, duration=days and Execution TASK_STARTED    .pmx | 30             | 1455892245368ebeb11c1a5001393784 | EXECUTE_CONDITIONAL_TRIGGER | Event Conditional, Multiple 6 | ACTIVE     | MULTIPLE       |                                  | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 1                          | DAYS          | 1        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |
        | Create Event Timer with single task, duration=days and Execution AFTER_TIME         .pmx | 31             | 1455892245368ebeb11c1a5001393784 | EXECUTE_TRIGGER             | Event Timer, Single 1         | ACTIVE     | SINGLE         | 6274755055368eed1116388064384542 |                                  |                                  | 1                          | DAYS          | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Timer with single task, duration=hours and Execution AFTER_TIME        .pmx | 32             | 1455892245368ebeb11c1a5001393784 | EXECUTE_TRIGGER             | Event Timer, Single 2         | ACTIVE     | SINGLE         | 6274755055368eed1116388064384542 |                                  |                                  | 2                          | HOURS         | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Timer with single task, duration=days and Execution TASK_STARTED       .pmx | 33             | 1455892245368ebeb11c1a5001393784 | EXECUTE_TRIGGER             | Event Timer, Single 3         | ACTIVE     | SINGLE         | 6274755055368eed1116388064384542 |                                  |                                  | 1                          | DAYS          | 1        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |
        | Create Event Timer with multiple task, duration=days and Execution AFTER_TIME       .pmx | 34             | 1455892245368ebeb11c1a5001393784 | EXECUTE_TRIGGER             | Event Timer, Multiple 4       | ACTIVE     | MULTIPLE       |                                  | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 1                          | DAYS          | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Timer with multiple task, duration=hours and Execution AFTER_TIME      .pmx | 35             | 1455892245368ebeb11c1a5001393784 | EXECUTE_TRIGGER             | Event Timer, Multiple 5       | ACTIVE     | MULTIPLE       |                                  | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 2                          | HOURS         | 1        | AFTER_TIME      | 712197294536bea56a8b4d0014148679 |
        | Create Event Timer with multiple task, duration=days and Execution TASK_STARTED     .pmx | 36             | 1455892245368ebeb11c1a5001393784 | EXECUTE_TRIGGER             | Event Timer, Multiple 6       | ACTIVE     | MULTIPLE       |                                  | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 1                          | DAYS          | 1        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |
                                   

    Scenario Outline: Get List all the events in the process
        Given I request "project/<project>/events"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <records> records

        Examples:

        | test_description                                      | project                          | records |
        | Get list event of the process Test(Triggers, Activity | 251815090529619a99a2bf4013294414 | 18      |
        | Get list event of the process Process Complete BPMN   | 1455892245368ebeb11c1a5001393784 | 18      |


    Scenario Outline: Get List all the events in the events Messages when there are exactly six events
        Given I request "project/<project>/events?filter=message"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <records> records

        Examples:

        | test_description                                      | project                          | records |
        | Get list event of the process Test(Triggers, Activity | 251815090529619a99a2bf4013294414 | 6       |
        | Get list event of the process Process Complete BPMN   | 1455892245368ebeb11c1a5001393784 | 6       |


    Scenario Outline: Get List all the events in the events Messages when there are exactly six events
        Given I request "project/<project>/events?filter=conditional"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <records> records

        Examples:

        | test_description                                      | project                          | records |
        | Get list event of the process Test(Triggers, Activity | 251815090529619a99a2bf4013294414 | 6       |
        | Get list event of the process Process Complete BPMN   | 1455892245368ebeb11c1a5001393784 | 6       |


    Scenario Outline: Get List all the events in the events Messages when there are exactly six events
        Given I request "project/<project>/events?filter=multiple"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <records> records

        Examples:

        | test_description                                      | project                          | records |
        | Get list event of the process Test(Triggers, Activity | 251815090529619a99a2bf4013294414 | 6       |
        | Get list event of the process Process Complete BPMN   | 1455892245368ebeb11c1a5001393784 | 6       |

    
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
        And I request "project/<project>/event"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | test_description                                                                                                                   | project                          | evn_uid_number | evn_action                  | evn_description                    | evn_status | evn_related_to | evn_tas_uid_from                 | evn_tas_uid_to                   | evn_tas_estimated_duration | evn_time_unit | evn_when | evn_when_occurs | tri_uid                          | 
        | Update evn_description, evn_status, evn_related_to, evn_tas_uid_from, evn_tas_estimated_duration, evn_when, evn_when_occurs 1 .pm  | 251815090529619a99a2bf4013294414 | 1              | SEND_MESSAGE                | Update Event Message, Single 1     | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Update evn_description, evn_status, evn_related_to, evn_tas_uid_from, evn_tas_estimated_duration, evn_when, evn_when_occurs 2 .pm  | 251815090529619a99a2bf4013294414 | 7              | EXECUTE_CONDITIONAL_TRIGGER | Update Event Conditional, Single 1 | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 3                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Update evn_description, evn_status, evn_related_to, evn_tas_uid_from, evn_tas_estimated_duration, evn_when, evn_when_occurs 3 .pm  | 251815090529619a99a2bf4013294414 | 13             | EXECUTE_TRIGGER             | Update Event Timer, Single 1       | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Update evn_description, evn_status, evn_related_to, evn_tas_uid_from, evn_tas_estimated_duration, evn_when, evn_when_occurs 1 .pmx | 1455892245368ebeb11c1a5001393784 | 19             | SEND_MESSAGE                | Update Event Message, Single 1     | INACTIVE   | MULTIPLE       | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 2                          | DAYS          | 2        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |
        | Update evn_description, evn_status, evn_related_to, evn_tas_uid_from, evn_tas_estimated_duration, evn_when, evn_when_occurs 2 .pmx | 1455892245368ebeb11c1a5001393784 | 25             | EXECUTE_CONDITIONAL_TRIGGER | Update Event Conditional, Single 1 | INACTIVE   | MULTIPLE       | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 3                          | DAYS          | 2        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |
        | Update evn_description, evn_status, evn_related_to, evn_tas_uid_from, evn_tas_estimated_duration, evn_when, evn_when_occurs 3 .pmx | 1455892245368ebeb11c1a5001393784 | 31             | EXECUTE_TRIGGER             | Update Event Timer, Single 1       | INACTIVE   | MULTIPLE       | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 2                          | DAYS          | 2        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |

    
    Scenario Outline: Get a Single Events and check some properties
        Given that I want to get a resource with the key "evn_uid" stored in session array as variable "evn_uid_<evn_uid_number>"
        And I request "project/<project>/event"
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
        
        | test_description      | project                          | evn_uid_number | evn_description                    | evn_status | evn_related_to | evn_tas_uid_from                 | evn_tas_uid_to                   | evn_tas_estimated_duration | evn_time_unit | evn_when | evn_when_occurs | tri_uid                          | 
        | Get after update .pm  | 251815090529619a99a2bf4013294414 | 1              | Update Event Message, Single 1     | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Get after update .pm  | 251815090529619a99a2bf4013294414 | 7              | Update Event Conditional, Single 1 | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 3                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Get after update .pm  | 251815090529619a99a2bf4013294414 | 13             | Update Event Timer, Single 1       | INACTIVE   | MULTIPLE       | 97192372152a5c78f04a794095806311 | 63843886052a5cc066e4c04056414372 | 2                          | DAYS          | 2        | TASK_STARTED    | 75916963152cc6ab085a704081670580 |
        | Get after update .pmx | 1455892245368ebeb11c1a5001393784 | 19             | Update Event Message, Single 1     | INACTIVE   | MULTIPLE       | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 2                          | DAYS          | 2        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |
        | Get after update .pmx | 1455892245368ebeb11c1a5001393784 | 25             | Update Event Conditional, Single 1 | INACTIVE   | MULTIPLE       | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 3                          | DAYS          | 2        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |
        | Get after update .pmx | 1455892245368ebeb11c1a5001393784 | 31             | Update Event Timer, Single 1       | INACTIVE   | MULTIPLE       | 6274755055368eed1116388064384542 | 4790702485368efad167477011123879 | 2                          | DAYS          | 2        | TASK_STARTED    | 712197294536bea56a8b4d0014148679 |

        
    Scenario Outline: Delete all Events created previously in this script
        Given that I want to delete a resource with the key "evn_uid" stored in session array as variable "evn_uid_<evn_uid_number>"
        And I request "project/<project>/event"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | project                          | evn_uid_number | 
        | 251815090529619a99a2bf4013294414 | 1              |
        | 251815090529619a99a2bf4013294414 | 2              |
        | 251815090529619a99a2bf4013294414 | 3              |
        | 251815090529619a99a2bf4013294414 | 4              |
        | 251815090529619a99a2bf4013294414 | 5              |
        | 251815090529619a99a2bf4013294414 | 6              |
        | 251815090529619a99a2bf4013294414 | 7              |
        | 251815090529619a99a2bf4013294414 | 8              |
        | 251815090529619a99a2bf4013294414 | 9              |
        | 251815090529619a99a2bf4013294414 | 10             |
        | 251815090529619a99a2bf4013294414 | 11             |
        | 251815090529619a99a2bf4013294414 | 12             |
        | 251815090529619a99a2bf4013294414 | 13             |
        | 251815090529619a99a2bf4013294414 | 14             |
        | 251815090529619a99a2bf4013294414 | 15             |
        | 251815090529619a99a2bf4013294414 | 16             |
        | 251815090529619a99a2bf4013294414 | 17             |
        | 251815090529619a99a2bf4013294414 | 18             |
        | 1455892245368ebeb11c1a5001393784 | 19             |
        | 1455892245368ebeb11c1a5001393784 | 20             |
        | 1455892245368ebeb11c1a5001393784 | 21             |
        | 1455892245368ebeb11c1a5001393784 | 22             |
        | 1455892245368ebeb11c1a5001393784 | 23             |
        | 1455892245368ebeb11c1a5001393784 | 24             |
        | 1455892245368ebeb11c1a5001393784 | 25             |
        | 1455892245368ebeb11c1a5001393784 | 26             |
        | 1455892245368ebeb11c1a5001393784 | 27             |
        | 1455892245368ebeb11c1a5001393784 | 28             |
        | 1455892245368ebeb11c1a5001393784 | 29             |
        | 1455892245368ebeb11c1a5001393784 | 30             |
        | 1455892245368ebeb11c1a5001393784 | 31             |
        | 1455892245368ebeb11c1a5001393784 | 32             |
        | 1455892245368ebeb11c1a5001393784 | 33             |
        | 1455892245368ebeb11c1a5001393784 | 34             |
        | 1455892245368ebeb11c1a5001393784 | 35             |
        | 1455892245368ebeb11c1a5001393784 | 36             |


    Scenario Outline: Get List all the events in the process when there are exactly zero events
        Given I request "project/<project>/events"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <record> record

        Examples:

        | test_description                                      | project                          | record |
        | Get list event of the process Test(Triggers, Activity | 251815090529619a99a2bf4013294414 | 0      |
        | Get list event of the process Process Complete BPMN   | 1455892245368ebeb11c1a5001393784 | 0      |