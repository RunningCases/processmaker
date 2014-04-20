@ProcessMakerMichelangelo @RestAPI
Feature: Departaments Main Tests
Requirements:
    a workspace with the 16 departments created already loaded

Background:
    Given that I have a valid access_token


    Scenario: List all Departaments in the workspace when exactly are 15 departaments created
        Given I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 15 record

    
    Scenario: Get a single department of de Sales Division department 
        Given I request "department/12921473252d567506e6e63079240767"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And the "dep_uid" property equals "12921473252d567506e6e63079240767"
        And the "dep_parent" property equals ""
        And the "dep_title" property equals "Sales Division"
        And the "dep_status" property equals "ACTIVE"
        And the "dep_manager" property equals "38102442252d5671a629009013495090"
        And the "dep_ldap_dn" property equals ""
        And the "dep_last" property equals "0"
        And the "dep_manager_username" property equals "dylan"
        And the "dep_manager_firstname" property equals "Dylan"
        And the "dep_manager_lastname" property equals "Burns"
        And the "has_children" property equals "0"


    Scenario: Get a List of Assigned User (Department: Sales Division)
        Given I request "department/12921473252d567506e6e63079240767/assigned-user"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 4 record
       
        
    Scenario Outline: Create a new departments in the workspace
        Given POST this data:
            """
            {

                "dep_title" : "<dep_title>",
                "dep_parent" : "<dep_parent>",
                "dep_status" : "<dep_status>"

            }
            """
        And I request "department"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "dep_uid" in session array as variable "dep_uid_<dep_uid_number>"

        Examples:

        | test_description                                | dep_uid_number | dep_title           | dep_parent                       | dep_status |
        | Created a department padre with status active   | 1              | Department A        |                                  | ACTIVE     |
        | Created a department hijo with status active    | 2              | Department B        | 28036037252d56752770585009591640 | ACTIVE     |
        | Created a department padre with status inactive | 3              | Department C        |                                  | INACTIVE   |
        | Created a department hijo with status inactive  | 4              | Department D        | 28036037252d56752770585009591640 | INACTIVE   |
        | Created a department with character special     | 5              | Department E!@#$%^& |                                  | ACTIVE     |


    Scenario: Create a department with same name
        Given POST this data:
            """
            {

                "dep_title" : "Department A",
                "dep_parent" : "",
                "dep_status" : "ACTIVE"

            }
            """
        And I request "department"
        Then the response status code should be 400
        And the response status message should have the following text "exist"
    
    
    Scenario: List all Departaments in the workspace when exactly are 20 departaments created
        Given I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 18 record

           
    Scenario Outline: Update a department created in this script
        Given PUT this data:
            """
            {

                "dep_title" : "<dep_title>",
                "dep_status" : "<dep_status>"

            }
            """
        And that I want to update a resource with the key "dep_uid" stored in session array as variable "dep_uid_<dep_uid_number>"
        And I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"

        Examples:

        | test_description                                | dep_uid_number | dep_title           | dep_status |
        | Update of field title and status of department  | 1              | Department A UPDATE | INACTIVE   |
        | Update of field title and status of department  | 2              | Department B UPDATE | ACTIVE     |
        

    Scenario Outline: Get a single department after update of the department created of this script 
        Given that I want to get a resource with the key "dep_uid" stored in session array as variable "dep_uid_<dep_uid_number>"
        And I request "department"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"
        And that "dep_title" is set to "<dep_title>"
        And that "dep_status" is set to "<dep_status>"

        Examples:

        | dep_uid_number | dep_title            | dep_status |
        | 1              | Department A UPDATE  | INACTIVE   |
        | 3              | Department B UPDATE  | ACTIVE     |


    Scenario Outline: Assign user to department created in this script
        Given PUT this data:
        """
        {

        }
        """
        And that I want to update "Assigned users"
        And I request "department/dep_uid/assign-user/<usr_uid>"  with the key "dep_uid" stored in session array as variable "dep_uid_<dep_uid_number>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        
        Examples:

        | Description                                                | dep_uid_number | usr_uid                          |
        | Assign user arlene in department 1 created in this script  | 1              | 23085901752d5671483a4c2059274810 |
        | Assign user andrew in department 1 created in this script  | 1              | 23085901752d5671483a4c2059274810 | 
        | Assign user amy in department 2 created in this script     | 2              | 25286582752d56713231082039265791 |
        | Assign user sandra in department 2 created in this script  | 2              | 25286582752d56713231082039265791 | 
        | Assign user francis in department 5 created in this script | 5              | 62511352152d5673bba9cd4062743508 | 

   
    Scenario Outline: Set manager user to department
        Given PUT this data:
        """
        {

        }
        """
        And that I want to update "Department supervisor"
        And I request "department/dep_uid/set-manager/<usr_uid>"  with the key "dep_uid" stored in session array as variable "dep_uid_<dep_uid_number>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"


        Examples:

        | Description                          | dep_uid_number | usr_uid                          |
        | Set manager user "arlene" in group 1 | 1              | 23085901752d5671483a4c2059274810 |
        | Set manager user "sandra" in group 2 | 2              | 25286582752d56713231082039265791 | 
        

    Scenario Outline: Get a single department of created in this script 
        Given that I want to get a resource with the key "dep_uid" stored in session array as variable "dep_uid_<dep_uid_number>"
        And I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "object"
        And that "dep_title" is set to "<dep_title>"
        And that "dep_status" is set to "<dep_status>"
        And that "dep_manager" is set to "<dep_manager>"
        And that "dep_manager_username" is set to "<dep_manager_username>"
        And that "dep_manager_firstname" is set to "<dep_manager_firstname>"
        And that "dep_manager_lastname" is set to "<dep_manager_lastname>"

        Examples:

        | dep_uid_number | dep_title           | dep_status | dep_manager                      | dep_manager_username | dep_manager_firstname | dep_manager_lastname |
        | 1              | Department A UPDATE | ACTIVE     | 23085901752d5671483a4c2059274810 | arlene               | Arlene                | Cleveland            |
        | 2              | Department B        | ACTIVE     | 25286582752d56713231082039265791 | sandra               | Sandra                | Casey                |
        
      
    Scenario Outline: Unassign a User to department
        Given POST this data:
        """
        {

        }
        """
        And that I want to update "Assigned users"
        And I request "department/dep_uid/unassign-user/<usr_uid>"  with the key "dep_uid" stored in session array as variable "dep_uid_<dep_uid_number>"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
       

        Examples:

        | Description                                                  | dep_uid_number | usr_uid                          |
        | Unassign user arlene in department 1 created in this script  | 1              | 23085901752d5671483a4c2059274810 |
        | Unassign user andrew in department 1 created in this script  | 1              | 23085901752d5671483a4c2059274810 | 
        | Unassign user amy in department 2 created in this script     | 2              | 25286582752d56713231082039265791 |
        | Unassign user sandra in department 2 created in this script  | 2              | 25286582752d56713231082039265791 | 
        | Unassign user francis in department 5 created in this script | 5              | 62511352152d5673bba9cd4062743508 |


    Scenario: List all Departaments in the workspace when exactly are 15 departaments created
        Given I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 18 record


    Scenario Outline: Delete a department created in this script
        Given that I want to delete a resource with the key "dep_uid" stored in session array as variable "dep_uid_<dep_uid_number>"
        And I request "department"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"

        Examples:

        | dep_uid_number |
        | 1              |
        | 2              |
        | 3              |
        | 4              |
        | 5              |


    Scenario: List all Departaments in the workspace when exactly are 16 departaments created
        Given I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 15 record