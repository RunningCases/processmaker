@ProcessMakerMichelangelo @RestAPI
Feature: Import/Export Process Negative Tests

  Background:
    Given that I have a valid access_token

Scenario Outline: Import a process negative tests
    Given POST upload a project file "<project_file>" to "project/import?option=<import_option>"
    Then the response status code should be <error_code>
    And the response status message should have the following text "<error_message>"


    Examples:
    | Description                                   | project_file              | import_option | error_code | error_message  |
    | Import process when the process alredy exists | Process_Complete_BPMN.pmx | create        | 400        | already exists |
    

Scenario Outline: Import a process with wrong "option"
    Given POST upload a project file "<project_file>" to "project/import?option=<option>&option_group=<option_group>"
    Then the response status code should be 400
    And the response status message should have the following text "<error_message>"


    Examples:
    | Description          | project_file               | option | option_group | error_message |
    | Invalid option       | Process_NewCreate_BPMN.pmx | sample | merge        | option        |
    | Invalid option_group | Process_Complete_BPMN.pmx  | create | sample       | option_group  |