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
      Then the response status code should be <http_code>
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "<type>"
      And store "prf_uid" in session array as variable "prf_uid<i>"

    Examples:
    | test_description             | file_name       | path                  | content  | http_code | type   | i |
    | into public folder           | testbehat1.txt  | public/               | test     | 200       | object | 0 |
    | into mailtemplates folder    | testbehat1.txt  | templates/            | test     | 200       | object | 1 |
    | into public subfolder        | testbehat1.txt  | public/test_folder    | test     | 200       | object | 2 |
    | into mailtemplates subfolder | testbehat1.txt  | templates/test_folder | test     | 200       | object | 3 |

  Scenario Outline: Post files
  Given PUT this data:
      """
      {
          "prf_content": "<content>"
      }
      """
      And that I want to update a resource with the key "prf_uid" stored in session array as variable "prf_uid<i>"
      And I request "project/1265557095225ff5c688f46031700471/file-manager"
      Then the response status code should be <http_code>
      And the response charset is "UTF-8"
      And the content type is "application/json"
      And the type is "<type>"

    Examples:
    | test_description                 | content  | http_code | type   | i |
    | put into public folder           | put test | 200       | object | 0 |
    | put into mailtemplates folder    | put test | 200       | object | 1 |
    | put into public subfolder        | put test | 200       | object | 2 |
    | put into mailtemplates subfolder | put test | 200       | object | 3 |

  Scenario Outline: Delete file
  Given that I want to delete a resource with the key "prf_uid" stored in session array as variable "prf_uid<i>"
        And I request "project/1265557095225ff5c688f46031700471/file-manager"
        Then the response status code should be 200
        And the response charset is "UTF-8"

    Examples:
    | test_description                 | i |
    | delete public folder             | 0 |
    | delete mailtemplates folder      | 1 |
    | delete public subfolder          | 2 |
    | delete mailtemplates subfolder   | 3 |

  #Para que funcione este test, debe existir el archivo que se quiere subir
  Scenario: Post files
    Given POST I want to upload the file "/home/daniel/test.txt" to path "templates". Url "project/1265557095225ff5c688f46031700471/file-manager"

  Scenario: Delete file
  Given that I want to delete a resource with the key "prf_uid" stored in session array as variable "prf_uid"
        And I request "project/1265557095225ff5c688f46031700471/file-manager"
        Then the response status code should be 200
        And the response charset is "UTF-8"
