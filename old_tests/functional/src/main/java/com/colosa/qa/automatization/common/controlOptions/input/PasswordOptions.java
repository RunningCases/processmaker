package com.colosa.qa.automatization.common.controlOptions.input;

import com.colosa.qa.automatization.common.BrowserInstance;
import org.openqa.selenium.WebElement;

public class PasswordOptions extends InputControlOptions{

	private Boolean autocomplete = false;
	
	public PasswordOptions(){
		this.sqlConnection = null;
		this.sql = null;
	}

	private void setSQLConnection(){}
	private void setSQL(){}

	public void setMaxLength(int length){
		this.maxLength = length;
	}

	public void setSize(int i){
		super.setSize(i);
	}

	public void setAutocomplete(Boolean autocomplete){
		this.autocomplete = autocomplete;
	}

	public void fillForm(BrowserInstance browser) throws Exception{
		super.fillForm(browser);
		WebElement we = browser.findElement("dynaformDesigner.webElement.blankDynaformModal.autocomplete");
		if(this.autocomplete != we.isSelected())
			we.click();
	}

}