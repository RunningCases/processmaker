@ProcessMakerMichelangelo @RestAPI
Feature: Process supervisor Resources
Requirements:
    a workspace with the process 85794888452ceeef3675164057928956 ("Test Process Supervisor") already loaded
    there are zero supervisor, dynaform and input document in the Process Supervisor of process


    Background:
      Given that I have a valid access_token

    Scenario Outline: Get a List of current process supervisors of a project
      Given I request "project/<project>/process-supervisors"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "array"
      And the response has <records> records
      
      Examples:
      | test_description                       | project                          | records |
      | List current unique process supervisor | 85794888452ceeef3675164057928956 | 1       |


    Scenario Outline: Get a List of available process supervisor of a project
      Given I request "project/<project>/available-process-supervisors"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "array"
      And the response has <records> records
      
      Examples:
      | test_description                                  | project                          | records |
      | List users and groups available to be supervisors | 85794888452ceeef3675164057928956 | 20      |


    Scenario Outline: Get a List of available groups process supervisor of a project
      Given I request "project/<project>/available-process-supervisors?obj_type=group"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "array"
      And the response has <records> records
      
      Examples:
      | test_description                               | project                          | records |
      | List the 23 groups available to be supervisors | 85794888452ceeef3675164057928956 | 19      |

    
    Scenario Outline: Get a List of available users elegible as process supervisor
      Given I request "project/<project>/available-process-supervisors?obj_type=user"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "array"
      And the response has <records> records
      
      Examples:
      | test_description                                      | project                          | records |
      | List the unique admin user available to be supervisor | 85794888452ceeef3675164057928956 | 1       |



    Scenario Outline: Get a specific process supervisor details of a project
      Given I request "project/<project>/process-supervisor/<pu_uid>"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "object"
      
    Examples:
      | test_description           | project                          | pu_uid                           |
      | Get the supervisor details | 85794888452ceeef3675164057928956 | 45562646852fa827abb79a5097602637 |

 
    Scenario Outline: Get a List of dynaforms assigned to a process supervisor
      Given I request "project/<project>/process-supervisor/dynaforms"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "array"
      And the response has <records> records
      
      Examples:
      | test_description                          | project                          | records |
      | List the 2 pre-assigned dynaforms #1 & #2 | 85794888452ceeef3675164057928956 | 2       |

    
     Scenario Outline: Get a specific dynaform detail assigned to a process supervisor
        Given I request "project/<project>/process-supervisor/dynaform/<pud_uid>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        
      Examples:
      | test_description                           | project                          | pud_uid                          |
      | Get details of the first assigend dynaform | 85794888452ceeef3675164057928956 | 56779160652cef174108c76074755720 |
        

    
     Scenario Outline: Get a List of available dynaforms to be assigned to a process supervisor
        Given I request "project/<project>/process-supervisor/available-dynaforms"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has <records> records
        
        Examples:
      | test_description                                 | project                          | records |
      | Get a list of available dynaforms to be assigned | 85794888452ceeef3675164057928956 | 1       |

    
    Scenario Outline: Get a List of assigend input-documents to a process supervisor
        Given I request "project/<project>/process-supervisor/input-documents"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has <records> records
        
        Examples:
      | test_description                           | project                          | records |
      | Get a list of pre-assigned input doucments | 85794888452ceeef3675164057928956 | 2       |

    
    Scenario Outline: Get a List of available input-documents to be assigned to a process supervisor
        Given I request "project/<project>/process-supervisor/available-input-documents"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has <records> records
        
        Examples:
      | test_description                                     | project                          | records |
      | Get a list of 1 record of available input documents  | 85794888452ceeef3675164057928956 | 1       |

    
    Scenario Outline: Get a specific input document assigned to a process supervisor
        Given I request "project/<project>/process-supervisor/input-document/<pui_uid>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        
        Examples:
      | test_description                       | project                          | pui_uid                          |
      | Get details of assigend input document | 85794888452ceeef3675164057928956 | 64558052052d8a715de8936029381436 |

    
    Scenario Outline: Assign a user and a group as process supervisors
        Given POST this data:
        """
       {
           "pu_type": "<pu_type>",
           "usr_uid": "<usr_uid>"
       }
       """
       And I request "project/<project>/process-supervisor"
       Then the response status code should be 201
       And the response charset is "UTF-8"
       And the content type is "application/json"
       And the type is "object"
       And store "pu_uid" in session array as variable "pu_uid_<pu_number>"

       Examples:
       | test_description             | project                          | pu_number        | pu_type                         | usr_uid                          |   
       | Assign a group as Supervisor | 85794888452ceeef3675164057928956 | 1                | GROUP_SUPERVISOR                | 54731929352d56741de9d42002704749 |
       | Assign a user as Supervisor  | 85794888452ceeef3675164057928956 | 2                | SUPERVISOR                      | 00000000000000000000000000000001 |
    

     Scenario: Assign a supervisor process when it was already assigned 
        Given POST this data:
        """
       {
           "pu_type": "SUPERVISOR",
           "usr_uid": "00000000000000000000000000000001"
       }
       """
       And I request "project/85794888452ceeef3675164057928956/process-supervisor"
       Then the response status code should be 400
       And the response status message should have the following text "already exist"


    
    Scenario Outline: Assign a dynaform to a process supervisor
        Given POST this data:
        """
       {
            "dyn_uid": "<dyn_uid>"
       }
       """
       And I request "project/<project>/process-supervisor/dynaform"
       Then the response status code should be 201
       And the response charset is "UTF-8"
       And the content type is "application/json"
       And the type is "object"
       And store "pud_uid" in session array as variable "pud_uid_<pud_number>"


       Examples:
       | test_description                     | project                          | pud_number       | dyn_uid                          |  
       | Assign a dynaform # 3 for Supervisor | 85794888452ceeef3675164057928956 | 1                | 92562207752ceef36c7d874048012431 |


    Scenario: Assign a dynaform to a process supervisor when it was already assigned
        Given POST this data:
        """
       {
            "dyn_uid": "92562207752ceef36c7d874048012431"
       }
       """
       And I request "project/85794888452ceeef3675164057928956/process-supervisor/dynaform"
       Then the response status code should be 400
       And the response status message should have the following text "already exist"


    
    Scenario Outline: Assign an input document to a process supervisor
        Given POST this data:
        """
       {
            "inp_doc_uid": "<inp_doc_uid>"
       }
       """
       And I request "project/<project>/process-supervisor/input-document"
       Then the response status code should be 201
       And the response charset is "UTF-8"
       And the content type is "application/json"
       And the type is "object"
       And store "pui_uid" in session array as variable "pui_inpdoc_uid_<dps_number>"

       
      
      Examples:
       | test_description                        | project                          | dps_number       | inp_doc_uid                      | 
       | Assign an Input document for Supervisor | 85794888452ceeef3675164057928956 | 1                | 54550354652ceef5e4e1c17096955890 |  

    
    Scenario Outline: Delete a process supervisor
        Given that I want to delete a resource with the key "pu_uid" stored in session array as variable "pu_uid_<pu_number>"
        And I request "project/<project>/process-supervisor"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

        Examples:
        | test_description                  | project                          | pu_number |
        | Remove first assigned supervisor  | 85794888452ceeef3675164057928956 | 1         |
        | Remove second assigned supervisor | 85794888452ceeef3675164057928956 | 2         |

    
       
    Scenario Outline: Delete a input-document process supervisor of a project
        Given that I want to delete a resource with the key "pui_uid" stored in session array as variable "pui_inpdoc_uid_<dps_number>"
        And I request "project/85794888452ceeef3675164057928956/process-supervisor/input-document"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        
        Examples:
       | test_description                        | project                          | dps_number       |
       | Assign an Input document for Supervisor | 85794888452ceeef3675164057928956 | 1                | 


     Scenario Outline: Delete an dynaform to a process supervisor
       Given that I want to delete a resource with the key "pui_uid" stored in session array as variable "pud_uid_<pud_number>"
       And I request "project/<project>/process-supervisor/dynaform"
       Then the response status code should be 200
       And the response charset is "UTF-8"
       

       Examples:
       | test_description                   | project                          | pud_number       |
       | Delete dynaform # 3 for Supervisor | 85794888452ceeef3675164057928956 | 1                |
