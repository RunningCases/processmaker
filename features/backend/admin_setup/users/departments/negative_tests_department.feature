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
        | Invalid dep_status                 | Department 3        |                                  | TRIGGER    | 400        | dep_status    |