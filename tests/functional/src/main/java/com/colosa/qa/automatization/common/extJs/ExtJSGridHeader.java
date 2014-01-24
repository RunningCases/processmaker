package com.colosa.qa.automatization.common.extJs;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * Author: herbert
 * Date: 5/16/13
 * Time: 11:40 AM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSGridHeader {
    WebElement grid = null;
    WebElement gridBody = null;
    WebElement gridHeader = null;
    List<WebElement> listColumns = null;

    /**
     * Initialized the grid header with element that represent grid
     * @param grid The element that has the class = x-grid-panel
     */
    public ExtJSGridHeader(WebElement grid){

        this.grid = grid;
        this.gridBody = this.grid.findElement(By.cssSelector("div.x-grid3"));

        this.gridHeader = this.gridBody.findElement(By.cssSelector("div.x-grid3-header"));
        this.listColumns = this.gridHeader.findElements(By.cssSelector("td.x-grid3-hd.x-grid3-cell:not([style='display:none'])"));
    }

    /**
     * Get the header column name in base to his position
     * @param headerNumber one based index of the column
     * @return the name of the column
     */
    public String getHeaderColumnName(Integer headerNumber){
        WebElement cell = this.listColumns.get(headerNumber-1).findElement(By.className("x-grid3-hd-inner"));
        return cell.getText();
    }

    public int countColumns(){
        return this.listColumns.size();
    }

    public Integer getHeaderColumnNumber(String headerName){
        Integer count = 0;
        Boolean found = false;
        for(WebElement header:this.listColumns){
            count++;
            if(header.findElement(By.className("x-grid3-hd-inner")).getText().equals(headerName)){
                //
                found = true;
                break;
            }
        }
        if(found){
            return count;
        }

        return null;
    }
}
