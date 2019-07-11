<?php

use Faker\Factory;
use Tests\TestCase;

class SpoolRunTest extends TestCase
{
    /**
     * Constructor of the class.
     */
    function __construct()
    {
    }

    /**
     * Check if "envelope_cc" and "envelope_bcc" was set correctly in consecutive calls
     *
     * @covers SpoolRun::setData()
     *
     * @test
     */
    public function it_should_check_if_cc_and_bcc_set_correctly_in_consecutive_calls()
    {
        // Initializing Faker instance
        $faker = Factory::create();

        // Instancing SpoolRun class
        $spoolRun = new SpoolRun();

        // Set a first set of data
        $spoolRun->setData(
            G::generateUniqueID(),
            $faker->words(3, true),
            $faker->companyEmail,
            $faker->freeEmail,
            $faker->text(),
            $faker->dateTime()->format('Y-m-d H:i:s'),
            $faker->companyEmail,
            $faker->freeEmail
        );

        // Build the "to", "cc" an "bcc" values
        $spoolRun->runHandleEnvelopeTo();

        // Set a second set of data
        $spoolRun->setData(
            G::generateUniqueID(),
            $faker->words(3, true),
            $faker->companyEmail,
            $faker->freeEmail,
            $faker->text(),
            $faker->dateTime()->format('Y-m-d H:i:s'),
            $faker->companyEmail,
            $faker->freeEmail
        );

        // Build the "to", "cc" an "bcc" values
        $spoolRun->runHandleEnvelopeTo();

        // Get data to check
        $fileData = $spoolRun->getFileData();

        // Asserts
        $this->assertCount(1, $fileData['envelope_to']);
        $this->assertCount(1, $fileData['envelope_cc']);
        $this->assertCount(1, $fileData['envelope_bcc']);
    }
}
