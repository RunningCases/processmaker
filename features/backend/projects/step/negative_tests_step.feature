@ProcessMakerMichelangelo @RestAPI
Feature: Steps Resources Negative Tests


  Background:
    Given that I have a valid access_token

  Scenario Outline: Assign a Steps to an Activity with bad parameters (negative tests)
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
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:

        | test_description                         | project                          | activity                         | step_type_obj   | step_uid_obj                     | step_condition | step_position | step_mode | error_code | error_message |
        | Invalid step_type_obj - Dynaform         | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | DYNA123@#$%     | 50332332752cd9b9a7cc989003652905 |                | 1             | EDIT      | 400        | step_type_obj |
        | Invalid step_type_obj - Input Document   | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | INP_DOCUM123@#$ | 83199959452cd62589576c1018679557 |                | 2             | EDIT      | 400        | step_type_obj |
        | Invalid step_type_obj - Output Document  | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | OUTT_DMEN123@#$ | 32743823452cd63105006e1076595203 |                | 3             | EDIT      | 400        | step_type_obj |
        | Invalid step_position                    | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | DYNAFORM        | 63293140052cd61b29e21a9056770986 | @@YEAR==2013   | 2,34/76       | EDIT      | 400        | step_position |
        | Invalid  step_mode                       | 16062437052cd6141881e06088349078 | 89706843252cd9decdcf9b3047762708 | DYNAFORM        | 63293140052cd61b29e21a9056770986 |                | 5             | sample12  | 400        | step_mode     |
        | Field requered project                   |                                  | 10163687452cd6234e0dd25086954968 | DYNAFORM        | 50332332752cd9b9a7cc989003652905 |                | 1             | EDIT      | 400        | prj_uid       |
        | Field requered activity                  | 16062437052cd6141881e06088349078 |                                  | DYNAFORM        | 50332332752cd9b9a7cc989003652905 |                | 1             | EDIT      | 400        | act_uid       | 
        | Field requered step_uid_obj              | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | DYNAFORM        |                                  |                | 1             | EDIT      | 400        | step_uid_obj  |


 Scenario Outline: Assign a triggers to a Step with bad parameters (negative tests)
       Given POST this data:
        """
        {
            "tri_uid": "<tri_uid>",
            "st_type": "<st_type>",
            "st_condition": "<st_condition>",
            "st_position": "<st_position>"
        }
        """
        And I request "project/<project>/activity/<activity>/step/step_uid/trigger"
        And the response status message should have the following text "<error_message>"
        
        Examples:

        | test_description    | project                          | activity                         | step_number | tri_uid_number | tri_uid                          | st_type  | st_condition | st_position | error_code | error_message |
        | Invalid st_type     | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 2              | 56359776552cd6378b38e47080912028 | sample   |              | 2           | 400        | st_type       |
        | Invalid st_position | 16062437052cd6141881e06088349078 | 10163687452cd6234e0dd25086954968 | 1           | 3              | 57401970252cd6393531551040242546 | BEFORE   |              | 0,33.44     | 400        | st_position   |
        