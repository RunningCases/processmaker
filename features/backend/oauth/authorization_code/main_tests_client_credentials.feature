@ProcessMakerMichelangelo @RestAPI
Feature: Generate token Grant type - Client Credentials Grant
Requirements:
    a workspace with installed application


Scenario Outline: Create new CLIENT_ID and CLIENT_SECRET
    Given POST this data:
    """
    {
        "grant_type":"<grant_type>"
    }
    """
    And I request a client credential grant
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "access_token" in session array as variable "access_token_<grant_number>"
    And store "expires_in" in session array as variable "expires_in_<grant_number>"
    And store "token_type" in session array as variable "token_type_<grant_number>"
    
    Examples:
    | Description         | grant_number | grant_type         |
    | Create token normal | 1            | client_credentials |
    | Create token normal | 2            | client_credentials |
    

#Endpoint para verificar el correcto funcionamiento del token generado en este script especificamente en la opción Running Cases
Scenario Outline: Returns a list of the cases for the logged in user (Inbox)
    Given that I assign an access token from session variable "access_token_<grant_number>"
    And I request "cases"
    Then the response status code should be 400
    And the response charset is "UTF-8"
    

    Examples:
    | Description         | grant_number |
    | Create token normal | 1            |
    | Create token normal | 2            |

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

