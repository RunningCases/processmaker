package com.colosa.qa.automatization.common;

import java.io.FileNotFoundException;
import java.io.IOException;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 6/6/13
 * Time: 2:44 PM
 * To change this template use File | Settings | File Templates.
 */
public class Logger {

    public Logger(){

    }

    public static void addLog(String logText) {
        //System.out.println("VerifyPage Login .");
        //System.out.printf("Instance Remote browser:%s, version:%s, platform:%s, url:%s \n",
        //        browserName, browserVersion, browserPlatform, remoteServerUrl);
        //debug.enable=1
        String debugEnabled = null;
        try {
            debugEnabled = ConfigurationSettings.getInstance().getSetting("debug.enable");
        } catch (IOException e) {
            e.printStackTrace();  //To change body of catch statement use File | Settings | File Templates.
        }
        if(debugEnabled.equals("1")){
            System.out.println(logText);
        }

        //add log code

    }
}
