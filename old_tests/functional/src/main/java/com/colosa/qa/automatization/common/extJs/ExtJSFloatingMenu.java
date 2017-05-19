package com.colosa.qa.automatization.common.extJs;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.Logger;
import org.openqa.selenium.By;
import org.openqa.selenium.WebElement;

import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 12/17/13
 * Time: 12:35 PM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSFloatingMenu {
    private BrowserInstance browserInstance;
    //private WebElement floatingMenuParent;
    private WebElement floatingMenu;
    private WebElement floatingMenuListContent;
    List<WebElement> auxSearchList;
    List<ExtJSMenuItem> listExtJSMenuItem = null;

    public ExtJSFloatingMenu(BrowserInstance browserInstance) throws Exception {
        //this.floatingMenuParent = floatingMenuParent;
        this.browserInstance = browserInstance;


        // findElement should not be used to look for non-present elements, use findElements(By) and assert zero length response instead.
        auxSearchList = browserInstance.getInstanceDriver().findElements(By.cssSelector("div.x-menu.x-menu-floating:not([style='visibility:hidden'])"));
        //auxSearchList = this.floatingMenuParent.findElements(By.cssSelector("div.x-menu")); //:not([style='visibility:hidden'])
        if(auxSearchList.size() > 0){
            //use the first x-toolbar found
            this.floatingMenu = auxSearchList.get(0);
            Logger.addLog("FloatingMenu found div.x-menu.x-menu-floating");
        }else
        {
            throw new Exception("FloatingMenu not found in specified element.");
        }
        //mejorar la selección de elementos


        /*
        if(auxSearchList.size() > 0){
            //use the first x-toolbar found
            this.floatingMenu = auxSearchList.get(0);
            Logger.addLog("FloatingMenu found div.x-menu.x-menu-floating");
        }else
        {
            throw new Exception("FloatingMenu not found in specified element.");
        }

        //search visible floating menu
        String classAttribute = floatingMenuParent.getAttribute("class");
        Logger.addLog("Attribute " + classAttribute);
        if(classAttribute.contains("x-menu") && classAttribute.contains("x-menu-floating")){
            //this is the toolbar element
            Logger.addLog("The passed element is the same floating menu: x-menu-floating");
            this.floatingMenu = floatingMenuParent;
        }else{
            //search for the toolbar element
            //Logger.addLog("before Toolbar find x-panel-tbar");
            // findElement should not be used to look for non-present elements, use findElements(By) and assert zero length response instead.
            browserInstance.getInstanceDriver().findElements(By.cssSelector("div.x-menu"));
            auxSearchList = this.floatingMenuParent.findElements(By.cssSelector("div.x-menu")); //:not([style='visibility:hidden'])

            //mejorar la selección de elementos



            if(auxSearchList.size() > 0){
                //use the first x-toolbar found
                this.floatingMenu = auxSearchList.get(0);
                Logger.addLog("FloatingMenu found div.x-menu.x-menu-floating");
            }else
            {
                throw new Exception("FloatingMenu not found in specified element.");
            }
        }*/

        //Logger.addLog("before Toolbar find x-toolbar-ct");
        auxSearchList = this.floatingMenu.findElements(By.cssSelector("ul.x-menu-list"));
        if(auxSearchList.size() > 0){
            this.floatingMenuListContent = auxSearchList.get(0);
            Logger.addLog("FloatingMenu content found ul.x-menu-list");
        }else
        {
            throw new Exception("FloatingMenu content not found in toolbar element.");
        }

        //detect all toolbar cells
        this.listExtJSMenuItem = queryListMenuItems();
    }

    private List<ExtJSMenuItem> queryListMenuItems(){

        List<WebElement> listItems = this.floatingMenuListContent.findElements(By.cssSelector("li.x-menu-list-item"));
        Logger.addLog("Get current list of Menu Items: " + listItems.size());


        List<ExtJSMenuItem> listMenuItems = new ArrayList<ExtJSMenuItem>(listItems.size());

        for (WebElement menuListItem : listItems) {
            //get menu item
            WebElement menuItem = menuListItem.findElement(By.className("x-menu-item"));
            listMenuItems.add(new ExtJSMenuItem(menuItem));

            Logger.addLog("   menu item data: " + menuItem.getTagName() + ":" + menuItem.getText());
        }

        return listMenuItems;
    }

    /**
     * Find menu item in menu based in the specified text.
     * @param menuText text to search item
     * @return ExtJSMenuItem found menu item, null in other case.
     */
    public ExtJSMenuItem findMenuItem(String menuText){
        ExtJSMenuItem resultMenuItem = null;

        for (ExtJSMenuItem menuItem : this.listExtJSMenuItem) {
            Logger.addLog("   if (menu item:" + menuItem.getText() + "==" + menuText + ")");

            if(menuItem.getText().trim().equals(menuText)){
                Logger.addLog("   menu item found:" + menuItem.getText());
                resultMenuItem = menuItem;
                break;
            }
        }

        return resultMenuItem;
    }
}
