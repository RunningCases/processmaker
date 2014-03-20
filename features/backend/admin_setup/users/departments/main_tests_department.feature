@ProcessMakerMichelangelo @RestAPI
Feature: Departaments Main Tests
Requirements:
    a workspace with the 16 departments created already loaded

Background:
    Given that I have a valid access_token


    Scenario: List all Departaments in the workspace when exactly are 16 departaments created
        Given I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 16 record

    
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
        | Created a department padre with status active   | 1              | Department 1        |                                  | ACTIVE     |
        | Created a department hijo with status active    | 2              | Department 2        | 28036037252d56752770585009591640 | ACTIVE     |
        | Created a department padre with status inactive | 3              | Department 3        |                                  | INACTIVE   |
        | Created a department hijo with status inactive  | 4              | Department 4        | 28036037252d56752770585009591640 | INACTIVE   |
        | Created a department with character special     | 5              | Department 5!@#$%^& |                                  | ACTIVE     |


    Scenario: Create a department with same name
        Given POST this data:
            """
            {

                "dep_title" : "Department 1",
                "dep_parent" : "",
                "dep_status" : "ACTIVE"

            }
            """
        And I request "department"
        Then the response status code should be 400
        And the response status message should have the following text "exist"
    
    
    Scenario: List all Departaments in the workspace when exactly are 21 departaments created
        Given I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 19 record

           
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
        | Update of field title and status of department  | 1              | Department 1 UPDATE | INACTIVE   |
        | Update of field title and status of department  | 3              | Department 3 UPDATE | ACTIVE     |
        

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
        | 1              | Department 1 UPDATE  | INACTIVE   |
        | 3              | Department 3 UPDATE  | ACTIVE     |


     #Scenario: Assign user to department created in this script
   #     Given POST this data:
    #        """
     #       {

             #  "dep_title" : "TestDepartment",
              #  "dep_parent" : "",git 
               # "dep_status" : "ACTIVE"

            #}
            #"""
        #And I request "department/<dep_uid>/assign-user/62511352152d5673bba9cd4062743508 "
        #Then the response status code should be 201
        #And the response charset is "UTF-8"
        #And the content type is "application/json"
        #And the type is "object"
        #And store "dep_uid" in session array

   
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
        And the response has 16 record