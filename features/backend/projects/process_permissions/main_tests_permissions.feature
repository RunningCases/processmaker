@ProcessMakerMichelangelo @RestAPI
Feature: Process Permissions Resources Tests
    Requirements:
    a workspace with the process 67021149152e27240dc54d2095572343 ("Test Process Permissions") already loaded
    there are zero Process Permissions in the process
    and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded

    Background:
      Given that I have a valid access_token

    Scenario Outline: Get a List of current Process Permissions of a project
       Given I request "project/<project>/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <record> record

        Examples:
        | test_description                                                   | project                          | record |
        | List process permissions of the process "Test Process Permissions" | 67021149152e27240dc54d2095572343 | 0      |
        | List process permissions of the process "Process Complete BPMN"    | 1455892245368ebeb11c1a5001393784 | 1      |


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
        And I request "project/<project>/process-permission"
        Then the response status code should be 201
        And store "op_uid" in session array
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And store "op_uid" in session array as variable "op_uid_<op_number>"

        Examples:

        | test_description                                                    | op_number | project                          | op_case_status | tas_uid                          | op_user_relation | usr_uid                          | op_task_source                   | op_participate | op_obj_type  | dynaforms                        | inputs                           | outputs                          | op_action | 
        | Create with Status Case All and type All in task 1             .pm  | 1         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 1                | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case All and type Dynaform in task 1        .pm  | 2         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 1                | 25286582752d56713231082039265791 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Create with Status Case All and type Input in task 1           .pm  | 3         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 2                | 54731929352d56741de9d42002704749 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Create with Status Case All and type Output in task 1          .pm  | 4         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 1                | 32444503652d5671778fd20059078570 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Create with Status Case All and type Case Note in task 1       .pm  | 5         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 1                | 16333273052d567284e6766029512960 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     | 
        | Create with Status Case All and type Messages in task 1        .pm  | 6         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 1                | 34289569752d5673d310e82094574281 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type All in task 2           .pm  | 7         | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 1                | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Draft and type Dynaform in task 2      .pm  | 8         | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 1                | 11206717452d5673913aa69053050085 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type Input in task 2         .pm  | 9         | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 2                | 21092802152d569a2e32b18087204577 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Create with Status Case Draft and type Output in task 2        .pm  | 10        | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 1                | 14093514252d56720bff5b4038518272 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Create with Status Case Draft and type Case Note in task 2     .pm  | 11        | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 1                | 19834612352d5673c73ea89076646062 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type Messages in task 2      .pm  | 12        | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 2                | 89064231952d567452ea008014804965 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type All in task 1           .pm  | 13        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case To Do and type Dynaform in task 1      .pm  | 14        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 36116269152d56733b20e86062657385 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type Input in task 1         .pm  | 15        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 2                | 66623507552d56742865613066097298 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Create with Status Case To Do and type Output in task 1        .pm  | 16        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 38102442252d5671a629009013495090 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Create with Status Case To Do and type Case Note in task 1     .pm  | 17        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 44114647252d567264eb9e4061647705 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type Messages in task 1      .pm  | 18        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 44811996752d567110634a1013636964 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type All in task 2          .pm  | 19        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 1                | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Paused and type Dynaform in task 2     .pm  | 20        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 1                | 50011635952d5673246a575079973262 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 51960945752e280ce802ce7007126361 |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type Input in task 2        .pm  | 21        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 2                | 81572528952d5673de56fa9048605800 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 61273332352e28125254f97072882826 |                                  | BLOCK     |
        | Create with Status Case Paused and type Output in task 2       .pm  | 22        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 1                | 50562411252d5671e788c02016273245 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56977080352e281696ead88064880692 | BLOCK     |           
        | Create with Status Case Paused and type Case Note in task 2    .pm  | 23        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 1                | 50912153352d5673b0b7e42000221953 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type Messages in task 2     .pm  | 24        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 2                | 13028697852d5674745cb64005883338 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type All in task 3       .pm  | 25        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 00000000000000000000000000000001 | 55416900252e272492318b9024750146 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Completed and type Dynaform in task 3  .pm  | 26        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 62511352152d5673bba9cd4062743508 | 55416900252e272492318b9024750146 | 1              | DYNAFORM     | 51960945752e280ce802ce7007126361 |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type Input in task 3     .pm  | 27        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 2                | 46520967652d56747f384f5069459364 | 55416900252e272492318b9024750146 | 0              | INPUT        |                                  | 61273332352e28125254f97072882826 |                                  | BLOCK     |
        | Create with Status Case Completed and type Output in task 3    .pm  | 28        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 69125570352d56720061f83026430750 | 55416900252e272492318b9024750146 | 1              | OUTPUT       |                                  |                                  | 56977080352e281696ead88064880692 | BLOCK     |           
        | Create with Status Case Completed and type Case Note in task 3 .pm  | 29        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 69578334752d5672aabb946025792134 | 55416900252e272492318b9024750146 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type Messages in task 3  .pm  | 30        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 73005191052d56727901138030694610 | 55416900252e272492318b9024750146 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case All and type All in task 1             .pmx | 31        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case All and type Dynaform in task 1        .pmx | 32        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 1                | 25286582752d56713231082039265791 | 6274755055368eed1116388064384542 | 1              | DYNAFORM     | 741536563536be333155026003350943 |                                  |                                  | BLOCK     |
        | Create with Status Case All and type Input in task 1           .pmx | 33        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 2                | 54731929352d56741de9d42002704749 | 6274755055368eed1116388064384542 | 0              | INPUT        |                                  | 880391746536be961e594e7014524130 |                                  | BLOCK     |
        | Create with Status Case All and type Output in task 1          .pmx | 34        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 1                | 32444503652d5671778fd20059078570 | 6274755055368eed1116388064384542 | 1              | OUTPUT       |                                  |                                  | 218529141536be955f0b646092366402 | BLOCK     |           
        | Create with Status Case All and type Case Note in task 1       .pmx | 35        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 1                | 16333273052d567284e6766029512960 | 6274755055368eed1116388064384542 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     | 
        | Create with Status Case All and type Messages in task 1        .pmx | 36        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 1                | 34289569752d5673d310e82094574281 | 6274755055368eed1116388064384542 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type All in task 2           .pmx | 37        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Draft and type Dynaform in task 2      .pmx | 38        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 1                | 11206717452d5673913aa69053050085 | 6274755055368eed1116388064384542 | 1              | DYNAFORM     | 741536563536be333155026003350943 |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type Input in task 2         .pmx | 39        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 2                | 21092802152d569a2e32b18087204577 | 6274755055368eed1116388064384542 | 0              | INPUT        |                                  | 880391746536be961e594e7014524130 |                                  | BLOCK     |
        | Create with Status Case Draft and type Output in task 2        .pmx | 40        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 1                | 14093514252d56720bff5b4038518272 | 6274755055368eed1116388064384542 | 1              | OUTPUT       |                                  |                                  | 218529141536be955f0b646092366402 | BLOCK     |           
        | Create with Status Case Draft and type Case Note in task 2     .pmx | 41        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 1                | 19834612352d5673c73ea89076646062 | 6274755055368eed1116388064384542 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Draft and type Messages in task 2      .pmx | 42        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 2                | 89064231952d567452ea008014804965 | 6274755055368eed1116388064384542 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type All in task 1           .pmx | 43        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case To Do and type Dynaform in task 1      .pmx | 44        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 36116269152d56733b20e86062657385 | 6274755055368eed1116388064384542 | 1              | DYNAFORM     | 741536563536be333155026003350943 |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type Input in task 1         .pmx | 45        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 2                | 66623507552d56742865613066097298 | 6274755055368eed1116388064384542 | 0              | INPUT        |                                  | 880391746536be961e594e7014524130 |                                  | BLOCK     |
        | Create with Status Case To Do and type Output in task 1        .pmx | 46        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 38102442252d5671a629009013495090 | 6274755055368eed1116388064384542 | 1              | OUTPUT       |                                  |                                  | 218529141536be955f0b646092366402 | BLOCK     |           
        | Create with Status Case To Do and type Case Note in task 1     .pmx | 47        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 44114647252d567264eb9e4061647705 | 6274755055368eed1116388064384542 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case To Do and type Messages in task 1      .pmx | 48        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 44811996752d567110634a1013636964 | 6274755055368eed1116388064384542 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type All in task 2          .pmx | 49        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Paused and type Dynaform in task 2     .pmx | 50        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 1                | 50011635952d5673246a575079973262 | 6274755055368eed1116388064384542 | 1              | DYNAFORM     | 898822326536be3a12addb0034537553 |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type Input in task 2        .pmx | 51        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 2                | 81572528952d5673de56fa9048605800 | 6274755055368eed1116388064384542 | 0              | INPUT        |                                  | 880391746536be961e594e7014524130 |                                  | BLOCK     |
        | Create with Status Case Paused and type Output in task 2       .pmx | 52        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 1                | 50562411252d5671e788c02016273245 | 6274755055368eed1116388064384542 | 1              | OUTPUT       |                                  |                                  | 218529141536be955f0b646092366402 | BLOCK     |           
        | Create with Status Case Paused and type Case Note in task 2    .pmx | 53        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 1                | 50912153352d5673b0b7e42000221953 | 6274755055368eed1116388064384542 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Paused and type Messages in task 2     .pmx | 54        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 2                | 13028697852d5674745cb64005883338 | 6274755055368eed1116388064384542 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type All in task 3       .pmx | 55        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 1                | 00000000000000000000000000000001 | 4790702485368efad167477011123879 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Create with Status Case Completed and type Dynaform in task 3  .pmx | 56        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 1                | 62511352152d5673bba9cd4062743508 | 4790702485368efad167477011123879 | 1              | DYNAFORM     | 898822326536be3a12addb0034537553 |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type Input in task 3     .pmx | 57        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 2                | 46520967652d56747f384f5069459364 | 4790702485368efad167477011123879 | 0              | INPUT        |                                  | 880391746536be961e594e7014524130 |                                  | BLOCK     |
        | Create with Status Case Completed and type Output in task 3    .pmx | 58        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 1                | 69125570352d56720061f83026430750 | 4790702485368efad167477011123879 | 1              | OUTPUT       |                                  |                                  | 218529141536be955f0b646092366402 | BLOCK     |           
        | Create with Status Case Completed and type Case Note in task 3 .pmx | 59        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 1                | 69578334752d5672aabb946025792134 | 4790702485368efad167477011123879 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Create with Status Case Completed and type Messages in task 3  .pmx | 60        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 1                | 73005191052d56727901138030694610 | 4790702485368efad167477011123879 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |



    Scenario Outline: Get a List of current Process Permissions of a project when there are exactly 30 Process Permissions
       Given I request "project/<project>/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <records> records

        Examples:
        | test_description                                                   | project                          | records |
        | List process permissions of the process "Test Process Permissions" | 67021149152e27240dc54d2095572343 | 30      |
        | List process permissions of the process "Process Complete BPMN"    | 1455892245368ebeb11c1a5001393784 | 31      |


    Scenario Outline: Get a Single Process Permissions of a project when the Process Permissions is previously created
      
        Given that I want to get a resource with the key "op_uid" stored in session array as variable "op_uid_<op_number>"
        And I request "project/<project>/process-permission"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And the "usr_uid" property equals "<usr_uid>"
        And the "op_user_relation" property equals "<op_user_relation>"
        And the "op_obj_type" property equals "<op_obj_type>"

        Examples:

        | test_description                                                 | op_number | project                          | op_case_status | tas_uid                          | op_user_relation | usr_uid                          | op_task_source                   | op_participate | op_obj_type  | dynaforms                        | inputs                           | outputs                          | op_action | 
        | Get with Status Case All and type All in task 1             .pm  | 1         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 1                | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Get with Status Case All and type Dynaform in task 1        .pm  | 2         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 1                | 25286582752d56713231082039265791 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Get with Status Case All and type Input in task 1           .pm  | 3         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 2                | 54731929352d56741de9d42002704749 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Get with Status Case All and type Output in task 1          .pm  | 4         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 1                | 32444503652d5671778fd20059078570 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Get with Status Case All and type Case Note in task 1       .pm  | 5         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 1                | 16333273052d567284e6766029512960 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     | 
        | Get with Status Case All and type Messages in task 1        .pm  | 6         | 67021149152e27240dc54d2095572343 | ALL            |                                  | 1                | 34289569752d5673d310e82094574281 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Draft and type All in task 2           .pm  | 7         | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 1                | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Get with Status Case Draft and type Dynaform in task 2      .pm  | 8         | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 1                | 11206717452d5673913aa69053050085 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Get with Status Case Draft and type Input in task 2         .pm  | 9         | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 2                | 21092802152d569a2e32b18087204577 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Get with Status Case Draft and type Output in task 2        .pm  | 10        | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 1                | 14093514252d56720bff5b4038518272 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Get with Status Case Draft and type Case Note in task 2     .pm  | 11        | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 1                | 19834612352d5673c73ea89076646062 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Draft and type Messages in task 2      .pm  | 12        | 67021149152e27240dc54d2095572343 | DRAFT          | 55416900252e272492318b9024750146 | 2                | 89064231952d567452ea008014804965 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case To Do and type All in task 1           .pm  | 13        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Get with Status Case To Do and type Dynaform in task 1      .pm  | 14        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 36116269152d56733b20e86062657385 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 86859555852e280acd84654094971976 |                                  |                                  | BLOCK     |
        | Get with Status Case To Do and type Input in task 1         .pm  | 15        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 2                | 66623507552d56742865613066097298 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 52547398752e28118ab06a3068272571 |                                  | BLOCK     |
        | Get with Status Case To Do and type Output in task 1        .pm  | 16        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 38102442252d5671a629009013495090 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56569355852e28145a16ec7038754814 | BLOCK     |           
        | Get with Status Case To Do and type Case Note in task 1     .pm  | 17        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 44114647252d567264eb9e4061647705 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case To Do and type Messages in task 1      .pm  | 18        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 44811996752d567110634a1013636964 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Paused and type All in task 2          .pm  | 19        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 1                | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Get with Status Case Paused and type Dynaform in task 2     .pm  | 20        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 1                | 50011635952d5673246a575079973262 | 36792129552e27247a483f6069605623 | 1              | DYNAFORM     | 51960945752e280ce802ce7007126361 |                                  |                                  | BLOCK     |
        | Get with Status Case Paused and type Input in task 2        .pm  | 21        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 2                | 81572528952d5673de56fa9048605800 | 36792129552e27247a483f6069605623 | 0              | INPUT        |                                  | 61273332352e28125254f97072882826 |                                  | BLOCK     |
        | Get with Status Case Paused and type Output in task 2       .pm  | 22        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 1                | 50562411252d5671e788c02016273245 | 36792129552e27247a483f6069605623 | 1              | OUTPUT       |                                  |                                  | 56977080352e281696ead88064880692 | BLOCK     |           
        | Get with Status Case Paused and type Case Note in task 2    .pm  | 23        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 1                | 50912153352d5673b0b7e42000221953 | 36792129552e27247a483f6069605623 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Paused and type Messages in task 2     .pm  | 24        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 2                | 13028697852d5674745cb64005883338 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Completed and type All in task 3       .pm  | 25        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 00000000000000000000000000000001 | 55416900252e272492318b9024750146 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Get with Status Case Completed and type Dynaform in task 3  .pm  | 26        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 62511352152d5673bba9cd4062743508 | 55416900252e272492318b9024750146 | 1              | DYNAFORM     | 51960945752e280ce802ce7007126361 |                                  |                                  | BLOCK     |
        | Get with Status Case Completed and type Input in task 3     .pm  | 27        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 2                | 46520967652d56747f384f5069459364 | 55416900252e272492318b9024750146 | 0              | INPUT        |                                  | 61273332352e28125254f97072882826 |                                  | BLOCK     |
        | Get with Status Case Completed and type Output in task 3    .pm  | 28        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 69125570352d56720061f83026430750 | 55416900252e272492318b9024750146 | 1              | OUTPUT       |                                  |                                  | 56977080352e281696ead88064880692 | BLOCK     |           
        | Get with Status Case Completed and type Case Note in task 3 .pm  | 29        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 69578334752d5672aabb946025792134 | 55416900252e272492318b9024750146 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Completed and type Messages in task 3  .pm  | 30        | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 73005191052d56727901138030694610 | 55416900252e272492318b9024750146 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case All and type All in task 1             .pmx | 31        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Get with Status Case All and type Dynaform in task 1        .pmx | 32        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 1                | 25286582752d56713231082039265791 | 6274755055368eed1116388064384542 | 1              | DYNAFORM     | 741536563536be333155026003350943 |                                  |                                  | BLOCK     |
        | Get with Status Case All and type Input in task 1           .pmx | 33        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 2                | 54731929352d56741de9d42002704749 | 6274755055368eed1116388064384542 | 0              | INPUT        |                                  | 880391746536be961e594e7014524130 |                                  | BLOCK     |
        | Get with Status Case All and type Output in task 1          .pmx | 34        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 1                | 32444503652d5671778fd20059078570 | 6274755055368eed1116388064384542 | 1              | OUTPUT       |                                  |                                  | 218529141536be955f0b646092366402 | BLOCK     |           
        | Get with Status Case All and type Case Note in task 1       .pmx | 35        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 1                | 16333273052d567284e6766029512960 | 6274755055368eed1116388064384542 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     | 
        | Get with Status Case All and type Messages in task 1        .pmx | 36        | 1455892245368ebeb11c1a5001393784 | ALL            |                                  | 1                | 34289569752d5673d310e82094574281 | 6274755055368eed1116388064384542 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Draft and type All in task 2           .pmx | 37        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Get with Status Case Draft and type Dynaform in task 2      .pmx | 38        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 1                | 11206717452d5673913aa69053050085 | 6274755055368eed1116388064384542 | 1              | DYNAFORM     | 741536563536be333155026003350943 |                                  |                                  | BLOCK     |
        | Get with Status Case Draft and type Input in task 2         .pmx | 39        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 2                | 21092802152d569a2e32b18087204577 | 6274755055368eed1116388064384542 | 0              | INPUT        |                                  | 880391746536be961e594e7014524130 |                                  | BLOCK     |
        | Get with Status Case Draft and type Output in task 2        .pmx | 40        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 1                | 14093514252d56720bff5b4038518272 | 6274755055368eed1116388064384542 | 1              | OUTPUT       |                                  |                                  | 218529141536be955f0b646092366402 | BLOCK     |           
        | Get with Status Case Draft and type Case Note in task 2     .pmx | 41        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 1                | 19834612352d5673c73ea89076646062 | 6274755055368eed1116388064384542 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Draft and type Messages in task 2      .pmx | 42        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 2                | 89064231952d567452ea008014804965 | 6274755055368eed1116388064384542 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case To Do and type All in task 1           .pmx | 43        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Get with Status Case To Do and type Dynaform in task 1      .pmx | 44        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 36116269152d56733b20e86062657385 | 6274755055368eed1116388064384542 | 1              | DYNAFORM     | 741536563536be333155026003350943 |                                  |                                  | BLOCK     |
        | Get with Status Case To Do and type Input in task 1         .pmx | 45        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 2                | 66623507552d56742865613066097298 | 6274755055368eed1116388064384542 | 0              | INPUT        |                                  | 880391746536be961e594e7014524130 |                                  | BLOCK     |
        | Get with Status Case To Do and type Output in task 1        .pmx | 46        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 38102442252d5671a629009013495090 | 6274755055368eed1116388064384542 | 1              | OUTPUT       |                                  |                                  | 218529141536be955f0b646092366402 | BLOCK     |           
        | Get with Status Case To Do and type Case Note in task 1     .pmx | 47        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 44114647252d567264eb9e4061647705 | 6274755055368eed1116388064384542 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case To Do and type Messages in task 1      .pmx | 48        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 44811996752d567110634a1013636964 | 6274755055368eed1116388064384542 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Paused and type All in task 2          .pmx | 49        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Get with Status Case Paused and type Dynaform in task 2     .pmx | 50        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 1                | 50011635952d5673246a575079973262 | 6274755055368eed1116388064384542 | 1              | DYNAFORM     | 898822326536be3a12addb0034537553 |                                  |                                  | BLOCK     |
        | Get with Status Case Paused and type Input in task 2        .pmx | 51        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 2                | 81572528952d5673de56fa9048605800 | 6274755055368eed1116388064384542 | 0              | INPUT        |                                  | 880391746536be961e594e7014524130 |                                  | BLOCK     |
        | Get with Status Case Paused and type Output in task 2       .pmx | 52        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 1                | 50562411252d5671e788c02016273245 | 6274755055368eed1116388064384542 | 1              | OUTPUT       |                                  |                                  | 218529141536be955f0b646092366402 | BLOCK     |           
        | Get with Status Case Paused and type Case Note in task 2    .pmx | 53        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 1                | 50912153352d5673b0b7e42000221953 | 6274755055368eed1116388064384542 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Paused and type Messages in task 2     .pmx | 54        | 1455892245368ebeb11c1a5001393784 | PAUSED         | 4790702485368efad167477011123879 | 2                | 13028697852d5674745cb64005883338 | 6274755055368eed1116388064384542 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Completed and type All in task 3       .pmx | 55        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 1                | 00000000000000000000000000000001 | 4790702485368efad167477011123879 | 1              | ANY          |                                  |                                  |                                  | VIEW      |
        | Get with Status Case Completed and type Dynaform in task 3  .pmx | 56        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 1                | 62511352152d5673bba9cd4062743508 | 4790702485368efad167477011123879 | 1              | DYNAFORM     | 898822326536be3a12addb0034537553 |                                  |                                  | BLOCK     |
        | Get with Status Case Completed and type Input in task 3     .pmx | 57        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 2                | 46520967652d56747f384f5069459364 | 4790702485368efad167477011123879 | 0              | INPUT        |                                  | 880391746536be961e594e7014524130 |                                  | BLOCK     |
        | Get with Status Case Completed and type Output in task 3    .pmx | 58        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 1                | 69125570352d56720061f83026430750 | 4790702485368efad167477011123879 | 1              | OUTPUT       |                                  |                                  | 218529141536be955f0b646092366402 | BLOCK     |           
        | Get with Status Case Completed and type Case Note in task 3 .pmx | 59        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 1                | 69578334752d5672aabb946025792134 | 4790702485368efad167477011123879 | 0              | CASES_NOTES  |                                  |                                  |                                  | BLOCK     |
        | Get with Status Case Completed and type Messages in task 3  .pmx | 60        | 1455892245368ebeb11c1a5001393784 | COMPLETED      | 2072984565368efc137a394001073529 | 1                | 73005191052d56727901138030694610 | 4790702485368efad167477011123879 | 0              | MSGS_HISTORY |                                  |                                  |                                  | BLOCK     |



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
        And I request "project/<project>/process-permission"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
      

         Examples:
        
        | test_description                                           | op_number | project                          | op_case_status | tas_uid                          | op_user_relation | usr_uid                          | op_task_source                   | op_participate | op_obj_type  | op_action | 
        | Update Status Case Completed and type All in task 3   .pm  | 6         | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 00000000000000000000000000000001 | 55416900252e272492318b9024750146 | 1              | ANY          | VIEW      |
        | Update Status Case Paused and type Messages in task 2 .pm  | 7         | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 2                | 81090718052d567492e1852081697260 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY | BLOCK     |
        | Update Status Case Paused and type All in task 2      .pm  | 12        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 1                | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          | VIEW      |
        | Update Status Case To Do and type Messages in task 1  .pm  | 13        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 44811996752d567110634a1013636964 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY | BLOCK     |
        | Update Status Case Paused and type Messages in task 2 .pmx | 37        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 6274755055368eed1116388064384542 | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          | VIEW      |
        | Update Status Case Paused and type All in task 2      .pmx | 42        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 2                | 89064231952d567452ea008014804965 | 6274755055368eed1116388064384542 | 0              | MSGS_HISTORY | BLOCK     |
        | Update Status Case To Do and type Messages in task 1  .pmx | 43        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          | VIEW      |
        

    Scenario Outline: Get a Single Process Permissions and check some properties
      Given that I want to get a resource with the key "op_uid" stored in session array as variable "op_uid_<op_number>"
        And I request "project/<project>/process-permission"
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

        | test_description                                        | op_number | project                          | op_case_status | tas_uid                          | op_user_relation | usr_uid                          | op_task_source                   | op_participate | op_obj_type  | op_action | 
        | Get Status Case Completed and type All in task 3   .pm  | 6         | 67021149152e27240dc54d2095572343 | COMPLETED      | 64296230152e2724a8b3589070508795 | 1                | 00000000000000000000000000000001 | 55416900252e272492318b9024750146 | 1              | ANY          | VIEW      |
        | Get Status Case Paused and type Messages in task 2 .pm  | 7         | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 2                | 81090718052d567492e1852081697260 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY | BLOCK     |
        | Get Status Case Paused and type All in task 2      .pm  | 12        | 67021149152e27240dc54d2095572343 | PAUSED         | 55416900252e272492318b9024750146 | 1                | 00000000000000000000000000000001 | 36792129552e27247a483f6069605623 | 1              | ANY          | VIEW      |
        | Get Status Case To Do and type Messages in task 1  .pm  | 13        | 67021149152e27240dc54d2095572343 | TO_DO          | 36792129552e27247a483f6069605623 | 1                | 44811996752d567110634a1013636964 | 36792129552e27247a483f6069605623 | 0              | MSGS_HISTORY | BLOCK     |
        | Get Status Case Paused and type Messages in task 2 .pmx | 37        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 6274755055368eed1116388064384542 | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          | VIEW      |
        | Get Status Case Paused and type All in task 2      .pmx | 42        | 1455892245368ebeb11c1a5001393784 | DRAFT          | 4790702485368efad167477011123879 | 2                | 89064231952d567452ea008014804965 | 6274755055368eed1116388064384542 | 0              | MSGS_HISTORY | BLOCK     |
        | Get Status Case To Do and type Messages in task 1  .pmx | 43        | 1455892245368ebeb11c1a5001393784 | TO_DO          | 6274755055368eed1116388064384542 | 1                | 00000000000000000000000000000001 | 6274755055368eed1116388064384542 | 1              | ANY          | VIEW      |
        
    
    Scenario Outline: Delete all Process Supervisor created previously in this script   
      Given that I want to delete a resource with the key "op_uid" stored in session array as variable "op_uid_<op_number>"
        And I request "project/<project>/process-permission"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        
        Examples:

        | project                          | op_number |         
        | 67021149152e27240dc54d2095572343 | 1         |
        | 67021149152e27240dc54d2095572343 | 2         |
        | 67021149152e27240dc54d2095572343 | 3         |
        | 67021149152e27240dc54d2095572343 | 4         |
        | 67021149152e27240dc54d2095572343 | 5         |
        | 67021149152e27240dc54d2095572343 | 6         |
        | 67021149152e27240dc54d2095572343 | 7         |
        | 67021149152e27240dc54d2095572343 | 8         |
        | 67021149152e27240dc54d2095572343 | 9         |
        | 67021149152e27240dc54d2095572343 | 10        |
        | 67021149152e27240dc54d2095572343 | 11        |
        | 67021149152e27240dc54d2095572343 | 12        |
        | 67021149152e27240dc54d2095572343 | 13        |
        | 67021149152e27240dc54d2095572343 | 14        |
        | 67021149152e27240dc54d2095572343 | 15        |
        | 67021149152e27240dc54d2095572343 | 16        |
        | 67021149152e27240dc54d2095572343 | 17        |
        | 67021149152e27240dc54d2095572343 | 18        |
        | 67021149152e27240dc54d2095572343 | 19        |
        | 67021149152e27240dc54d2095572343 | 20        |
        | 67021149152e27240dc54d2095572343 | 21        |
        | 67021149152e27240dc54d2095572343 | 22        |
        | 67021149152e27240dc54d2095572343 | 23        |
        | 67021149152e27240dc54d2095572343 | 24        |
        | 67021149152e27240dc54d2095572343 | 25        |
        | 67021149152e27240dc54d2095572343 | 26        |
        | 67021149152e27240dc54d2095572343 | 27        |
        | 67021149152e27240dc54d2095572343 | 28        |
        | 67021149152e27240dc54d2095572343 | 29        |
        | 67021149152e27240dc54d2095572343 | 30        |
        | 1455892245368ebeb11c1a5001393784 | 31        |
        | 1455892245368ebeb11c1a5001393784 | 32        |
        | 1455892245368ebeb11c1a5001393784 | 33        |
        | 1455892245368ebeb11c1a5001393784 | 34        |
        | 1455892245368ebeb11c1a5001393784 | 35        |
        | 1455892245368ebeb11c1a5001393784 | 36        |
        | 1455892245368ebeb11c1a5001393784 | 37        |
        | 1455892245368ebeb11c1a5001393784 | 38        |
        | 1455892245368ebeb11c1a5001393784 | 39        |
        | 1455892245368ebeb11c1a5001393784 | 40        |
        | 1455892245368ebeb11c1a5001393784 | 41        |
        | 1455892245368ebeb11c1a5001393784 | 42        |
        | 1455892245368ebeb11c1a5001393784 | 43        |
        | 1455892245368ebeb11c1a5001393784 | 44        |
        | 1455892245368ebeb11c1a5001393784 | 45        |
        | 1455892245368ebeb11c1a5001393784 | 46        |
        | 1455892245368ebeb11c1a5001393784 | 47        |
        | 1455892245368ebeb11c1a5001393784 | 48        |
        | 1455892245368ebeb11c1a5001393784 | 49        |
        | 1455892245368ebeb11c1a5001393784 | 50        |
        | 1455892245368ebeb11c1a5001393784 | 51        |
        | 1455892245368ebeb11c1a5001393784 | 52        |
        | 1455892245368ebeb11c1a5001393784 | 53        |
        | 1455892245368ebeb11c1a5001393784 | 54        |
        | 1455892245368ebeb11c1a5001393784 | 55        |
        | 1455892245368ebeb11c1a5001393784 | 56        |
        | 1455892245368ebeb11c1a5001393784 | 57        |
        | 1455892245368ebeb11c1a5001393784 | 58        |
        | 1455892245368ebeb11c1a5001393784 | 59        |
        | 1455892245368ebeb11c1a5001393784 | 60        |

            
    Scenario Outline: Get a List of current Process Permissions of a project
       Given I request "project/<project>/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has <record> record

        Examples:
        | test_description                                                   | project                          | record |
        | List process permissions of the process "Test Process Permissions" | 67021149152e27240dc54d2095572343 | 0      |
        | List process permissions of the process "Process Complete BPMN"    | 1455892245368ebeb11c1a5001393784 | 1      |


#Prueba del BUG 15085, donde se pueda crear los permisos Resend para Message History


    Scenario: Get a List of current Process Permissions of a project
       Given I request "project/1455892245368ebeb11c1a5001393784/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 1 record


    Scenario Outline: Create a new Process permission in proyect "Process Complete BPMN"
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
        And I request "project/1455892245368ebeb11c1a5001393784/process-permission"
        Then the response status code should be 201
        And store "op_uid" in session array
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And store "op_uid" in session array as variable "op_uid_<op_number>"

         Examples:

        | op_number | op_case_status  | tas_uid                          | op_user_relation| usr_uid                          | op_task_source                   | op_participate | op_obj_type  | dynaforms                        | inputs                           | outputs                          | op_action | 
        | 1         | COMPLETED       |                                  | 1               | 00000000000000000000000000000001 |                                  | 0              | MSGS_HISTORY |                                  |                                  |                                  | RESEND    |
        | 2         | COMPLETED       |                                  | 1               | 00000000000000000000000000000001 |                                  | 0              | MSGS_HISTORY |                                  |                                  |                                  | RESEND    |


    Scenario: Get a List of current Process Permissions of a project
        Given I request "project/1455892245368ebeb11c1a5001393784/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 3 record

    Scenario Outline: Delete all Process Supervisor created previously in this script   
        Given that I want to delete a resource with the key "op_uid" stored in session array as variable "op_uid_<op_number>"
        And I request "project/1455892245368ebeb11c1a5001393784/process-permission"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        
        Examples:

        | op_number |         
        | 1         |
        | 2         |

  Scenario: Get a List of current Process Permissions of a project
        Given I request "project/1455892245368ebeb11c1a5001393784/process-permissions"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the response has 1 record