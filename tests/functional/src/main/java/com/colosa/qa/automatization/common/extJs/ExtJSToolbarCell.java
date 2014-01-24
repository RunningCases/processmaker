package com.colosa.qa.automatization.common.extJs;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.Logger;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 6/10/13
 * Time: 1:45 PM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSToolbarCell {
    private BrowserInstance browserInstance;
    private WebElement toolbarCell; //element with class x-toolbar-cell
    private List<WebElement> toolbarCellOptions;
    /*private WebElement toolbarCellButton;
    private WebElement toolbarCellText;
    private WebElement toolbarCellSeparator;
    private WebElement toolbarCellWrap;*/

    /**
     * Initialize with an element that represent a Toolbar Cell, that has the class x-toolbar-cell
     * @param toolbarCell webelement with class x-toolbar-cell
     * @param browserInstance browser instance
     */
    public ExtJSToolbarCell(WebElement toolbarCell, BrowserInstance browserInstance) {
        this.browserInstance = browserInstance;

        //this.headerToolbar = this.grid.findElement(By.cssSelector("div.x-panel-tbar"));
        //this.headerToolbarCells = this.headerToolbar.findElements(By.cssSelector("td.x-toolbar-cell"));
        this.toolbarCell = toolbarCell;
        //WebElement toolbarCellContent;
        //get the child
        /*
        this.toolbarCellTable = this.toolbarCell.findElement(By.cssSelector("table"));
        this.driver = driver;

        //detect what type of cell is
        String classAttribute = this.toolbarCellTable.getAttribute("class");

        */
        /*
        Logger.addLog("New Toolbar cell identify type");

        this.browserInstance.turnOffImplicitWaits();
        this.toolbarCellOptions = this.toolbarCell.findElements(By.cssSelector(".x-btn, .xtb-text, .xtb-sep, .x-form-field-wrap"));
        this.browserInstance.turnOnImplicitWaits();

        if(this.toolbarCellOptions.size() > 0){
            Logger.addLog("Toolbar element found!!!");
            //an element was found
            toolbarCellContent = this.toolbarCellOptions.get(0);
            //detect what type of cell is
            String classAttribute = toolbarCellContent.getAttribute("class");
            if(classAttribute.contains("x-btn")){
                Logger.addLog("It's a button!!!");
                this.toolbarCellButton = toolbarCellContent;
                Logger.addLog(" text:" + getCellText());
            }

            if(classAttribute.contains("xtb-text")){
                Logger.addLog("It's a text!!!");
                this.toolbarCellText = toolbarCellContent;
                Logger.addLog(" text:" + getCellText());
            }

            if(classAttribute.contains("xtb-sep")){
                Logger.addLog("It's a separator!!!");
                this.toolbarCellSeparator = toolbarCellContent;
                Logger.addLog(" text:" + getCellText());
            }

            if(classAttribute.contains("x-form-field-wrap")){
                Logger.addLog("It's a wrapper!!!");
                this.toolbarCellWrap = toolbarCellContent;
                Logger.addLog(" text:" + getCellText());
            }
        } */
    }

    /**
     * Get webelement of toolbar-cell
     */
    public WebElement getWebElement(){
        return this.toolbarCell;
        //html.ext-strict body#ext-gen3.ext-gecko div#navPanel.x-panel div#ext-gen16.x-panel-bwrap div#ext-gen17.x-panel-tbar div#ext-comp-1004.x-toolbar table.x-toolbar-ct tbody tr td.x-toolbar-left table tbody tr.x-toolbar-left-row td#ext-gen27.x-toolbar-cell table#caseNotes.x-btn tbody.x-btn-small tr td.x-btn-mc em button#ext-gen28.x-btn-text
    }

    /**
     * Click any non-identified element in toolbar-cell
     */
    public void click(){
        this.toolbarCell.click();
        //html.ext-strict body#ext-gen3.ext-gecko div#navPanel.x-panel div#ext-gen16.x-panel-bwrap div#ext-gen17.x-panel-tbar div#ext-comp-1004.x-toolbar table.x-toolbar-ct tbody tr td.x-toolbar-left table tbody tr.x-toolbar-left-row td#ext-gen27.x-toolbar-cell table#caseNotes.x-btn tbody.x-btn-small tr td.x-btn-mc em button#ext-gen28.x-btn-text
    }

    /**
     * Click toolbar button
     */
    public void clickButton() throws Exception {
        WebElement cellButton = null;
        Logger.addLog("click button");

        this.browserInstance.turnOffImplicitWaits();
        this.toolbarCellOptions = this.toolbarCell.findElements(By.cssSelector(".x-btn"));
        this.browserInstance.turnOnImplicitWaits();

        if(this.toolbarCellOptions.size() > 0){
            Logger.addLog("button element found!!!");
            //an element was found
            cellButton = this.toolbarCellOptions.get(0);
            cellButton.findElement(By.cssSelector("button")).click();
        }else{
            throw new Exception("Button not found!!");
        }

        //x-btn x-btn-text-icon
        //click the element if is a button
    }

    /**
     * Get text in cell
     * @return
     */
    public String getCellText(){
        String cellText = "";

        cellText = this.toolbarCell.getText();

        /*
        //if is a button or a text get the text
        // x-btn-text
        if(this.toolbarCellText != null){

            cellText = this.toolbarCellText.getText();
            //Logger.addLog("Cell text:" + cellText);
        }

        if(this.toolbarCellButton != null){
            WebElement button = this.toolbarCellButton.findElement(By.className("x-btn-text"));
            if(button != null){
                cellText =  button.getText();
                //Logger.addLog("Button text:" + cellText);
            }
        }

        if(this.toolbarCellSeparator != null){
            cellText = "|";
            //Logger.addLog("Separator text:" + cellText);
        }

        if(this.toolbarCellWrap != null){
            cellText = "[]";
            //Logger.addLog("Wrap text:" + cellText);
        } */

        return cellText;
    }

    /**
     * Set text in cell
     * @param text
     */
    public void setCellText(String text){
        this.toolbarCell.sendKeys(text);
    }
}
