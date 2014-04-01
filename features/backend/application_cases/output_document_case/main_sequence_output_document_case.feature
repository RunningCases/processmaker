@ProcessMakerMichelangelo @RestAPI
Feature: Output Documents cases Main Tests
Requirements:
    a workspace with one case of the process "Test Output Document Case" 
    and there are six Output Documents in the process

Background:
    Given that I have a valid access_token


Scenario Outline: Generate or regenerates an output documents for a given case
        Given POST this data:
            """
            {
                "out_doc_uid": "<out_doc_uid>"         
            }
            """
        And I request "cases/33125846153383cecdf64f1079330191/output-document"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "array"
        And store "app_doc_uid" in session array as variable "app_doc_uid_<app_doc_uid_number>"
        
        Examples:

        | test_description                             | app_doc_uid_number | out_doc_uid                      |
        | Generate "output document only doc"          | 1                  | 2087233055331ef4127d238097105696 |
        | Generate "output document with versioning"   | 2                  | 5961108155331efc976cee7011445347 |
        | Generate "output document only pdf"          | 3                  | 7074907425331ef837aa8b2055964905 |
        | Generate "output document old version"       | 4                  | 7385645355331ee70ea6a87029841722 |
        | Generate "output document with pdf security" | 5                  | 8594478445331eff2d30767061922215 |


Scenario: Returns a list of the generated documents for a given cases
    Given I request "cases/33125846153383cecdf64f1079330191/output-documents"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "array"
    And the response has 6 records
    And the "app_doc_filename" property in row 0 equals "output document new version.pdf"
    And the "app_doc_filename" property in row 1 equals "output document with pdf security.pdf"
    And the "app_doc_filename" property in row 2 equals "output document only pdf.pdf"
    And the "app_doc_filename" property in row 3 equals "output document only doc.doc"
    And the "app_doc_filename" property in row 4 equals "output document with versioning.pdf"
    And the "app_doc_filename" property in row 5 equals "output document old version.pdf"
    

Scenario Outline: Returns an generated document for a given case
    Given I request "cases/33125846153383cecdf64f1079330191/output-document/app_doc_uid"  with the key "app_doc_uid" stored in session array as variable "app_doc_uid_<app_doc_uid_number>"
    Then the response status code should be 200
    And the response charset is "UTF-8"
    And the type is "Object"
    And the "app_doc_uid" property equals "<app_doc_uid>"
    And the "app_doc_filename" property equals "<app_doc_filename>"
    And the "doc_uid" property equals "<doc_uid>"
    And the "app_doc_version" property equals "<app_doc_version>"
    And the "app_doc_create_date" property equals "<app_doc_create_date>"
    And the "app_doc_create_user" property equals "<app_doc_create_user>"
    And the "app_doc_type" property equals "<app_doc_type>"
    And the "app_doc_index" property equals "<app_doc_index>"
    And the "app_doc_link" property equals "<app_doc_link>"


    Examples:

    | test_description                                   | app_doc_uid_number               | app_doc_filename                      | doc_uid                          | app_doc_version | app_doc_create_date | app_doc_create_user     | app_doc_type | app_doc_index | app_doc_link                                                                                    |            
    | Get Output "output document new version.pdf"       | 1                                | output document new version.pdf       | 3391282325331ee81c84715031595672 | 1               | 2014-03-26 12:29:30 | , Administrator (admin) | OUTPUT BOTH  | 1             | cases/cases_ShowOutputDocument?a=3000248055333006ab56a01005891659&v=1&ext=pdf&random=1256696859 |
    | Get Output "output document old version.pdf"       | 2                                | output document old version.pdf       | 7385645355331ee70ea6a87029841722 | 1               | 2014-03-26 12:29:33 | , Administrator (admin) | OUTPUT BOTH  | 2             | cases/cases_ShowOutputDocument?a=8865432395333006d75d824038425476&v=1&ext=pdf&random=1838956992 |
    | Get Output "output document only doc.doc"          | 3                                | output document only doc.doc          | 2087233055331ef4127d238097105696 | 1               | 2014-03-26 12:29:35 | , Administrator (admin) | OUTPUT DOC   | 3             | cases/cases_ShowOutputDocument?a=4447256265333006fe6fb00061503934&v=1&ext=doc&random=949245639  |
    | Get Output "output document only pdf.pdf"          | 4                                | output document only pdf.pdf          | 7074907425331ef837aa8b2055964905 | 1               | 2014-03-26 12:29:38 | , Administrator (admin) | OUTPUT PDF   | 4             | cases/cases_ShowOutputDocument?a=828039615533300724fdcb6091842678&v=1&ext=pdf&random=401448562  |
    | Get Output "output document with pdf security.pdf" | 5                                | output document with pdf security.pdf | 8594478445331eff2d30767061922215 | 1               | 2014-03-26 12:29:40 | , Administrator (admin) | OUTPUT BOTH  | 5             | cases/cases_ShowOutputDocument?a=25293137553330074713ab9073501576&v=1&ext=pdf&random=324546362  |
    | Get Output "output document with versioning.pdf"   | 6                                | output document with versioning.pdf   | 5961108155331efc976cee7011445347 | 1               | 2014-03-26 12:29:42 | , Administrator (admin) | OUTPUT BOTH  | 6             | cases/cases_ShowOutputDocument?a=354826487533300769e65e0027827984&v=1&ext=pdf&random=1682978530 |



Scenario Outline: Delete an uploaded or generated document from a case.
    Given that I want to delete a resource with the key "app_doc_uid" stored in session array as variable "app_doc_uid_<app_doc_uid_number>"
    And I request "cases/33125846153383cecdf64f1079330191/output-document"
    And the response status code should be 200
    And the content type is "application/json"
    And the response charset is "UTF-8"
    And the type is "object" 

    Examples:

    | app_doc_uid_number |
    | 1                  |
    | 2                  |
    | 3                  |
    | 4                  |
    | 5                  |      


        