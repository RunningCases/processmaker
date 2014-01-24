package com.colosa.qa.automatization.common;

import java.io.IOException;

/**
 * Created with IntelliJ IDEA.
 * User: Herbert Saal
 * Date: 3/8/13
 * Time: 5:16 PM
 * To change this template use File | Settings | File Templates.
 */
public class BrowserConfiguration {
    private static BrowserConfiguration ourInstance = new BrowserConfiguration();

    public static BrowserConfiguration getInstance() {
        return ourInstance;
    }

    private BrowserConfiguration() {
        //init browser configuration by default
        try {
            getBrowserMode();
            getBrowserName();
            getBrowserVersion();
            getBrowserPlatform();
            getRemoteServerUrl();
        } catch (IOException e) {
            e.printStackTrace();  //To change body of catch statement use File | Settings | File Templates.
        }

    }

    public String getBrowserMode() throws IOException {
        //read configuration from registry if not found read from configuration file and store in registry
        String browserMode = (String)Registry.getInstance().getReference("browser.mode");
        if(browserMode == null){
            browserMode = ConfigurationSettings.getInstance().getSetting("browser.mode");
            Registry.getInstance().register("browser.mode", browserMode);
        }

        return browserMode;

    }

    public void setBrowserMode(String browserMode) throws IOException {
        //set configuration in registry
        Registry.getInstance().register("browser.mode", browserMode);
    }

    public String getBrowserName() throws IOException {
        String browserName = (String)Registry.getInstance().getReference("browser.name");
        if(browserName == null){
            browserName = ConfigurationSettings.getInstance().getSetting("browser.name");
            Registry.getInstance().register("browser.name", browserName);
        }

        return browserName;
    }

    public void setBrowserName(String browserName) throws IOException {
        //set configuration in registry
        Registry.getInstance().register("browser.name", browserName);
    }

    public String getBrowserVersion() throws IOException {
        String browserVersion = (String)Registry.getInstance().getReference("browser.version");
        if(browserVersion == null){
            browserVersion = ConfigurationSettings.getInstance().getSetting("browser.version");
            Registry.getInstance().register("browser.version", browserVersion);
        }

        return browserVersion;
    }

    public void setBrowserVersion(String browserVersion) throws IOException {
        //set configuration in registry
        Registry.getInstance().register("browser.version", browserVersion);
    }

    public String getBrowserPlatform() throws IOException {
        String browserPlatform = (String)Registry.getInstance().getReference("browser.platform");
        if(browserPlatform == null){
            browserPlatform = ConfigurationSettings.getInstance().getSetting("browser.platform");
            Registry.getInstance().register("browser.platform", browserPlatform);
        }
        return browserPlatform;
    }

    public void setBrowserPlatform(String browserPlatform) throws IOException {
        //set configuration in registry
        Registry.getInstance().register("browser.platform", browserPlatform);
    }

    public String getRemoteServerUrl() throws IOException {
        String remoteServerUrl = (String)Registry.getInstance().getReference("remote.server.url");
        if(remoteServerUrl == null){
            remoteServerUrl = ConfigurationSettings.getInstance().getSetting("remote.server.url");
            Registry.getInstance().register("remote.server.url", remoteServerUrl);
        }

        return remoteServerUrl;
    }

    public void setRemoteServerUrl(String remoteServerUrl) throws IOException {
        //set configuration in registry
        Registry.getInstance().register("remote.server.url", remoteServerUrl);
    }

}
