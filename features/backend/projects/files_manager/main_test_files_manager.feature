@ProcessMakerMichelangelo @RestAPI
Feature: Files Manager Resources Main Tests
  Requirements:
  a workspace with the process 1265557095225ff5c688f46031700471 ("Test Michelangelo") already loaded
  there are two output documents in the process
  and workspace with the process 1455892245368ebeb11c1a5001393784 - "Process Complete BPMN" already loaded" already loaded

Background:
  Given that I have a valid access_token

 
Scenario Outline: Get a list of main process files manager
  Given I request "project/<project>/file-manager"
  Then the response status code should be 200
  And the response charset is "UTF-8"
  And the content type is "application/json"
  And the type is "array"
  And the "name" property in row 0 equals "templates"
  And the "name" property in row 1 equals "public"

  Examples:
  | test_description                          | project                          |
  | Get list of process Test Michelangelo     | 1265557095225ff5c688f46031700471 |
  | Get list of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 |


Scenario Outline: Get a list public folder of process files manager
  Given I request "project/<project>/file-manager?path=public"
  Then the response status code should be 200
  And the response charset is "UTF-8"
  And the content type is "application/json"
  And the type is "array"
  And the response has <records> records
  
  Examples:
  | test_description                          | project                          | records |
  | Get list of process Test Michelangelo     | 1265557095225ff5c688f46031700471 | 0       |
  | Get list of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 4       |


Scenario Outline: Get a list templates folder of process files manager
  Given I request "project/<project>/file-manager?path=templates"
  Then the response status code should be 200
  And the response charset is "UTF-8"
  And the content type is "application/json"
  And the type is "array"
  And the response has 2 records

  Examples:
  | test_description                          | project                          | records |
  | Get list of process Test Michelangelo     | 1265557095225ff5c688f46031700471 | 2       |
  | Get list of process Process Complete BPMN | 1455892245368ebeb11c1a5001393784 | 2       |


Scenario Outline: Create files and subfolders 
  Given POST this data:
  """
  {
    "prf_filename": "<prf_filename>",
    "prf_path": "<prf_path>",
    "prf_content": "<prf_content>"
  }
  """
  And I request "project/<project>/file-manager"
  Then the response status code should be <http_code>
  And the response charset is "UTF-8"
  And the content type is "application/json"
  And the type is "<type>"
  And store "prf_uid" in session array as variable "prf_uid_<prf_number>"

  Examples:
  | test_description                  | project                          | prf_filename     | prf_path                      | prf_content                   | http_code | type   | prf_number |
  | into public folder           .pm  | 1265557095225ff5c688f46031700471 | file_test_1.txt  | public/                       | only text                     | 200       | object | 0          |
  | into mailtemplates folder    .pm  | 1265557095225ff5c688f46031700471 | file_test_2.html | templates/                    | <h1>Test</h1><p>html test</p> | 200       | object | 1          |
  | into public subfolder        .pm  | 1265557095225ff5c688f46031700471 | file_test_3      | public/public_subfolder       | test                          | 200       | object | 2          |
  | into mailtemplates subfolder .pm  | 1265557095225ff5c688f46031700471 | file_test_4      | templates/templates_subfolder | test                          | 200       | object | 3          |
  | into public folder           .pmx | 1455892245368ebeb11c1a5001393784 | file_test_1.txt  | public/                       | only text                     | 200       | object | 8          |
  | into mailtemplates folder    .pmx | 1455892245368ebeb11c1a5001393784 | file_test_2.html | templates/                    | <h1>Test</h1><p>html test</p> | 200       | object | 9          |
  | into public subfolder        .pmx | 1455892245368ebeb11c1a5001393784 | file_test_3      | public/public_subfolder       | test                          | 200       | object | 10         |
  | into mailtemplates subfolder .pmx | 1455892245368ebeb11c1a5001393784 | file_test_4      | templates/templates_subfolder | test                          | 200       | object | 11         |


Scenario: Create files and subfolders with same name in path public
  Given POST this data:
  """
  {
    "prf_filename": "file_test_1.txt",
    "prf_path": "public/",
    "prf_content": "only text"
  }
  """
  And I request "project/1265557095225ff5c688f46031700471/file-manager"
  Then the response status code should be 400
  And the response status message should have the following text "already exists"


Scenario: Create files and subfolders with same name in path templates
  Given POST this data:
  """
  {
    "prf_filename": "file_test_2.html",
    "prf_path": "templates/",
    "prf_content": "<h1>Test</h1><p>html test</p>"
  }
  """
  And I request "project/1265557095225ff5c688f46031700471/file-manager"
  Then the response status code should be 400
  And the response status message should have the following text "already exists"
  
 
Scenario Outline: Update files by updating the content
  Given PUT this data:
  """
  {
    "prf_content": "<prf_content>"
  }
  """
  And that I want to update a resource with the key "prf_uid" stored in session array as variable "prf_uid_<prf_number>"
  And I request "project/<project>/file-manager"
  Then the response status code should be <http_code>
  And the response charset is "UTF-8"
  And the content type is "application/json"
  And the type is "<type>"

  Examples:
  | test_description                 .pm  | project                          | prf_filename     | prf_content                                  | http_code | type   | prf_number |
  | put into public folder           .pm  | 1265557095225ff5c688f46031700471 | file_test_1.txt  | only text - modified                         | 200       | object | 0          |
  | put into mailtemplates folder    .pm  | 1265557095225ff5c688f46031700471 | file_test_2.html | <h1>Test</h1><p>html test</p><i>modified</i> | 200       | object | 1          |
  | put into public subfolder        .pm  | 1265557095225ff5c688f46031700471 | file_test_3      | put test                                     | 200       | object | 2          |
  | put into mailtemplates subfolder .pm  | 1265557095225ff5c688f46031700471 | file_test_4      | put test                                     | 200       | object | 3          |
  | put into public folder           .pmx | 1455892245368ebeb11c1a5001393784 | file_test_1.txt  | only text - modified                         | 200       | object | 8          |
  | put into mailtemplates folder    .pmx | 1455892245368ebeb11c1a5001393784 | file_test_2.html | <h1>Test</h1><p>html test</p><i>modified</i> | 200       | object | 9          |
  | put into public subfolder        .pmx | 1455892245368ebeb11c1a5001393784 | file_test_3      | put test                                     | 200       | object | 10         |
  | put into mailtemplates subfolder .pmx | 1455892245368ebeb11c1a5001393784 | file_test_4      | put test                                     | 200       | object | 11         |


Scenario Outline: Get a single Files Manager and check some properties
  Given that I want to get a resource with the key "prf_uid" stored in session array as variable "prf_uid_<prf_number>"
  Given I request "project/<project>/file-manager"
  Then the response status code should be 200
  And the response charset is "UTF-8"
  And the content type is "application/json"
  And the type is "object"
  And that "prf_filename" is set to "<prf_filename>"
  And that "prf_path" is set to "<prf_path>"
  And that "prf_content" is set to "<prf_content>"
  
  Examples:
  | test_description                 .pm  | project                          | prf_filename     | prf_content                                  | http_code | type   | prf_number | row | prf_path                      |
  | put into public folder           .pm  | 1265557095225ff5c688f46031700471 | file_test_1.txt  | only text - modified                         | 200       | object | 0          | 1   | public/                       |
  | put into mailtemplates folder    .pm  | 1265557095225ff5c688f46031700471 | file_test_2.html | <h1>Test</h1><p>html test</p><i>modified</i> | 200       | object | 1          | 1   | templates/                    |
  | put into public subfolder        .pm  | 1265557095225ff5c688f46031700471 | file_test_3      | put test                                     | 200       | object | 2          | 0   | public/public_subfolder       |
  | put into mailtemplates subfolder .pm  | 1265557095225ff5c688f46031700471 | file_test_4      | put test                                     | 200       | object | 3          | 0   | templates/templates_subfolder |
  | put into public folder           .pmx | 1455892245368ebeb11c1a5001393784 | file_test_1.txt  | only text - modified                         | 200       | object | 8          | 1   | public/                       |
  | put into mailtemplates folder    .pmx | 1455892245368ebeb11c1a5001393784 | file_test_2.html | <h1>Test</h1><p>html test</p><i>modified</i> | 200       | object | 9          | 1   | templates/                    |
  | put into public subfolder        .pmx | 1455892245368ebeb11c1a5001393784 | file_test_3      | put test                                     | 200       | object | 10         | 0   | public/public_subfolder       |
  | put into mailtemplates subfolder .pmx | 1455892245368ebeb11c1a5001393784 | file_test_4      | put test                                     | 200       | object | 11         | 0   | templates/templates_subfolder |

  
Scenario Outline: Upload files to same folders
  Given POST I want to upload the file "<file>" to path "<prf_path>". Url "project/1265557095225ff5c688f46031700471/file-manager"
  And store "prf_uid" in session array as variable "prf_uid_<prf_number>"

  Examples:
  | file       | prf_path  | prf_number |
  | test1.html | templates | 4          | 
  | test2.html | templates | 5          |
  | test.txt   | public    | 6          |
  | TestQA.html| templates | 7          |


Scenario Outline: Verify if TestQA was overwrited 
  Given that I want to get a resource with the key "prf_uid" stored in session array as variable "prf_uid_<prf_number>"
  Given I request "project/1265557095225ff5c688f46031700471/file-manager"
  Then the response status code should be 200
  And the response charset is "UTF-8"
  And the content type is "application/json"
  And the type is "object"
  And that "prf_filename" is set to "<prf_filename>"
  And that "prf_content" is set to "<prf_content>"

  Examples:
  | prf_ filename | prf_content                                                 | prf_number |
  | TestQA.html   | Test QA -  cuando se realiza la sobreescritura desde upload | 7          | 
   

Scenario Outline: Update the overwritten file to return to their original values
  Given PUT this data:
  """
  {
    "prf_content": "Test QA"
  }
  """
  And that I want to update a resource with the key "prf_uid" stored in session array as variable "prf_uid_<prf_number>"
  And I request "project/1265557095225ff5c688f46031700471/file-manager"
  Then the response status code should be 200
  And the response charset is "UTF-8"
  And the content type is "application/json"
  And that "prf_filename" is set to "TestQA.html"
  And that "prf_content" is set to "Test QA"

  Examples:
  | prf_ filename | prf_content                                                 | prf_number |
  | TestQA.html   | Test QA -  cuando se realiza la sobreescritura desde upload | 7          |

    

Scenario Outline: Download files
  Given I request "project/1265557095225ff5c688f46031700471/file-manager/prf_uid/download"  with the key "prf_uid" stored in session array as variable "prf_uid_<prf_number>"
  Then the response status code should be 200
        
  Examples:
  | test_description  | prf_number | project                          |
  | Download file     | 0          | 1265557095225ff5c688f46031700471 |
  | Download file     | 1          | 1265557095225ff5c688f46031700471 |
  | Download file     | 2          | 1265557095225ff5c688f46031700471 |
  | Download file     | 4          | 1265557095225ff5c688f46031700471 |
  | Download file     | 8          | 1455892245368ebeb11c1a5001393784 |
  | Download file     | 9          | 1455892245368ebeb11c1a5001393784 |
  | Download file     | 10         | 1455892245368ebeb11c1a5001393784 |
  | Download file     | 11         | 1455892245368ebeb11c1a5001393784 |

    
Scenario Outline: Delete file
  Given that I want to delete a resource with the key "prf_uid" stored in session array as variable "prf_uid_<prf_number>"
  And I request "project/1265557095225ff5c688f46031700471/file-manager"
  Then the response status code should be 200
  And the response charset is "UTF-8"

  Examples:
  | test_description                 | prf_number |
  | delete public folder             | 0          |
  | delete mailtemplates folder      | 1          |
  | delete public subfolder          | 2          |
  | delete mailtemplates subfolder   | 3          |
  | delete mailtemplates subfolder   | 4          |
  | delete mailtemplates subfolder   | 5          |
  | delete mailtemplates subfolder   | 6          |
  | delete                           | 7          |
  | delete                           | 8          |
  | delete                           | 9          |
  | delete                           | 10         |
  | delete                           | 11         |


Scenario Outline: Delete folder
  Given that I want to delete the folder
  And I request "project/1265557095225ff5c688f46031700471/file-manager/folder?path=<prf_path>"
  Then the response status code should be 200
  And the response charset is "UTF-8"

  Examples:
  | test_description            | prf_path                      |
  | delete public sub folder    | templates/templates_subfolder |
  | delete templates sub folder | public/public_subfolder       |


#BUG 15207, The "Upload" accepts files with other extensions

Scenario Outline: Upload files with incorret extension ".exe" - "Project - Process Complete BPMN"
  Given POST I want to upload the file "<file>" to path "<prf_path>". Url "project/1265557095225ff5c688f46031700471/file-manager"
  #And store "prf_uid" in session array as variable "prf_uid_<prf_number>"
  And the response status message should have the following text "incorrect extension"

  Examples:
  | file            | prf_path  | prf_number |
  | filemanager.exe | templates | 1          | 