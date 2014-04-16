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
        | Field required tri title             | 251815090529619a99a2bf4013294414 |                              | Trigger con nombre en blanco                             |SCRIPT    | 400        | tri_title       |
        

#Test delete trigger when it asignee on the step

Scenario: Get the Triggers List when there are exactly two triggers
        Given I request "project/99209594750ec27ea338927000421575/triggers"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And the response has 5 records
        

Scenario: Delete a Triggers created in the process - (Derivation rules - sequential)
        Given that I want to delete a "Trigger"
        And I request "project/99209594750ec27ea338927000421575/trigger/96762672253418c5fde42f1084230135"
        Then the response status code should be 400
        And the response status message should have the following text "Dependencies were found for this trigger"