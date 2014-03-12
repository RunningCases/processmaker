@ProcessMakerMichelangelo @RestAPI
Feature: Process Permissions Resources Tests
    Requirements:
    a workspace with the process 67021149152e27240dc54d2095572343 ("Test Process Permissions") already loaded
    there are zero Process Permissions in the process

    Background:
      Given that I have a valid access_token

    Scenario: Get a List of current Process Permissions of a project
       Given I request "project/67021149152e27240dc54d2095572343/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record


    Scenario Outline: Create a new Process permission 
      Given POST this data:
            """
            {
                "op_case_status": "<op_case_status>",
                "tas_uid": "<tas_uid>",
                "op_user_relation": "<op_user_relation>",
                "usr_uid": "<usr_uid>",
                "op_task_source" : "<op_task_source>",
                "op_participate": "<op_participate>",
                "op_obj_type": "<op_obj_type>",
                "dynaforms" : "<dynaforms>",
                "inputs" : "<inputs>",
                "outputs" : "<outputs>",                
                "op_action": "<op_action>"
            }
            """
        And I request "project/67021149152e27240dc54d2095572343/process-permission"
        Then the response status code should be 201
        And store "op_uid" in session array
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And store "op_uid" in session array as variable "op_uid_<op_number>"

         Examples:

        | test_description                                               | op_number | op_case_status  | tas_uid                          | op_user_relation| usr_uid                          | op_task_source                   | op_participate | op_obj_type  | dynaforms                        | inputs                           | outputs                          | op_action | 
        | Create with Status Case All and type All in task 1             | 1         | ALL             |                                  | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case All and type Dynaform in task 1        | 2         | ALL             |                                  | 1               | 25286582752d56713231082039265791 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Create with Status Case All and type Input in task 1           | 3         | ALL             |                                  | 2               | 54731929352d56741de9d42002704749 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Create with Status Case All and type Output in task 1          | 4         | ALL             |                                  | 1               | 32444503652d5671778fd20059078570 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Create with Status Case All and type Case Note in task 1       | 5         | ALL             |                                  | 1               | 16333273052d567284e6766029512960 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     | 
        | Create with Status Case All and type Messages in task 1        | 6         | ALL             |                                  | 1               | 34289569752d5673d310e82094574281 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type All in task 2           | 7         | DRAFT           | 55416900252e272492318b9024750146 | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Draft and type Dynaform in task 2      | 8         | DRAFT           | 55416900252e272492318b9024750146 | 1               | 11206717452d5673913aa69053050085 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type Input in task 2         | 9         | DRAFT           | 55416900252e272492318b9024750146 | 2               | 21092802152d569a2e32b18087204577 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Create with Status Case Draft and type Output in task 2        | 10        | DRAFT           | 55416900252e272492318b9024750146 | 1               | 14093514252d56720bff5b4038518272 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Create with Status Case Draft and type Case Note in task 2     | 11        | DRAFT           | 55416900252e272492318b9024750146 | 1               | 19834612352d5673c73ea89076646062 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type Messages in task 2      | 12        | DRAFT           | 55416900252e272492318b9024750146 | 2               | 89064231952d567452ea008014804965 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type All in task 1           | 13        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case To Do and type Dynaform in task 1      | 14        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 36116269152d56733b20e86062657385 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type Input in task 1         | 15        | TO_DO           | 36792129552e27247a483f6069605623 | 2               | 66623507552d56742865613066097298 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Create with Status Case To Do and type Output in task 1        | 16        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 38102442252d5671a629009013495090 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Create with Status Case To Do and type Case Note in task 1     | 17        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 44114647252d567264eb9e4061647705 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type Messages in task 1      | 18        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 44811996752d567110634a1013636964 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type All in task 2          | 19        | PAUSED          | 55416900252e272492318b9024750146 | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Paused and type Dynaform in task 2     | 20        | PAUSED          | 55416900252e272492318b9024750146 | 1               | 50011635952d5673246a575079973262 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 51960945752e280ce802ce7007126361 |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type Input in task 2        | 21        | PAUSED          | 55416900252e272492318b9024750146 | 2               | 81572528952d5673de56fa9048605800 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 61273332352e28125254f97072882826 |                                  | BLOCK     |
        | Create with Status Case Paused and type Output in task 2       | 22        | PAUSED          | 55416900252e272492318b9024750146 | 1               | 50562411252d5671e788c02016273245 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56977080352e281696ead88064880692 | BLOCK     |           
        | Create with Status Case Paused and type Case Note in task 2    | 23        | PAUSED          | 55416900252e272492318b9024750146 | 1               | 50912153352d5673b0b7e42000221953 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type Messages in task 2     | 24        | PAUSED          | 55416900252e272492318b9024750146 | 2               | 13028697852d5674745cb64005883338 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type All in task 3       | 25        | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 00000000000000000000000000000001 | 55416900252e272492318b9024750146 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Completed and type Dynaform in task 3  | 26        | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 62511352152d5673bba9cd4062743508 | 55416900252e272492318b9024750146 | 1              | DYNAFORM     | 51960945752e280ce802ce7007126361 |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type Input in task 3     | 27        | COMPLETED       | 64296230152e2724a8b3589070508795 | 2               | 46520967652d56747f384f5069459364 | 55416900252e272492318b9024750146 | 0              | INPUT        |                                  | 61273332352e28125254f97072882826 |                                  | BLOCK     |
        | Create with Status Case Completed and type Output in task 3    | 28        | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 69125570352d56720061f83026430750 | 55416900252e272492318b9024750146 | 1              | OUTPUT       |                                  |                                  | 56977080352e281696ead88064880692 | BLOCK     |           
        | Create with Status Case Completed and type Case Note in task 3 | 29        | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 69578334752d5672aabb946025792134 | 55416900252e272492318b9024750146 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type Messages in task 3  | 30        | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 73005191052d56727901138030694610 | 55416900252e272492318b9024750146 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |



    Scenario: Get a List of current Process Permissions of a project when there are exactly 30 Process Permissions
       Given I request "project/67021149152e27240dc54d2095572343/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 30 records


    Scenario Outline: Get a Single Process Permissions of a project when the Process Permissions is previously created
      
        Given that I want to get a resource with the key "op_uid" stored in session array as variable "op_uid_<op_number>"
        And I request "project/67021149152e27240dc54d2095572343/process-permission"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And the "usr_uid" property equals "<usr_uid>"
        And the "op_user_relation" property equals "<op_user_relation>"
        And the "op_obj_type" property equals "<op_obj_type>"

         Examples:

        | test_description                                               | op_number | op_case_status  | tas_uid                          | op_user_relation| usr_uid                          | op_task_source                   | op_participate | op_obj_type  | dynaforms                        | inputs                           | outputs                          | op_action | 
        | Create with Status Case All and type All in task 1             | 1         | ALL             |                                  | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case All and type Dynaform in task 1        | 2         | ALL             |                                  | 1               | 25286582752d56713231082039265791 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Create with Status Case All and type Input in task 1           | 3         | ALL             |                                  | 2               | 54731929352d56741de9d42002704749 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Create with Status Case All and type Output in task 1          | 4         | ALL             |                                  | 1               | 32444503652d5671778fd20059078570 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Create with Status Case All and type Case Note in task 1       | 5         | ALL             |                                  | 1               | 16333273052d567284e6766029512960 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     | 
        | Create with Status Case All and type Messages in task 1        | 6         | ALL             |                                  | 1               | 34289569752d5673d310e82094574281 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type All in task 2           | 7         | DRAFT           | 55416900252e272492318b9024750146 | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Draft and type Dynaform in task 2      | 8         | DRAFT           | 55416900252e272492318b9024750146 | 1               | 11206717452d5673913aa69053050085 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type Input in task 2         | 9         | DRAFT           | 55416900252e272492318b9024750146 | 2               | 21092802152d569a2e32b18087204577 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Create with Status Case Draft and type Output in task 2        | 10        | DRAFT           | 55416900252e272492318b9024750146 | 1               | 14093514252d56720bff5b4038518272 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Create with Status Case Draft and type Case Note in task 2     | 11        | DRAFT           | 55416900252e272492318b9024750146 | 1               | 19834612352d5673c73ea89076646062 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type Messages in task 2      | 12        | DRAFT           | 55416900252e272492318b9024750146 | 2               | 89064231952d567452ea008014804965 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type All in task 1           | 13        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case To Do and type Dynaform in task 1      | 14        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 36116269152d56733b20e86062657385 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type Input in task 1         | 15        | TO_DO           | 36792129552e27247a483f6069605623 | 2               | 66623507552d56742865613066097298 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Create with Status Case To Do and type Output in task 1        | 16        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 38102442252d5671a629009013495090 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Create with Status Case To Do and type Case Note in task 1     | 17        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 44114647252d567264eb9e4061647705 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type Messages in task 1      | 18        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 44811996752d567110634a1013636964 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type All in task 2          | 19        | PAUSED          | 55416900252e272492318b9024750146 | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Paused and type Dynaform in task 2     | 20        | PAUSED          | 55416900252e272492318b9024750146 | 1               | 50011635952d5673246a575079973262 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 51960945752e280ce802ce7007126361 |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type Input in task 2        | 21        | PAUSED          | 55416900252e272492318b9024750146 | 2               | 81572528952d5673de56fa9048605800 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 61273332352e28125254f97072882826 |                                  | BLOCK     |
        | Create with Status Case Paused and type Output in task 2       | 22        | PAUSED          | 55416900252e272492318b9024750146 | 1               | 50562411252d5671e788c02016273245 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56977080352e281696ead88064880692 | BLOCK     |           
        | Create with Status Case Paused and type Case Note in task 2    | 23        | PAUSED          | 55416900252e272492318b9024750146 | 1               | 50912153352d5673b0b7e42000221953 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type Messages in task 2     | 24        | PAUSED          | 55416900252e272492318b9024750146 | 2               | 13028697852d5674745cb64005883338 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type All in task 3       | 25        | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 00000000000000000000000000000001 | 55416900252e272492318b9024750146 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Completed and type Dynaform in task 3  | 26        | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 62511352152d5673bba9cd4062743508 | 55416900252e272492318b9024750146 | 1              | DYNAFORM     | 51960945752e280ce802ce7007126361 |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type Input in task 3     | 27        | COMPLETED       | 64296230152e2724a8b3589070508795 | 2               | 46520967652d56747f384f5069459364 | 55416900252e272492318b9024750146 | 0              | INPUT        |                                  | 61273332352e28125254f97072882826 |                                  | BLOCK     |
        | Create with Status Case Completed and type Output in task 3    | 28        | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 69125570352d56720061f83026430750 | 55416900252e272492318b9024750146 | 1              | OUTPUT       |                                  |                                  | 56977080352e281696ead88064880692 | BLOCK     |           
        | Create with Status Case Completed and type Case Note in task 3 | 29        | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 69578334752d5672aabb946025792134 | 55416900252e272492318b9024750146 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type Messages in task 3  | 30        | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 73005191052d56727901138030694610 | 55416900252e272492318b9024750146 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |



    Scenario Outline: Update the Process Permission and then check if the values had changed 
      Given PUT this data:
            """
            {
                "tas_uid": "<tas_uid>",
                "usr_uid": "<usr_uid>",
                "op_user_relation": "<op_user_relation>",
                "op_obj_type": "<op_obj_type>",
                "op_task_source" : "<op_task_source>",
                "op_participate": "<op_participate>",
                "op_action": "<op_action>",
                "op_case_status": "<op_case_status>"
            }
            """
        

        And that I want to update a resource with the key "op_uid" stored in session array as variable "op_uid_<op_number>"
        And I request "project/67021149152e27240dc54d2095572343/process-permission"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
      

         Examples:
        
        | test_description                                               | op_number | op_case_status  | tas_uid                          | op_user_relation| usr_uid                          | op_task_source                   | op_participate | op_obj_type  | op_action | 
        | Update Status Case Completed and type All in task 3            | 6         | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 00000000000000000000000000000001 | 55416900252e272492318b9024750146 | 1              | ANY          | VIEW      |
        | Update Status Case Paused and type Messages in task 2          | 7         | PAUSED          | 55416900252e272492318b9024750146 | 2               | 81090718052d567492e1852081697260 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY | BLOCK     |
        | Update Status Case Paused and type All in task 2               | 12        | PAUSED          | 55416900252e272492318b9024750146 | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          | VIEW      |
        | Update Status Case To Do and type Messages in task 1           | 13        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 44811996752d567110634a1013636964 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY | BLOCK     |
        


    Scenario Outline: Get a Single Process Permissions and check some properties
      
        
        Given that I want to get a resource with the key "op_uid" stored in session array as variable "op_uid_<op_number>"
        And I request "project/67021149152e27240dc54d2095572343/process-permission"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And the "op_case_status" property equals "<op_case_status>"
        And the "tas_uid" property equals "<tas_uid>"
        And the "op_user_relation" property equals "<op_user_relation>"
        And the "usr_uid" property equals "<usr_uid>"
        And the "op_task_source" property equals "<op_task_source>"
        And the "op_participate" property equals "<op_participate>"
        And the "op_obj_type" property equals "<op_obj_type>"
        And the "op_action" property equals "<op_action>"
        

        Examples:

        | test_description                                               | op_number | op_case_status  | tas_uid                          | op_user_relation| usr_uid                          | op_task_source                   | op_participate | op_obj_type  | op_action | 
        | Update Status Case Completed and type All in task 3            | 6         | COMPLETED       | 64296230152e2724a8b3589070508795 | 1               | 00000000000000000000000000000001 | 55416900252e272492318b9024750146 | 1              | ANY          | VIEW      |
        | Update Status Case Paused and type Messages in task 2          | 7         | PAUSED          | 55416900252e272492318b9024750146 | 2               | 81090718052d567492e1852081697260 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY | BLOCK     |
        | Update Status Case Paused and type All in task 2               | 12        | PAUSED          | 55416900252e272492318b9024750146 | 1               | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          | VIEW      |
        | Update Status Case To Do and type Messages in task 1           | 13        | TO_DO           | 36792129552e27247a483f6069605623 | 1               | 44811996752d567110634a1013636964 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY | BLOCK     |

    
    Scenario Outline: Delete all Process Supervisor created previously in this script   
      Given that I want to delete a resource with the key "op_uid" stored in session array as variable "op_uid_<op_number>"
        And I request "project/67021149152e27240dc54d2095572343/process-permission"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        
        Examples:

        | op_number  |         
        | 1          |
        | 2          |
        | 3          |
        | 4          |
        | 5          |
        | 6          |
        | 7          |
        | 8          |
        | 9          |
        | 10         |
        | 11         |
        | 12         |
        | 13         |
        | 14         |
        | 15         |
        | 16         |
        | 17         |
        | 18         |
        | 19         |
        | 20         |
        | 21         |
        | 22         |
        | 23         |
        | 24         |
        | 25         |
        | 26         |
        | 27         |
        | 28         |
        | 29         |
        | 30         |
            
    Scenario: Get a List of current Process Permissions of a project
       Given I request "project/67021149152e27240dc54d2095572343/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 0 record