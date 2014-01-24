package com.colosa.qa.automatization.common.controlOptions.input;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.controlOptions.ControlOptions;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.support.ui.Select;

import java.util.ArrayList;

class InputControlOptions extends ControlOptions{
	
	private ArrayList<Integer> size = new ArrayList<Integer>();
	protected String validate = null;
	protected String mask = null;
	protected int maxLength = -1;

	protected void fillForm(BrowserInstance browser) throws Exception{
		super.fillForm(browser);
		this.fillSize(browser);
		this.fillValidate(browser);
		this.fillMask(browser);
		this.fillMaxLength(browser);
	}

	protected void setSize(int... args){
		this.size.clear();
		for(int i: args)
			this.size.add(i);
	}

	private void fillSize(BrowserInstance browser) throws Exception{
		if(this.size.size()==1)
		{
			WebElement we =	browser.findElement("dynaformDesigner.webElement.blankDynaformModal.size");
			we.clear();
			we.sendKeys(Integer.toString(this.size.get(0)));
		}
		else if(this.size.size()==2)
		{
			WebElement we =	browser.findElement("dynaformDesigner.webElement.blankDynaformModal.columns");
			we.clear();
			we.sendKeys(Integer.toString(this.size.get(0)));
			we = browser.findElement("dynaformDesigner.webElement.blankDynaformModal.rows");
			we.clear();
			we.sendKeys(Integer.toString(this.size.get(1)));
		}
	}

	private void fillValidate(BrowserInstance browser) throws Exception{
		if(this.validate == null)
			return;
		Select we = new Select(browser.findElement("dynaformDesigner.webElement.blankDynaformModal.validate"));
		we.selectByValue(this.validate);
	}

	private void fillMask(BrowserInstance browser) throws Exception{
		if(this.mask == null)
			return;
		WebElement we =	browser.findElement("dynaformDesigner.webElement.blankDynaformModal.mask");
		we.clear();
		we.sendKeys(this.mask);
	}

	private void fillMaxLength(BrowserInstance browser) throws Exception{
		if(this.maxLength < 0)
			return;
		WebElement we = browser.findElement("dynaformDesigner.webElement.blankDynaformModal.maxLength");
		we.clear();
		we.sendKeys(Integer.toString(this.maxLength));
	}

}