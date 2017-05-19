package com.colosa.qa.automatization.common;

import java.io.*;
import java.util.Properties;

public class ConfigurationSettings{

	private static ConfigurationSettings INSTANCE = null;
	private Properties applicationProperties;
	private String appConfigurationFile;

	private ConfigurationSettings(String defaultConfFile, String applicationConfFile) throws FileNotFoundException, IOException{
		FileInputStream propertiesFile = new FileInputStream(defaultConfFile);
		Properties defaultProperties = new Properties();

		this.appConfigurationFile = applicationConfFile;
		defaultProperties.load(propertiesFile);
		propertiesFile.close();

		this.applicationProperties = new Properties(defaultProperties);
		propertiesFile = new FileInputStream(this.appConfigurationFile);
		this.applicationProperties.load(propertiesFile);
		propertiesFile.close();
	}

	private static void createInstance(String defaultConfFile, String applicationConfFile) throws FileNotFoundException, IOException{
		if(INSTANCE == null)
			INSTANCE = new ConfigurationSettings(defaultConfFile, applicationConfFile);
	}

	public static ConfigurationSettings getInstance() throws FileNotFoundException, IOException{
        File f = new File("default.conf");
        File fa = new File("app.conf");
        if(f.exists() && fa.exists()) {
            ConfigurationSettings.getInstance("default.conf", "app.conf");
        }else{
            f = new File("." +  File.separator +"src"+ File.separator + "main"+ File.separator + "default.conf");
            fa = new File("." + File.separator +"src"+ File.separator +"main"+ File.separator + "app.conf");
            if(f.exists() && fa.exists()) {
                ConfigurationSettings.getInstance("." + File.separator +"src"+ File.separator + "main"+ File.separator + "default.conf", "." + File.separator +"src"+ File.separator + "main"+ File.separator + "app.conf");
            }
        }
		return INSTANCE;
	}

	public static ConfigurationSettings getInstance(String defaultConfFile, String applicationConfFile) throws FileNotFoundException, IOException{
		ConfigurationSettings.createInstance(defaultConfFile, applicationConfFile);
		return INSTANCE;
	}

	public String getSetting(String key){
		return this.applicationProperties.getProperty(key);
	}

	public void setSetting(String key, String value) throws IOException{
		this.setSetting(key, value, null);
	}

	public void setSetting(String key, String value, String comment) throws IOException{
		FileOutputStream fos = new FileOutputStream(this.appConfigurationFile);
		this.applicationProperties.setProperty(key, value);
		this.applicationProperties.store(fos, comment);
		fos.close();
	}
}