package com.colosa.qa.automatization.tests.common;

import com.colosa.qa.automatization.common.BrowserSettings;
import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.ConfigurationSettings;
import com.colosa.qa.automatization.common.Logger;
import com.colosa.qa.automatization.pages.Pages;
import org.junit.runner.RunWith;
import org.junit.runners.Parameterized;
import org.junit.runners.Parameterized.Parameters;
import org.junit.After;
import org.junit.runners.Suite;

import java.io.IOException;
import java.util.Arrays;
import java.util.Collection;

/**
 * Created with IntelliJ IDEA.
 * User: Herbert Saal
 * Date: 3/28/13
 * Time: 11:58 AM
 * To change this template use File | Settings | File Templates.
 */
@RunWith(value = Parameterized.class)
public abstract class Test {
    protected String browserName;
    protected BrowserInstance browserInstance;
    protected Pages pages;

    /*public Test() throws IOException {

        String browserName = "firefox";

        this.browserName = browserName;
        Logger.addLog("Test with browser:" + browserName);
        //initialize test pages
        initializeTest(browserName);
        //initialize pages
        pages = new Pages(browserInstance);
    } */

    public Test(String browserName) throws IOException {

        this.browserName = browserName;
        Logger.addLog("Test with browser:" + browserName);
        //initialize test pages
        initializeTest(browserName);
        //initialize pages
        pages = new Pages(browserInstance);
    }

    @Parameters
    public static Collection<Object[]> data() {
        int browserCount = 1;
        Object[][] data = null;
        //get the supported browsers from configuration file:
        try {
            browserCount = Integer.parseInt(ConfigurationSettings.getInstance().getSetting("browser.count"));

            data = new Object[browserCount][1];
            for (int i = 0; i< browserCount; i++){
                String browserName = ConfigurationSettings.getInstance().getSetting("browser.browser" + (i + 1));
                data[i][0] = browserName;
                Logger.addLog("Add Browser for test: "+browserName);
            }
        } catch (IOException e) {
            e.printStackTrace();  //To change body of catch statement use File | Settings | File Templates.
        }

        //Object[][] data = new Object[][] { { "firefox" }, { "chrome" }, { "ie" } };
        //Logger.addLog("Browsers to test with:" + Arrays.toString(data));

        return Arrays.asList(data);

        /*return Arrays.asList(new Object[][]) {
            { "AGCCG", "AGTTA" },
            { "AGTTA", "GATCA" },
            { "GGGAT", "AGCCA" }
        } */
    }

    public void initializeTest(String browserName) throws IOException {
        Logger.addLog("initializeTest .");
        //creates the instance of the Browser
        BrowserSettings browserSettings = BrowserInstance.getDefaultBrowserSettings(browserName);
        Logger.addLog("initializeTest ..");
        //change browser name
        browserSettings.setBrowserName(browserName);
        Logger.addLog("initializeTest ...");
        browserInstance = new BrowserInstance(browserSettings);
        Logger.addLog("initializeTest ....");
    }

    public void goToUrl(String url){
        browserInstance.gotoUrl(url);
    }
}
