@ProcessMakerMichelangelo @RestAPI
Feature: Reorder Process Supervisor - Dynaform and Input
Requirements:
    a workspace with the process 857888611534814982bc651033834642 ("Ordenamiento Main") already loaded
    there are two activities and eight steps in the process

  Background:
    Given that I have a valid access_token  


  Scenario Outline: obtain the position of dynaform in process supervisor
    Given I request "project/857888611534814982bc651033834642/process-supervisor/dynaform/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "pud_position" is set to "<pud_position>"

    Examples:

        | test_description        | step_uid                         | pud_position | 
        | Position-dynaform1      | 674032139534bd91f9331a5032066933 | 1            |
        | Position-dynaform2      | 583050735534bd923984f24007464958 | 2            |
        | Position-dynaform3      | 114660532534bd926817991070085867 | 3            |
        | Position-dynaform4      | 105517492534bd929a58c15055718131 | 4            |
                

  Scenario Outline: Change order the dynaform of "dynaform3" by position one
    Given PUT this data:
    """
    {
        "pud_position": "1"
    }
    """
    And I request "project/857888611534814982bc651033834642/process-supervisor/dynaform/<step_uid>"
    Then the response status code should be 200

    Examples:

        | test_description        | step_uid                         | pud_position | 
        | Position-dynaform1      | 674032139534bd91f9331a5032066933 | 2            |

  
  Scenario Outline: Obtain the position of the steps after changing position
    Given I request "project/857888611534814982bc651033834642/process-supervisor/dynaform/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "pud_position" is set to "<pud_position>"

    Examples:

        | test_description        | step_uid                         | pud_position  | 
        | Position-dynaform1      | 674032139534bd91f9331a5032066933 | 2             |
        | Position-dynaform2      | 583050735534bd923984f24007464958 | 3             |
        | Position-dynaform3      | 114660532534bd926817991070085867 | 1             |
        | Position-dynaform4      | 105517492534bd929a58c15055718131 | 4             |
        
  

  Scenario Outline: Assign a dynaform to a process supervisor
        Given POST this data:
        """
       {
            "dyn_uid": "<dyn_uid>"
       }
       """
       And I request "project/857888611534814982bc651033834642/process-supervisor/dynaform"
       Then the response status code should be 201
       And the response charset is "UTF-8"
       And the content type is "application/json"
       And the type is "object"
       And store "pud_uid" in session array as variable "pud_uid_<pud_number>"


       Examples:
       | test_description                  | pud_number       | dyn_uid                          |  
       | Assign a dynaform5 for Supervisor | 1                | 856447360534bdeab3c4a72086906269 |       


  Scenario Outline: Obtain the position of the steps after add new dynaform
    Given I request "project/857888611534814982bc651033834642/process-supervisor/dynaform/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "pud_position" is set to "<pud_position>"

    Examples:

        | test_description        | step_uid                         | pud_position  | 
        | Position-dynaform1      | 674032139534bd91f9331a5032066933 | 2             |
        | Position-dynaform2      | 583050735534bd923984f24007464958 | 3             |
        | Position-dynaform3      | 114660532534bd926817991070085867 | 1             |
        | Position-dynaform4      | 105517492534bd929a58c15055718131 | 4             |
        
  
  Scenario Outline: Delete an dynaform5 to a process supervisor
       Given that I want to delete a resource with the key "pui_uid" stored in session array as variable "pud_uid_<pud_number>"
       And I request "project/<project>/process-supervisor/dynaform"
       Then the response status code should be 200
       And the response charset is "UTF-8"
       

       Examples:
       | test_description                | project                          | pud_number       |
       | Delete dynaform5 for Supervisor | 856447360534bdeab3c4a72086906269 | 1                |


  
  Scenario Outline: Obtain the position of the dynaforms after changing position
    Given I request "project/857888611534814982bc651033834642/process-supervisor/dynaform/<step_uid>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"
    And that "pud_position" is set to "<pud_position>"

    Examples:

        | test_description        | step_uid                         | pud_position  | 
        | Position-dynaform1      | 674032139534bd91f9331a5032066933 | 1             |
        | Position-dynaform2      | 583050735534bd923984f24007464958 | 2             |
        | Position-dynaform3      | 114660532534bd926817991070085867 | 3             |
        | Position-dynaform4      | 105517492534bd929a58c15055718131 | 4             |



Scenario Outline: obtain the position of Input Documents in process supervisor
    Given I request "project/857888611534814982bc651033834642/process-supervisors"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And that "pud_position" is set to "<pud_position>"

    Examples:

        | test_description | step_uid                         | pud_position | 
        | Position-Input1  | 152101193534bdfdb530c96029300152 | 1            |
        | Position-Input2  | 370888112534bdfde887e79055241597 | 2            |
        | Position-Input3  | 392695739534bdfe1542bb4007328326 | 3            |
        


  Scenario: Change order the Input document of "Input3" by position one
    Given PUT this data:
    """
    {
        "pud_position": "1"
    }
    """
    And I request "project/857888611534814982bc651033834642/process-supervisor/input-document/519852825534bdf430c81e5083980052"
    Then the response status code should be 200
                   
  
  Scenario Outline: Obtain the position of the steps after changing position
    Given I request "project/857888611534814982bc651033834642/process-supervisors"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And that "pud_position" is set to "<pud_position>"

    Examples:

        | test_description | step_uid                         | pud_position | 
        | Position-Input1  | 152101193534bdfdb530c96029300152 | 2            |
        | Position-Input2  | 370888112534bdfde887e79055241597 | 3            |
        | Position-Input3  | 392695739534bdfe1542bb4007328326 | 1            |


  Scenario: Change order the Input document of "Input3" by position one
    Given PUT this data:
    """
    {
        "pud_position": "3"
    }
    """
    And I request "project/857888611534814982bc651033834642/process-supervisor/input-document/519852825534bdf430c81e5083980052"
    Then the response status code should be 200

  
  Scenario Outline: Obtain the position of the steps after changing position
    Given I request "project/857888611534814982bc651033834642/process-supervisors"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And that "pud_position" is set to "<pud_position>"

    Examples:

        | test_description | step_uid                         | pud_position | 
        | Position-Input1  | 152101193534bdfdb530c96029300152 | 1            |
        | Position-Input2  | 370888112534bdfde887e79055241597 | 2            |
        | Position-Input3  | 392695739534bdfe1542bb4007328326 | 3            |