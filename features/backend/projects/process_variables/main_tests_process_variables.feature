@ProcessMakerMichelangelo @RestAPI
Feature: Process Variables Main Tests
    Requirements:
        a workspace with the process 3306142435318cd22d1eba2015305561 ("Process variables") already loaded
        there are three activities in the process
        and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded

    Background:
        Given that I have a valid access_token

    Scenario Outline: Get all variables of a Process .pm "Process variables"
        And I request "project/3306142435318cd22d1eba2015305561/variables"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "var_name" property in row <i> equals "<var_name>"

        Examples:
        | i  | var_name     |
        | 0  | SYS_LANG     |
        | 1  | SYS_SKIN     |
        | 2  | SYS_SYS      |
        | 3  | APPLICATION  |
        | 4  | PROCESS      |
        | 5  | TASK         |
        | 6  | INDEX        |
        | 7  | USER_LOGGED  |
        | 8  | USR_USERNAME |
        | 9  | PIN          |
        | 10 | grilla2      |
        | 11 | grilla3      |
        | 12 | grilla1      |


    Scenario Outline: Get all variables of a Process .pmx "Process Complete BPMN"
        And I request "project/1455892245368ebeb11c1a5001393784/variables"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the "var_name" property in row <i> equals "<var_name>"

        Examples:
        | i  | var_name           |
        | 0  | SYS_LANG           |
        | 1  | SYS_SKIN           |
        | 2  | SYS_SYS            |
        | 3  | APPLICATION        |
        | 4  | PROCESS            |
        | 5  | TASK               |
        | 6  | INDEX              |
        | 7  | USER_LOGGED        |
        | 8  | USR_USERNAME       |
        | 9  | PIN                |
        | 10 | ID_PAIS            |
        | 11 | TXT_CARRERA_DATOS  |
        | 12 | TXT_NUMERO         |
        | 13 | JS                 |
        | 14 | PHP_URL            |
        | 15 | PMTABLE_CONNECTION |
        | 16 | IDENTIFICACION     |
        | 17 | NOMBRE             |
        | 18 | APELLIDO           |
        | 19 | EDAD               |
        | 20 | DIRECCION          |
        | 21 | FECHA              |
        | 22 | ESTATURA           |
        | 23 | WEB_TXT_DATO1      |
    

    Scenario Outline: Get grid variables of a Process
        Given I request "project/<project>/grid/variables"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has <records> records

        Examples:
        | test_description        | project                          | records |
        | Get of the process .pm  | 3306142435318cd22d1eba2015305561 | 0       |
        | Get of the process .pmx | 1455892245368ebeb11c1a5001393784 | 0       |
        
    
    Scenario: Get all variables of a Grid
        Given I request "project/3306142435318cd22d1eba2015305561/grid/8246998615318cd7cc451d2089449499/variables"
        And the content type is "application/json"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And that "var_name" is set to "fecha"
        And that "var_label" is set to "Date Static Date Y/m/d 24/07-31/07"
        And that "var_type" is set to "date"
        And that "var_name" is set to "text"
        And that "var_label" is set to "Text Field + Validate Any"
        And that "var_type" is set to "text"
        And that "var_name" is set to "currency"
        And that "var_label" is set to "Currency Fields Real Number ###,###,###,###.##"
        And that "var_type" is set to "currency"
        And that "var_name" is set to "porcentage"
        And that "var_label" is set to "Porcentage Field + Real Number ###.## %"
        And that "var_type" is set to "percentage"
        And that "var_name" is set to "textarea"
        And that "var_label" is set to "Text area Field"
        And that "var_type" is set to "textarea"
        And that "var_name" is set to "dropdown"
        And that "var_label" is set to "Dropdown Field"
        And that "var_type" is set to "dropdown"
        And that "var_name" is set to "yesno"
        And that "var_label" is set to "Yes/No Field"
        And that "var_type" is set to "yesno"
        And that "var_name" is set to "checkbox"
        And that "var_label" is set to "Check Box Field"
        And that "var_type" is set to "checkbox"
        And that "var_name" is set to "suggest1"
        And that "var_label" is set to "Suggest Usuarios"
        And that "var_type" is set to "suggest"
        And that "var_name" is set to "link"
        And that "var_label" is set to "Link Field"
        And that "var_type" is set to "link"
        And that "var_name" is set to "addfile"
        And that "var_label" is set to "Add File Field"
        And that "var_type" is set to "file"
