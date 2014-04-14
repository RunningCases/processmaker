@ProcessMakerMichelangelo @RestAPI
Feature: Reorder Steps
Requirements:
    a workspace with the process 857888611534814982bc651033834642 ("Ordenamiento Main") already loaded
    there are two activities and eight steps in the process

  Background:
    Given that I have a valid access_token  


  Scenario Outline: obtain the position of the steps of activity Task 1
    Given I request "project/857888611534814982bc651033834642/activity/1816381825348149bce1eb7071800593/step/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "<step_position>"

    Examples:

        | test_description        | step_uid                         | step_position | 
        | Position-dynaform1      | 940208541534815470963a6096949846 | 1             |
        | Position-dynaform2      | 4489324795348154b6bf378055159323 | 2             |
        | Position-dynaform3      | 5029631175348154f92d713030841274 | 3             |
        | Position-dynaform4      | 87462252653481552cee045066635556 | 4             |
        | Position-input1         | 72197903553481555d114d6063884645 | 5             |
        | Position-input2         | 12394884153481558997b21066123068 | 6             |
        

  Scenario: Change order the step of "dynaform3" by position five
    Given PUT this data:
    """
    {
        "step_position": "5"
    }
    """
    And I request "project/857888611534814982bc651033834642/activity/1816381825348149bce1eb7071800593/step/5029631175348154f92d713030841274"
    Then the response status code should be 200

  
  Scenario Outline: Obtain the position of the steps after changing position
    Given I request "project/857888611534814982bc651033834642/activity/7976552835322023005e069088446535/step/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "<step_position>"

    Examples:

        | test_description        | step_uid                         | step_position | 
        | Position-dynaform1      | 940208541534815470963a6096949846 | 1             |
        | Position-dynaform2      | 4489324795348154b6bf378055159323 | 2             |
        | Position-dynaform4      | 87462252653481552cee045066635556 | 3             |
        | Position-input1         | 72197903553481555d114d6063884645 | 4             |
        | Position-dynaform3      | 5029631175348154f92d713030841274 | 5             |
        | Position-input2         | 12394884153481558997b21066123068 | 6             |

 

 Scenario: Change order the step of "input2"= position 6 by position two
    Given PUT this data:
    """
    {
        "step_position": "2"
    }
    """
    And I request "project/857888611534814982bc651033834642/activity/1816381825348149bce1eb7071800593/step/12394884153481558997b21066123068"
    Then the response status code should be 200

  
  Scenario Outline: Obtain the position of the steps after changing position
    Given I request "project/857888611534814982bc651033834642/activity/1816381825348149bce1eb7071800593/step/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "<step_position>"

    Examples:

        | test_description        | step_uid                         | step_position | 
        | Position-dynaform1      | 940208541534815470963a6096949846 | 1             |
        | Position-input2         | 12394884153481558997b21066123068 | 2             |
        | Position-dynaform2      | 4489324795348154b6bf378055159323 | 3             |
        | Position-dynaform4      | 87462252653481552cee045066635556 | 4             |
        | Position-input1         | 72197903553481555d114d6063884645 | 5             |
        | Position-dynaform3      | 5029631175348154f92d713030841274 | 6             |


Scenario: Change order the step of "input2"= position 2 by position six
    Given PUT this data:
    """
    {
        "step_position": "6"
    }
    """
    And I request "project/857888611534814982bc651033834642/activity/1816381825348149bce1eb7071800593/step/12394884153481558997b21066123068"
    Then the response status code should be 200

  
  Scenario Outline: Obtain the position of the steps after changing position
    Given I request "project/857888611534814982bc651033834642/activity/1816381825348149bce1eb7071800593/step/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "<step_position>"

    Examples:

        | test_description        | step_uid                         | step_position | 
        | Position-dynaform1      | 940208541534815470963a6096949846 | 1             |
        | Position-dynaform2      | 4489324795348154b6bf378055159323 | 2             |
        | Position-dynaform4      | 87462252653481552cee045066635556 | 3             |
        | Position-input1         | 72197903553481555d114d6063884645 | 4             |
        | Position-dynaform3      | 5029631175348154f92d713030841274 | 5             |
        | Position-input2         | 12394884153481558997b21066123068 | 6             |


Scenario: Change order the step of "dynaform3"= position 5 by position 3
    Given PUT this data:
    """
    {
        "step_position": "3"
    }
    """
    And I request "project/857888611534814982bc651033834642/activity/1816381825348149bce1eb7071800593/step/5029631175348154f92d713030841274"
    Then the response status code should be 200

  
  Scenario Outline: Obtain the position of the steps after changing position
    Given I request "project/857888611534814982bc651033834642/activity/1816381825348149bce1eb7071800593/step/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "step_position" is set to "<step_position>"

    Examples:

        | test_description        | step_uid                         | step_position | 
        | Position-dynaform1      | 940208541534815470963a6096949846 | 1             |
        | Position-dynaform2      | 4489324795348154b6bf378055159323 | 2             |
        | Position-dynaform3      | 5029631175348154f92d713030841274 | 3             |
        | Position-dynaform4      | 87462252653481552cee045066635556 | 4             |
        | Position-input1         | 72197903553481555d114d6063884645 | 5             |
        | Position-input2         | 12394884153481558997b21066123068 | 6             |