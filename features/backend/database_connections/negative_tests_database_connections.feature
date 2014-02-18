@ProcessMakerMichelangelo @RestAPI
Feature: DataBase Connections Negative Tests

  Background:
    Given that I have a valid access_token

  Scenario Outline: Test database connection to test
        Given POST this data:
            """
            {
                "dbs_type": "<dbs_type>",
                "dbs_server": "<dbs_server>",
                "dbs_database_name": "<dbs_database_name>",
                "dbs_username": "<dbs_username>",
                "dbs_password": "<dbs_password>",
                "dbs_port": 3306,
                "dbs_encode": "<dbs_encode>",
                "dbs_description": "<dbs_description>"
            }
            """
        And I request "project/<project>/database-connection"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"
        

        Examples:

        | test_description                 | project                          | dbs_type | dbs_server                 | dbs_database_name | dbs_username | dbs_password | dbs_encode | dbs_description    | error_code | error_message     |
        | Field required dbs_type          | 74737540052e1641ab88249082085472 |          | michelangelo-be.colosa.net | test              | testuser     | sample       | utf8       | mysql connection   | 400        | dbs_type          |         
        | Field required dbs_server        | 74737540052e1641ab88249082085472 | mysql    |                            | test              | testuser     | sample       | utf8       | mysql connection   | 400        | dbs_server        |         
        | Field required dbs_database_name | 74737540052e1641ab88249082085472 | mysql    | michelangelo-be.colosa.net |                   | testuser     | sample       | utf8       | mysql connection   | 400        | dbs_database_name |         
        | Field required dbs_encode        | 74737540052e1641ab88249082085472 | mysql    | michelangelo-be.colosa.net | test              | testuser     | sample       |            | mysql connection   | 400        | dbs_encode        |         
        | Field required project           |                                  | mysql    | michelangelo-be.colosa.net | test              | testuser     | sample       | utf8       | mysql connection   | 400        | prj_uid           |     


  Scenario: Test database connection to test with parameter wrong port
        Given POST this data:
            """
            {
                "dbs_type": "mysql",
                "dbs_server": "michelangelo-be.colosa.net",
                "dbs_database_name": "test",
                "dbs_username": "testuser",
                "dbs_password": "sample",
                "dbs_port": 10000,
                "dbs_encode": "utf8",
                "dbs_description": "mysql connection"
            }
            """
        And I request "project/74737540052e1641ab88249082085472/database-connection"
        Then the response status code should be 400
        And the response status message should have the following text "port"