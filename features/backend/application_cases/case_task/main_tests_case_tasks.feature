@ProcessMakerMichelangelo @RestAPI
Feature: Case Tasks Main Tests
Requirements:
    a workspace with the case 383652497533b1492846753088523464 "case #174" and 587530805533b0d5031bd35011041644 "case #146"  of the process ("Test Case Variables and Derivation rules-selection") already loaded
    
Background:
    Given that I have a valid access_token


Scenario: Get list case tasks of case 174
    Given I request "cases/383652497533b1492846753088523464/tasks"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And that "tas_uid" is set to "63847491053347e25555c29086425576"
    And that "tas_title" is set to "Task 1"
    And that "tas_description" is set to ""
    And that "tas_start" is set to "1"
    And that "tas_type" is set to "NORMAL"
    And that "tas_derivation" is set to "NORMAL"
    And that "tas_assign_type" is set to "BALANCED"
    

    And that "rou_type" is set to "0"
    And that "rou_next_task" is set to "93695356653347e2702ab38033892652"
    And that "rou_condition" is set to ""
    And that "rou_to_last_user" is set to "FALSE"
    And that "rou_optional" is set to "FALSE"
    And that "usr_uid" is set to "Administrator"
    And that "usr_firstname" is set to "Administrator"
    And that "usr_lastname" is set to ""
    And that "del_init_date" is set to "2014-04-01 15:33:38"
    And that "del_task_due_date" is set to "2014-04-02 15:33:38"
    And that "del_finish_date" is set to "2014-04-01 15:33:47"
    And that "duration" is set to "0 Hours 0 Minutes 9 Seconds"
    And that "color" is set to "#006633"
    And that "tas_uid" is set to "93695356653347e2702ab38033892652"
    And that "tas_type" is set to "NORMAL"
    And that "tas_title" is set to "Task 2"
    And that "rou_type" is set to "0"
    And that "rou_next_task" is set to "-1"
    And that "rou_condition" is set to ""
    And that "rou_to_last_user" is set to "FALSE"
    And that "rou_optional" is set to "FALSE"
    And that "usr_uid" is set to "Administrator"
    And that "usr_firstname" is set to "Administrator"
    And that "usr_lastname" is set to ""
    And that "del_init_date" is set to "Case not started yet"
    And that "del_task_due_date" is set to "2014-04-02 15:33:47"
    And that "del_finish_date" is set to "Not finished"
    And that "duration" is set to "Not finished"
    And that "color" is set to "#FF0000"

Scenario: Get list case tasks of case 146
    Given I request "cases/587530805533b0d5031bd35011041644/tasks"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And that "tas_uid" is set to "17300415050ec6a1687f439060824658"
    And that "tas_type" is set to "NORMAL"
    And that "tas_title" is set to "Self Service"
    And that "rou_type" is set to ""
    And that "color" is set to "#939598"

    And that "tas_uid" is set to "18637084950ec669487b1b3034500214"
    And that "tas_type" is set to "NORMAL"
    And that "tas_title" is set to "Cyclical"
    And that "rou_type" is set to ""
    And that "color" is set to "#939598"
    
    And that "tas_uid" is set to "56900024450ec668e4a9243080698854"
    And that "tas_type" is set to "NORMAL"
    And that "tas_title" is set to "Init"
    And that "rou_type" is set to "1"
    
    And that "rou_next_task" is set to "-1"
    And that "rou_condition" is set to ""
    And that "rou_to_last_user" is set to "FALSE"
    And that "rou_optional" is set to "FALSE"
    And that "usr_uid" is set to "Administrator"
    And that "usr_firstname" is set to "Administrator"
    And that "usr_lastname" is set to ""
    And that "del_init_date" is set to "2014-04-01 15:02:40"
    And that "del_task_due_date" is set to "2014-04-01 15:02:40"
    And that "del_finish_date" is set to "2014-04-01 15:02:51"
    And that "duration" is set to "0 Hours 0 Minutes 11 Seconds"

    And that "rou_next_task" is set to "18637084950ec669487b1b3034500214"
    And that "rou_condition" is set to ""
    And that "rou_to_last_user" is set to "FALSE"
    And that "rou_optional" is set to "FALSE"
    And that "usr_uid" is set to "Administrator"
    And that "usr_firstname" is set to "Administrator"
    And that "usr_lastname" is set to ""
    And that "del_init_date" is set to "2014-04-01 15:02:40"
    And that "del_task_due_date" is set to "2014-04-02 15:02:40"
    And that "del_finish_date" is set to "2014-04-01 15:02:51"
    And that "duration" is set to "0 Hours 0 Minutes 11 Seconds"
    And that "color" is set to "#006633"

    And that "tas_uid" is set to "79440307650ec67a8ba3969022801548"
    And that "tas_type" is set to "NORMAL"
    And that "tas_title" is set to "Reports to"
    And that "rou_type" is set to ""
    And that "color" is set to "#939598"

    And that "tas_uid" is set to "82464599650ec679055e040061009891"
    And that "tas_type" is set to "NORMAL"
    And that "tas_title" is set to "Manual"
    And that "rou_type" is set to ""
    And that "color" is set to "#939598"