package com.colosa.qa.automatization.common.controlOptions;

import com.colosa.qa.automatization.common.BrowserInstance;

import java.util.ArrayList;

interface DependentFieldsApplicableBehavior{
	public void fillDependentFields(BrowserInstance browser, ArrayList<String> fields) throws Exception;
}