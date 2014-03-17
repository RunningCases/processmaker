@ProcessMakerMichelangelo @RestAPI
Feature: Reorder Steps
Requirements:
    a workspace with the process 7557786515322022952dcc8014985410 ("Ordenamiento") already loaded
    there are two activities and eight steps in the process

  Background:
    Given that I have a valid access_token  


  Scenario Outline: obtain the position of the steps
    Given I request "project/7557786515322022952dcc8014985410/activity/7976552835322023005e069088446535/step/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "<step_position>"

    Examples:

        | test_description        | step_uid                         | step_position | 
        | Position-dynaform1      | 8257746325322026c0e45e3047837732 | 1             |
        | Position-dynaform2      | 30547852753220293960227013371359 | 2             |
        | Position-dynaform3      | 840380819532202d132fb91020992676 | 3             |
        | Position-dynaform4      | 663853222532202eec8a913042063689 | 4             |
        | Position-input1         | 853418037532209018ab711041079957 | 5             |
        | Position-input2         | 5384383215322090e71aef1047228013 | 6             |
        

  Scenario: Change order the step of "dynaform3" by position five
    Given PUT this data:
    """
    {
        "step_position": "5"
    }
    """
    And I request "project/7557786515322022952dcc8014985410/activity/7976552835322023005e069088446535/step/840380819532202d132fb91020992676"
    Then the response status code should be 200

  
  Scenario Outline: Obtain the position of the steps after changing position
    Given I request "project/7557786515322022952dcc8014985410/activity/7976552835322023005e069088446535/step/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "<step_position>"

    Examples:

        | test_description        | step_uid                         | step_position | 
        | Position-dynaform1      | 8257746325322026c0e45e3047837732 | 1             |
        | Position-dynaform2      | 30547852753220293960227013371359 | 2             |
        | Position-dynaform4      | 663853222532202eec8a913042063689 | 3             |
        | Position-input1         | 853418037532209018ab711041079957 | 4             |
        | Position-dynaform3      | 840380819532202d132fb91020992676 | 5             |
        | Position-input2         | 5384383215322090e71aef1047228013 | 6             |

 

 Scenario: Change order the step of "input2"= position 6 by position two
    Given PUT this data:
    """
    {
        "step_position": "2"
    }
    """
    And I request "project/7557786515322022952dcc8014985410/activity/7976552835322023005e069088446535/step/5384383215322090e71aef1047228013"
    Then the response status code should be 200

  
  Scenario Outline: Obtain the position of the steps after changing position
    Given I request "project/7557786515322022952dcc8014985410/activity/7976552835322023005e069088446535/step/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "<step_position>"

    Examples:

        | test_description        | step_uid                         | step_position | 
        | Position-dynaform1      | 8257746325322026c0e45e3047837732 | 1             |
        | Position-input2         | 5384383215322090e71aef1047228013 | 2             |
        | Position-dynaform2      | 30547852753220293960227013371359 | 3             |
        | Position-dynaform4      | 663853222532202eec8a913042063689 | 4             |
        | Position-input1         | 853418037532209018ab711041079957 | 5             |
        | Position-dynaform3      | 840380819532202d132fb91020992676 | 6             |  