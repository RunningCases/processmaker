@ProcessMakerMichelangelo @RestAPI
Feature: Departments Negative Tests


Background:
    Given that I have a valid access_token

Scenario Outline: Create a new departments in the workspace with bad parameters (negative tests)
        Given POST this data:
            """
            {

                "dep_title" : "<dep_title>",
                "dep_parent" : "<dep_parent>",
                "dep_status" : "<dep_status>"

            }
            """
        And I request "department"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:

        | test_description                   | dep_title           | dep_parent                       | dep_status | error_code | error_message |
        | without dep_title                  |                     |                                  | ACTIVE     | 400        | dep_title     |
        | Invalid dep_parent                 | Department 2        | 28036030000000000000005009591640 | ACTIVE     | 400        | dep_parent    |
        | Invalid dep_status                 | Department 1        |                                  | SAMPLE     | 400        | dep_status    |
       

 Scenario Outline: Assign user to department (NEGATIVE TEST)
        Given PUT this data:
        """
        {

        }
        """
        And I request "department/<dep_uid>/assign-user/<usr_uid>"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"

        Examples:

        | Description            | dep_uid                          | usr_uid                          | error_code | error_message |
        | Invalid usr_uid        | 56255940652d5674c75bc70062927441 | 23085900000000000000002059274810 | 400        | usr_uid       |
        | Invalid dep_uid        | 56255900000000000000000062927441 | 23085901752d5671483a4c2059274810 | 400        | dep_uid       | 
        | Field Requered usr_uid | 56255940652d5674c75bc70062927441 |                                  | 404        | Not Found     |
        | Field Requered dep_uid |                                  | 25286582752d56713231082039265791 | 404        | Not Found     | 
        


Scenario: Delete a department when have asigned user (negative tests)
        Given that I want to delete a resource with the key "15978182252d5674d210310076985235"
        And I request "department/15978182252d5674d210310076985235"
        Then the response status code should be 400
        And the response status message should have the following text "<cannot be deleted>"
        