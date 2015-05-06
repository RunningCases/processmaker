

var ViewDashboardHelper = function () {
};

ViewDashboardHelper.prototype.userDashboards = function(userId, callBack) {
};

ViewDashboardHelper.prototype.stringIfNull = function (val){
	if(val === null || val == undefined || val == "?"){
		val = "?";
	} else {
		val = (parseFloat(val)).toFixed(2);
	}
	return val;
};

ViewDashboardHelper.prototype.zeroIfNull = function (val) {
	var retval = 0;
	if(val === null || val === undefined || val === "") {
		retval = 0;
	} else {
		retval = val;
	}
	return retval;
};

ViewDashboardHelper.prototype.labelIfEmpty = function (val){
	if(val === null || val == undefined || val == ""){
		val = "(No Name)";
	} else {
		val = val
	}
	return val;
};

ViewDashboardHelper.prototype.assert = function (condition, message) {
    if (!condition) {
        message = message || "Assertion failed";
        if (typeof Error !== "undefined") {
            throw new Error(message);
        }
        throw message; // Fallback
    }
}

ViewDashboardHelper.prototype.truncateString = function (string, len) {
	this.assert(len != null && len > 0, "Var len not valid. String must by truncated to a positive non zero length.");
	this.assert(string != null, "var string can't be null.");

	var retval = "";
	if(string.length > len){
		retval = string.substring(0, len ) + "...";
	} 
	else{
		retval = string;
	}
	return retval;
}

ViewDashboardHelper.prototype.getKeyValue = function (obj, key, undefined) {
  var reg = /\./gi
    , subKey
    , keys
    , context
    , x
    ;
  
  if (reg.test(key)) {
    keys = key.split(reg);
    context = obj;
    
    for (x = 0; x < keys.length; x++) {
      subKey = keys[x];
      
      //the values of all keys except for
      //the last one should be objects
      if (x < keys.length -1) {
        if (!context.hasOwnProperty(subKey)) {
          return undefined;
        }
        
        context = context[subKey];
      }
      else {
        return context[subKey];
      }
    }
  }
  else {
    return obj[key];
  }
};

ViewDashboardHelper.prototype.setKeyValue = function (obj, key, value) {
  var reg = /\./gi
    , subKey
    , keys
    , context
    , x
    ;
  
  //check to see if we need to process 
  //multiple levels of objects
  if (reg.test(key)) {
    keys = key.split(reg);
    context = obj;
    
    for (x = 0; x < keys.length; x++) {
      subKey = keys[x];
      
      //the values of all keys except for
      //the last one should be objects
      if (x < keys.length -1) {
        if (!context[subKey]) {
          context[subKey] = {};
        }
        
        context = context[subKey];
      }
      else {
        context[subKey] = value;
      }
    }
  }
  else {
    obj[key] = value;
  }
};

ViewDashboardHelper.prototype.merge = function (objFrom, objTo, propMap) {
  var toKey
    , fromKey
    , x
    , value
    , def
    , transform
    , key
    , keyIsArray
    ;
    
  if (!objTo) {
    objTo = {};
  }
  
  for(fromKey in propMap) {
    if (propMap.hasOwnProperty(fromKey)) {
      toKey = propMap[fromKey];

      //force toKey to an array of toKeys
      //if (!Array.isArray(toKey)) {
      if (!$.isArray(toKey)) {
        toKey = [toKey];
      }

      for(x = 0; x < toKey.length; x++) {
        def = null;
        transform = null;
        key = toKey[x];
        //keyIsArray = Array.isArray(key);
        keyIsArray = $.isArray(key);

        if (typeof(key) === "object" && !keyIsArray) {
          //def = (key.default || null);
		  def = null;
          transform = key.transform || null;
          key = key.key;
	  //evaluate if the new key is an array
	 // keyIsArray = Array.isArray(key);
	    keyIsArray = $.isArray(key);
        }

	if (keyIsArray) {
          //key[toKeyName,transform,default]
          def = key[2] || null;
          transform = key[1] || null;
          key = key[0];
        }

        if (def && typeof(def) === "function" ) {
          def = def(objFrom, objTo);
        }

        value = this.getKeyValue(objFrom, fromKey);
        
        if (transform) {
          value = transform(value, objFrom, objTo);
        }
        
        if (typeof value !== 'undefined') {
          this.setKeyValue(objTo, key, value);
        }
        else if (typeof def !== 'undefined') {
          this.setKeyValue(objTo, key, def);
        }
      }
    }
  }
  
  return objTo;
}; 


