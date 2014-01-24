package com.colosa.qa.automatization.common.extJs;

import com.colosa.qa.automatization.common.Logger;
import org.openqa.selenium.By;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;

import java.util.ArrayList;
import java.util.List;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 5/7/13
 * Time: 2:07 PM
 * To change this template use File | Settings | File Templates.
 */
public class ExtJSTreeNode {
    WebElement treeNode = null;
    private WebDriver driver;
    List<ExtJSTreeNode> childTreeNodes = null;

    /**
     * This class represent a tree node element, must be initialized -> class x-tree-node-el
     * @param treeNode element with class: x-tree-node-el
     * @param driver
     */
    public ExtJSTreeNode(WebElement treeNode, WebDriver driver){
        this.treeNode = treeNode;
        this.driver = driver;

        //search the child nodes of this node
        //childTreeNodes = findListChildNodes();
    }

    public String getNodeText(){
        WebElement node = this.treeNode.findElement(By.cssSelector("div.x-tree-node-el a.x-tree-node-anchor span"));
        return node.getText();
    }

    public void click(){
        WebElement node = this.treeNode.findElement(By.cssSelector("div.x-tree-node-el a.x-tree-node-anchor"));
        node.click();
    }

    public void doubleClick(){
        Actions action = new Actions(this.driver);

        WebElement node = this.treeNode.findElement(By.cssSelector("div.x-tree-node-el"));
        action.doubleClick(node);
        action.perform();
    }

    public int countChildNodes(){
        //List<WebElement> childList = this.treeNode.findElements(By.cssSelector("ul.x-tree-node-ct li.x-tree-node"));
        return childTreeNodes.size();
    }

    private List<ExtJSTreeNode> findListChildNodes(){
        List<WebElement> childList = this.treeNode.findElements(By.cssSelector(this.treeNode.getTagName() + " > ul.x-tree-node-ct li.x-tree-node:not([style='display: none;'])")); //style="display: none;"

        Logger.addLog("ExtJSTreeNode()->findListChildNodes: " + this.treeNode.getTagName() + " > ul.x-tree-node-ct li.x-tree-node:not([style='display: none;']) count:"  + childList.size());

        List<ExtJSTreeNode> listTreeChildNodes = new ArrayList<ExtJSTreeNode>(childList.size());

        for (WebElement childNode : childList) {
            ExtJSTreeNode jsTreeNode = new ExtJSTreeNode(childNode, this.driver);
            Logger.addLog("ExtJSTreeNode()->findListChildNodes add node to list: " + jsTreeNode.getNodeText());

            listTreeChildNodes.add(jsTreeNode);
        }
        return listTreeChildNodes;
    }

    public List<ExtJSTreeNode> getListChildNodes(){
        return childTreeNodes;
    }

    /*public WebElement getWebElementNode(){
        return this.treeNode;
    }*/

    public ExtJSTreeNode getTreeNode(String nodePath, Boolean useRegularExpresion) throws Exception {
        //find child tree nodes
        this.childTreeNodes = findListChildNodes();

        return this.getTreeNodeInList(this.childTreeNodes, nodePath, useRegularExpresion);
    }

    /**
     * Get the tree node in base to search path starting from current search node
     * @param nodePath search path to find a node
     * @param useRegularExpresion true if regular expression is used in search path
     * @return
     * @throws Exception
     */
    public static ExtJSTreeNode getTreeNodeInList(List<ExtJSTreeNode> listTreeNodes, String nodePath, Boolean useRegularExpresion) throws Exception {
        ExtJSTreeNode resultTreeNode = null;
        String searchPath = nodePath;

        //search first node from left of path (root node)
        String nodeName = getLeftNodePath(searchPath);
        searchPath = removeLeftNodePath(searchPath);

        Logger.addLog("ExtJSTree()->getTreeNode: search node:" + nodeName + " pending path:" + searchPath );

        //search in child nodes
        for(ExtJSTreeNode treeNode:listTreeNodes){

            //check if is the same node
            if(useRegularExpresion){
                Logger.addLog("ExtJSTree()->getTreeNode: usign reg expresions if "+ treeNode.getNodeText() + " == " + nodeName );
                if(treeNode.getNodeText().matches(nodeName)){
                    //verify if is the node that we are searching for
                    if(searchPath.equals("")){
                        //this is the search node
                        resultTreeNode = treeNode;
                    }else{
                        //continue searching nodes
                        resultTreeNode = treeNode.getTreeNode(searchPath, useRegularExpresion);
                    }
                    break;
                }
            }else{
                Logger.addLog("ExtJSTree()->getTreeNode: usign equals if "+ treeNode.getNodeText() + " == " + nodeName );
                if(treeNode.getNodeText().equals(nodeName)){
                    //verify if is the node that we are searching for
                    if(searchPath.equals("")){
                        resultTreeNode = treeNode;
                    }else{
                        //continue searching nodes
                        resultTreeNode = treeNode.getTreeNode(searchPath, useRegularExpresion);
                    }
                    break;
                }
            }
        }

        //if node null -> error
        if(resultTreeNode == null){
            throw new Exception("No treeNode found with the specified path.");
        }

        return resultTreeNode;

    }

    public static String getLeftNodePath(String path){
        String workingPath = path;

        workingPath = removeSeparatorPath(workingPath);

        if(path.trim().equals("")){
            return "";
        }

        String[] splits = workingPath.split("/");

        //se trata de
        return splits[0];
    }

    public static String removeLeftNodePath(String path){
        String workingPath = path;

        workingPath = removeSeparatorPath(workingPath);

        if(workingPath.trim().equals("")){
            return "";
        }

        int firstIndex = workingPath.indexOf("/");

        //there's no more nodes
        if(firstIndex == -1){
            return "";
        }
        //se trata de
        return workingPath.substring(firstIndex);
    }

    public static String removeSeparatorPath(String path){
        String workingPath = path;

        if(path.trim().equals("")){
            return "";
        }

        //quitar el nodo root si existe
        if(workingPath.charAt(0) == '/'){
            workingPath = workingPath.substring(1);
        }
        //remove the last character
        if(workingPath.charAt(workingPath.length()-1) == '/'){
            workingPath = workingPath.substring(0, workingPath.length()-1);
        }
        //se trata de
        return workingPath;
    }

}
