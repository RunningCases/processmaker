package com.colosa.qa.automatization.common;

/**
 * Created with IntelliJ IDEA.
 * User: Herbert Saal
 * Date: 3/28/13
 * Time: 3:00 PM
 * To change this template use File | Settings | File Templates.
 */
public class BrowserSettings {
    private String _browserMode;
    private String _browserName;
    private String _browserVersion;
    private String _browserPlatform;
    private String _remoteServerURL;

    public String getBrowserMode() {
        return _browserMode;
    }

    public void setBrowserMode(String browserMode) {
        if(browserMode != null)
            _browserMode = browserMode;
    }

    public String getBrowserName() {
        return _browserName;
    }

    public void setBrowserName(String browserName) {
        if(browserName != null)
            _browserName = browserName;
    }

    public String getBrowserVersion() {
        return _browserVersion;
    }

    public void setBrowserVersion(String browserVersion) {
        if(browserVersion != null)
            _browserVersion = browserVersion;
    }

    public String getBrowserPlatform() {
        return _browserPlatform;
    }

    public void setBrowserPlatform(String browserPlatform) {
        if(browserPlatform != null)
            _browserPlatform = browserPlatform;
    }


    public String getRemoteServerUrl() {
        return _remoteServerURL;
    }

    public void setRemoteServerUrl(String remoteServerUrl) {
        if(remoteServerUrl != null)
            _remoteServerURL = remoteServerUrl;
    }

    public String toString() {
        return "Browser Mode:" + _browserMode + ", Browser Name:" + _browserName +
                ", Browser Version:" + _browserVersion + ", Browser Platform:" + _browserPlatform +
                ", Remote Server URL:" + _remoteServerURL;
    }
}
