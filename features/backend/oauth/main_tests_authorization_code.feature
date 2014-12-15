@ProcessMakerMichelangelo @RestAPI
Feature: Generate token Grant type - Authorization Code
Requirements:
    a workspace with open session and installed application


Scenario Outline: Create new CLIENT_ID and CLIENT_SECRET
    Given OAUTH register an application
    """
    {
        "name":"<application_name>",
        "description":"<application_description>",
        "webSite":"<application_website>",
        "redirectUri":"<application_redirectUri>",
        "applicationNumber":"<application_number>"
    }
    """
    Examples:

    | Description         | application_number | application_name | application_description | application_website         | application_redirectUri                                                      |
    | Create token normal | 1                  | Demo3            | Demo3 desc              | http://www.processmaker.com | http://michelangelo-be.colosa.net/sysmichelangelo/en/neoclassic/oauth2/grant |
    | Create token normal | 2                  | Demo4            | Demo4 desc              | http://www.processmaker.com | http://michelangelo-be.colosa.net/sysmichelangelo/en/neoclassic/oauth2/grant |
    

#Endpoint para verificar el correcto funcionamiento del token generado en este script
Scenario Outline: Get the Output Documents List both process
    Given that I assign an access token from session variable "access_token_<application_number>"
    And I request "project/<project>/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "out_doc_title" property in row 0 equals "<out_doc_title>"
    
    Examples:

    | test_description                                               | project                          | records | out_doc_title               | application_number |
    | List Outputs in process "Test Users-Step-Properties End Point" | 4224292655297723eb98691001100052 | 2       | Endpoint Old Version (base) | 1                  |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 1       | Output Document             | 2                  |


Scenario Outline: Get the Output Documents List both process (without valid token)
    Given I request "project/<project>/output-documents"
    Then the response status code should be 401
        
    Examples:

    | test_description                                               | project                          | records | out_doc_title               | application_number | 
    | List Outputs in process "Test Users-Step-Properties End Point" | 4224292655297723eb98691001100052 | 2       | Endpoint Old Version (base) | 1                  |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 1       | Output Document             | 2                  |


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
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 1       | Output Document             | 2                  | Unauthorized  |


#Grant type Refresh Token
Scenario Outline: Refresh token
    Given POST this data:
    """
    {
        
    }
    """
    And I request a refresh token for "refresh_token_<grant_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "object"
    And store "access_token" in session array as variable "access_token_<refresh_token_number>"
    And store "expires_in" in session array as variable "expires_in_<refresh_token_number>"
    And store "token_type" in session array as variable "token_type_<refresh_token_number>"
    And store "scope" in session array as variable "scope_<refresh_token_number>"
    
    Examples:

    | Description         | grant_number | refresh_token_number |
    | Create token normal | 1            | 3                    |
    | Create token normal | 2            | 4                    |
 

#Endpoint para verificar el correcto funcionamiento del Refresh Token generado en este script
Scenario Outline: Get the Output Documents List both process
    Given that I assign an access token from session variable "access_token_<application_number>"
    And I request "project/<project>/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the content type is "application/json"
    And the type is "array"
    And the response has <records> records
    And the "out_doc_title" property in row 0 equals "<out_doc_title>"
    
    Examples:

    | test_description                                               | project                          | records | out_doc_title               | application_number |
    | List Outputs in process "Test Users-Step-Properties End Point" | 4224292655297723eb98691001100052 | 2       | Endpoint Old Version (base) | 3                  |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 1       | Output Document             | 4                  |


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
    | 1                  |
    | 2                  |
    | 3                  |
    | 4                  |
    

#Endpoint para verificar que el token ya no existe
Scenario Outline: Get the Output Documents List both process
    Given that I assign an access token from session variable "access_token_<application_number>"
    And I request "project/<project>/output-documents"
    Then the response status code should be 401
    And the response status message should have the following text "<error_message>"
   
    Examples:

    | test_description                                               | project                          | records | out_doc_title               | application_number | error_message |
    | List Outputs in process "Test Users-Step-Properties End Point" | 4224292655297723eb98691001100052 | 2       | Endpoint Old Version (base) | 1                  | Unauthorized  |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 1       | Output Document             | 2                  | Unauthorized  |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 1       | Output Document             | 3                  | Unauthorized  |
    | List Outputs in process "Process Complete BPMN"                | 1455892245368ebeb11c1a5001393784 | 1       | Output Document             | 4                  | Unauthorized  |
