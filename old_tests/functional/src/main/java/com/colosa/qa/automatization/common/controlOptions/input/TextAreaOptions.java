package com.colosa.qa.automatization.common.controlOptions.input;

import com.colosa.qa.automatization.common.BrowserInstance;

public class TextAreaOptions extends InputControlOptions{

	public void fillForm(BrowserInstance browser) throws Exception{
		super.fillForm(browser);
	}

	public void setSize(int columns, int rows){
		super.setSize(columns, rows);
	}

	public void setSize(int i){
		this.setSize(i, i);
	}
	
}