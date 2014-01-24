package com.colosa.qa.automatization.common.controlOptions;

import com.colosa.qa.automatization.common.BrowserInstance;
import org.openqa.selenium.support.ui.Select;

import java.util.ArrayList;

public class DependentFieldsApplicableOption implements DependentFieldsApplicableBehavior{

	public void fillDependentFields(BrowserInstance browser, ArrayList<String> fields) throws Exception{
		if(fields == null)
			return;
		Select we = new Select(browser.findElement("dynaformDesigner.webElement.blankDynaformModal.dependentFields"));
		for(String fieldName:fields)
		{
			we.selectByVisibleText(fieldName);
		}
	}
	
}