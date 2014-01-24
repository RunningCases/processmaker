package com.colosa.qa.automatization.common.extJs;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.Logger;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 12/18/13
 * Time: 11:30 AM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSFormItem {
    private BrowserInstance browserInstance;
    private WebElement xFormItem;
    private WebElement xFormItemLabel;
    private WebElement xFormItemElement;
    List<WebElement> auxSearchList;

    public ExtJSFormItem(WebElement xFormItem,  BrowserInstance browserInstance) throws Exception {
        //search the first visible windows
        this.browserInstance = browserInstance;

        //verify that this is a form item
        String classAttribute = xFormItem.getAttribute("class");
        if(classAttribute.contains("x-form-item")){
            //this is the toolbar element
            Logger.addLog("The passed element is a form item: x-form-item");
            this.xFormItem = xFormItem;
        }else{
            throw new Exception("xform item not found in specified element.");
        }

        auxSearchList = this.xFormItem.findElements(By.cssSelector("label.x-form-item-label"));
        if(auxSearchList.size() > 0){
            //use the first x-toolbar found
            this.xFormItemLabel = auxSearchList.get(0);
            Logger.addLog("form found label.x-form-item-label");
        }else
        {
            throw new Exception("Form label not found in specified element.");
        }

        auxSearchList = this.xFormItem.findElements(By.cssSelector("div.x-form-element"));
        if(auxSearchList.size() > 0){
            //use the first x-toolbar found
            this.xFormItemElement = auxSearchList.get(0);
            Logger.addLog("form found div.x-form-element");
        }else
        {
            throw new Exception("Form element not found in specified element.");
        }
    }

    /**
     * Get item label
     * @return
     */
    public String getLabel(){

        return this.xFormItemLabel.getText();
    }


}
