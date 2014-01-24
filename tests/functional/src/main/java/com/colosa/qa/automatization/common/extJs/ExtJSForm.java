package com.colosa.qa.automatization.common.extJs;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.Logger;
import com.colosa.qa.automatization.common.WaitTool;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 12/18/13
 * Time: 11:23 AM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSForm {
    private BrowserInstance browserInstance;
    private WebElement xForm;
    List<WebElement> auxSearchList;
    List<ExtJSFormItem> xFormItems;

    public ExtJSForm(BrowserInstance browserInstance) throws Exception {
        //search the first visible windows
        this.browserInstance = browserInstance;

        // findElement should not be used to look for non-present elements, use findElements(By) and assert zero length response instead.
        auxSearchList = browserInstance.getInstanceDriver().findElements(By.cssSelector("form.x-form"));
        if(auxSearchList.size() > 0){
            //use the first x-toolbar found
            this.xForm = auxSearchList.get(0);
            Logger.addLog("xForm found form.x-form");
        }else
        {
            throw new Exception("xForm not found in specified element.");
        }

        this.xFormItems = this.queryListFormItems();
    }

    private List<ExtJSFormItem> queryListFormItems() throws Exception {

        List<WebElement> listFormItems = this.xForm.findElements(By.cssSelector("div.x-form-item"));
        Logger.addLog("Get current list of form items: " + listFormItems.size());

        List<ExtJSFormItem> listXFormItems = new ArrayList<ExtJSFormItem>(listFormItems.size());

        for (WebElement xFormItem : listFormItems) {
            listXFormItems.add(new ExtJSFormItem(xFormItem, this.browserInstance));

            Logger.addLog("   form item: " + xFormItem.getTagName() + ":" + xFormItem.getText());
        }

        return listXFormItems;
    }

    /**
     * Find Form Item based in the item label.
     * @param itemLabel text to search item
     * @return ExtJSFormItem found form item null in other case.
     */
    public ExtJSFormItem findFormItem(String itemLabel){
        ExtJSFormItem resultFormItem = null;

        for (ExtJSFormItem extJSFormItem : this.xFormItems) {
            Logger.addLog("   form item:" + extJSFormItem.getLabel() + "==" + itemLabel);

            if(extJSFormItem.getLabel().trim().contains(itemLabel)){
                Logger.addLog("   form item found:" + extJSFormItem.getLabel());
                resultFormItem = extJSFormItem;
                break;
            }
        }

        return resultFormItem;
    }

    /**
     * Find form item in base to zero based index
     * @param formItemIndex The Zero based index of the form item.
     * @return ExtJSFormItem the found form item
     */
    public ExtJSFormItem findFormItem(int formItemIndex){
        ExtJSFormItem resultFormItem = null;
        resultFormItem = this.xFormItems.get(formItemIndex);
        Logger.addLog("   return toolbar cell:" + formItemIndex);
        return resultFormItem;
    }
}
