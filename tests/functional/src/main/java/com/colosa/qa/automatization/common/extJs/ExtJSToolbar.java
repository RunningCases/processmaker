package com.colosa.qa.automatization.common.extJs;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.Logger;
import com.colosa.qa.automatization.common.WaitTool;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 6/10/13
 * Time: 1:33 PM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSToolbar {
    private BrowserInstance browserInstance;
    private WebElement toolbar;
    private WebElement toolbarContent;
    private List<ExtJSToolbarCell> listExtJSToolbarCells;
    List<WebElement> auxSearchList;


    /**
     * Represents a ExtJs Toolbar
     * @param toolbar element with class x-toolbar
     * @param browserInstance browser instance
     */
    public ExtJSToolbar(WebElement toolbar, BrowserInstance browserInstance) throws Exception {
        this.browserInstance = browserInstance;
        this.toolbar = toolbar;

        String classAttribute = toolbar.getAttribute("class");
        if(classAttribute.contains("x-toolbar")){
            //this is the toolbar element
            Logger.addLog("The passed element is the same toolbar: x-toolbar");
            this.toolbar = toolbar;
        }else{
            //search for the toolbar element
            //Logger.addLog("before Toolbar find x-panel-tbar");
            // findElement should not be used to look for non-present elements, use findElements(By) and assert zero length response instead.
            auxSearchList = toolbar.findElements(By.className("x-toolbar"));
            if(auxSearchList.size() > 0){
                //use the first x-toolbar found
                this.toolbar = auxSearchList.get(0);
                Logger.addLog("Toolbar found x-toolbar");
            }else
            {
                throw new Exception("Toolbar not found in specified element.");
            }
        }

        //Logger.addLog("before Toolbar find x-toolbar-ct");
        auxSearchList = this.toolbar.findElements(By.className("x-toolbar-ct"));
        if(auxSearchList.size() > 0){
            this.toolbarContent = auxSearchList.get(0);
            Logger.addLog("Toolbar content found x-toolbar-ct");
        }else
        {
            throw new Exception("Toolbar content not found in toolbar element.");
        }

        //detect all toolbar cells
        this.listExtJSToolbarCells = queryListToolbarCells();
    }

    private List<ExtJSToolbarCell> queryListToolbarCells(){

        List<WebElement> listCells = this.toolbarContent.findElements(By.cssSelector("td.x-toolbar-cell"));
        Logger.addLog("Get current list of cells: " + listCells.size());


        List<ExtJSToolbarCell> listToolbarCells = new ArrayList<ExtJSToolbarCell>(listCells.size());

        for (WebElement toolbarCell : listCells) {
            listToolbarCells.add(new ExtJSToolbarCell(toolbarCell, this.browserInstance));

            Logger.addLog("   cell data: " + toolbarCell.getTagName() + ":" + toolbarCell.getText());
        }

        return listToolbarCells;
    }

    public List<ExtJSToolbarCell> getListToolbarCells(){
        return queryListToolbarCells();
    }

    /**
     * Find cell in toolbar based in the cell text.
     * @param buttonText text to search cell
     * @return ExtJSToolbarCell found cell null in other case.
     */
    public ExtJSToolbarCell findToolbarCell(String buttonText){
        ExtJSToolbarCell resultToolbarCell = null;

        for (ExtJSToolbarCell extjsToolbarCell : this.listExtJSToolbarCells) {
            Logger.addLog("   toolbar cell:" + extjsToolbarCell.getCellText() + "==" + buttonText);

            if(extjsToolbarCell.getCellText().trim().equals(buttonText)){
                Logger.addLog("   toolbar cell found:" + extjsToolbarCell.getCellText());
                resultToolbarCell = extjsToolbarCell;
                break;
            }
        }

        return resultToolbarCell;
    }

    /**
     * Find Toolbar cell in base to zero based index of cell
     * @param cellIndex The Zero based index of the cell to return.
     * @return ExtJSToolbarCell the found cell
     */
    public ExtJSToolbarCell findToolbarCell(int cellIndex){
        ExtJSToolbarCell resultToolbarCell = null;
        resultToolbarCell = this.listExtJSToolbarCells.get(cellIndex);
        WaitTool.waitForElementVisibleAndEnable(browserInstance.getInstanceDriver(), resultToolbarCell.getWebElement(), 5);
        Logger.addLog("   return toolbar cell:" + cellIndex);
        return resultToolbarCell;
    }

    /*
    public ExtJSToolbarCell waitForToolbarCellDisplay(int cellIndex){
        ExtJSToolbarCell resultToolbarCell = null;
        resultToolbarCell = this.listExtJSToolbarCells.get(cellIndex);

        //resultToolbarCell.getWebElement().isDisplayed();
        //Logger.addLog("   return toolbar cell:" + cellIndex);
        return resultToolbarCell;
    }*/


}
