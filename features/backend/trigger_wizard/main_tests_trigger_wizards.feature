@ProcessMakerMichelangelo @RestAPI
Feature: Group
    Requirements:
        a workspace with the process 14414793652a5d718b65590036026581 ("Sample Project #1") already loaded
        there are three activities in the process

    Background:
        Given that I have a valid access_token

   
   Scenario Outline: Get the Trigger Wizard List when there are exactly 6 library 
        And I request "project/14414793652a5d718b65590036026581/trigger-wizards"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 6 records
        And the "lib_name" property in row <i> equals "<lib_name>"
        And the "lib_title" property in row <i> equals "<lib_title>"
        And the "lib_class_name" property in row <i> equals "<lib_class_name>"

        Examples:
        | i | lib_name       | lib_title                      | lib_class_name                       |
        | 0 | pmFunctions    | ProcessMaker Functions         | class.pmFunctions.php                |
        | 1 | pmTrSharepoint | Sharepoint DWS Triggers v. 0.1 | class.pmTrSharepoint.pmFunctions.php |        
        | 2 | pmTrAlfresco   | Alfresco DM Triggers v. 0.1    | class.pmTrAlfresco.pmFunctions.php   |
        | 3 | pmZimbra       | Zimbra Triggers v. 0.1         | class.pmZimbra.pmFunctions.php       |
        | 4 | pmSugar        | Sugar CRM Triggers             | class.pmSugar.pmFunctions.php        |
        | 5 | pmTalend       | Talend ETL Integration         | class.pmTalend.pmFunctions.php       |
        

    Scenario Outline: Get a single Library
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "lib_name" is set to "<lib_name>"
        And that "lib_title" is set to "<lib_title>"
        And that "lib_class_name" is set to "<lib_class_name>"

        Examples:
        | lib_name       | lib_title                      | lib_class_name                       |
        | pmFunctions    | ProcessMaker Functions         | class.pmFunctions.php                |
        | pmTrSharepoint | Sharepoint DWS Triggers v. 0.1 | class.pmTrSharepoint.pmFunctions.php |
        | pmTrAlfresco   | Alfresco DM Triggers v. 0.1    | class.pmTrAlfresco.pmFunctions.php   |
        | pmZimbra       | Zimbra Triggers v. 0.1         | class.pmZimbra.pmFunctions.php       |
        | pmTalend       | Talend ETL Integration         | class.pmTalend.pmFunctions.php       |
        | pmSugar        | Sugar CRM Triggers             | class.pmSugar.pmFunctions.php        |

    
    Scenario Outline: Get a single Function of the Library
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "fn_name" is set to "<fn_name>"
        And that "fn_label" is set to "<fn_label>"

        Examples:
        | lib_name       | fn_name                 | fn_label                                         |
        | pmFunctions    | PMFAddAttachmentToArray | Add Element in Array                             |
        | pmTrSharepoint | createDWS               | Create a DWS in Sharepoint server                |
        | pmTrAlfresco   | Checkin                 | Checkin document/file                            |
        | pmZimbra       | createZimbraAppointment | Create Appointment                               |
        | pmTalend       | executeTalendWebservice | Executes a Talend Web Service                    |
        | pmSugar        | CreateSugarAccount      | Creates SugarCRM entries from the Account module |



    Scenario: Get a List of triggers of a project
        And I request "project/14414793652a5d718b65590036026581/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array


    Scenario Outline: Create new Trigger: PMFAddAttachmentToArray
        Given POST this data:
        """
        {
            "tri_title": "<tri_title>",
            "tri_description": "<tri_description>",
            "tri_type": "<tri_type>",
            "tri_params": {
                    "input": {
                    "arrayData": "<tri_params.input.arrayData>",
                    "index": "<tri_params.input.index>",
                    "value": "<tri_params.input.value>",
                    "suffix": "<tri_params.input.suffix>"
                },
                "output": {
                    "tri_answer": "<tri_params.output.tri_answer>"
                }
            }
        }
        """
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "tri_uid" in session array as variable "tri_uid<i>"

        Examples:
        | i | lib_name    | fn_name                 | tri_title    | tri_description | tri_type | tri_params.input.arrayData | tri_params.input.index | tri_params.input.value | tri_params.input.suffix | tri_params.output.tri_answer |
        | 0 | pmFunctions | PMFAddAttachmentToArray | My trigger   |                 | SCRIPT   | array(1, 2)                | 1                      | 2                      | My Copy({i})            | $respuesta                   |


Scenario Outline: Create new Trigger: createDWS
        Given POST this data:
        """
        {
            "tri_title": "<tri_title>",
            "tri_description": "<tri_description>",
            "tri_type": "<tri_type>",
            "tri_params": {
                    "input": {
                    
                    "sharepointServer": "<tri_params.input.sharepointServer>",
                    "auth": "<tri_params.input.auth>",
                    "name": "<tri_params.input.name>",
                    "users": "<tri_params.input.users>",
                    "title": "<tri_params.input.title>",
                    "documents": "<tri_params.input.documents>"
                    
                },
                "output": {
                    "tri_answer": "<tri_params.output.tri_answer>"
                }
            }
        }
        """
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "tri_uid" in session array as variable "tri_uid<i>"

        Examples:
        | i | lib_name       | fn_name   | tri_title      | tri_description | tri_type | tri_params.input.sharepointServer | tri_params.input.auth | tri_params.input.name | tri_params.input.users | tri_params.input.title | tri_params.input.documents | tri_params.output.tri_answer |
        | 1 | pmTrSharepoint | createDWS | Sharepoint 1   |                 | SCRIPT   | @@SERVER                          | username:password     | Test DWS              | @@users                | Test DWS               | /files/test.doc            | $respuesta                   |
    
    Scenario Outline: Update Trigger
        Given PUT this data:
        """
        {
            "tri_title": "<tri_title>",
            "tri_description": "<tri_description>",
            "tri_type": "<tri_type>",
            "tri_params": {
                "input": {
                    "arrayData": "<tri_params.input.arrayData>",
                    "index": "<tri_params.input.index>",
                    "value": "<tri_params.input.value>",
                    "suffix": "<tri_params.input.suffix>"
                },
                "output": {
                    "tri_answer": "<tri_params.output.tri_answer>"
                }
            }
        }
        """
        And that I want to update a resource with the key "tri_uid" stored in session array as variable "tri_uid<i>"
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i | lib_name    | fn_name                 | tri_title     | tri_description | tri_type | tri_params.input.arrayData | tri_params.input.index | tri_params.input.value | tri_params.input.suffix | tri_params.output.tri_answer |
        | 0 | pmFunctions | PMFAddAttachmentToArray | My trigger... | ...             | SCRIPT   | array(1, 2, 3, 4)          | 1                      | 2                      | My Copy2({i})           | $r                           |


Scenario Outline: Create new Trigger: createDWS
        Given PUT this data:
        """
        {
            "tri_title": "<tri_title>",
            "tri_description": "<tri_description>",
            "tri_type": "<tri_type>",
            "tri_params": {
                    "input": {
                    
                    "sharepointServer": "<tri_params.input.sharepointServer>",
                    "auth": "<tri_params.input.auth>",
                    "name": "<tri_params.input.name>",
                    "users": "<tri_params.input.users>",
                    "title": "<tri_params.input.title>",
                    "documents": "<tri_params.input.documents>"
                    
                },
                "output": {
                    "tri_answer": "<tri_params.output.tri_answer>"
                }
            }
        }
        """
        And that I want to update a resource with the key "tri_uid" stored in session array as variable "tri_uid<i>"
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        
        Examples:
        | i | lib_name       | fn_name   | tri_title                 | tri_description | tri_type | tri_params.input.sharepointServer | tri_params.input.auth | tri_params.input.name | tri_params.input.users | tri_params.input.title | tri_params.input.documents | tri_params.output.tri_answer |
        | 1 | pmTrSharepoint | createDWS | Sharepoint 1 - Modified   |                 | SCRIPT   | @@SERVER_URL                      | username:password     | Test DWS              | @@users                | Test DWS               | /files/test.doc            | $respuesta                   |

    
    Scenario Outline: Get a Trigger that was created with the wizard
        Given that I want to get a resource with the key "tri_uid" stored in session array as variable "tri_uid<i>"
        And I request "project/14414793652a5d718b65590036026581/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "tri_title" is set to "<tri_title>"
        And that "tri_description" is set to "<tri_description>"
        And that "tri_type" is set to "<tri_type>"

        Examples:
       
        | i | lib_name       | fn_name                             | tri_title     | tri_description | tri_type |
        | 0 | pmFunctions    | PMFAddAttachmentToArray             | My trigger... | ...             | SCRIPT   |
        | 1 | pmTrSharepoint | createDWS                           | Test DWS      |                 | SCRIPT   |

    
    Scenario Outline: Delete a trigger of a project
        Given that I want to delete a resource with the key "tri_uid" stored in session array as variable "tri_uid<i>"
        And I request "project/14414793652a5d718b65590036026581/trigger"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:
        | i |
        | 0 |
        | 1 |

    
    Scenario: Get a List of triggers of a project
        And I request "project/14414793652a5d718b65590036026581/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the json data is an empty array