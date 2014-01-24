package com.colosa.qa.automatization.common.extJs;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 5/15/13
 * Time: 3:30 PM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSGridRow{
    WebElement gridRow = null;
    List<WebElement> listColumns = null;

    /**
     * Initialized with the webelement that represent a Tree-node <div class="x-grid3-row"></li>
     * @param gridRow
     */
    public ExtJSGridRow(WebElement gridRow){
        //validate

        this.gridRow = gridRow;
        this.listColumns = this.gridRow.findElements(By.cssSelector("td.x-grid3-col.x-grid3-cell:not([style='display:none'])"));
    }

    /**
     * Get the cell(row, column) text
     * @param columnNumber one based index of the column
     * @return the text of the cell
     */
    public String getRowColumnText(Integer columnNumber){
        WebElement cell = this.listColumns.get(columnNumber-1).findElement(By.className("x-grid3-cell-inner"));
        return cell.getText();
    }

    public int countColumns(){
        return this.listColumns.size();
    }

    public WebElement getGridRow(){
        return this.gridRow;
    }

}
