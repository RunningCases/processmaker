@ProcessMakerMichelangelo @RestAPI
Feature: Cases Actions - the features in this script are (getCaseInfo, taskCase, newCase, newCaseImpersonate, reassignCase, routeCase, cancelCase, pauseCase, unpauseCase, executeTrigger and DELETE Case) Negative Tests

Background:
    Given that I have a valid access_token


Scenario Outline: Create a new case (Negative Test)
        Given POST this data:
            """
            {
                "pro_uid": "<pro_uid>",
                "tas_uid": "<tas_uid>",
                "variables": [{"name": "admin", "amount":"1030"}]
            }
            """
        And I request "cases"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"
        
        
        Examples:
        | Description                                     | pro_uid                          | tas_uid                          | error_code | error_message |
        | Create new case with pro_uid wrong              | 99209594750ec1111111927000421575 | 68707275350ec281ada1c95068712556 | 400        | pro_uid       |
        | Create new case with task_uid wrong             | 46279907250ec73b9b25a78031279680 | 99371337811111111111116024620271 | 400        | task_uid      |
        | Create new case without pro_uid                 |                                  | 52838134750ec7dd0989fc0015625952 | 400        | pro_uid       |
        | Create new case without tas_uid                 | 34579467750ec8d55e8b115057818502 |                                  | 400        | task_uid      |
        | Create new case whith pro_uid and tas_uid wrong | 8245849601111111181ecc7039804404 | 5690001111111118e4a9243080698854 | 400        | pro_uid       |

        


Scenario Outline: Create a new case Impersonate (Negative Test)
        Given POST this data:
            """
            {
                "pro_uid": "99209594750ec27ea338927000421575",
                "usr_uid": "<usr_uid>",
                "tas_uid": "68707275350ec281ada1c95068712556",
                "variables": [{"name": "pruebaQA", "amount":"10400"}]
            }
            """
        And I request "cases/impersonate"
         Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"
        
        Examples:
        | Description                                                                                     | pro_uid                          | usr_uid                          | tas_uid                          | error_code | error_message | 
        | Create new case with process "Derivation rules - sequential" invalid pro_uid                    | 99201111111111111138927000421575 | 51049032352d56710347233042615067 | 68707275350ec281ada1c95068712556 | 400        | pro_uid       |
        | Create new case with process "Derivation rules - evaluation" invalid task_uid                   | 99209594750ec27ea338927000421575 | 44811996752d567110634a1013636964 | 68707211111111111111111111712556 | 400        | task_uid      |
        | Create new case with process "Derivation rules - Parallel" usr_uid                              | 99209594750ec27ea338927000421575 | 24166331111111111111115035621101 | 68707275350ec281ada1c95068712556 | 400        | usr_uid       |
        | Create new case with process "Derivation rules - without pro_uid                                |                                  | 86677227852d5671f40ba25017213081 | 68707275350ec281ada1c95068712556 | 400        | pro_uid       |
        | Create new case with process "Derivation rules - selection" tas_uid                             | 99209594750ec27ea338927000421575 | 62625000752d5672d6661e6072881167 |                                  | 400        | tas_uid       |


Scenario Outline: Create a case, derivate and cancel. then try do pause or route
        #Create case
        Given POST this data:
            """
            {
                "pro_uid": "<pro_uid>",
                "tas_uid": "<tas_uid>",
                "variables": [{"name": "admin", "amount":"1030"}]
            }
            """
        And I request "cases"
        Then the response status code should be 200   
        And store "app_uid" in session array as variable "app_uid_<case_number>"
        #Send some variables
        And PUT this data:
            """
            {
                "continue": "yes",
                "tasks": "Cyclical"
                
            }
            """
        And I request "cases/app_uid/variable"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        #Cancel case
        And  PUT this data:
        """
        {
        
        }
        """
        And I request "cases/app_uid/cancel"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"

        #Route case: it should not allow
        And PUT this data:
            """
            {
                "case_uid": "<case_number>",
                "del_index": "1"
            }
            """
        And I request "cases/app_uid/route-case"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
        Then the response status code should be 400
        And the response status message should have the following text "<error_message_route>"

        #Pause case
        And PUT this data:
        """
            {
              "unpaused_date": "2016-12-12"  
            }
            """
    And I request "cases/app_uid/pause"  with the key "app_uid" stored in session array as variable "app_uid_<case_number>"
    Then the response status code should be 400
    And the response status message should have the following text "<error_message_pause>"
    

    Examples:
        | Description                                                           | case_number | pro_uid                          | tas_uid                          | error_message_route                    | error_message_pause                    |
        | Create new case with process "Derivation rules - sequential"          | 1           | 99209594750ec27ea338927000421575 | 68707275350ec281ada1c95068712556 | This case delegation is already closed | This case delegation is already closed |
             