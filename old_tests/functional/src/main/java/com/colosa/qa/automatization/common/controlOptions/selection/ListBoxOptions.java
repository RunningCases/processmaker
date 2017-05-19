package com.colosa.qa.automatization.common.controlOptions.selection;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.controlOptions.DependentFieldsNotApplicableOption;
import org.openqa.selenium.WebElement;

public class ListBoxOptions extends DropDownOptions{

	private int size = 4;

	public ListBoxOptions(){

	}

	//protected void setReadOnly(Boolean readOnly){}

	public void setSize(int size){
		this.size = size;
	}

	@Override
	public void fillForm(BrowserInstance browser) throws Exception{
		this.dependentFieldsApplicableBehavior = new DependentFieldsNotApplicableOption();;
		this.readOnly = null;
		super.fillForm(browser);
		WebElement we = browser.findElement("dynaformDesigner.webElement.blankDynaformModal.size");
		we.clear();
		we.sendKeys(Integer.toString(this.size));
	}
	
}