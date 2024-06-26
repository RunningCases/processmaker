@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents Negative Tests


  Background:
    Given that I have a valid access_token

  Scenario Outline: Create a new case scheduler for a project with bad parameters (negative tests)
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
      And I request "project/1265557095225ff5c688f46031700471/case-scheduler"
      Then the response status code should be <error_code>
      And the response status message should have the following text "<error_message>"


      Examples:
 
      | test_description                                                                          | sch_del_user_name | tas_uid                          | sch_name                               | sch_option | sch_start_date |  sch_end_date | sch_start_time | sch_week_days    | sch_start_day | sch_start_day_opt_1 | sch_months                      | sch_start_day_opt_2 | sch_repeat_every | error_code | error_message       |
      | Invalid sch_option                                                                        | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Daily 123@#$ 21         | 20         | 2014-01-30     | 2014-02-20    | 12:00          |                  |               |                     |                                 |                     |                  | 400        | sch_option          |
      | Invalid sch_del_user_name                                                                 | sample            | 46941969352af5be2ab3f39001216717 | Case Scheduler-Weekly monday 345%$#    | 2          | 2014-02-20     | 2014-03-20    | 08:00          | 1                |               |                     |                                 |                     |                  | 400        | user                |
      | Invalid tas_uid                                                                           | admin             | 00000000000005be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 1        | 3          | 2014-03-21     | 2014-04-18    | 18:00          |                  | 1             | 15                  | 3\|4                            |                     |                  | 400        | Task not found      |
      | Invalid sch_start_time                                                                    | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 2        | 3          | 2014-03-21     | 2014-04-18    | 39:00:99       |                  | 1             | 15                  | 1\|2\|5\|6\|7\|8\|9\|10\|11\|12 |                     |                  | 400        | sch_start_time      |
      | Invalid sch_start_date                                                                    | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 3        | 3          | 2014-20-35     | 2014-04-18    | 18:00          |                  | 2             |                     | 3\|4\|5                         | 1\|7                |                  | 400        | sch_start_date      |
      | Invalid sch_week_days                                                                     | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 4        | 2          | 2014-03-21     | 2014-04-18    | 18:00          | 8\|9\|10         |               |                     |                                 |                     |                  | 400        | sch_week_days       |
      | Invalid sch_start_date                                                                    | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 5        | 3          | 2014-33-76     | 2014-04-18    | 18:00          |                  | 2             |                     | 3\|4\|5                         | 3\|5                |                  | 400        | sch_start_date      |
      | Invalid sch_start_day_opt_1                                                               | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 6        | 3          | 2014-03-21     | 2014-04-18    | 18:00          |                  | 1             | 87                  | 3\|4\|5                         | 2\|4                |                  | 400        | sch_start_day_opt_1 |
      | Invalid sch_start_day_opt_2                                                               | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 7        | 3          | 2014-03-21     | 2014-04-18    | 18:00          |                  | 2             |                     | 3\|4\|5                         | 9\|10               |                  | 400        | sch_start_day_opt_2 |
      | Invalid sch_months                                                                        | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 8        | 3          | 2014-03-21     | 2014-04-18    | 18:00          |                  | 2             |                     | 13\|54\|65                      | 5\|3                |                  | 400        | sch_months          |
      | Invalid sch_end_date                                                                      | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Daily 123@#$ 23         | 1          | 2014-01-30     | 2015-54-87    | 12:00          |                  |               |                     |                                 |                     |                  | 400        | sch_end_date        |
      | Invalid sch_repeat_every                                                                  | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Every 987&%@ 10         | 5          |                |               |                |                  |               |                     |                                 |                     | 43:30            | 400        | sch_repeat_every    |
      | Field requered sch_option                                                                 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Daily 123@#$ 11         |            | 2014-01-30     | 2014-02-20    | 12:00          |                  |               |                     |                                 |                     |                  | 400        | sch_option          |
      | Field requered sch_name                                                                   | admin             | 46941969352af5be2ab3f39001216717 |                                        | 2          | 2014-02-20     | 2014-03-20    | 08:00          | 1                |               |                     |                                 |                     |                  | 400        | sch_name            |
      | Field requered sch_del_user_name                                                          |                   | 46941969352af5be2ab3f39001216717 | Case Scheduler-Weekly 345%$# 12        | 2          | 2014-02-20     | 2014-03-20    | 08:00          | 2\|3\|4\|5\|6\|7 |               |                     |                                 |                     |                  | 400        | user                |
      | Field requered tas_uid                                                                    | admin             |                                  | Case Scheduler-Monthly 567&^% 14       | 3          | 2014-03-21     | 2014-04-18    | 39:00:99       |                  | 1             | 15                  | 1\|2\|5\|6\|7\|8\|9\|10\|11\|12 |                     |                  | 400        | tas_uid             |
      | Field requered sch_start_time                                                             | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 15       | 3          | 2014-20-35     | 2014-04-18    |                |                  | 2             |                     | 3\|4\|5                         | 1\|7                |                  | 400        | sch_start_time      |
      | Field requered sch_start_date                                                             | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 16       | 3          |                | 2014-04-18    | 18:00          | 8\|9\|10         | 2             |                     | 3\|4\|5                         | 2\|6                |                  | 400        | sch_start_date      |
      | Field requered sch_week_days                                                              | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Weekly monday 345%$# 17 | 2          | 2014-02-20     | 2014-03-20    | 08:00          |                  |               |                     |                                 |                     |                  | 400        | sch_week_days       |
      | Field requered sch_start_day                                                              | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 18       | 3          | 2014-33-76     | 2014-04-18    | 18:00          |                  |               |                     | 3\|4\|5                         | 3\|5                |                  | 400        | sch_start_day       |
      | Field requered sch_months                                                                 | admin             | 46941969352af5be2ab3f39001216717 | Case Scheduler-Monthly 567&^% 19       | 3          | 2014-03-21     | 2014-04-18    | 18:00          |                  | 1             | 16                  |                                 | 2\|4                |                  | 400        | sch_months          |
     