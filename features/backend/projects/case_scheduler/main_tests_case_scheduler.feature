@ProcessMakerMichelangelo @RestAPI
Feature: Case Scheduler Main Tests
  Requirements:
    a workspace with the process 1265557095225ff5c688f46031700471 ("Test Michelangelo") already loaded
    there are zero case scheduler in the process and there four tasks in the process
    and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded

Background:
  Given that I have a valid access_token
 
Scenario Outline: Get the case schedulers list when there are exactly case schedulers 
    Given I request "project/<project>/case-schedulers"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <record> record

    Examples:
 
    | test_description                                    | project                          | record |
    | Get case scheduler of process Test Michelangelo     | 1265557095225ff5c688f46031700471 | 0      |
    | Get case scheduler of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 1      |
  

Scenario Outline: Create any case scheduler for a project
    Given POST this data:
    """
    {
      "sch_del_user_name": "<sch_del_user_name>",
      "tas_uid": "<tas_uid>",
      "sch_name": "<sch_name>",
      "sch_option": "<sch_option>",
      "sch_start_date": "<sch_start_date>",
      "sch_end_date": "<sch_end_date>",
      "sch_start_time": "<sch_start_time>",
      "sch_week_days": "<sch_week_days>",
      "sch_start_day": "<sch_start_day>",
      "sch_start_day_opt_1": "<sch_start_day_opt_1>",
      "sch_months": "<sch_months>",          
      "sch_start_day_opt_2": "<sch_start_day_opt_2>",          
      "sch_repeat_every": "<sch_repeat_every>"          
    }
    """
    And I request "project/<project>/case-scheduler"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "sch_uid" in session array as variable "sch_uid_<sch_uid_number>"

    Examples:
 
    | test_description                                                                                          | sch_uid_number | project                          | sch_del_user_name | tas_uid                          | sch_name                            | sch_option | sch_start_date | sch_end_date | sch_start_time | sch_week_days    | sch_start_day | sch_start_day_opt_1 | sch_months                      | sch_start_day_opt_2 | sch_repeat_every |
    | Create with Daily                                                                         of process .pm  | 1              | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Daily 123@#$         | 1          | 2014-01-30     | 2014-02-20   | 12:00          |                  |               |                     |                                 |                     |                  |
    | Create with Weekly, sch_week_days=monday                                                  of process .pm  | 2              | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Weekly monday 345%$# | 2          | 2014-02-20     | 2014-03-20   | 08:00          | 1                |               |                     |                                 |                     |                  |
    | Create with Weekly, sch_week_days=tuesday, wednesday, thursday, friday, saturday, sunday  of process .pm  | 3              | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Weekly 345%$#        | 2          | 2014-02-20     | 2014-03-20   | 08:00          | 2\|3\|4\|5\|6\|7 |               |                     |                                 |                     |                  |
    | Create with Monthly and day of month, day of month=1, of the month(s)=3,4                 of process .pm  | 4              | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 1     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 1             | 15                  | 3\|4                            |                     |                  |
    | Create with Monthly and day of month, day of month=1, of the month=1,2,5,6,7,8,9,10,11,12 of process .pm  | 5              | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 2     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 1             | 15                  | 1\|2\|5\|6\|7\|8\|9\|10\|11\|12 |                     |                  |
    | Create with Monthly and the day=first and Monday                                          of process .pm  | 6              | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 3     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 3\|4\|5                         | 1\|7                |                  |
    | Create with Monthly and the day=second and Saturday                                       of process .pm  | 7              | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 4     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 3\|4\|5                         | 2\|6                |                  |
    | Create with Monthly and the day=Third and Friday                                          of process .pm  | 8              | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 5     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 3\|4\|5                         | 3\|5                |                  |
    | Create with Monthly and the day=second and Thursday                                       of process .pm  | 9              | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 6     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 3\|4\|5                         | 2\|4                |                  |
    | Create with Monthly and the day=last and Wednesday                                        of process .pm  | 10             | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 7     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 3\|4\|5                         | 5\|3                |                  |
    | Create with Monthly and the day=last and Wednesday, of the month=1,2,6,7,8,9,10,11,12     of process .pm  | 11             | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 8     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 1\|2\|6\|7\|8\|9\|10\|11\|12    | 5\|3                |                  |
    | Create with One time only                                                                 of process .pm  | 12             | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-One Time only 678%$@ | 4          |                |              | 20:00          |                  |               |                     |                                 |                     |                  |
    | Create with Every                                                                         of process .pm  | 13             | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Every 987&%@         | 5          |                |              |                |                  |               |                     |                                 |                     | 12.30            |
    | Create with Daily - Test BUG 15316                                                        of process .pm  | 14             | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | Bug 15316                           | 1          | 2014-01-30     |              | 12:00          |                  |               |                     |                                 |                     |                  |
    | Test BUG 15330, 15331: Every format in the properties Invalid Start Timer 1.00            of process .pm  | 15             | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | BUG 15330 1.0                       | 5          |                |              |                |                  |               |                     |                                 |                     | 1.00             |
    | Test BUG 15330, 15331: Every format in the properties Invalid Start Timer 01.00           of process .pm  | 16             | 1265557095225ff5c688f46031700471 | admin             | 46941969352af5be2ab3f39001216717 | BUG 15330 01.00                     | 5          |                |              |                |                  |               |                     |                                 |                     | 01.00            |
    | Create with Daily                                                                         of process .pmx | 17             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Daily 123@#$         | 1          | 2014-01-30     | 2014-02-20   | 12:00          |                  |               |                     |                                 |                     |                  |
    | Create with Weekly, sch_week_days=monday                                                  of process .pmx | 18             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Weekly monday 345%$# | 2          | 2014-02-20     | 2014-03-20   | 08:00          | 1                |               |                     |                                 |                     |                  |
    | Create with Weekly, sch_week_days=tuesday, wednesday, thursday, friday, saturday, sunday  of process .pmx | 19             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Weekly 345%$#        | 2          | 2014-02-20     | 2014-03-20   | 08:00          | 2\|3\|4\|5\|6\|7 |               |                     |                                 |                     |                  |
    | Create with Monthly and day of month, day of month=1, of the month(s)=3,4                 of process .pmx | 20             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Monthly 567&^% 1     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 1             | 15                  | 3\|4                            |                     |                  |
    | Create with Monthly and day of month, day of month=1, of the month=1,2,5,6,7,8,9,10,11,12 of process .pmx | 21             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Monthly 567&^% 2     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 1             | 15                  | 1\|2\|5\|6\|7\|8\|9\|10\|11\|12 |                     |                  |
    | Create with Monthly and the day=first and Monday                                          of process .pmx | 22             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Monthly 567&^% 3     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 3\|4\|5                         | 1\|7                |                  |
    | Create with Monthly and the day=second and Saturday                                       of process .pmx | 23             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Monthly 567&^% 4     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 3\|4\|5                         | 2\|6                |                  |
    | Create with Monthly and the day=Third and Friday                                          of process .pmx | 24             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Monthly 567&^% 5     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 3\|4\|5                         | 3\|5                |                  |
    | Create with Monthly and the day=second and Thursday                                       of process .pmx | 25             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Monthly 567&^% 6     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 3\|4\|5                         | 2\|4                |                  |
    | Create with Monthly and the day=last and Wednesday                                        of process .pmx | 26             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Monthly 567&^% 7     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 3\|4\|5                         | 5\|3                |                  |
    | Create with Monthly and the day=last and Wednesday, of the month=1,2,6,7,8,9,10,11,12     of process .pmx | 27             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Monthly 567&^% 8     | 3          | 2014-03-21     | 2014-04-18   | 18:00          |                  | 2             |                     | 1\|2\|6\|7\|8\|9\|10\|11\|12    | 5\|3                |                  |
    | Create with One time only                                                                 of process .pmx | 28             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-One Time only 678%$@ | 4          |                |              | 20:00          |                  |               |                     |                                 |                     |                  |
    | Create with Every                                                                         of process .pmx | 29             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Case Scheduler-Every 987&%@         | 5          |                |              |                |                  |               |                     |                                 |                     | 12.30            |
    | Create with Daily - Test BUG 15316                                                        of process .pmx | 30             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | Bug 15316                           | 1          | 2014-01-30     |              | 12:00          |                  |               |                     |                                 |                     |                  |
    | Test BUG 15330, 15331: Every format in the properties Invalid Start Timer 1.00            of process .pmx | 31             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | BUG 15330 1.0                       | 5          |                |              |                |                  |               |                     |                                 |                     | 1.00             |
    | Test BUG 15330, 15331: Every format in the properties Invalid Start Timer 01.00           of process .pmx | 32             | 1455892245368ebeb11c1a5001393784 | admin             | 4790702485368efad167477011123879 | BUG 15330 01.00                     | 5          |                |              |                |                  |               |                     |                                 |                     | 01.00            |
      

Scenario: Create a new case scheduler with same name
      Given POST this data:
      """
      {
          "sch_option": "5",
          "sch_name": "Case Scheduler-Every 987&%@",
          "sch_del_user_name": "admin",
          "tas_uid": "46941969352af5be2ab3f39001216717",
          "sch_start_time": "",
          "sch_start_date": "",
          "sch_end_date": "",
          "sch_week_days": "", 
          "sch_start_day": "",
          "sch_start_day_opt_1": "",
          "sch_start_day_opt_2": "",
          "sch_months": "",    
          "sch_repeat_every": "12.30"
      }
      """
      And I request "project/1265557095225ff5c688f46031700471/case-scheduler"
      Then the response status code should be 400
      And the response status message should have the following text "Duplicate"
      
  
Scenario Outline: Get the case schedulers list when there are exactly 16 after 17 case schedulers in each process
      Given I request "project/<project>/case-schedulers"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "array"
      And the response has <record> record

      Examples:
 
      | test_description                                    | project                          | record |
      | Get case scheduler of process Test Michelangelo     | 1265557095225ff5c688f46031700471 | 16     |
      | Get case scheduler of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 17     |
  

Scenario Outline: Update the case schedulers for a project and then check if the values had changed
    Given PUT this data:
      """
      {
          "sch_del_user_name": "<sch_del_user_name>",
          "tas_uid": "<tas_uid>",
          "sch_name": "<sch_name>",
          "sch_option": "<sch_option>",
          "sch_start_date": "<sch_start_date>",
          "sch_end_date": "<sch_end_date>",
          "sch_start_time": "<sch_start_time>",
          "sch_week_days": "<sch_week_days>",
          "sch_start_day": "<sch_start_day>",
          "sch_start_day_opt_1": "<sch_start_day_opt_1>",
          "sch_months": "<sch_months>",          
          "sch_start_day_opt_2": "<sch_start_day_opt_2>",          
          "sch_repeat_every": "<sch_repeat_every>",
          "sch_state": "<sch_state>"
      } 
      """
      And that I want to update a resource with the key "sch_uid" stored in session array as variable "sch_uid_<sch_uid_number>"
      And I request "project/<project>/case-scheduler"
      And the content type is "application/json"
      Then the response status code should be 200
      And the response charset is "UTF-8"

      Examples:

      | test_description                                                                     | sch_uid_number | project                          | sch_del_user_name | tas_uid                          | sch_name                                   | sch_option | sch_start_date |  sch_end_date | sch_start_time | sch_week_days | sch_start_day | sch_start_day_opt_1 | sch_months | sch_start_day_opt_2 | sch_repeat_every |sch_state |
      | Update Daily                                                         of process .pm  | 1              | 1265557095225ff5c688f46031700471 | admin             | 1352844695225ff5fe54de2005407079 | Update Case Scheduler-Daily 123@#$         | 1          | 2014-02-30     | 2014-03-20    | 12:30          |               |               |                     |            |                     |                  |ACTIVE    |
      | Update Weekly, sch_week_days=monday                                  of process .pm  | 2              | 1265557095225ff5c688f46031700471 | admin             | 1352844695225ff5fe54de2005407079 | Update Case Scheduler-Weekly monday 345%$# | 2          | 2014-03-20     | 2014-04-20    | 08:30          | 2             |               |                     |            |                     |                  |ACTIVE    |
      | Update Monthly and day of month, day of month=1, of the month(s)=3,4 of process .pm  | 4              | 1265557095225ff5c688f46031700471 | admin             | 1352844695225ff5fe54de2005407079 | Update Case Scheduler-Monthly 567&^% 1     | 3          | 2014-04-21     | 2014-05-18    | 18:30          |               | 1             | 18                  | 3\|4\|5    |                     |                  |ACTIVE    |
      | Update One time only                                                 of process .pm  | 12             | 1265557095225ff5c688f46031700471 | admin             | 1352844695225ff5fe54de2005407079 | Update Case Scheduler-One Time only 678%$@ | 4          |                |               | 20:30          |               |               |                     |            |                     |                  |ACTIVE    |
      | Update Every                                                         of process .pm  | 13             | 1265557095225ff5c688f46031700471 | admin             | 1352844695225ff5fe54de2005407079 | Update Case Scheduler-Every 987&%@         | 5          |                |               |                |               |               |                     |            |                     | 18.30            |ACTIVE    |
      | Update Daily                                                         of process .pmx | 17             | 1455892245368ebeb11c1a5001393784 | admin             | 6274755055368eed1116388064384542 | Update Case Scheduler-Daily 123@#$         | 1          | 2014-02-30     | 2014-03-20    | 12:30          |               |               |                     |            |                     |                  |ACTIVE    |
      | Update Weekly, sch_week_days=monday                                  of process .pmx | 18             | 1455892245368ebeb11c1a5001393784 | admin             | 6274755055368eed1116388064384542 | Update Case Scheduler-Weekly monday 345%$# | 2          | 2014-03-20     | 2014-04-20    | 08:30          | 2             |               |                     |            |                     |                  |ACTIVE    |
      | Update Monthly and day of month, day of month=1, of the month(s)=3,4 of process .pmx | 20             | 1455892245368ebeb11c1a5001393784 | admin             | 6274755055368eed1116388064384542 | Update Case Scheduler-Monthly 567&^% 1     | 3          | 2014-04-21     | 2014-05-18    | 18:30          |               | 1             | 18                  | 3\|4\|5    |                     |                  |ACTIVE    |
      | Update One time only                                                 of process .pmx | 29             | 1455892245368ebeb11c1a5001393784 | admin             | 6274755055368eed1116388064384542 | Update Case Scheduler-One Time only 678%$@ | 4          |                |               | 20:30          |               |               |                     |            |                     |                  |ACTIVE    |
      | Update Every                                                         of process .pmx | 30             | 1455892245368ebeb11c1a5001393784 | admin             | 6274755055368eed1116388064384542 | Update Case Scheduler-Every 987&%@         | 5          |                |               |                |               |               |                     |            |                     | 18.30            |ACTIVE    |



Scenario Outline: Get a single case scheduler of a project and check some properties
      Given that I want to get a resource with the key "sch_uid" stored in session array as variable "sch_uid_<sch_uid_number>"
      And I request "project/<project>/case-scheduler"
      Then the response status code should be 200
      And the content type is "application/json"
      And the type is "object"
      And the response charset is "UTF-8"
      And that "tas_uid" is set to "<tas_uid>"
      And that "sch_name" is set to "<sch_name>"
      And that "sch_start_date" is set to "<sch_start_date>"
      And that "sch_end_date" is set to "<sch_end_date>"
      And that "sch_start_time" is set to "<sch_start_time>"
      And that "sch_week_days" is set to "<sch_week_days>"
      And that "sch_start_day" is set to "<sch_start_day>"
      And that "sch_start_day_opt_1" is set to "<sch_start_day_opt_1>"
      And that "sch_months" is set to "<sch_months>"
      And that "sch_repeat_every" is set to "<sch_repeat_every>"

      Examples:

      | test_description                                                                     | sch_uid_number | project                          | sch_del_user_name | tas_uid                          | sch_name                                   | sch_option | sch_start_date |  sch_end_date | sch_start_time | sch_week_days | sch_start_day | sch_start_day_opt_1 | sch_months | sch_start_day_opt_2 | sch_repeat_every |sch_state |
      | Update Daily                                                         of process .pm  | 1              | 1265557095225ff5c688f46031700471 | admin             | 1352844695225ff5fe54de2005407079 | Update Case Scheduler-Daily 123@#$         | 1          | 2014-02-30     | 2014-03-20    | 12:30          |               |               |                     |            |                     |                  |ACTIVE    |
      | Update Weekly, sch_week_days=monday                                  of process .pm  | 2              | 1265557095225ff5c688f46031700471 | admin             | 1352844695225ff5fe54de2005407079 | Update Case Scheduler-Weekly monday 345%$# | 2          | 2014-03-20     | 2014-04-20    | 08:30          | 2             |               |                     |            |                     |                  |ACTIVE    |
      | Update Monthly and day of month, day of month=1, of the month(s)=3,4 of process .pm  | 4              | 1265557095225ff5c688f46031700471 | admin             | 1352844695225ff5fe54de2005407079 | Update Case Scheduler-Monthly 567&^% 1     | 3          | 2014-04-21     | 2014-05-18    | 18:30          |               | 1             | 18                  | 3\|4\|5    |                     |                  |ACTIVE    |
      | Update One time only                                                 of process .pm  | 12             | 1265557095225ff5c688f46031700471 | admin             | 1352844695225ff5fe54de2005407079 | Update Case Scheduler-One Time only 678%$@ | 4          |                |               | 20:30          |               |               |                     |            |                     |                  |ACTIVE    |
      | Update Every                                                         of process .pm  | 13             | 1265557095225ff5c688f46031700471 | admin             | 1352844695225ff5fe54de2005407079 | Update Case Scheduler-Every 987&%@         | 5          |                |               |                |               |               |                     |            |                     | 18.30            |ACTIVE    |
      | Update Daily                                                         of process .pmx | 17             | 1455892245368ebeb11c1a5001393784 | admin             | 6274755055368eed1116388064384542 | Update Case Scheduler-Daily 123@#$         | 1          | 2014-02-30     | 2014-03-20    | 12:30          |               |               |                     |            |                     |                  |ACTIVE    |
      | Update Weekly, sch_week_days=monday                                  of process .pmx | 18             | 1455892245368ebeb11c1a5001393784 | admin             | 6274755055368eed1116388064384542 | Update Case Scheduler-Weekly monday 345%$# | 2          | 2014-03-20     | 2014-04-20    | 08:30          | 2             |               |                     |            |                     |                  |ACTIVE    |
      | Update Monthly and day of month, day of month=1, of the month(s)=3,4 of process .pmx | 20             | 1455892245368ebeb11c1a5001393784 | admin             | 6274755055368eed1116388064384542 | Update Case Scheduler-Monthly 567&^% 1     | 3          | 2014-04-21     | 2014-05-18    | 18:30          |               | 1             | 18                  | 3\|4\|5    |                     |                  |ACTIVE    |
      | Update One time only                                                 of process .pmx | 29             | 1455892245368ebeb11c1a5001393784 | admin             | 6274755055368eed1116388064384542 | Update Case Scheduler-One Time only 678%$@ | 4          |                |               | 20:30          |               |               |                     |            |                     |                  |ACTIVE    |
      | Update Every                                                         of process .pmx | 30             | 1455892245368ebeb11c1a5001393784 | admin             | 6274755055368eed1116388064384542 | Update Case Scheduler-Every 987&%@         | 5          |                |               |                |               |               |                     |            |                     | 18.30            |ACTIVE    |



Scenario Outline: Delete all case scheduler of a project created previously in this script
    Given that I want to delete a resource with the key "sch_uid" stored in session array as variable "sch_uid_<sch_uid_number>"
    And I request "project/<project>/case-scheduler"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:
 
    | sch_uid_number | project                          |
    | 1              | 1265557095225ff5c688f46031700471 |
    | 2              | 1265557095225ff5c688f46031700471 |
    | 3              | 1265557095225ff5c688f46031700471 |
    | 4              | 1265557095225ff5c688f46031700471 |
    | 5              | 1265557095225ff5c688f46031700471 |
    | 6              | 1265557095225ff5c688f46031700471 |
    | 7              | 1265557095225ff5c688f46031700471 |
    | 8              | 1265557095225ff5c688f46031700471 |
    | 9              | 1265557095225ff5c688f46031700471 |
    | 10             | 1265557095225ff5c688f46031700471 |
    | 11             | 1265557095225ff5c688f46031700471 |
    | 12             | 1265557095225ff5c688f46031700471 |
    | 13             | 1265557095225ff5c688f46031700471 |
    | 14             | 1265557095225ff5c688f46031700471 |
    | 15             | 1265557095225ff5c688f46031700471 |
    | 16             | 1265557095225ff5c688f46031700471 |
    | 17             | 1455892245368ebeb11c1a5001393784 |
    | 18             | 1455892245368ebeb11c1a5001393784 |
    | 19             | 1455892245368ebeb11c1a5001393784 |
    | 20             | 1455892245368ebeb11c1a5001393784 |
    | 21             | 1455892245368ebeb11c1a5001393784 |
    | 22             | 1455892245368ebeb11c1a5001393784 |
    | 23             | 1455892245368ebeb11c1a5001393784 |
    | 24             | 1455892245368ebeb11c1a5001393784 |
    | 25             | 1455892245368ebeb11c1a5001393784 |
    | 26             | 1455892245368ebeb11c1a5001393784 |
    | 27             | 1455892245368ebeb11c1a5001393784 |
    | 28             | 1455892245368ebeb11c1a5001393784 |
    | 29             | 1455892245368ebeb11c1a5001393784 |
    | 30             | 1455892245368ebeb11c1a5001393784 |
    | 31             | 1455892245368ebeb11c1a5001393784 |
    | 32             | 1455892245368ebeb11c1a5001393784 |
    
    
#Scenario para la revision del "BUG 15040" donde se comprueba la creacion de nuevos case scheduler en diferentes proyectos BPMN.

Scenario Outline: Create a new case scheduler with same name
  Given POST this data:
    """
    {
      "sch_option": "5",
      "sch_name": "sample",
      "sch_del_user_name": "admin",
      "tas_uid": "4790702485368efad167477011123879",
      "sch_start_time": "",
      "sch_start_date": "",
      "sch_end_date": "",
      "sch_week_days": "", 
      "sch_start_day": "",
      "sch_start_day_opt_1": "",
      "sch_start_day_opt_2": "",
      "sch_months": "",    
      "sch_repeat_every": "12.30"
    }
    """
    And I request "project/<project>/case-scheduler"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "sch_uid" in session array as variable "sch_uid_<sch_uid_number>"

    Examples:
 
    | test_description                                           | sch_uid_number | project                          |
    | Create with Daily in project Derivation rules - evaluation | 1              | 46279907250ec73b9b25a78031279680 |
    | Create with Daily in project Derivation rules - Parallel   | 2              | 35894775350ec7daa099378048029617 |
    

Scenario Outline: Delete case scheduler of a project created previously in this script
    Given that I want to delete a resource with the key "sch_uid" stored in session array as variable "sch_uid_<sch_uid_number>"
      And I request "project/46279907250ec73b9b25a78031279680/case-scheduler"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the type is "object"

    Examples:
 
    | sch_uid_number |
    | 1              | 
    | 2              |


Scenario Outline: Create new Projects with event case scheduler
    Given POST data from file "<project_template>"
    And I request "projects"
    Then the response status code should be 201
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And store "new_uid" in session array as variable "project_new_uid_<project_new_uid_number>" where an object has "object" equal to "project"
    And store "new_uid" in session array as variable "diagram_new_uid_<project_new_uid_number>" where an object has "object" equal to "diagram"
    And store "new_uid" in session array as variable "activity_new_uid_<project_new_uid_number>" where an object has "object" equal to "activity"
    And store "new_uid" in session array as variable "evn_uid_<evn_uid_number>" where an object has "object" equal to "event"
    And store "new_uid" in session array as variable "flow_new_uid_<project_new_uid_number>" where an object has "object" equal to "flow"
    
    Examples:

    | Description                                      | project_new_uid_number | evn_uid_number | project_template                 |
    | Create a new project with event case scheduler 1 | 1                      | 3              | project_bug_case_scheduler1.json |
    | Create a new project with event case scheduler 2 | 2                      | 4              | project_bug_case_scheduler2.json |
    

Scenario Outline: Delete a Project previously created in this script
    Given that I want to delete a resource with the key "new_uid" stored in session array as variable "project_new_uid_<project_new_uid_number>" in position 0
    And I request "projects"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

    Examples:

    | project_new_uid_number |
    | 1                      |
    | 2                      |