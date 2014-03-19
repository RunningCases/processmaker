@ProcessMakerMichelangelo @RestAPI
Feature: Project Properties - Step Resources Main Tests
  Requirements:
    a workspace with the process 16062437052cd6141881e06088349078 already loaded
    the process name is "Sample Project #3 (Project Properties - Step Resources)"
    there are two steps in the process

  Background:
    Given that I have a valid access_token


    Scenario Outline: List assigned Steps to "Task1" & "Task2" (empty)  
        Given I request "project/<project>/activity/<activity>/steps"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

    Examples:

    | test_description  | project                          | activity                         | records |
    | 0 steps in Task 1 | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 0       |
    | 1 steps in Task 2 | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 1       |


    Scenario Outline: Assign a 5 Steps to an Activity
       Given POST this data:
        """
        {
            "step_type_obj": "<step_type_obj>",
            "step_uid_obj": "<step_uid_obj>",
            "step_condition": "<step_condition>",
            "step_position": "<step_position>",
            "step_mode": "<step_mode>"
        }
        """
        And I request "project/<project>/activity/<activity>/step"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "step_uid" in session array as variable "step_uid_<step_number>"

        Examples:

        | test_description                                            | project                          | activity                         | step_type_obj   | step_uid_obj                     | step_condition | step_position | step_mode | step_number |
        | Dynaform assigned to Task 1 in mode edit                    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | DYNAFORM        | 50332332752cd9b9a7cc989003652905 |                | 1             | EDIT      | 1           |
        | Input Document assigned to Task 1 in mode edit              | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | INPUT_DOCUMENT  | 83199959452cd62589576c1018679557 |                | 2             | EDIT      | 2           |
        | Output Document assigned to Task 1 in mode edit             | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | OUTPUT_DOCUMENT | 32743823452cd63105006e1076595203 |                | 3             | EDIT      | 3           |
        | Dynaform assigned to Task 2 in mode view                    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | DYNAFORM        | 50332332752cd9b9a7cc989003652905 |                | 2             | VIEW      | 4           |
        | External step to Task 2                                     | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | EXTERNAL        | 6869969705306aaae049a62048840877 |                | 3             | VIEW      | 5           |


    Scenario: Dynaform assigned to the task when it was already assigned 
       Given POST this data:
        """
        {
            "step_type_obj": "DYNAFORM",
            "step_uid_obj": "50332332752cd9b9a7cc989003652905",
            "step_condition": "",
            "step_position": "1",
            "step_mode": "EDIT"
        }
        """
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/step"
        Then the response status code should be 400
        And the response status message should have the following text "exists"

    
    Scenario: Try delete a Input Document when it is assigned to a step
        And that I want to delete a resource with the key "32743823452cd63105006e1076595203" stored in session array
        And I request "project/14414793652a5d718b65590036026581/input-document"
        And the content type is "application/json"
        Then the response status code should be 400
        

    Scenario: Try delete a Output document when it is assigned to a step
        Given that I want to delete a resource with the key "83199959452cd62589576c1018679557" stored in session array
        And I request "project/<project>/output-document"
        And the content type is "application/json"
        Then the response status code should be 400
          


    Scenario Outline: Update the five steps and then check if the values had changed
      Given PUT this data:
        """
        {
            "step_type_obj": "<step_type_obj>",
            "step_uid_obj": "<step_uid_obj>",
            "step_condition": "<step_condition>",
            "step_position": "<step_position>",
            "step_mode": "<step_mode>"
        }
        """
        And that I want to update a resource with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And I request "project/<project>/activity/<activity>/step"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"


        Examples:

        | test_description                                                         | project                          | activity                         | step_condition | step_position | step_mode | step_type_obj   | step_uid_obj                     | step_number |
        | Update Dynaform Task 1 (step_condition, step_position, step_mode)        | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | @@YEAR==2013   | 4             | VIEW      | DYNAFORM        | 50332332752cd9b9a7cc989003652905 | 1           |
        | Update Input Document Task 1 (step_condition, step_position, step_mode)  | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | @@YEAR==2014   | 5             | VIEW      | INPUT_DOCUMENT  | 83199959452cd62589576c1018679557 | 2           |
        | Update Output Document Task 1 (step_condition, step_position, step_mode) | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 |                | 6             | VIEW      | OUTPUT_DOCUMENT | 32743823452cd63105006e1076595203 | 3           |
        | Update Dynaform Task 2 (step_condition, step_position, step_mode)        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | @@YEAR==2014   | 2             | EDIT      | DYNAFORM        | 50332332752cd9b9a7cc989003652905 | 4           |
        | Update Dynaform Task 2 (step_condition, step_position, step_mode)        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | @@YEAR==2014   | 3             | EDIT      | EXTERNAL        | 6869969705306aaae049a62048840877 | 5           |


    Scenario Outline: List assigned Steps to "Task1" & "Task 2"
        Given I request "project/<project>/activity/<activity>/steps"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records
        And the "step_uid_obj" property in row 0 equals "<step_uid_obj>"
        And the "step_type_obj" property in row 0 equals "<step_type_obj>"



        Examples:

        | test_description                                                        | project                          | activity                         | records | step_type_obj   | step_uid_obj                     |
        | 3 steps in task 1 - verify that the first record is the first position  | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3       | DYNAFORM        | 50332332752cd9b9a7cc989003652905 |
        | 2 steps in task 2 - verify that the first record is the first position  | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 3       | DYNAFORM        | 63293140052cd61b29e21a9056770986 |


    #STEP TRIGGERS

    Scenario Outline: List assigned Triggers to "Task2"
        Given I request "project/<project>/activity/<activity>/step/step_uid/triggers"  with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

        Examples:

        | test_description                             | project                          | activity                         | step_number |
        | List a triggers in dynaform of task 1        | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           |
        | List a triggers in Input Document of Task 1  | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           |
        | List a triggers in Output Document of Task 1 | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           |
        | List a triggers in Dynaform of task 2        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           |
        | List a triggers in External Step of task 2   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 5           |


    Scenario Outline: List available Triggers for each assigned step
      Given I request "project/<project>/activity/<activity>/step/step_uid/available-triggers/before"  with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 3 records

        Examples:

        | test_description                                       | project                          | activity                         | step_number |
        | List available a triggers in dynaform of task 1        | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           |
        | List available a triggers in Input Document of Task 1  | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           |
        | List available a triggers in Output Document of Task 1 | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           |
        | List available a triggers in Dynaform of task 2        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           |
        | List available a triggers in External Step of task 2   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 5           |



    Scenario Outline: Assign a 3 triggers to a Step
       Given POST this data:
        """
        {
            "tri_uid": "<tri_uid>",
            "st_type": "<st_type>",
            "st_condition": "<st_condition>",
            "st_position": "<st_position>"
        }
        """
        And I request "project/<project>/activity/<activity>/step/step_uid/trigger"  with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        

        Examples:

        | test_description                            | project                          | activity                         | step_number | tri_uid_number | tri_uid                          | st_type  | st_condition      | st_position |
        | Trigger assigned to Task 1 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 1              | 81919273152cd636c665080083928728 | BEFORE   |                   | 1           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 2              | 56359776552cd6378b38e47080912028 | BEFORE   |                   | 2           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 3              | 57401970252cd6393531551040242546 | BEFORE   |                   | 3           |
        | Trigger assigned to Task 1 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 4              | 81919273152cd636c665080083928728 | AFTER    |                   | 1           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 5              | 56359776552cd6378b38e47080912028 | AFTER    |                   | 2           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 6              | 57401970252cd6393531551040242546 | AFTER    |                   | 3           |
        | Trigger assigned to Task 1 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 7              | 81919273152cd636c665080083928728 | BEFORE   |                   | 1           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 8              | 56359776552cd6378b38e47080912028 | BEFORE   |                   | 2           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 9              | 57401970252cd6393531551040242546 | BEFORE   |                   | 3           |
        | Trigger assigned to Task 1 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 10             | 81919273152cd636c665080083928728 | AFTER    |                   | 1           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 11             | 56359776552cd6378b38e47080912028 | AFTER    |                   | 2           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 12             | 57401970252cd6393531551040242546 | AFTER    |                   | 3           |
        | Trigger assigned to Task 1 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 13             | 81919273152cd636c665080083928728 | BEFORE   |                   | 1           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 14             | 56359776552cd6378b38e47080912028 | BEFORE   |                   | 2           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 15             | 57401970252cd6393531551040242546 | BEFORE   |                   | 3           |
        | Trigger assigned to Task 1 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 16             | 81919273152cd636c665080083928728 | AFTER    |                   | 1           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 17             | 56359776552cd6378b38e47080912028 | AFTER    |                   | 2           |
        | Trigger assigned to Task 1 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 18             | 57401970252cd6393531551040242546 | AFTER    |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 19             | 81919273152cd636c665080083928728 | BEFORE   |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 20             | 56359776552cd6378b38e47080912028 | BEFORE   |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 21             | 57401970252cd6393531551040242546 | BEFORE   |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 22             | 81919273152cd636c665080083928728 | AFTER    |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 23             | 56359776552cd6378b38e47080912028 | AFTER    |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 24             | 57401970252cd6393531551040242546 | AFTER    |                   | 3           |


Scenario: Trigger assigned to the step when it was already assigned
       Given POST this data:
        """
        {
            "tri_uid": "81919273152cd636c665080083928728",
            "st_type": "BEFORE",
            "st_condition": "",
            "st_position": "1"
        }
        """
        And I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/step/50332332752cd9b9a7cc989003652905/trigger" with the key "step_uid" stored in session array
        Then the response status code should be 400
        And the response status message should have the following text "exists"


    Scenario Outline: Try delete a trigger when it is assigned to a step
      Given that I want to delete a "trigger"
        And I request "project/<project>/trigger/<tri_uid>"
        Then the response status code should be 400
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | project                          | tri_uid                          |
        | 16062437052cd6141881e06088349078 | 81919273152cd636c665080083928728 |
        | 16062437052cd6141881e06088349078 | 57401970252cd6393531551040242546 |
    

    Scenario Outline: List available Triggers for each assigned step
      Given I request "project/<project>/activity/<activity>/step/step_uid/available-triggers/before"  with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 records

        Examples:

        | test_description                                       | project                          | activity                         | step_number |
        | List available a triggers in dynaform of task 1        | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           |
        | List available a triggers in Input Document of Task 1  | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           |
        | List available a triggers in Output Document of Task 1 | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           |
        | List available a triggers in Dynaform of task 2        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           |
        | List available a triggers in External Step of task 2   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 5           |



    Scenario Outline: Update a Trigger assignation of a Step if the values had changed
      Given PUT this data:
        """
        {
            "tri_uid": "<tri_uid>",
            "st_type": "<st_type>",
            "st_condition": "<st_condition>",
            "st_position": "<st_position>"
        }
        """
        And I request "project/<project>/activity/<activity>/step/step_uid/trigger/<tri_uid>"  with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"


        Examples:

        | test_description                             | project                          | activity                         | step                             |tri_uid_number | step_number | tri_uid                          | st_type  | st_condition      | st_position |
        | Update st_type, st_condition                 | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 63293140052cd61b29e21a9056770986 | 1             | 1           | 81919273152cd636c665080083928728 | BEFORE   | @@var1 == 1       | 1           |
        | Update st_type, st_condition and st_position | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 63293140052cd61b29e21a9056770986 | 2             | 1           | 56359776552cd6378b38e47080912028 | BEFORE   | @@var1 == 2       | 2           |
        | Update st_type, st_condition and st_position | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 63293140052cd61b29e21a9056770986 | 3             | 1           | 57401970252cd6393531551040242546 | BEFORE   | @@var1 == 1       | 3           |



   Scenario Outline: Get a single Triggers and check some properties
      Given  I request "project/<project>/activity/<activity>/step/step_uid/trigger/<tri_uid>/<st_type>"  with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And that "tri_uid" is set to "<tri_uid>"
        And that "st_type" is set to "<st_type>"
        And that "st_condition" is set to "<st_condition>"
        And that "st_position" is set to "<st_position>"

        Examples:

        | test_description                            | project                          | activity                         | step_number | tri_uid_number | tri_uid                          | st_type  | st_condition      | st_position |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 1              | 81919273152cd636c665080083928728 | before   |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 2              | 56359776552cd6378b38e47080912028 | before   |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 3              | 57401970252cd6393531551040242546 | before   |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 4              | 81919273152cd636c665080083928728 | after    |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 5              | 56359776552cd6378b38e47080912028 | after    |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 6              | 57401970252cd6393531551040242546 | after    |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 7              | 81919273152cd636c665080083928728 | before   |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 8              | 56359776552cd6378b38e47080912028 | before   |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 9              | 57401970252cd6393531551040242546 | before   |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 10             | 81919273152cd636c665080083928728 | after    |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 11             | 56359776552cd6378b38e47080912028 | after    |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 12             | 57401970252cd6393531551040242546 | after    |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 13             | 81919273152cd636c665080083928728 | before   |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 14             | 56359776552cd6378b38e47080912028 | before   |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 15             | 57401970252cd6393531551040242546 | before   |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 16             | 81919273152cd636c665080083928728 | after    |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 17             | 56359776552cd6378b38e47080912028 | after    |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 18             | 57401970252cd6393531551040242546 | after    |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 19             | 81919273152cd636c665080083928728 | before   |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 20             | 56359776552cd6378b38e47080912028 | before   |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 21             | 57401970252cd6393531551040242546 | before   |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 22             | 81919273152cd636c665080083928728 | after    |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 23             | 56359776552cd6378b38e47080912028 | after    |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 24             | 57401970252cd6393531551040242546 | after    |                   | 3           |

     

    Scenario Outline: Delete all Triggers created in task 1 for step "Output document demo" and  task 2 for step "Dynaform Demo 1"
      Given that I want to delete a resource with the key "tri_uid" stored in session array as variable "tri_uid_<tri_uid_number>"
        And I request "project/<project>/activity/<activity>/step/step_uid/trigger/<tri_uid>/<st_type>"  with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

        Examples:

        | test_description                            | project                          | activity                         | step_number | tri_uid_number | tri_uid                          | st_type  | st_condition      | st_position |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 13             | 81919273152cd636c665080083928728 | before   |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 14             | 56359776552cd6378b38e47080912028 | before   |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 15             | 57401970252cd6393531551040242546 | before   |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 16             | 81919273152cd636c665080083928728 | after    |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 17             | 56359776552cd6378b38e47080912028 | after    |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 18             | 57401970252cd6393531551040242546 | after    |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 19             | 81919273152cd636c665080083928728 | before   |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 20             | 56359776552cd6378b38e47080912028 | before   |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 21             | 57401970252cd6393531551040242546 | before   |                   | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 22             | 81919273152cd636c665080083928728 | after    |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 23             | 56359776552cd6378b38e47080912028 | after    |                   | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 24             | 57401970252cd6393531551040242546 | after    |                   | 3           |


    
    Scenario Outline: List assigned Triggers to each step. The las two shoul report 0 records
        Given I request "project/<project>/activity/<activity>/step/step_uid/triggers"  with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

         Examples:

        | test_description                            | project                          | activity                         | step_number | records |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 6       |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           | 6       |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           | 0       |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           | 0       |
        | Trigger assigned to External in type before | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 5           | 0       |


    Scenario Outline: Unassign all "DynaForm Demo1" from "Task1" and "Task 2"
        Given that I have a valid access_token
        And that I want to delete a resource with the key "step1" stored in session array
        Given that I want to delete a resource with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And I request "project/<project>/activity/<activity>/step"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

        Examples:

        | test_description                            | project                          | activity                         | step_number |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 2           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 4           |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 5           |


    
    Scenario Outline: List assigned Steps to "Task1" & "Task2" (empty)  (verify if everithing was deleted)
        Given I request "project/<project>/activity/<activity>/steps"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

    Examples:

    | test_description  | project                          | activity                         | records |
    | 0 steps in Task 1 | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 0       |
    | 1 steps in Task 2 | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 1       |