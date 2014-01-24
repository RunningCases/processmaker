package com.colosa.qa.automatization.common.extJs;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.Logger;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import java.util.ArrayList;
import java.util.List;

/**
 * Represents a x windows Toolbar.
 * User: herbert
 * Date: 10/4/13
 * Time: 11:01 AM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSWindowToolbar {
    private BrowserInstance browserInstance;
    private WebElement toolbar;
    private WebElement toolbarContent;
    private List<ExtJSToolbarCell> listExtJSToolbarCells;

    /**
     * Represents a ExtJs Window Toolbar
     * @param toolbar element with class x-window-tbar
     * @param browserInstance
     */
    public ExtJSWindowToolbar(WebElement toolbar, BrowserInstance browserInstance){
        this.browserInstance = browserInstance;
        this.toolbar = toolbar;

        String classAttribute = toolbar.getAttribute("class");
        if(classAttribute.contains("x-window-tbar")){
            //this is the toolbar element
            Logger.addLog("The passed element is the same toolbar: x-window-tbar");
            this.toolbar = toolbar;
        }else{
            //search for the toolbar element
            //Logger.addLog("before Toolbar find x-panel-tbar");
            this.toolbar = toolbar.findElement(By.className("x-window-tbar"));
            Logger.addLog("Toolbar found x-window-tbar");
        }

        //Logger.addLog("before Toolbar find x-toolbar-ct");
        //this.toolbarContent = this.toolbar.findElement(By.className("x-toolbar-ct"));
        //Logger.addLog("Toolbar content found x-toolbar-ct");

        this.listExtJSToolbarCells = queryListToolbarCells();
    }

    private List<ExtJSToolbarCell> queryListToolbarCells(){

        //List<WebElement> listCells = this.toolbarContent.findElements(By.cssSelector("td.x-toolbar-cell"));
        List<WebElement> listCells = this.toolbar.findElements(By.cssSelector("td.x-toolbar-cell"));
        Logger.addLog("Found current list of cells: " + listCells.size());

        List<ExtJSToolbarCell> listToolbarCells = new ArrayList<ExtJSToolbarCell>(listCells.size());

        for (WebElement toolbarCell : listCells) {
            Logger.addLog("   create toolbar cell data.");

            listToolbarCells.add(new ExtJSToolbarCell(toolbarCell, this.browserInstance));

            Logger.addLog("   cell data: " + toolbarCell.getTagName() + ":" + toolbarCell.getText());
        }

        return listToolbarCells;
    }

    public ExtJSToolbarCell findToolbarCell(String buttonText){
        ExtJSToolbarCell resultToolbarCell = null;

        for (ExtJSToolbarCell extjsToolbarCell : this.listExtJSToolbarCells) {
            //Logger.addLog("   toolbar cell:" + extjsToolbarCell.getCellText() + " vs " + buttonText);

            if(extjsToolbarCell.getCellText().trim().equals(buttonText)){
                Logger.addLog("   toolbar cell found:" + extjsToolbarCell.getCellText());
                resultToolbarCell = extjsToolbarCell;
                break;
            }
        }

        return resultToolbarCell;
    }

    public ExtJSToolbarCell findToolbarCell(int cellIndex){
        ExtJSToolbarCell resultToolbarCell = this.listExtJSToolbarCells.get(cellIndex);
        return resultToolbarCell;
    }


}
