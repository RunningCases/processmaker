package com.colosa.qa.automatization.common.controlOptions.selection;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.controlOptions.ControlOptions;
import com.colosa.qa.automatization.common.controlOptions.DependentFieldsApplicableOption;

import java.util.ArrayList;

public class DropDownOptions extends ControlOptions{

	private ArrayList<DropDownOption> options = new ArrayList<DropDownOption>();

	public DropDownOptions(){
		this.dependentFieldsApplicableBehavior = new DependentFieldsApplicableOption();
	}

	public void addOption(String label, String value){
		options.add(new DropDownOption(label, value));
	}

	public void addDependentField(String fieldName){
		super.addDependentField(fieldName);
	}

	public void setDefaultValue(String value){
		this.defaultValue = value;
	}

	@Override
	public void fillForm(BrowserInstance browser) throws Exception{
		super.fillForm(browser);
		int i = 1;
		for(DropDownOption opt:this.options)
		{
			if(i>1)
				browser.findElement("dynaformDesigner.webElement.dropdownModal.gridNewElementButton").click();
			browser.getElementf("dynaformDesigner.webElement.dropdownModal.gridValueElementLocator", i).sendKeys(opt.getValue());
			browser.getElementf("dynaformDesigner.webElement.dropdownModal.gridLabelElementLocator", i).sendKeys(opt.getKey());
			i++;
		}
	}
	
}