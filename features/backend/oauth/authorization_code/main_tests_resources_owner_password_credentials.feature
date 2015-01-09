@ProcessMakerMichelangelo @RestAPI
Feature: Generate token Grant type - Resources Owner Password Credential Grant
  Requirements:
  a workspace with installed application


  Scenario Outline: Create new CLIENT_ID and CLIENT_SECRET
    Given POST this data:
    """
    {
        "grant_type":"<grant_type>",
        "username":"<username>",
        "password":"<password>",
        "scope":"<scope>"
    }
    """
    And I request a owner password credential grant
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "access_token" in session array as variable "access_token_<grant_number>"
    And store "expires_in" in session array as variable "expires_in_<grant_number>"
    And store "token_type" in session array as variable "token_type_<grant_number>"
    And store "scope" in session array as variable "scope_<grant_number>"
    And store "refresh_token" in session array as variable "refresh_token_<grant_number>"


  Examples:
    | Description         | grant_number | grant_type | username | password   | scope |
    | Create token normal | 1            | password   | amy      | sample     | *     |
    | Create token normal | 2            | password   | admin    | sample123* | *     |


#Endpoint para verificar el correcto funcionamiento del token generado en este script especificamente en la opción Running Cases
  Scenario Outline: Returns a list of the cases for the logged in user (Inbox)
    Given that I assign an access token from session variable "access_token_<grant_number>"
    And I request "cases"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has <records> records

  Examples:
    | Description         | grant_number | records |
    | Create token normal | 1            | 4       |
    | Create token normal | 2            | 14      |


#Endpoint para hacer que expire los token creados en este script
  Scenario Outline: Expire token created in this script
    Given POST this data:
    """
    {
    }
    """
    And I request "oauth2/access_token/expire"  with the key "access_token" stored in session array as variable "access_token_<application_number>"
    Then the response status code should be 200

  Examples:
    | Description    | application_number |
    | Expire token 1 | 1                  |


  Scenario Outline: Expire token created in this script
    Given POST this data:
    """
    {
    }
    """
    And I request "oauth2/access_token/expire"  with the key "access_token" stored in session array as variable "access_token_<application_number>"
    Then the response status code should be 200

  Examples:
    | Description    | application_number |
    | Expire token 2 | 2                  |


#Endpoint para verificar que el token haya expirado
  Scenario Outline: Get the Output Documents List both process
    Given that I assign an access token from session variable "access_token_<application_number>"
    And I request "project/<project>/output-documents"
    Then the response status code should be 401
    And the response status message should have the following text "<error_message>"

  Examples:
    | test_description                                               | project                          | records | out_doc_title               | application_number | error_message |
    | List Outputs in process "Test Users-Step-Properties End Point" | 4224292655297723eb98691001100052 | 2       | Endpoint Old Version (base) | 1                  | Unauthorized  |


  Scenario Outline: Get the Output Documents List both process
    Given that I assign an access token from session variable "access_token_<application_number>"
    And I request "project/<project>/output-documents"
    Then the response status code should be 401
    And the response status message should have the following text "<error_message>"

  Examples:
    | test_description                                               | project                          | records | out_doc_title               | application_number | error_message |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 1       | Output Document             | 2                  | Unauthorized  |


#Endpoint para borrar el token creado en este script

  Scenario Outline: Delete all tokens created previously in this script
    Given that I want to delete a resource with the key "access_token_<application_number>" stored in session array
    And I request "oauth2"
    And the content type is "application/json"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "object"

  Examples:

    | application_number |
    | 1              |
    | 2              |

#Endpoint para verificar que el token ya no existe
  Scenario Outline: Get the Output Documents List both process
    Given that I assign an access token from session variable "access_token_<application_number>"
    And I request "project/<project>/output-documents"
    Then the response status code should be 401
    And the response status message should have the following text "<error_message>"

  Examples:
    | test_description                                               | project                          | records | out_doc_title               | application_number | error_message |
    | List Outputs in process "Test Users-Step-Properties End Point" | 4224292655297723eb98691001100052 | 2       | Endpoint Old Version (base) | 1                  | Unauthorized  |


  Scenario Outline: Get the Output Documents List both process
    Given that I assign an access token from session variable "access_token_<application_number>"
    And I request "project/<project>/output-documents"
    Then the response status code should be 401
    And the response status message should have the following text "<error_message>"

  Examples:
    | test_description                                | project                          | records | out_doc_title   | application_number | error_message |
    | List Outputs in process "Process Complete BPMN" | 1455892245368ebeb11c1a5001393784 | 1       | Output Document | 2                  | Unauthorized  |

