package com.colosa.qa.automatization.common;

import org.openqa.selenium.*;
import org.openqa.selenium.support.ui.ExpectedCondition;
import org.openqa.selenium.support.ui.ExpectedConditions;
import org.openqa.selenium.support.ui.Select;
import org.openqa.selenium.support.ui.WebDriverWait;

import java.util.List;
import java.util.concurrent.TimeUnit;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 11/1/13
 * Time: 4:43 PM
 * To change this template use File | Settings | File Templates.
 */
public class WaitTool {
    /** Default wait time for an element. 7 seconds. */
    public static final int DEFAULT_WAIT_4_ELEMENT = 7;
    /** Default wait time for a page to be displayed. 12 seconds.
     * The average webpage load time is 6 seconds in 2012.
     * Based on your tests, please set this value.
     * "0" will nullify implicitlyWait and speed up a test. */
    public static final int DEFAULT_WAIT_4_PAGE = 12;




    /**
     * Wait for the element to be present in the DOM, and displayed on the page.
     * And returns the first WebElement using the given method.
     *
     * @param driver        The driver object to be used
     * @param by        selector to find the element
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return WebElement        the first WebElement using the given method, or null (if the timeout is reached)
     */
    public static WebElement waitForElement(WebDriver driver, final By by, int timeOutInSeconds) {
        WebElement element;
        try{
            //To use WebDriverWait(), we would have to nullify implicitlyWait().
            //Because implicitlyWait time also set "driver.findElement()" wait time.
            //info from: https://groups.google.com/forum/?fromgroups=#!topic/selenium-users/6VO_7IXylgY
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()

            WebDriverWait wait = new WebDriverWait(driver, timeOutInSeconds);
            element = wait.until(ExpectedConditions.visibilityOfElementLocated(by));

            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return element; //return the element
        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }

    /**
     * Wait for the specified element to be present in the DOM, and displayed on the page.
     * And returns the first WebElement using the given method.
     *
     * @param driver        The driver object to be used
     * @param element        The element
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return WebElement        the first WebElement using the given method, or null (if the timeout is reached)
     */
    public static void waitForElementVisibleAndEnable(WebDriver driver, final WebElement element, int timeOutInSeconds) {
        //WebElement element;
        try{
            //To use WebDriverWait(), we would have to nullify implicitlyWait().
            //Because implicitlyWait time also set "driver.findElement()" wait time.
            //info from: https://groups.google.com/forum/?fromgroups=#!topic/selenium-users/6VO_7IXylgY
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()

            WebDriverWait wait = new WebDriverWait(driver, timeOutInSeconds);
            wait.until(new ExpectedCondition<Boolean>() {
                @Override
                public Boolean apply(WebDriver driverObject) {
                    return element.isDisplayed() && element.isEnabled();
                }
            });

            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return; //return
        } catch (Exception e) {
            e.printStackTrace();
        }
        return;
    }



    /**
     * Wait for the element to be present in the DOM, regardless of being displayed or not.
     * And returns the first WebElement using the given method.
     *
     * @param driver        The driver object to be used
     * @param by        selector to find the element
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return WebElement        the first WebElement using the given method, or null (if the timeout is reached)
     */
    public static WebElement waitForElementPresent(WebDriver driver, final By by, int timeOutInSeconds) {
        WebElement element;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()

            WebDriverWait wait = new WebDriverWait(driver, timeOutInSeconds);
            element = wait.until(ExpectedConditions.presenceOfElementLocated(by));

            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return element; //return the element
        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }


    /**
     * Wait for the List<WebElement> to be present in the DOM, regardless of being displayed or not.
     * Returns all elements within the current page DOM.
     *
     * @param driver        The driver object to be used
     * @param by        selector to find the element
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return List<WebElement> all elements within the current page DOM, or null (if the timeout is reached)
     */
    public static List<WebElement> waitForListElementsPresent(WebDriver driver, final By by, int timeOutInSeconds) {
        List<WebElement> elements;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()

            WebDriverWait wait = new WebDriverWait(driver, timeOutInSeconds);
            wait.until((new ExpectedCondition<Boolean>() {
                @Override
                public Boolean apply(WebDriver driverObject) {
                    return areElementsPresent(driverObject, by);
                }
            }));

            elements = driver.findElements(by);
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return elements; //return the element
        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }

    /**
     * Wait for an element to appear on the refreshed web-page.
     * And returns the first WebElement using the given method.
     *
     * This method is to deal with dynamic pages.
     *
     * Some sites I (Mark) have tested have required a page refresh to add additional elements to the DOM.
     * Generally you (Chon) wouldn't need to do this in a typical AJAX scenario.
     *
     * @param driver        The driver object to use to perform this element search
     * @param by        selector to find the element
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return WebElement        the first WebElement using the given method, or null(if the timeout is reached)
     *
     * @author Mark Collin
     */
    public static WebElement waitForElementRefresh(WebDriver driver, final By by,
                                                   int timeOutInSeconds) {
        WebElement element;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
            new WebDriverWait(driver, timeOutInSeconds) {
            }.until(new ExpectedCondition<Boolean>() {

                @Override
                public Boolean apply(WebDriver driverObject) {
                    driverObject.navigate().refresh(); //refresh the page ****************
                    return isElementPresentAndDisplay(driverObject, by);
                }
            });
            element = driver.findElement(by);
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return element; //return the element
        } catch (Exception e) {
            e.printStackTrace();
        }
        return null;
    }

    /**
     * Wait for the Text to be present in the given element, regardless of being displayed or not.
     *
     * @param driver        The driver object to be used to wait and find the element
     * @param by        selector of the given element, which should contain the text
     * @param text        The text we are looking
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return boolean
     */
    public static boolean waitForTextPresent(WebDriver driver, final By by, final String text, int timeOutInSeconds) {
        boolean isPresent = false;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
            new WebDriverWait(driver, timeOutInSeconds) {
            }.until(new ExpectedCondition<Boolean>() {

                @Override
                public Boolean apply(WebDriver driverObject) {
                    return isTextPresent(driverObject, by, text); //is the Text in the DOM
                }
            });
            isPresent = isTextPresent(driver, by, text);
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return isPresent;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }


    /**
     * Wait for the Text to be present in the given element, regardless of being displayed or not.
     *
     * @param driver        The driver object to be used to wait and find the element
     * @param element       The given element, which should contain the text
     * @param text        The text we are looking
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return boolean
     */
    public static boolean waitForTextPresent(WebDriver driver, final WebElement element, final String text, int timeOutInSeconds) {
        boolean isPresent = false;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
            new WebDriverWait(driver, timeOutInSeconds) {
            }.until(new ExpectedCondition<Boolean>() {

                @Override
                public Boolean apply(WebDriver driverObject) {
                    return element.getText().contains(text);
                }
            });
            isPresent = element.getText().contains(text);
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return isPresent;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    /**
     * Wait for the Text to change in the given element, regardless of being displayed or not.
     *
     * @param driver        The driver object to be used to wait and find the element
     * @param by        selector of the given element, which should contain the text
     * @param currentText        The text we are looking
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return boolean
     */
    public static boolean waitForTextToChange(WebDriver driver, final By by, final String currentText, int timeOutInSeconds) {
        boolean isPresent = false;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
            new WebDriverWait(driver, timeOutInSeconds) {
            }.until(new ExpectedCondition<Boolean>() {

                @Override
                public Boolean apply(WebDriver driverObject) {
                    return !isTextPresent(driverObject, by, currentText); //is the Text different in the DOM
                }
            });
            isPresent = !isTextPresent(driver, by, currentText);
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return isPresent;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }


    /**
     * Wait for the Text to change in the given element, regardless of being displayed or not.
     *
     * @param driver        The driver object to be used to wait and find the element
     * @param element        selector of the given element, which should contain the value
     * @param currentText        The text we are looking
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return boolean
     */
    public static boolean waitForValueToChange(WebDriver driver, final WebElement element, final String currentText, int timeOutInSeconds) {

        boolean isPresent = false;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
            new WebDriverWait(driver, timeOutInSeconds) {
            }.until(new ExpectedCondition<Boolean>() {
                @Override
                public Boolean apply(WebDriver driverObject) {
                    Logger.addLog("waitForValueToChange element value:" + element.getAttribute("value") + " != " + currentText + " => " + !element.getAttribute("value").equals(""));
                    if(currentText.equals("")){
                        return !(element.getAttribute("value").equals(""));
                    }else{
                        return !(element.getAttribute("value").contains(currentText));
                    }
                }
            });
            if(currentText.equals("")){
                isPresent = !(element.getAttribute("value").equals(""));
            }else{
                isPresent = !(element.getAttribute("value").contains(currentText));
            }
            //isPresent = !element.getText().contains(currentText);
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return isPresent;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    /**
     * Wait for the Text to change in the given element, regardless of being displayed or not.
     *
     * @param driver        The driver object to be used to wait and find the element
     * @param element        selector of the given element, which should contain the text
     * @param currentText        The text we are looking
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return boolean
     */
    public static boolean waitForTextToChange(WebDriver driver, final WebElement element, final String currentText, int timeOutInSeconds) {

        boolean isPresent = false;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
            new WebDriverWait(driver, timeOutInSeconds) {
            }.until(new ExpectedCondition<Boolean>() {
                @Override
                public Boolean apply(WebDriver driverObject) {
                    Logger.addLog("waitForTextToChange element text:" + element.getText() + " != " + currentText + " => " + !element.getText().equals(""));
                    if(currentText.equals("")){
                        return !(element.getText().equals(""));
                    }else{
                        return !(element.getText().contains(currentText));
                    }
                }
            });
            if(currentText.equals("")){
                isPresent = !(element.getText().equals(""));
            }else{
                isPresent = !(element.getText().contains(currentText));
            }
            //isPresent = !element.getText().contains(currentText);
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return isPresent;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    /**
     * Wait for selected element to change in the given select element, regardless of being displayed or not.
     *
     * @param driver        The driver object to be used to wait and find the element
     * @param selectElement        selector of the given element, which should contain the text
     * @param currentText        The text we are looking
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return boolean
     */
    public static boolean waitForSelectedTextToChange(WebDriver driver, final WebElement selectElement, final String currentText, int timeOutInSeconds) {

        boolean isPresent = false;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
            new WebDriverWait(driver, timeOutInSeconds) {
            }.until(new ExpectedCondition<Boolean>() {
                @Override
                public Boolean apply(WebDriver driverObject) {
                    Select selectList = new Select(selectElement);
                    String selectedText = "";
                    try{
                        WebElement selectedOption = selectList.getFirstSelectedOption();
                        selectedText = selectedOption.getText();
                    }catch (NoSuchElementException ex){
                        //no selected element
                        selectedText = "";
                    }
                    Logger.addLog("waitForSelectedTextToChange Element Text:" + selectedText + " != " + currentText);
                    return (!selectedText.equals(currentText));
                }
            });
            Select selectList = new Select(selectElement);
            String selectedText = "";
            try{
                WebElement selectedOption = selectList.getFirstSelectedOption();
                selectedText = selectedOption.getText();
            }catch (NoSuchElementException ex){
                //no selected element
                selectedText = "";
            }
            isPresent = !(selectedText.equals(currentText));
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return isPresent;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    /**
     * Wait for selected element to change in the given select element, regardless of being displayed or not.
     *
     * @param driver        The driver object to be used to wait and find the element
     * @param selectElement        selector of the given element, which should contain the text
     * @param currentValue        The text we are looking
     * @param timeOutInSeconds        The time in seconds to wait until returning a failure
     *
     * @return boolean
     */
    public static boolean waitForSelectedValueToChange(WebDriver driver, final WebElement selectElement, final String currentValue, int timeOutInSeconds) {

        boolean isPresent = false;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
            new WebDriverWait(driver, timeOutInSeconds) {
            }.until(new ExpectedCondition<Boolean>() {
                @Override
                public Boolean apply(WebDriver driverObject) {
                    Select selectList = new Select(selectElement);
                    String selectedValue = "";
                    try{
                        WebElement selectedOption = selectList.getFirstSelectedOption();
                        selectedValue = selectedOption.getAttribute("value");
                    }catch (NoSuchElementException ex){
                        //no selected element
                        selectedValue = "";
                    }
                    Logger.addLog("waitForSelectedValueToChange Element Value:" + selectedValue + " != " + currentValue);
                    return (!selectedValue.equals(currentValue));
                }
            });
            Select selectList = new Select(selectElement);
            String selectedValue = "";
            try{
                WebElement selectedOption = selectList.getFirstSelectedOption();
                selectedValue = selectedOption.getAttribute("value");
            }catch (NoSuchElementException ex){
                //no selected element
                selectedValue = "";
            }
            isPresent = !(selectedValue.equals(currentValue));
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return isPresent;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }

    /**
     * Waits for the Condition of JavaScript.
     *
     *
     * @param driver                The driver object to be used to wait and find the element
     * @param javaScript            The javaScript condition we are waiting. e.g. "return (xmlhttp.readyState >= 2 && xmlhttp.status == 200)"
     * @param timeOutInSeconds      The time in seconds to wait until returning a failure
     *
     * @return boolean true or false(condition fail, or if the timeout is reached)
     **/
    public static boolean waitForJavaScriptCondition(WebDriver driver, final String javaScript,
                                                     int timeOutInSeconds) {
        boolean jscondition = false;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
            new WebDriverWait(driver, timeOutInSeconds) {
            }.until(new ExpectedCondition<Boolean>() {

                @Override
                public Boolean apply(WebDriver driverObject) {
                    return (Boolean) ((JavascriptExecutor) driverObject).executeScript(javaScript);
                }
            });
            jscondition = (Boolean) ((JavascriptExecutor) driver).executeScript(javaScript);
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return jscondition;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return false;
    }


    /** Waits for the completion of Ajax jQuery processing by checking "return jQuery.active == 0" condition.
     *
     * @param driver - The driver object to be used to wait and find the element
     * @param timeOutInSeconds - The time in seconds to wait until returning a failure
     *
     * @return boolean true or false(condition fail, or if the timeout is reached)
     * */
    public static boolean waitForJQueryProcessing(WebDriver driver, int timeOutInSeconds){
        boolean jQcondition = false;
        try{
            driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
            new WebDriverWait(driver, timeOutInSeconds) {
            }.until(new ExpectedCondition<Boolean>() {

                @Override
                public Boolean apply(WebDriver driverObject) {
                    return (Boolean) ((JavascriptExecutor) driverObject).executeScript("return jQuery.active == 0");
                }
            });
            jQcondition = (Boolean) ((JavascriptExecutor) driver).executeScript("return jQuery.active == 0");
            driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
            return jQcondition;
        } catch (Exception e) {
            e.printStackTrace();
        }
        return jQcondition;
    }


    /**
     * Coming to implicit wait, If you have set it once then you would have to explicitly set it to zero to nullify it -
     */
    public static void nullifyImplicitWait(WebDriver driver) {
        driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
    }


    /**
     * Set driver implicitlyWait() time.
     */
    public static void setImplicitWait(WebDriver driver, int waitTime_InSeconds) {
        driver.manage().timeouts().implicitlyWait(waitTime_InSeconds, TimeUnit.SECONDS);
    }

    /**
     * Reset ImplicitWait.
     * To reset ImplicitWait time you would have to explicitly
     * set it to zero to nullify it before setting it with a new time value.
     */
    public static void resetImplicitWait(WebDriver driver) {
        driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
        driver.manage().timeouts().implicitlyWait(DEFAULT_WAIT_4_PAGE, TimeUnit.SECONDS); //reset implicitlyWait
    }


    /**
     * Reset ImplicitWait.
     * @param newWaittime_InSeconds - a new wait time in seconds
     */
    public static void resetImplicitWait(WebDriver driver, int newWaittime_InSeconds) {
        driver.manage().timeouts().implicitlyWait(0, TimeUnit.SECONDS); //nullify implicitlyWait()
        driver.manage().timeouts().implicitlyWait(newWaittime_InSeconds, TimeUnit.SECONDS); //reset implicitlyWait
    }


    /**
     * Checks if the text is present in the element.
     *
     * @param driver - The driver object to use to perform this element search
     * @param by - selector to find the element that should contain text
     * @param text - The Text element you are looking for
     * @return true or false
     */
    private static boolean isTextPresent(WebDriver driver, By by, String text)
    {
        try {
            return driver.findElement(by).getText().contains(text);
        } catch (NullPointerException e) {
            return false;
        }
    }

    /**
     * Checks if the current text is equal to the specified.
     *
     * @param driver - The driver object to use to perform this element search
     * @param by - selector to find the element that should contain text
     * @param text - The current Text element you are looking for
     * @return true or false
     */
    private static boolean isTextEqual(WebDriver driver, By by, String text)
    {
        try {
            return driver.findElement(by).getText().equals(text);
        } catch (NullPointerException e) {
            return false;
        }
    }


    /**
     * Checks if the elment is in the DOM, regardless of being displayed or not.
     *
     * @param driver - The driver object to use to perform this element search
     * @param by - selector to find the element
     * @return boolean
     */
    private static boolean isElementPresent(WebDriver driver, By by) {
        try {
            driver.findElement(by);//if it does not find the element throw NoSuchElementException, which calls "catch(Exception)" and returns false;
            return true;
        } catch (NoSuchElementException e) {
            return false;
        }
    }


    /**
     * Checks if the List<WebElement> are in the DOM, regardless of being displayed or not.
     *
     * @param driver - The driver object to use to perform this element search
     * @param by - selector to find the element
     * @return boolean
     */
    private static boolean areElementsPresent(WebDriver driver, By by) {
        try {
            driver.findElements(by);
            return true;
        } catch (NoSuchElementException e) {
            return false;
        }
    }

    /**
     * Checks if the elment is in the DOM and displayed.
     *
     * @param driver - The driver object to use to perform this element search
     * @param by - selector to find the element
     * @return boolean
     */
    private static boolean isElementPresentAndDisplay(WebDriver driver, By by) {
        try {
            return driver.findElement(by).isDisplayed();
        } catch (NoSuchElementException e) {
            return false;
        }
    }
}
