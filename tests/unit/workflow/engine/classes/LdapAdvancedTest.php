<?php

namespace Tests\unit\workflow\engine\classes;

use Illuminate\Support\Facades\Cache;
use LdapAdvanced;
use Tests\TestCase;

class LdapAdvancedTest extends TestCase
{
    private $ldapAdvanced;

    /**
     * Method setUp.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ldapAdvanced = new LdapAdvanced();
    }

    /**
     * This tests the getDiagnosticMessage method.
     * Many paths cannot be covered because an active connection is required for 
     * Active Directory or LDAP.
     * @test
     * @covers LdapAdvanced::getDiagnosticMessage()
     */
    public function it_should_test_getDiagnosticMessage_method()
    {
        $logDirectory = PATH_DATA . "log";
        if (!is_dir($logDirectory)) {
            mkdir($logDirectory);
        }
        $linkIdentifier = ldap_connect('localhost');
        $this->ldapAdvanced->getDiagnosticMessage($linkIdentifier);
        $message = Cache::get('ldapMessageError');
        $this->assertEquals('Success.', $message);

        @ldap_bind($linkIdentifier, 'uid=user1,ou=system', 'password');
        $this->ldapAdvanced->getDiagnosticMessage($linkIdentifier);
        $message = Cache::get('ldapMessageError');
        $this->assertEquals("Can't contact LDAP server.", $message);
    }
}
