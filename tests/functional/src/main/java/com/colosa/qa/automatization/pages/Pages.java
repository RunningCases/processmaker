package com.colosa.qa.automatization.pages;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.ConfigurationSettings;

import java.io.IOException;

public class Pages{
    protected BrowserInstance _browserInstance;

    public Pages(BrowserInstance browserInstance){
        _browserInstance = browserInstance;
    }

    public void gotoDefaultUrl() throws IOException {
        String url;
        //default url
        url = ConfigurationSettings.getInstance().getSetting("server.url");

        _browserInstance.gotoUrl(url);
    }

    /*
	public Login Login() throws Exception{

		Login loginPage = new Login(_browserInstance);
		
		return loginPage;
	}

	public Main Main() throws Exception{

		Main mainPage = new Main(_browserInstance);

		return mainPage;
	}
    */


}