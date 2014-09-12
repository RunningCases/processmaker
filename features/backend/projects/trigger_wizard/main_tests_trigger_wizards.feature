@ProcessMakerMichelangelo @RestAPI
Feature: Group
    Requirements:
        a workspace with the process 14414793652a5d718b65590036026581 ("Sample Project #1") already loaded
        there are three activities in the process
        and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded

    Background:
        Given that I have a valid access_token


   Scenario Outline: Get the Trigger Wizard List when there are exactly 6 library
        And I request "project/<project>/trigger-wizards"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 6 records
        And the "lib_name" property in row <i> equals "<lib_name>"
        And the "lib_title" property in row <i> equals "<lib_title>"
        And the "lib_class_name" property in row <i> equals "<lib_class_name>"

        Examples:
        | test_description    | i  | project                          | lib_name       | lib_title                      | lib_class_name                       |
        | Get in process .pm  | 0  | 14414793652a5d718b65590036026581 | pmFunctions    | ProcessMaker Functions         | class.pmFunctions.php                |
        | Get in process .pm  | 1  | 14414793652a5d718b65590036026581 | pmSugar        | Sugar CRM Triggers             | class.pmSugar.pmFunctions.php        |
        | Get in process .pm  | 2  | 14414793652a5d718b65590036026581 | pmTalend       | Talend ETL Integration         | class.pmTalend.pmFunctions.php       |
        | Get in process .pm  | 3  | 14414793652a5d718b65590036026581 | pmTrAlfresco   | Alfresco DM Triggers v. 0.1    | class.pmTrAlfresco.pmFunctions.php   |
        | Get in process .pm  | 4  | 14414793652a5d718b65590036026581 | pmTrSharepoint | Sharepoint DWS Triggers v. 0.1 | class.pmTrSharepoint.pmFunctions.php |
        | Get in process .pm  | 5  | 14414793652a5d718b65590036026581 | pmZimbra       | Zimbra Triggers v. 0.1         | class.pmZimbra.pmFunctions.php       |
        | Get in process .pmx | 0  | 1455892245368ebeb11c1a5001393784 | pmFunctions    | ProcessMaker Functions         | class.pmFunctions.php                |
        | Get in process .pmx | 1  | 1455892245368ebeb11c1a5001393784 | pmSugar        | Sugar CRM Triggers             | class.pmSugar.pmFunctions.php        |
        | Get in process .pmx | 2  | 1455892245368ebeb11c1a5001393784 | pmTalend       | Talend ETL Integration         | class.pmTalend.pmFunctions.php       |
        | Get in process .pmx | 3  | 1455892245368ebeb11c1a5001393784 | pmTrAlfresco   | Alfresco DM Triggers v. 0.1    | class.pmTrAlfresco.pmFunctions.php   |
        | Get in process .pmx | 4  | 1455892245368ebeb11c1a5001393784 | pmTrSharepoint | Sharepoint DWS Triggers v. 0.1 | class.pmTrSharepoint.pmFunctions.php |
        | Get in process .pmx | 5  | 1455892245368ebeb11c1a5001393784 | pmZimbra       | Zimbra Triggers v. 0.1         | class.pmZimbra.pmFunctions.php       |


    Scenario Outline: Get a single Library
        And I request "project/<project>/trigger-wizard/<lib_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "lib_name" is set to "<lib_name>"
        And that "lib_title" is set to "<lib_title>"
        And that "lib_class_name" is set to "<lib_class_name>"

        Examples:
        | test_description    | project                          | lib_name       | lib_title                      | lib_class_name                       |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmFunctions    | ProcessMaker Functions         | class.pmFunctions.php                |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmTrSharepoint | Sharepoint DWS Triggers v. 0.1 | class.pmTrSharepoint.pmFunctions.php |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmTrAlfresco   | Alfresco DM Triggers v. 0.1    | class.pmTrAlfresco.pmFunctions.php   |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmZimbra       | Zimbra Triggers v. 0.1         | class.pmZimbra.pmFunctions.php       |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmTalend       | Talend ETL Integration         | class.pmTalend.pmFunctions.php       |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmSugar        | Sugar CRM Triggers             | class.pmSugar.pmFunctions.php        |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmFunctions    | ProcessMaker Functions         | class.pmFunctions.php                |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmTrSharepoint | Sharepoint DWS Triggers v. 0.1 | class.pmTrSharepoint.pmFunctions.php |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmTrAlfresco   | Alfresco DM Triggers v. 0.1    | class.pmTrAlfresco.pmFunctions.php   |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmZimbra       | Zimbra Triggers v. 0.1         | class.pmZimbra.pmFunctions.php       |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmTalend       | Talend ETL Integration         | class.pmTalend.pmFunctions.php       |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmSugar        | Sugar CRM Triggers             | class.pmSugar.pmFunctions.php        |


    Scenario Outline: Get a single Function of the Library
        And I request "project/<project>/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "fn_name" is set to "<fn_name>"
        And that "fn_label" is set to "<fn_label>"

        Examples:
        | test_description    | project                          | lib_name       | fn_name                 | fn_label                                         |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmFunctions    | PMFAddAttachmentToArray | Add Element in Array                             |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmTrSharepoint | createDWS               | Create a DWS in Sharepoint server                |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmTrAlfresco   | Checkin                 | Checkin document/file                            |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmZimbra       | createZimbraAppointment | Create Appointment                               |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmTalend       | executeTalendWebservice | Executes a Talend Web Service                    |
        | Get in process .pm  | 14414793652a5d718b65590036026581 | pmSugar        | CreateSugarAccount      | Creates SugarCRM entries from the Account module |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmFunctions    | PMFAddAttachmentToArray | Add Element in Array                             |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmTrSharepoint | createDWS               | Create a DWS in Sharepoint server                |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmTrAlfresco   | Checkin                 | Checkin document/file                            |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmZimbra       | createZimbraAppointment | Create Appointment                               |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmTalend       | executeTalendWebservice | Executes a Talend Web Service                    |
        | Get in process .pmx | 1455892245368ebeb11c1a5001393784 | pmSugar        | CreateSugarAccount      | Creates SugarCRM entries from the Account module |


    Scenario Outline: Get a List of triggers of a project
        And I request "project/<project>/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

        Examples:
        | test_description                    | project                          | records |
        | Get list a triggers in process .pm  | 14414793652a5d718b65590036026581 | 0       |             
        | Get list a triggers in process .pmx | 1455892245368ebeb11c1a5001393784 | 3       |             


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
        And I request "project/<project>/trigger-wizard/<lib_name>/<fn_name>"
        And the content type is "application/json"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the type is "object"
        And store "tri_uid" in session array as variable "tri_uid<i>"

        Examples:
        | i | project                          | lib_name    | fn_name                 | tri_title    | tri_description | tri_type | tri_params.input.arrayData | tri_params.input.index | tri_params.input.value | tri_params.input.suffix | tri_params.output.tri_answer |
        | 0 | 14414793652a5d718b65590036026581 | pmFunctions | PMFAddAttachmentToArray | My trigger   |                 | SCRIPT   | array(1, 2)                | 1                      | 2                      | My Copy({i})            | $respuesta                   |
        
        
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
        | i | project                          | Description                                          | lib_name       | fn_name   | tri_title      | tri_description | tri_type | tri_params.input.sharepointServer | tri_params.input.auth | tri_params.input.name | tri_params.input.users | tri_params.input.title | tri_params.input.documents | tri_params.output.tri_answer |
        | 1 | 14414793652a5d718b65590036026581 | Create pmTrSharpoint                                 | pmTrSharepoint | createDWS | Sharepoint 1   |                 | SCRIPT   | @@SERVER                          | username:password     | Test DWS              | @@users                | Test DWS               | /files/test.doc            | $respuesta                   |
        | 2 | 14414793652a5d718b65590036026581 | Create a trigger without sending fields not required | pmTrSharepoint | createDWS | Sharepoint 2   |                 | SCRIPT   | @@SERVER                          | username:password     | Test DWS 1            | @@users                | Test DWS               | /files/test.doc            | $respuesta                   |
        

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
        | 2 |
        

    Scenario Outline: Get a List of triggers of a project
        And I request "project/<project>/triggers"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

        Examples:
        | test_description                    | project                          | records |
        | Get list a triggers in process .pm  | 14414793652a5d718b65590036026581 | 0       |             
        | Get list a triggers in process .pmx | 1455892245368ebeb11c1a5001393784 | 3       |       