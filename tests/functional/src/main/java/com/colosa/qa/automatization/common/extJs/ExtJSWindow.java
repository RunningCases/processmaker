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
 * Time: 10:14 AM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSWindow {
    private BrowserInstance browserInstance;
    private WebElement xWindow;
    private WebElement xWindowContent;
    List<WebElement> auxSearchList;

    public ExtJSWindow(BrowserInstance browserInstance) throws Exception {
        //search the first visible windows
        this.browserInstance = browserInstance;

        // findElement should not be used to look for non-present elements, use findElements(By) and assert zero length response instead.
        auxSearchList = browserInstance.getInstanceDriver().findElements(By.cssSelector("div.x-window")); //[style='visibility: visible']
        if(auxSearchList.size() > 0){
            //use the first x-toolbar found
            this.xWindow = auxSearchList.get(0);
            Logger.addLog("xWindow found div.x-window [style='visibility:visible']");
        }else
        {
            throw new Exception("xWindow not found in specified element.");
        }

        auxSearchList = this.xWindow.findElements(By.cssSelector("div.x-window-body"));
        if(auxSearchList.size() > 0){
            this.xWindowContent = auxSearchList.get(0);
            Logger.addLog("xWindow content found div.x-window-body");
        }else
        {
            throw new Exception("xWindow content not found in browser.");
        }
    }

    public WebElement getWindowElement(){
        return this.xWindow;
    }
}
