package com.colosa.qa.automatization.common;

/**
 * Created with IntelliJ IDEA.
 * User: herbert
 * Date: 5/6/13
 * Time: 5:46 PM
 * To change this template use File | Settings | File Templates.
 */
public class InvalidPageException extends Exception {
    public InvalidPageException() { super(); }
    public InvalidPageException(String message) { super(message); }
    public InvalidPageException(String message, Throwable cause) { super(message, cause); }
    public InvalidPageException(Throwable cause) { super(cause); }
}
