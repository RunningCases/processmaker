package com.colosa.qa.automatization.common;


import java.util.HashMap;
import java.util.Map;

/**
 * Registry pattern
 * User: Herbert Saal
 * Date: 3/7/13
 * Time: 3:50 PM
 * To change this template use File | Settings | File Templates.
 */
public class Registry {
    private static Registry ourInstance = new Registry();
    private final Map registry = new HashMap();

    public static Registry getInstance() {
        return ourInstance;
    }

    private Registry() {
    }

    /**
     * Get Reference of stored object in registry identified by the key
     * @param key the identifier of the object
     * @return return the found object
     */
    public Object getReference(
            final Object key) {
        Object result = null;
        if (isRegistered(key)) {
            result = registry.get(key);
        }
        return result;
    }

    private boolean isRegistered(Object key) {
        return registry.containsKey(key);
    }

    /**
     * Register the specified object (value) int he registry identified by the specified key
     * @param key The identifier of the object.
     * @param value The value - object to be stored in the registry
     */
    public synchronized void register(
            Object key, Object value) {
        registry.put(key, value);
    }

    /**
     * Unregister the specified object from registry.
     * @param key the identifier of the object -value to unregister
     */
    public synchronized void unregister(
            Object key) {
        registry.remove(key);

    }

}


