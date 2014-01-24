package com.colosa.qa.automatization.tests.test;

import com.colosa.qa.automatization.common.FieldType;
import com.colosa.qa.automatization.common.Logger;
import org.junit.After;
import org.junit.Assert;
import org.junit.Before;
import org.junit.Test;

import java.io.FileNotFoundException;
import java.io.IOException;

/**
 * Created with IntelliJ IDEA.
 * User: marco
 * Date: 09-07-13
 * Time: 04:41 PM
 * To change this template use File | Settings | File Templates.
 */
public class Example extends com.colosa.qa.automatization.tests.common.Test {

    public Example(String browserName) throws IOException {
        super(browserName);
    }

    @Before
    public void setup(){

    }

    @After
    public void cleanup(){
        //browserInstance.quit();
    }

    @Test
    public void testExample() throws FileNotFoundException, IOException, Exception{
        pages.gotoDefaultUrl();

        Logger.addLog("Test testDependentFieldsCase with browserName:" + this.browserName);

    }
}
