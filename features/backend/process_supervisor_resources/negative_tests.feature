@ProcessMakerMichelangelo @RestAPI
Feature: Process Supervisor Negative tests

  Background:
    Given that I have a valid access_token

  Scenario Outline: Assign a user and a group to a process supervisor for a project with bad parameters (negative tests)
    Given POST this data:
      """
       {
           "pu_type": "<pu_type>",
           "usr_uid": "<usr_uid>"
       }
       """
       And I request "project/<project>/process-supervisor"
       Then the response status code should be <error_code>
       And the response status message should have the following text "<error_message>"

       Examples:
       | test_description             | project                          | pu_type                         | usr_uid                          | error_code | error_message |
       | Invalid pu_type			        | 85794888452ceeef3675164057928956 | GROUP_USER_DEPARTAMENT          | 46138556052cda43a051110007756836 | 400        | pu_type       |
       | Invalid usr_uid              | 85794888452ceeef3675164057928956 | SUPERVISOR                      | 0000000000000gtr@#$0000000000001 | 400        | id            |     
       | Field requered project		    | 								                 | SUPERVISOR                      | 00000000000000000000000000000001 | 400        | prjUid        |
       | Field requered pu_type		    | 85794888452ceeef3675164057928956 |                                 | 00000000000000000000000000000001 | 400        | pu_type       |
       | Field requered urs_uid		    | 85794888452ceeef3675164057928956 | SUPERVISOR                      |                                  | 400        | usr_uid       |

      
  Scenario Outline: Assign a dynaform to a process supervisor for a project with bad parameters (negative tests)
        Given POST this data:
        """
       {
            "dyn_uid": "<dyn_uid>"
       }
       """
       And I request "project/<project>/process-supervisor/dynaform"
       Then the response status code should be <error_code>
       And the response status message should have the following text "<error_message>"


       Examples:
       | test_description                 | project                          | dyn_uid                          | error_code | error_message | 
       | Invalid dyn_uid                  | 85794888452ceeef3675164057928956 | 2ceeef36751640grgrtgrg#$%#%#     | 400        | dyn_uid       |   
       | Field requered project		        | 								                 | 78212661352ceef2dc4e987081647602 | 400        | prjUid        |
       | Field requered dyn_uid		        | 85794888452ceeef3675164057928956 | 78212661352ceef2dc4e987081640002 | 400        | dyn_uid       |


  Scenario Outline: Assign a Input Document to a process supervisor for a project with bad parameters (negative tests)
        Given POST this data:
        """
       {
            "dyn_uid": "<inp_doc_uid>"
       }
       """
       And I request "project/<project>/process-supervisor/input-document"
       Then the response status code should be <error_code>
       And the response status message should have the following text "<error_message>"


       Examples:
       | test_description                 | project                          | inp_doc_uid                      | error_code | error_message | 
       | Invalid inp_doc_uid              | 85794888452ceeef3675164057928956 | 25205290452ceef5@#$41c3067266323 | 400        | inp_doc_uid   |   
       | Field requered project		        | 					                			 | 25205290452ceef570741c3067266323 | 400        | prjUid        |
       | Field requered inp_doc_uid	      | 85794888452ceeef3675164057928956 |                                  | 400        | inp_doc_uid   |