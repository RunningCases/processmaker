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
                "dbs_port": <dbs_port>,
                "dbs_encode": "<dbs_encode>",
                "dbs_description": "<dbs_description>"
            }
            """
        And I request "project/<project>/database-connection/test"
        Then the response status code should be <error_code>
        And the response status message should have the following text "<error_message>"
        

        Examples:

        | test_description                 | project                          | dbs_type | dbs_server    | dbs_database_name | dbs_username   | dbs_password     | dbs_port | dbs_encode | dbs_description    | error_code | error_message     |
        | Field required dbs_type          | 74737540052e1641ab88249082085472 |          | 192.168.11.71 | rb_cochalo        | root           | atopml2005       | 3306     | utf8       | mysql connection   | 400        | dbs_type          |         
        | Field required dbs_server        | 74737540052e1641ab88249082085472 | mysql    |               | rb_cochalo        | root           | atopml2005       | 3306     | utf8       | mysql connection   | 400        | dbs_server        |         
        | Field required dbs_database_name | 74737540052e1641ab88249082085472 | mysql    | 192.168.11.71 |                   | root           | atopml2005       | 3306     | utf8       | mysql connection   | 400        | dbs_database_name |         
        | Field required dbs_username      | 74737540052e1641ab88249082085472 | mysql    | 192.168.11.71 | rb_cochalo        |                | atopml2005       | 3306     | utf8       | mysql connection   | 400        | dbs_username      |         
        | Field required dbs_port          | 74737540052e1641ab88249082085472 | mysql    | 192.168.11.71 | rb_cochalo        | root           | atopml2005       |          | utf8       | mysql connection   | 400        | dbs_port          |         
        | Field required dbs_encode        | 74737540052e1641ab88249082085472 | mysql    | 192.168.11.71 | rb_cochalo        | root           | atopml2005       | 3306     |            | mysql connection   | 400        | dbs_encode        |         
        | Incorrect dbs_password           | 74737540052e1641ab88249082085472 | mysql    | 192.168.11.71 | rb_cochalo        | root           | atsample005      | 3306     | utf8       | mysql connection   | 400        | dbs_password      |         
        | Field required project           |                                  | mysql    | 192.168.11.71 | rb_cochalo        | root           | atopml2005       | 3306     | utf8       | mysql connection   | 400        | prj_uid           |     

  

  