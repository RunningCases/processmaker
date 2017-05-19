/*
Pensado y Desarrollado por Daniel Canedo
para Colosa QA
http://duhnnie.net
last update: 2012-10-23 Hrs. 16:40
*/

package com.colosa.qa.automatization.tests.common;

import com.colosa.qa.automatization.common.BrowserInstance;
import com.colosa.qa.automatization.common.Logger;
import com.colosa.qa.automatization.common.extJs.ExtJSGrid;
import org.openqa.selenium.By;
import org.openqa.selenium.Keys;
import org.openqa.selenium.WebDriver;
import org.openqa.selenium.WebElement;
import org.openqa.selenium.interactions.Actions;

import java.util.List;
import java.util.concurrent.TimeUnit;

public class WebDriverFunctionsPMOS2{
	
	private WebDriver driver = null;
    private BrowserInstance _browserInstance = null;
	private int skin = 1; //0: classic / 1: uxmodern
	private int timeout = 30; //default timeout in seconds

	public WebDriverFunctionsPMOS2(BrowserInstance browserInstance, int skin){
		this.driver = browserInstance.getInstanceDriver();
        this._browserInstance = browserInstance;
		this.skin = skin;
	}

	public WebDriverFunctionsPMOS2(BrowserInstance browserInstance, int skin, int timeout){
		this(browserInstance, skin);
		this.timeout = timeout;
		this.driver.manage().timeouts().implicitlyWait(this.timeout, TimeUnit.SECONDS);
	}

	public boolean login(String username, String password, String workspace){
		this.waitForElementPresent(By.name("form[USR_USERNAME]"));
		if(this.skin == 1)
		{
			WebElement we = this.driver.findElement(By.name("form[USR_USERNAME]"));
			we.clear();
	        we.sendKeys(username);
	        we = this.driver.findElement(By.name("form[USR_PASSWORD]"));
			we.clear();
	        we.sendKeys(password);
	       	we = this.driver.findElement(By.id("workspace"));
			we.clear();
	        we.sendKeys(workspace);
	        this.driver.findElement(By.name("form[USR_PASSWORD]")).sendKeys(Keys.RETURN);
		}
		else
		{
			WebElement we = this.driver.findElement(By.id("form[USR_USERNAME]"));
			we.clear();
	        we.sendKeys(username);
	        we = this.driver.findElement(By.name("form[USR_PASSWORD]"));
			we.clear();
	        we.sendKeys(password);
	        we = this.driver.findElement(By.id("form[USER_ENV]"));
	        if(we.getAttribute("type").equals("text"))
				we.clear();
	        we.sendKeys(workspace);
	        we = this.driver.findElement(By.id("form[BSUBMIT]"));
	        we.click();
		}
		return true;
	}

	public void logout(){
		this.driver.switchTo().defaultContent();
		if(this.skin==0)
			this.driver.findElement(By.xpath("//*[@id='pm_header']/table/tbody/tr[1]/td[2]/div/small/a")).click();
	}

	public boolean goSection(String sectionName){

		WebElement we = null;
		List<WebElement> wel;
		this.driver.switchTo().defaultContent();
		Logger.addLog("Redireccionando a seccion " + sectionName + "...");
		if(this.skin == 0)
		{
			this.waitForElementPresent(By.cssSelector("ul#pm_menu li a"),60);
			we = this.driver.findElement(By.id("pm_menu"));
			we = we.findElement(By.linkText(sectionName));
		}
		else
		{
			this.waitForElementPresent(By.xpath("//div[@id='mainTabPanel']"), 60);
			wel = this.driver.findElements(By.xpath("//div[@id='mainTabPanel']/div/div/ul/li"));
			for(WebElement we2:wel)
			{
				we = we2.findElement(By.xpath("a[2]/em/span/span"));
				if(we.getText().equals(sectionName))
					break;
			}
		}
		//Logger.addLog(we); //raro pero se necesita esta linea para que funcione correctamente
		if(we == null)
			return false;
		we.click();
		return true;
	}

	private String[] pathToArray(String path){
		if(path.charAt(0) == '/')
			path = path.substring(1);
		if(path.charAt(path.length()-1) == '/')
			path = path.substring(0, path.length());

		return path.split("/");
	}

	public boolean selectMenuTreePanelOption(String path) throws Exception{
		List<WebElement> wel;
		WebElement option = null;
		String[] pathLevels;
		String aux="";

		if(path.length() == 0)
			return false;
		
		pathLevels = this.pathToArray(path.toLowerCase());

		if(pathLevels.length>2)
			throw new Exception("the PATH parameter must contain up to 2 path levels.");

		Logger.addLog("Intentando ingresar a " + pathLevels[0] + ((pathLevels.length > 1) ? " => " + pathLevels[1] : "") + "...");

		this.goSection("HOME");
		if(this.skin == 0)
			this.driver.switchTo().frame("casesFrame");
		else
		{
			this.waitForElementPresent(By.id("pm-frame-cases"),this.timeout);
			this.driver.switchTo().frame("pm-frame-cases");
		}

		if(pathLevels.length==2 || path.charAt(path.length()-1)=='/')
			wel = this.driver.findElements(By.xpath("//div[@id='tree-panel']/div/div/ul/div/li"));
		else
			wel = this.driver.findElements(By.xpath("//div[@id='tree-panel']/div/div/ul/div/li/ul/li"));
		for(WebElement we:wel)
			try{
				aux = we.findElement(By.xpath("div/a/span")).getText().toLowerCase();
				if(aux.length()>=pathLevels[0].length())
					if(aux.substring(0, pathLevels[0].length()).equals(pathLevels[0]))
					{
						option = we;
						break;
					}
			}catch(java.lang.StringIndexOutOfBoundsException e){
				throw new Exception("No se encontró el grupo de opciones: \""+pathLevels[0]+"\"");
			}
		if(option == null)
			throw new Exception("No se encontró el grupo de opciones: \""+pathLevels[0]+"\"");
		if(pathLevels.length==2)
		{
			wel = option.findElements(By.xpath("ul/li"));
			option = null;
			for(WebElement we:wel)
			{
				aux = we.findElement(By.xpath("div/a/span")).getText().toLowerCase();
				try{
					if(aux.substring(0, pathLevels[1].length()).equals(pathLevels[1]))
					{
						option = we;
						break;
					}
				}catch(java.lang.StringIndexOutOfBoundsException e)
				{
					throw new Exception("No se encontró opción: \""+pathLevels[1] + "\" en el grupo de opciones: \""+pathLevels[0]+"\"");
				}
			}
			if(option == null)
				throw new Exception("No se encontró opción: \""+pathLevels[1] + "\" en el grupo de opciones: \""+pathLevels[0]+"\"");
		}
		option.findElement(By.xpath("div/a/span")).click();
		return true;
	}

	public int startCase(String processName) throws Exception{
		WebElement we;
		String[] path = this.pathToArray(processName);
		List<WebElement> wel;
		Actions action = new Actions(this.driver);
		boolean flag = false;
		int value = 0;

		this.selectMenuTreePanelOption("Cases/New case");
		this.driver.switchTo().frame("casesSubFrame");
		//this.waitForElementPresent(By.id("startCaseTreePanel"),30);//para telefónica
		we = this.driver.findElement(By.id("startCaseTreePanel"));
		if(path.length>2)
			throw new Exception("the string Path parameter can contain up to 2 segments, you asshole!");

		Logger.addLog("Buscando caso " + processName + "...");
		if(path.length==2)
		{
			wel = we.findElements(By.xpath("div/div[2]/ul/div/li"));
			we = null;
			for(WebElement we2:wel)
				if(we2.findElement(By.xpath("div/a/span")).getText().equals(path[0]))
				{
					we = we2;
					break;
				}
			if(we == null)
				throw new Exception("No se encontro el proceso: "+processName);
			wel = we.findElements(By.xpath("ul/li"));
			for(WebElement we2:wel)
			{
				we = we2.findElement(By.xpath("div/a/span"));
				if(we.getText().equals(path[1]))
				{
					flag = true;
					break;
				}
			}
			if(!flag)
				throw new Exception("No se encontro el proceso: "+processName);
		}
		else{
			wel = we.findElements(By.xpath("div/div[2]/ul/div/li/ul/li"));
			for(WebElement we2:wel)
			{
				we = we2.findElement(By.xpath("div/a/span"));
				if(we.getText().equals(path[0]))
				{
					flag = true;
					break;
				}
			}
			if(!flag)
				throw new Exception("No se encontro el proceso: "+processName);
		}
		Logger.addLog("Iniciando caso " + processName + "..." + we);
		action.doubleClick(we);
        action.perform();

        value = Integer.parseInt(this.driver.findElement(By.xpath("//div[@id='caseTabPanel']/div[1]/div[1]/ul/li[@id='caseTabPanel__casesTab']")).getText().trim().substring(8));
        return value;
	}

	public int startCase(String processName, boolean flag) throws Exception{
		int value = this.startCase(processName);
		if(flag)
		{
			this.waitForElementPresent(By.id("openCaseFrame"), this.timeout);
			this.driver.switchTo().frame("openCaseFrame");
		}
		return value;
	}

	public boolean openProcess(String processName) throws Exception {
		this.goSection("DESIGNER");
		WebElement we = null;
		Actions action = new Actions(this.driver);

		if(this.skin == 0)
			this.driver.switchTo().frame("frameMain");
		if(this.skin == 1)
			this.driver.switchTo().frame("pm-frame-processes");
		
		WebElement grid = this.driver.findElement(By.id("processesGrid"));
		ExtJSGrid extGrid = new ExtJSGrid(grid, this._browserInstance);
		WebElement pager = this.driver.findElement(By.xpath("//div[@id='processesGrid']/div/div[3]/div/table/tbody/tr/td[1]/table/tbody/tr"));
		List<WebElement> wl;
		int index = 1;
		int pages = Integer.parseInt(pager.findElement(By.xpath("td[6]/div")).getText().trim().substring(3));

		Logger.addLog("Buscando proceso \"" + processName + "\"...");

		while(extGrid.getCurrentPage()<=extGrid.getTotalPages() && we==null){
			Logger.addLog("Buscando en pagina " + extGrid.getCurrentPage() + " de " + extGrid.getTotalPages() + "...");
			wl = grid.findElements(By.xpath("div/div[2]/div/div[1]/div[2]/div/div"));
			for(WebElement we2:wl)
			{
				//Logger.addLog(we2.findElement(By.xpath("table/tbody/tr[1]/td[5]/div")).getText()+"   "+processName);
				if(we2.findElement(By.xpath("table/tbody/tr[1]/td[5]/div")).getText().equals(processName))
				{
					we = we2;
					Logger.addLog("Se encontro el proceso \"" + processName + "\"");
					break;
				}
			}
			if(extGrid.getCurrentPage()==extGrid.getTotalPages() || we!=null)
				break;
			extGrid.nextPage();
		}		

		if(we==null)
			Logger.addLog("ERROR al intentar abrir el proceso \"" + processName + "\", no se encontró el proceso"); //talvez se deberia lanzar un error
		else
		{
			Logger.addLog("Abriendo proceso \"" + processName + "\"...");
			action.doubleClick(we);
        	action.perform();
		}
		return true;
	}
/*
	public void openCase(int numCase){
		ExtJSGrid grid;
		this.selectMenuTreePanelOption("Cases/Inbox");
		this.driver.switchTo().frame("casesSubFrame");
		grid = new ExtJSGrid(this.driver.findElement(By.id("casesGrid")), this.driver);
		grid.getRowByColumn("#", Integer.toString(numCase));
	}*/

	public boolean openProcess(String processName, String category){
		return true;
	}

	public WebElement getTask(String taskName){
		WebElement designPanel = this.driver.findElement(By.xpath("//div[@id='pm_target']/div[1]/div[1]/div[3]"));
		return designPanel.findElement(By.xpath("div[@class='processmap_task___processmaker'][div[1]='"+taskName.trim()+"']"));
	}

	public void selectTaskContextMenuOption(String taskName, String option){
		WebElement contextMenu;
		new Actions(this.driver).contextClick(this.getTask(taskName)).perform();
		contextMenu = this.driver.findElement(By.cssSelector("body > div.app_menuRight_container___processmaker"));
		contextMenu.findElement(By.xpath("div[div[2]='"+option.trim()+"']")).click();
	}

    public boolean waitForElementPresent(By elementLocator){
		return this.waitForElementPresent(elementLocator, this.timeout);
	}

	public boolean waitForElementPresent(By elementLocator, long timeout){
        this.driver.manage().timeouts().implicitlyWait(timeout, TimeUnit.SECONDS);
        //try{
            this.driver.findElement(elementLocator);
            
		this.driver.manage().timeouts().implicitlyWait(this.timeout, TimeUnit.SECONDS);
            return true;
        //}catch(NoSuchElementException e){
            //System.err.print(e.getMessage());
        //}
        //return false;
     }
}