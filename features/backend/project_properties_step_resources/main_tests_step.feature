@ProcessMakerMichelangelo @RestAPI
Feature: Project Properties - Step Resources Main Tests
  Requirements:
    a workspace with the process 16062437052cd6141881e06088349078 already loaded
    the process name is "Sample Project #3 (Project Properties - Step Resources)"
    there are two steps in the process 

  Background:
    Given that I have a valid access_token

    
    Scenario: List assigned Steps to "Task1"
        Given I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/steps"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array



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
        | Dynaform assigned to Task 2 in mode edit with condition     | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | DYNAFORM        | 63293140052cd61b29e21a9056770986 | @@YEAR==2013   | 1             | EDIT      | 4           |
        | Dynaform assigned to Task 2 in mode view                    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | DYNAFORM        | 50332332752cd9b9a7cc989003652905 |                | 2             | VIEW      | 5           |



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
        | Update Dynaform Task 1 (step_condition, step_position, step_mode)        | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | @@YEAR==2013   | 2             | VIEW      | DYNAFORM        | 50332332752cd9b9a7cc989003652905 | 1           |
        | Update Input Document Task 1 (step_condition, step_position, step_mode)  | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | @@YEAR==2014   | 3             | VIEW      | INPUT_DOCUMENT  | 83199959452cd62589576c1018679557 | 2           |
        | Update Output Document Task 1 (step_condition, step_position, step_mode) | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 |                | 1             | VIEW      | OUTPUT_DOCUMENT | 32743823452cd63105006e1076595203 | 3           |
        | Update Dynaform Task 2 (step_condition, step_position, step_mode)        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | @@YEAR==2014   | 2             | VIEW      | DYNAFORM        | 63293140052cd61b29e21a9056770986 | 4           |
        | Update Dynaform Task 2 (step_condition, step_position, step_mode)        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | @@YEAR==2014   | 1             | EDIT      | DYNAFORM        | 50332332752cd9b9a7cc989003652905 | 5           |


    Scenario Outline: List assigned Steps to "Task1" & "Task 2"
        Given I request "project/<project>/activity/<activity>/steps"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records
        And the "step_uid_obj" property in row 0 equals "<step_uid_obj>"
        And the "aas_type" property in row 0 equals "<step_type_obj>"



        Examples:

        | test_description                                                        | project                          | activity                         | records | step_type_obj   | step_uid_obj                     |
        | 3 steps in task 1 - verify that the first record is the first position  | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 3       | OUTPUT_DOCUMENT | 32743823452cd63105006e1076595203 |
        | 2 steps in task 2 - verify that the first record is the first position  | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 2       | DYNAFORM        | 50332332752cd9b9a7cc989003652905 |



    Scenario Outline: Unassign all steps assigned previously in this script in task 1
      Given that I want to delete a resource with the key "step_uid" stored in session array as variable "step_uid_<step_number>"
        And I request "project/<project>/activity/<activity>/step"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"


        Examples:

        | project                          | activity                         | step_uid_obj                     | step_number |
        | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 50332332752cd9b9a7cc989003652905 | 1           |
        | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 83199959452cd62589576c1018679557 | 2           |
        | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 32743823452cd63105006e1076595203 | 3           |

        
        

    Scenario: List assigned Steps to "Task1"
        Given I request "project/16062437052cd6141881e06088349078/activity/10163687452cd6234e0dd25086954968/steps"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array
    
     
    #STEP TRIGGERS

    Scenario: List assigned Triggers to "Task2"
        Given I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/63293140052cd61b29e21a9056770986/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

         Examples:

        | test_description                                            | project                          | activity                         | step_type_obj   | step_uid_obj                     | step_condition | step_position | step_mode | step_number |
        | Dynaform assigned to Task 2 in mode edit with condition     | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | DYNAFORM        | 63293140052cd61b29e21a9056770986 | @@YEAR==2013   | 1             | EDIT      | 4           |
        | Dynaform assigned to Task 2 in mode view                    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | DYNAFORM        | 50332332752cd9b9a7cc989003652905 |                | 2             | VIEW      | 5           |


    Scenario: List available Triggers to "Task2" when there are exactly three triggers
      Given I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/available-triggers/before"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 3 records


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
        And I request "project/<project>/activity/<activity>/step/<step>/trigger"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "step_uid" in session array as variable "tri_uid_<tri_uid_number>"

        Examples:

        | test_description                            | project                          | activity                         | step                             |tri_uid_number | tri_uid                          | st_type  | st_condition      | st_position |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 1             | 81919273152cd636c665080083928728 | BEFORE   |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 2             | 56359776552cd6378b38e47080912028 | AFTER    |                   | 1           |  
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 3             | 57401970252cd6393531551040242546 | AFTER    |                   | 2           |  
    
    
    Scenario: List available Triggers to "Task2" when there are exactly zero triggers
      Given I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/available-triggers/before"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 records



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
        And that I want to update a resource with the key "step_uid" stored in session array as variable "tri_uid_<tri_uid_number>"
        And I request "project/<project>/activity/<activity>/step/<step>/trigger"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        

        Examples:

        | test_description                             | project                          | activity                         | step                             |tri_uid_number | tri_uid                          | st_type  | st_condition      | st_position |
        | Update st_type, st_condition                 | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 1             | 81919273152cd636c665080083928728 | AFTER    | @@var1 == 1       | 1           |
        | Update st_type, st_condition and st_position | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 2             | 56359776552cd6378b38e47080912028 | BEFORE   | @@var1 == 2       | 2           |  
        | Update st_type, st_condition and st_position | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 3             | 57401970252cd6393531551040242546 | BEFORE   | @@var1 == 1       | 1           |  


   
   Scenario Outline: Get a single Triggers and check some properties
      Given that I want to get a resource with the key "tri_uid" stored in session array as variable "tri_uid_<tri_uid_number>"
        And I request "project/<project>/activity/<activity>/step/<step>/trigger"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And that "tri_uid" is set to "<tri_uid>"
        And that "st_type" is set to "<st_type>"
        And that "st_condition" is set to "<st_condition>"
        And that "st_position" is set to "<st_position>"

        Examples:

        | test_description                             | project                          | activity                         | step                             |tri_uid_number | tri_uid                          | st_type  | st_condition      | st_position |
        | Update st_type, st_condition                 | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 1             | 81919273152cd636c665080083928728 | AFTER    | @@var1 == 1       | 1           |
        | Update st_type, st_condition and st_position | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 2             | 56359776552cd6378b38e47080912028 | BEFORE   | @@var1 == 2       | 2           |  
        | Update st_type, st_condition and st_position | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 3             | 57401970252cd6393531551040242546 | BEFORE   | @@var1 == 1       | 1           |  


     Scenario Outline: Delete all Triggers assignation of a Step
      Given that I want to delete a resource with the key "tri_uid" stored in session array as variable "tri_uid_<tri_uid_number>"
        And I request "project/<project>/activity/<activity>/step/<step>/trigger/<trigger>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

          Examples:

        | project                          | activity                         | step                             |tri_uid_number | tri_uid                          |
        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 1             | 81919273152cd636c665080083928728 |
        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 2             | 56359776552cd6378b38e47080912028 |  
        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 3             | 57401970252cd6393531551040242546 |              

 
    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/triggers
    #    List assigned Triggers to a Step
    Scenario: List Triggers assigned to first Step of "Task2"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array

    

    #TRIGGERS OF STEP "ASSIGN TASK"

    Scenario: List Triggers assigned to Step "Assign Task" of "Task2", when are exactly three triggers
       Given I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array


    Scenario Outline: Assign a three triggers to a Step "Assign Task"
       Given POST this data:
        """
        {
            "tri_uid": "<tri_uid>",
            "st_type": "<st_type>",
            "st_condition": "<st_condition>",
            "st_position": "<st_position>"
        }
        """
        And I request "project/<project>/activity/<activity>/step/<step>/trigger"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "step_uid" in session array as variable "tri_uid_<tri_uid_number>"

        Examples:

        | test_description                            | project                          | activity                         | step                             |tri_uid_number | tri_uid                          | st_type           | st_condition      | st_position |
        | Trigger assigned to Task 2 in type before   | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 1             | 81919273152cd636c665080083928728 | BEFORE_ASSIGNMENT |                   | 1           |
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 2             | 56359776552cd6378b38e47080912028 | BEFORE_ROUTING    |                   | 1           |  
        | Trigger assigned to Task 2 in type After    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 3             | 57401970252cd6393531551040242546 | AFTER_ROUTING     |                   | 2           |  

  

    Scenario: List available Triggers to "Task2" when there are exactly zero triggers
      Given I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/available-triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 0 records

    
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
        And that I want to update a resource with the key "step_uid" stored in session array as variable "tri_uid_<tri_uid_number>"
        And I request "project/<project>/activity/<activity>/step/<step>/trigger"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        

        Examples:

        | test_description                             | project                          | activity                         | step                             |tri_uid_number | tri_uid                          | st_type           | st_condition      | st_position |
        | Update st_type, st_condition                 | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 1             | 81919273152cd636c665080083928728 | AFTER_ROUTING     | @@var1 == 1       | 1           |
        | Update st_type, st_condition and st_position | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 2             | 56359776552cd6378b38e47080912028 | BEFORE_ASSIGNMENT | @@var1 == 2       | 2           |  
        | Update st_type, st_condition and st_position | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 3             | 57401970252cd6393531551040242546 | BEFORE_ROUTING    | @@var1 == 1       | 1           |  


    Scenario Outline: Get a single Triggers and check some properties
      Given that I want to get a resource with the key "tri_uid" stored in session array as variable "tri_uid_<tri_uid_number>"
        And I request "project/<project>/activity/<activity>/step/<step>/trigger"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And that "tri_uid" is set to "<tri_uid>"
        And that "st_type" is set to "<st_type>"
        And that "st_condition" is set to "<st_condition>"
        And that "st_position" is set to "<st_position>"

    
        Examples:

        | test_description                             | project                          | activity                         | step                             |tri_uid_number | tri_uid                          | st_type           | st_condition      | st_position |
        | Update st_type, st_condition                 | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 1             | 81919273152cd636c665080083928728 | AFTER_ROUTING     | @@var1 == 1       | 1           |
        | Update st_type, st_condition and st_position | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 2             | 56359776552cd6378b38e47080912028 | BEFORE_ASSIGNMENT | @@var1 == 2       | 2           |  
        | Update st_type, st_condition and st_position | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 3             | 57401970252cd6393531551040242546 | BEFORE_ROUTING    | @@var1 == 1       | 1           |  


     Scenario Outline: Delete all Triggers assignation of a Step
      Given that I want to delete a resource with the key "tri_uid" stored in session array as variable "tri_uid_<tri_uid_number>"
        And I request "project/<project>/activity/<activity>/step/<step>/trigger/<trigger>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

          Examples:

        | project                          | activity                         | step                             |tri_uid_number | tri_uid                          |
        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 1             | 81919273152cd636c665080083928728 |
        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 2             | 56359776552cd6378b38e47080912028 |  
        | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | 65093024352cd9df93d9675058012924 | 3             | 57401970252cd6393531551040242546 |              

 
    #GET /api/1.0/{workspace}/project/{prj_uid}/activity/{act_uid}/step/{step_uid}/triggers
    #    List assigned Triggers to a Step
    Scenario: List Triggers assigned to first Step of "Task2"
        Given that I have a valid access_token
        And I request "project/16062437052cd6141881e06088349078/activity/89706843252cd9decdcf9b3047762708/step/65093024352cd9df93d9675058012924/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array