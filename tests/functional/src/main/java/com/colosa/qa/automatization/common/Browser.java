package com.colosa.qa.automatization.common;

/*
public class Browser {

	private static WebDriver _driver = null;
    private static BrowserInstance _browserInstance = null;
	private static long _timeoutSeconds = 0;


    public static void checkBrowserSettingsFromSystemProperties() throws IOException {
        //Read browser information from default page this is done in the constructor of Browsersettings

        //Read browser information from registry
        //-Dbrowser.settings=1 -Dbrowser.mode=local -Dbrowser.name=firefox
        //-Dbrowser.settings=1 -Dbrowser.mode=remote -Dbrowser.name=firefox -Dremote.server.url=http:// browser.version=
        //if variable set from system properties overrride configuration of browser
        String setBrowserSettings = System.getProperty("browser.settings");

        if(setBrowserSettings != null){
            System.out.printf("User Browser Settings detected\n");
            String browserMode = System.getProperty("browser.mode");
            BrowserConfiguration.getInstance().setBrowserMode(browserMode);
            String browserName = System.getProperty("browser.name");
            BrowserConfiguration.getInstance().setBrowserName(browserName);

            System.out.printf("   Browser Mode: %s, Browser Name: %s \n",
                    browserMode, browserName);

            if(browserMode.equals("remote")){
                String browserVersion = System.getProperty("browser.version");
                BrowserConfiguration.getInstance().setBrowserVersion(browserVersion);
                String browserPlatform = System.getProperty("browser.platform");
                BrowserConfiguration.getInstance().setBrowserPlatform(browserPlatform);
                String remoteServerUrl = System.getProperty("remote.server.url");
                BrowserConfiguration.getInstance().setRemoteServerUrl(remoteServerUrl);

                System.out.printf("   Browser Version: %s, Browser Platform: %s, Remote Server URL:%s\n",
                        browserVersion, browserPlatform, remoteServerUrl );

            }
        }

    }

    //
     * Create a default instance of the browser using default configuration from registry
     * @return Return the instance of the browser created by default
     * @throws FileNotFoundException
     * @throws IOException
    //
	public static WebDriver driver() throws FileNotFoundException, IOException{
		if(_driver == null){
            System.out.printf("Create New Browser instance.\n");

            checkBrowserSettingsFromSystemProperties();


			String browserMode = BrowserConfiguration.getInstance().getBrowserMode(); //ConfigurationSettings.getInstance().getSetting("browser.mode");
			String browserName = "firefox";

			browserName = BrowserConfiguration.getInstance().getBrowserName();

			if(browserMode.equals("local")){
                _browserInstance = new BrowserInstance(browserMode, browserName, "", "", "");
                _driver = _browserInstance.getInstanceDriver();
			}

			if(browserMode.equals("remote")){
				String browserVersion = BrowserConfiguration.getInstance().getBrowserVersion();//ConfigurationSettings.getInstance().getSetting("browser.version");
				String browserPlatform = BrowserConfiguration.getInstance().getBrowserPlatform();//ConfigurationSettings.getInstance().getSetting("browser.platform");
				String remoteServerUrl = BrowserConfiguration.getInstance().getRemoteServerUrl();//ConfigurationSettings.getInstance().getSetting("remote.server.url");

				System.out.printf("Remote browser:%s, version:%s, platform:%s, url:%s \n", 
					browserName, browserVersion, browserPlatform, remoteServerUrl); 
				
				_browserInstance = new BrowserInstance(browserMode, browserName, browserVersion, browserPlatform, remoteServerUrl);

                _driver = _browserInstance.getInstanceDriver();
			}
		}
		return _driver;
	}



    public static void maximize(){
        _browserInstance.maximize();
    }

	public static void gotoUrl(String url){
		_browserInstance.gotoUrl(url);
	}

	public static String title(){
		return _browserInstance.title();
	}

	public static void close(){
		_browserInstance.quit();

        _driver = null;
        _browserInstance = null;
	}

    public static void quit(){
        _browserInstance.quit();
        _driver = null;
        _browserInstance = null;
    }

	public static By getBySearchCriteria(String str, Object... args) throws Exception{
		return _browserInstance.getBySearchCriteria(str, args);
	}	

	public static By getBySearchCriteriaUsingCriteria(String searchCriteria) throws Exception{
		return _browserInstance.getBySearchCriteriaUsingCriteria(searchCriteria);
	}	

	public static WebElement getParent(WebElement element) throws Exception{
		return _browserInstance.getParent(element);
	}

	public static WebElement getElement(String str) throws Exception{
		return  _browserInstance.getElement(str);
	}

	public static List<WebElement> getElements(String str) throws Exception{
		return _browserInstance.getElements(str);
	}

	public static WebElement getElementf(String str, Object... args) throws Exception{
		return _browserInstance.getElementf(str, args);
	}

	public static Boolean elementExists(String key, int occurrences) throws Exception{
		return _browserInstance.elementExists(key, occurrences);
	}

	public static Boolean elementExists(String key) throws Exception{
		return _browserInstance.elementExists(key);
	}

	public static Boolean elementExistsSearchCriteria(String searchCriteria, int occurrences) throws Exception{
		return _browserInstance.elementExistsSearchCriteria(searchCriteria, occurrences);
	}

	public static Boolean elementExistsSearchCriteria(String searchCriteria) throws Exception{
		return _browserInstance.elementExistsSearchCriteria(searchCriteria);
	}

	public static Boolean waitForElement(By elementLocator, long timeoutSeconds) throws Exception{

		return _browserInstance.waitForElement(elementLocator, timeoutSeconds);
     }

	public static void waitForElement(String key, long timeoutSeconds) throws Exception{
        _browserInstance.waitForElement(key, timeoutSeconds);
     }

     public static List<WebElement> getPreviousSimblingElements(WebElement currentElement){
		return _browserInstance.getPreviousSimblingElements(currentElement);
     }
}       */