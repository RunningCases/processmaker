@ProcessMakerMichelangelo @RestAPI
Feature: Files Manager Resources Main Tests
  Requirements:
    a workspace with the process 1265557095225ff5c688f46031700471 ("Test Michelangelo") already loaded
    there are two output documents in the process

  Background:
    Given that I have a valid access_token

 
  Scenario: Get a list of main process files manager
    Given I request "project/1265557095225ff5c688f46031700471/file-manager"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the "name" property in row 0 equals "templates"
    And the "name" property in row 1 equals "public"

  Scenario: Get a list public folder of process files manager
  Given I request "project/1265557095225ff5c688f46031700471/file-manager?path=public"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 0 records
  
  Scenario: Get a list templates folder of process files manager
  Given I request "project/1265557095225ff5c688f46031700471/file-manager?path=templates"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has 1 records

  Scenario Outline: Create files and subfolders 
  Given POST this data:
      """
      {
          "prf_filename": "<file_name>",
          "prf_path": "<path>",
          "prf_content": "<content>"
      }
      """
      And I request "project/1265557095225ff5c688f46031700471/file-manager"
      Then the response status code should be <http_code>
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "<type>"
      And store "prf_uid" in session array as variable "prf_uid_<prf_number>"

    Examples:
    | test_description             | file_name         | path                  | content      | http_code | type   | prf_number |
    | into public folder           | file_test_1.txt   | public/               | only text     | 200       | object | 0          |
    | into mailtemplates folder    | file_test_2.html  | templates/            | <h1>Test</h1><p>html test</p>     | 200       | object | 1          |
    | into public subfolder        | file_test_3.txt  | public/public_subfolder    | test     | 200       | object | 2          |
    | into mailtemplates subfolder | file_test_4.html  | templates/templates_subfolder | test     | 200       | object | 3          |

  Scenario Outline: Post files
  Given PUT this data:
      """
      {
          "prf_content": "<content>"
      }
      """
      And that I want to update a resource with the key "prf_uid" stored in session array as variable "prf_uid_<prf_number>"
      And I request "project/1265557095225ff5c688f46031700471/file-manager"
      Then the response status code should be <http_code>
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "<type>"

    Examples:
    | test_description                 | content  | http_code | type   | prf_number |
    | put into public folder           | only text - modified | 200       | object | 0          |
    | put into mailtemplates folder    | <h1>Test</h1><p>html test</p><i>modified</i> | 200       | object | 1          |
    | put into public subfolder        | put test | 200       | object | 2          |
    | put into mailtemplates subfolder | put test | 200       | object | 3          |

  #Para que funcione este test, debe existir el archivo que se quiere subir
  Scenario Outline: Upload files to same folders
    Given POST I want to upload the file "<file>" to path "<path>". Url "project/1265557095225ff5c688f46031700471/file-manager"
    And store "prf_uid" in session array as variable "prf_uid_<prf_number>"

    Examples:
    | file                   | path      | prf_number |
    |/home/daniel/test1.html | templates | 4 |
    |/home/daniel/test2.html | templates | 5 |
    |/home/daniel/test.txt   | public    | 6 |


  Scenario Outline: Delete file
  Given that I want to delete a resource with the key "prf_uid" stored in session array as variable "prf_uid_<prf_number>"
  And I request "project/1265557095225ff5c688f46031700471/file-manager"
  
        Then the response status code should be 200
        And the response charset is "UTF-8"

    Examples:
    | test_description                 | prf_number |
    | delete public folder             | 0          |
    | delete mailtemplates folder      | 1          |
    | delete public subfolder          | 2          |
    | delete mailtemplates subfolder   | 3          |
    | delete mailtemplates subfolder   | 4          |
    | delete mailtemplates subfolder   | 5          |
    | delete mailtemplates subfolder   | 6          |

  

  