Feature: WebEntry2
    PROD-181: As a process architect I want an option to force login on web
    entry forms so my users can start cases without having to go to the standard
    home/inbox section and without having to click "New Case."

    Scenario: Test WebEntry2 when session_block=1
        Given a new workspace
        Then Import process "WebEntryEventTest.pmx"
        Then Config env.ini with "session_block=1"
        Then Open a browser
        Then Go to Processmaker login
        Then Login as "admin" "admin"
        When Inside "frameMain"
        Then Double click on "WebEntryEvent"
        Then Right click on "first"
        Then Click on "Web Entry" inside "menu"
        Then Click on "Link" inside "tab"
        Then Copy "href" of "//*[@id='webEntryLink']//a"
        Then Logout Processmaker
        Then Open URL copied
        Then Verify the page does not redirect to the standard /login/login
        When Inside "iframe"
        Then Login as "admin" "admin"
        Then Verify the page goes to the WebEntry steps
        Then close the browser
