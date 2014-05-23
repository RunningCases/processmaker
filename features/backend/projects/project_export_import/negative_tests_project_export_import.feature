@ProcessMakerMichelangelo @RestAPI
Feature: Import/Export Process Negative Tests

  Background:
    Given that I have a valid access_token

Scenario Outline: Import a process negative tests
    Given POST upload a project file "<project_file>" to "project/import?option=<import_option>"
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"


    Examples:
    | Description                                   | project_file                                       | import_option | error_code | error_message  |
    | Import process when the process alredy exists | /home/wendy/uploadfiles/Process_Complete_BPMN.pmx  | create        | 400        | already exists |
    #| Invalid path                                  | /processmaker/sample/Project_invalido.pmx          | create        | 400        | invalid        |