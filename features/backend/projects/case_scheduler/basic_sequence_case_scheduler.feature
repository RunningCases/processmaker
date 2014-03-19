@ProcessMakerMichelangelo @RestAPI
Feature: Case Scheduler Resources

  Background:
    Given that I have a valid access_token
 
  Scenario: Create a new case scheduler for a project
      Given POST this data:
      """
      {
          "sch_option": "3",
          "sch_name": "Test scheduler #1",
          "sch_del_user_name": "admin",
          "tas_uid": "46941969352af5be2ab3f39001216717",    
          "sch_start_time": "18:00",
          "sch_start_date": "2014-01-20",
          "sch_end_date": "2014-01-20",
          "sch_every_days": "",
          "sch_week_days": "", 
          "sch_start_day": "1",
          "sch_start_day_opt_1": "2",
          "sch_start_day_opt_2": "",
          "sch_months": "1|6|12",    
          "sch_repeat_every": "",
          "sch_repeat_until": ""
      }
      """
      And I request "project/1265557095225ff5c688f46031700471/case-scheduler"
      Then the response status code should be 201
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "object"
      And store "sch_uid" in session array as variable "sch_uid"

  Scenario: Get a List of case scheduler of a project
      Given I request "project/1265557095225ff5c688f46031700471/case-schedulers"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "array"
      And the response has 1 record

  Scenario: Get a single case scheduler of a project
      Given that I want to get a resource with the key "sch_uid" stored in session array
      And I request "project/1265557095225ff5c688f46031700471/case-scheduler"
      Then the response status code should be 200
      And the content type is "application/json"
      And the type is "object"

  Scenario: Update a case scheduler for a project
    Given PUT this data:
      """
      {
          "sch_name": "Test scheduler #1 modify",
          "sch_del_user_name": "admin",
          "tas_uid": "46941969352af5be2ab3f39001216717",
          "sch_start_time": "20:00",
          "sch_start_date": "2014-02-01",
          "sch_end_date": "2014-02-01",
          "sch_every_days": "",
          "sch_week_days": "",
          "sch_start_day": "1",
          "sch_start_day_opt_1": "2",
          "sch_start_day_opt_2": "",
          "sch_months": "1|6|12",
          "sch_repeat_every": "",
          "sch_repeat_until": "",
          "sch_state": "ACTIVE"
      } 
      """
      And that I want to update a resource with the key "sch_uid" stored in session array
      And I request "project/1265557095225ff5c688f46031700471/case-scheduler"
      And the content type is "application/json"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the type is "object"

  Scenario: Delete a case scheduler of a project
    Given that I want to delete a resource with the key "sch_uid" stored in session array
      And I request "project/1265557095225ff5c688f46031700471/case-scheduler"
      Then the response status code should be 200
      And the response charset is "UTF-8"