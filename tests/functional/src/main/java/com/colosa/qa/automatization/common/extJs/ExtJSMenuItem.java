package com.colosa.qa.automatization.common.extJs;

import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 12/17/13
 * Time: 2:16 PM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSMenuItem {
    WebElement menuItem = null;
    WebElement menuItemText = null;

    public ExtJSMenuItem(WebElement menuItem){
        this.menuItem = menuItem;
        this.menuItemText = this.menuItem.findElement(By.className("x-menu-item-text"));
    }

    /**
     * Click element in Menu
     */
    public void click(){
        this.menuItem.click();
    }

    /**
     * Get text in menu
     * @return
     */
    public String getText(){

        return this.menuItemText.getText();
    }
}
