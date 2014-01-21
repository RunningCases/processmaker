@ProcessMakerMichelangelo @RestAPI
Feature: Triggers Negative Tests


  Background:
    Given that I have a valid access_token

  Scenario Outline: Create a new Triggers for a project with bad parameters (negative tests)
    Given POST this data:
        """
        {
         "tri_title": "<tri_title>",
         "tri_description": "<tri_description>",
         "tri_type": "<tri_type>",
         "tri_webbot": "<tri_webbot>",
         "tri_param": "<tri_param>"
        }
        """
        And I request "project/<project>/trigger"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"
        
        Examples:

        | test_description                     | project                          | tri_title                    | tri_description                                          |tri_type  | error_code | error_message   |
        | Invalid tri type                     | 251815090529619a99a2bf4013294414 | Trigger 5                    | Trigger valores invalidos                                |Value12@#$| 400        | tri_type        |
        | Field required tri title             | 251815090529619a99a2bf4013294414 |                              | Trigger con nombre en blanco                             |SCRIPT    | 400        | tri_title       |
        