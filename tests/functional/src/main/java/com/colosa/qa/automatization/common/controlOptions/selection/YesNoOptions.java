package com.colosa.qa.automatization.common.controlOptions.selection;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.controlOptions.ControlOptions;
import org.openqa.selenium.support.ui.Select;

public class YesNoOptions extends ControlOptions{
	
	public enum Options{
		YES("1"), NO("");

		private String value;

		Options(String value){
			this.value = value;
		}

		public String getValue(){
			return this.value;
		}
	}

	public void setDefaultValue(Options option){
		this.defaultValue = option.getValue();
	}

	@Override
	protected void fillDefaultValue(BrowserInstance browser) throws Exception{
		new Select(browser.findElement("dynaformDesigner.webElement.blankDynaformModal.defaultValue")).selectByValue(this.defaultValue);
	}

	private void setRequired(){}

	private void setSQL(/* String sql */){}

	private void setSQLConnection(/*SQLConnection connection*/){}

	public void fillForm(BrowserInstance browser) throws Exception{
		this.sqlConnection = null;
		this.required = null;
		super.fillForm(browser);
	}
}