@ProcessMakerMichelangelo @RestAPI
Feature: Files Manager Resources

  Background:
    Given that I have a valid access_token

  Scenario: Get a list of main process files manager
    Given I request "project/1265557095225ff5c688f46031700471/file-manager"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

  Scenario: Get a list public folder of process files manager
  Given I request "project/1265557095225ff5c688f46031700471/file-manager?path=public"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
  
  Scenario: Get a list templates folder of process files manager
  Given I request "project/1265557095225ff5c688f46031700471/file-manager?path=templates"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"

  Scenario Outline: Post files
  Given POST this data:
      """
      {
          "prf_filename": "<file_name>",
          "prf_path": "<path>",
          "prf_content": "<content>"
      }
      """
      And I request "project/1265557095225ff5c688f46031700471/file-manager"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "<type>"
      And store "prf_uid" in session array as variable "prf_uid<i>"

    Examples:
    | test_description          | file_name      | path                  | content  | http_code | type   | i |
    | into public folder        | testbehat.txt  | public/               | test     | 200       | object | 0 |
    | into maintemplates folder | testbehat.txt  | templates/            | test     | 200       | object | 1 |
    | into public subfolder     | testbehat.txt  | public/test_folder    | test     | 200       | object | 2 |
    | into public subfolder     | testbehat.txt  | templates/test_folder | test     | 200       | object | 3 |



  Scenario Outline: Post files
  Given PUT this data:
      """
      {
          "prf_filename": "<file_name>",
          "prf_content": "<content>"
      }
      """
      And I request "project/1265557095225ff5c688f46031700471/file-manager?path=<path>"
      Then the response status code should be 200
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "<type>"

    Examples:
    | test_description              | file_name      | path                  | content  | http_code | type   |
    | put into public folder        | testbehat.txt  | public/               | put test | 200       | object |
    | put into maintemplates folder | testbehat.txt  | templates/            | put test | 200       | object |
    | put into public subfolder     | testbehat.txt  | public/test_folder    | put test | 200       | object |
    | put into public subfolder     | testbehat.txt  | templates/test_folder | put test | 200       | object |

  Scenario Outline: Delete file
  Given that I want to delete a "<path>"
        And I request "project/1265557095225ff5c688f46031700471/file-manager?path=<path>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    Examples:
    | test_description                 | path                                |
    | delete public folder             | public/testbehat.txt                |
    | delete maintemplates folder      | templates/testbehat.txt             |
    | delete public subfolder          | public/test_folder/testbehat.txt    |
    | delete public subfolder          | templates/test_folder/testbehat.txt |