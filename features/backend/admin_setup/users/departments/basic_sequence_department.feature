@ProcessMakerMichelangelo @RestAPI
Feature: Departaments
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
  
    Scenario: Create a new department in the workspace
        Given POST this data:
            """
            {

                "dep_title" : "TestDepartment",
                "dep_parent" : "",
                "dep_status" : "ACTIVE"

            }
            """
        And I request "department"
        Then the response status code should be 201
        And the response charset is "UTF-8"
        And the content type is "application/json"
        And the type is "object"
        And store "dep_uid" in session array
    
    
    Scenario: Update a department created in this script
        Given PUT this data:
            """
            {
                "dep_title" : "TestDepartment Update",
                "dep_status" : "INACTIVE"
            }
            """
        And that I want to update a resource with the key "dep_uid" stored in session array
        And I request "department"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"



    Scenario: Get a single department after update of the department created of this script 
        Given that I want to get a resource with the key "dep_uid" stored in session array
        Given I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the "dep_title" property equals "TestDepartment Update"
        And the "dep_status" property equals "INACTIVE"


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


    Scenario: List all Departaments in the workspace when exactly are 16 departaments created
        Given I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 17 record


    Scenario: Delete a department created in this script
        Given that I want to delete a resource with the key "dep_uid" stored in session array
        And I request "department"
        Then the response status code should be 200
        And the content type is "application/json"
        And the response charset is "UTF-8"
        And the type is "object"


    Scenario: List all Departaments in the workspace when exactly are 16 departaments created
        Given I request "department"
        Then the response status code should be 200
        And the response charset is "UTF-8"
        And the type is "array"
        And the response has 16 record