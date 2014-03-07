@ProcessMakerMichelangelo @RestAPI
Feature: File Manager Negative Tests


  Background:
    Given that I have a valid access_token

  Scenario: Get a list public folder of process files manager with bad parameters (negative tests)
  Given I request "project/1265557095225ff5c688f46031700471/file-manager?path=sample"
    Then the response status code should be 400
    And the response status message should have the following text "path"
    
  
  Scenario: Get a list templates folder of process files manager with bad parameters (negative tests)
  Given I request "project/1265557095225ff5c688f46031700471/file-manager?path=sample"
    Then the response status code should be 400
    And the response status message should have the following text "path"
    

  Scenario Outline: Create files and subfolders for a project with bad parameters (negative tests)
  Given POST this data:
      """
      {
          "prf_filename": "<prf_filename>",
          "prf_path": "<prf_path>",
          "prf_content": "<prf_content>"
      }
      """
      And I request "project/<project>/file-manager"
      Then the response status code should be <error_code>
      And the response status message should have the following text "<error_message>"

    Examples:
    | test_description                        | project                          | prf_filename      | prf_path                      | prf_content                   | error_code | error_message |
    | Invalid path public                     | 1265557095225ff5c688f46031700471 | file_test_1.txt   | file_input_public/            | only text                     | 400        | prf_path      |
    | Invalid path template                   | 1265557095225ff5c688f46031700471 | file_test_2.html  | temptes_manager/              | <h1>Test</h1><p>html test</p> | 400        | prf_path      |
    | Field Required prf_filename in public   | 1265557095225ff5c688f46031700471 |                   | public/                       | only text                     | 400        | prf_filename  |
    | Field Required prf_filename in template | 1265557095225ff5c688f46031700471 |                   | templates/                    | <h1>Test</h1><p>html test</p> | 400        | prf_filename  |
    | Field Required project                  |                                  | file_test_1.txt   | public/                       | only text                     | 400        | prj_uid       |