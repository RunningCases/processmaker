package com.colosa.qa.automatization;

import com.colosa.qa.automatization.common.Logger;
import org.junit.AfterClass;
import org.junit.BeforeClass;
import org.junit.runner.RunWith;
import org.junit.runners.Suite;
import org.junit.runners.Suite.SuiteClasses;

/**
 * Created with IntelliJ IDEA.
 * User: Herbert Saal
 * Date: 3/4/13
 * Time: 2:27 PM
 * To change this template use File | Settings | File Templates.

 */
@RunWith(value = Suite.class)
@SuiteClasses({
                com.colosa.qa.automatization.tests.test.Example.class

                })
public class TestSuiteAll {
    @BeforeClass
    public static void setUpClass() {
        Logger.addLog("Master setup");

    }

    @AfterClass public static void tearDownClass() {
       // Logger.addLog("Master tearDown");
    }
}
