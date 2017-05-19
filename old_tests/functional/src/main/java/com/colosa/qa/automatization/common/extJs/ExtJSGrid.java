/*
 * Created with IntelliJ IDEA.
 * Author: Herbert Saal
 * Date: 5/16/13
 * Time: 11:40 AM
 *
*/

package com.colosa.qa.automatization.common.extJs;

import java.util.ArrayList;
import java.util.List;
//import java.util.concurrent.TimeUnit;
import java.lang.Exception;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.Logger;
import com.colosa.qa.automatization.common.WaitTool;
import org.openqa.selenium.*;
//import org.openqa.selenium.Keys;
//import org.openqa.selenium.JavascriptExecutor;
import org.openqa.selenium.support.ui.WebDriverWait;
//import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.ExpectedCondition;

public class ExtJSGrid{

	private WebDriver driver;
	private WebElement grid;
    private WebElement headerToolbar;
    private List<WebElement> headerToolbarCells;
    private WebElement searchInput;
    private WebElement gridBody;
    private ExtJSGridHeader gridBodyHeader;
    private ExtJSToolbar bottomToolbar = null;
    private WebElement bottomCellCounterText;
    //private List<ExtJSGridHeader> gridHeaders;
    private List<WebElement> rows;
    private List<WebElement> auxListElements;

	private List<WebElement> pager;
	private WebElement btn_first;
	private WebElement btn_previous;
	private WebElement btn_next;
	private WebElement btn_last;
	private WebElement btn_refresh;
	private int currentPage;
	private int totalPages;
	private int items; //*falta dar utilidad
	private int timeout;

    /**
     * Creates an ExtJsGrid class.
     * @param grid The element that has the class = x-grid-panel
     * @param browserInstance The browser driver
     * @param timeout
     */
	public ExtJSGrid(WebElement grid, BrowserInstance browserInstance, int timeout) throws Exception {
		//verify x-grid-panel class

        this.driver = browserInstance.getInstanceDriver();
		this.grid = grid;
		this.timeout = timeout;
        this.headerToolbar = this.grid.findElement(By.cssSelector("div.x-panel-tbar"));
        this.headerToolbarCells = this.headerToolbar.findElements(By.cssSelector("td.x-toolbar-cell"));
        this.gridBody = this.grid.findElement(By.cssSelector("div.x-grid3"));
        this.auxListElements = this.grid.findElements(By.cssSelector("div.x-panel-bbar"));
        if(this.auxListElements.size()>0){
            this.bottomToolbar = new ExtJSToolbar(this.auxListElements.get(0), browserInstance);
            this.bottomCellCounterText = this.bottomToolbar.findToolbarCell(11).getWebElement();
            Logger.addLog("Bottom Toolbar found, cell 11:" + this.bottomCellCounterText.getText());
        }
        //this.gridBodyHeader = this.gridBody.findElement(By.cssSelector("div.x-grid3-header"));

        //this.gridHeaders = this.gridBodyHeader.findElements(By.cssSelector("td.x-grid3-hd.x-grid3-cell:not([style='display:none'])"));
		this.pager = this.grid.findElements(By.xpath("div/div[3]/div/table/tbody/tr/td/table/tbody/tr/td"));
		this.btn_first = this.pager.get(0).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		this.btn_previous = this.pager.get(1).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		this.btn_next = this.pager.get(7).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		this.btn_last = this.pager.get(8).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		this.btn_refresh = this.pager.get(10).findElement(By.xpath("table/tbody/tr[2]/td[2]/em/button"));
		this.init();

        WaitTool.waitForJavaScriptCondition(this.driver, "return (document.readyState == 'complete');", 15);
	}

    /**
     *
     * @param grid
     * @param browserInstance
     */
	public ExtJSGrid(WebElement grid, BrowserInstance browserInstance) throws Exception {
		this(grid, browserInstance, 30);
	}

	private void init(){
		if(!this.driver.getClass().getName().equals("org.openqa.selenium.firefox.FirefoxDriver"))
		{
			WebDriverWait wait = new WebDriverWait(this.driver, this.timeout);
			//try{
				//wait.until(new PageStatusChanged(this.grid.findElement(By.xpath("div/div[3]/div/table/tbody/tr/td[2]/table/tbody/tr/td[1]/table/tbody/tr/td/div")).getText().trim()));
				//wait.until(new PageStatusChanged(""));
			String[] statuses = {"", "Displaying Empty"};
			wait.until(new PageStatusChanged(statuses));
			//}
		}
		this.updateCurrentPage();
		this.updateTotalPages();
	}

	private void updateTotalPages(){
		this.totalPages = Integer.parseInt(this.pager.get(5).findElement(By.xpath("div")).getText().trim().substring(3));
	}

	private void updateCurrentPage(){
		this.currentPage = Integer.parseInt(this.pager.get(4).findElement(By.xpath("input")).getAttribute("value"));
	}

	public int getCurrentPage(){
		return this.currentPage;
	}

	public int getTotalPages(){
		return this.totalPages;
	}

	public int nextPage(){
		Logger.addLog("next");
		return this.moveToPage(1);
	}

	public int previousPage(){
		return this.moveToPage(0);
	}

	public int firstPage(){
		return this.moveToPage(2);
	}

	public int lastPage(){
		return this.moveToPage(3);	
	}

	public int refreshPage(){
		return this.moveToPage(4);
	}

	public List<WebElement> getRows(){
		return this.grid.findElements(By.xpath("div/div[2]/div/div[1]/div[2]/div/div"));
	}

    public void setSearchFieldGrid(Boolean headerToolBar, Integer cellNumber){
        this.searchInput = null;
        /*
        Logger.addLog("setSearchFieldGrid search cells: " + cellNumber);
        //list of toolbar CELLS
        Logger.addLog("setSearchFieldGrid Total toolbar cells: " + headerToolbarCells.size());
        Integer counter = 0;
        for(WebElement toolbarElement: headerToolbarCells){
            counter++;
            Logger.addLog("  setSearchFieldGrid Toolbar cells[" + counter + "]: " + toolbarElement.getText());
        }*/

        //pm_search_text_field

        //set the search cell as a headerToolbar of footer cell in toolbar.
        if(headerToolBar){
            this.searchInput = headerToolbarCells.get(cellNumber-1).findElement(By.cssSelector("input.x-form-text[type='text']"));
        }
    }

    public List<ExtJSGridRow> getCurrentListRows(){
        //clear previous list of rows

        //div#ext-gen25.x-grid3 div#ext-gen26.x-grid3-viewport div#ext-gen28.x-grid3-scroller div#ext-gen30.x-grid3-body div.x-grid3-row
        List<WebElement> listRows = this.gridBody.findElements(By.cssSelector("div.x-grid3-body div.x-grid3-row"));
        Logger.addLog("Get current list of rows total: " + listRows.size());

        //WaitTool.waitForJavaScriptCondition(this.driver, "return (document.readyState == 'complete');", 15);


        List<ExtJSGridRow> listGridRows = new ArrayList<ExtJSGridRow>(listRows.size());

        for (WebElement gridRow : listRows) {
            Logger.addLog("   row data: " + gridRow.getText());

            listGridRows.add(new ExtJSGridRow(gridRow));
        }

        return listGridRows;
    }

    public List<ExtJSGridRow> findRowsBySearchField(String searchCriteria) throws Exception {
        if(this.searchInput == null){
            throw new Exception("Search field not defined, define it with setSearchFieldGrid function.");
        }

        String currentTextStatus = this.bottomCellCounterText.getText().trim();

        this.searchInput.sendKeys(searchCriteria);
        this.searchInput.sendKeys(Keys.RETURN);

        //wait for counter of grid changes to one if the row is found.
        //only wait for one, wait for text to change
        //WaitTool.waitForTextPresent(this.driver, this.bottomCellCounterText, "Display Items 1 - 1 of 1", 15);
        WaitTool.waitForTextToChange(this.driver, this.bottomCellCounterText, currentTextStatus, 15);

        //return list of rows, only the filtered list is returned
        return this.getCurrentListRows();
    }

    public ExtJSGridRow searchAndReturnRow(String searchCriteria, String columnName) throws Exception {
        ExtJSGridRow resultGridRow = null;
        Boolean rowFound = false;

        if(this.searchInput == null){
            throw new Exception("Search field not defined, define it with setSearchFieldGrid function.");
        }

        String currentTextStatus = this.bottomCellCounterText.getText().trim();

        this.searchInput.sendKeys(searchCriteria);
        this.searchInput.sendKeys(Keys.RETURN);

        //wait for counter of grid changes to one if the row is found.
        //wait for text to change
        WaitTool.waitForTextToChange(this.driver, this.bottomCellCounterText, currentTextStatus, 15);

        //return list of rows, only the filtered list is returned
        List<ExtJSGridRow> listFoundRows = this.getCurrentListRows();
        Logger.addLog("Search grid total items: " + listFoundRows.size());

        //update header
        this.gridBodyHeader = new ExtJSGridHeader(this.grid);

        //compare each found row with the value of the specified column
        for (ExtJSGridRow gridRow:listFoundRows){
            //get the number of the column of the header
            Integer headerIndex =  this.gridBodyHeader.getHeaderColumnNumber(columnName);
            Logger.addLog("   Header with name: " + columnName + " Index:" + headerIndex);

            Logger.addLog("   Row Data: " + gridRow.getRowColumnText(headerIndex));

            if(gridRow.getRowColumnText(headerIndex).equals(searchCriteria)){
                resultGridRow = gridRow;
                //rowFound = true;
                break;
            }
        }

        return resultGridRow;
    }

	public WebElement getRowByColumnValue(String columnName, String columnValue) throws Exception{
		WebElement header = this.grid.findElement(By.xpath("div/div[2]/div/div[1]/div[1]"));
		List<WebElement> headerFields = header.findElements(By.xpath("div/div/table/thead/tr/td"));
		int columnNumber = 1;
		boolean flag = false;
		for(WebElement field:headerFields)
		{
			if(field.getText().trim().equals(columnName))
			{
				flag = true;
				break;
			}
			columnNumber++;
		}
		if(!flag)
			throw new Exception("No se encontro una columna en el grid con el nombre \""+columnName+"\"");
		flag = false;
		while(this.currentPage <= this.totalPages)
		{	
			headerFields = this.getRows();
			Logger.addLog(headerFields.size() + "   " + this.currentPage + " de " + this.totalPages);
			for(WebElement row:headerFields)
				if(row.findElement(By.xpath("table/tbody/tr/td["+Integer.toString(columnNumber)+"]/div")).getText().trim().equals(columnValue))		
					return row;
			if(this.currentPage == this.totalPages)
				break;
			this.nextPage();
		}
		return null;
	}

	public WebElement getRowByColumnsValue(String columnName, String otherColumnName, String columnValue, String otherColumnValue) throws Exception{
		WebElement header = this.grid.findElement(By.xpath("div/div[2]/div/div[1]/div[1]"));
		List<WebElement> headerFields = header.findElements(By.xpath("div/div/table/thead/tr/td"));
		int columnNumber = 1;
		int otherColumnNumber = 1;
		boolean flag = false;
		for(WebElement field:headerFields)
		{
			if(field.getText().trim().equals(columnName))
			{
					flag = true;
					break;
			}
			columnNumber++;			
		}
		if(!flag)
			throw new Exception("No se encontraron columnas en el grid con el nombre \""+columnName+"\" ");
		flag = false;
		while(this.currentPage <= this.totalPages)
		{	
			headerFields = this.getRows();
			Logger.addLog(headerFields.size() + "   " + this.currentPage + " de " + this.totalPages);
			for(WebElement row:headerFields)
				if((row.findElement(By.xpath("table/tbody/tr/td["+Integer.toString(columnNumber)+"]/div")).getText().trim().equals(columnValue))	&& (row.findElement(By.xpath("table/tbody/tr/td["+Integer.toString(otherColumnNumber)+"]/div")).getText().trim().equals(columnValue)))	
					return row;																																																																										
			if(this.currentPage == this.totalPages)
				break;
			this.nextPage();
		}
		return null;
	}
	
	
	
	private int moveToPage(int option){
		WebDriverWait wait = new WebDriverWait(this.driver, this.timeout);
		PageStatusChanged statusChanged = new PageStatusChanged();
		PageRefreshed pageRefreshed = new PageRefreshed();
		
		switch(option)
		{
			case 0:
				if(this.currentPage == 1)
					return 0;
				this.btn_previous.click();
				break;
			case 1: 
				if(this.currentPage >= this.totalPages)
					return 0;
				this.btn_next.click();
				break;
			case 2: 
				if(this.currentPage == 1)
					return 0;
				this.btn_first.click();
				break;
			case 3: 
				if(this.currentPage == this.totalPages)
					return 0;
				this.btn_last.click();
				break;
			case 4:
				this.btn_refresh.click();
		}
		if(option!=4)
			wait.until(statusChanged);
		else
			wait.until(pageRefreshed);
		this.updateTotalPages();
		this.updateCurrentPage();
		return this.getCurrentPage();
	}

	private int inArray(String[] arr, String str){
		for(int i=0; i<arr.length; i++)
			if(arr[i].equals(str))
				return i;
		return -1;
	}

	private class PageStatusChanged implements ExpectedCondition<Boolean>{

		private String status;
		private String[] statuses = {};

		public PageStatusChanged(String status){
			this.status = status;
			Logger.addLog("actual " + this.status);
		}

		public PageStatusChanged(String[] statuses){
			this.statuses = statuses;
		}

		public PageStatusChanged(){
			this.status = grid.findElement(By.xpath("div/div[3]/div/table/tbody/tr/td[2]/table/tbody/tr/td[1]/table/tbody/tr/td/div")).getText().trim();
			//Logger.addLog("actual "+this.status);
		}

		@Override
		public Boolean apply(WebDriver input) {
			String actual = grid.findElement(By.xpath("div/div[3]/div/table/tbody/tr/td[2]/table/tbody/tr/td[1]/table/tbody/tr/td/div")).getText().trim();
			//Logger.addLog(actual+" / "+this.status+" => "+!actual.equals(this.status));
			if(this.statuses.length == 0)
				return !actual.equals(this.status);
			else
				return !(inArray(this.statuses, actual)>-1);
		}	  
	}

	private class PageRefreshed implements ExpectedCondition<Boolean>{

		@Override
		public Boolean apply(WebDriver input) {
			return pager.get(10).findElement(By.xpath("table")).getAttribute("class").indexOf("x-item-disabled") == -1;
		}	  
	}

}