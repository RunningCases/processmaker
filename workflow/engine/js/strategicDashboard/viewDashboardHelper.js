var ViewDashboardHelper = function () {
	this.cache = [];
	this.forceRemote=false; //if true, the next call will go to the remote server
};

ViewDashboardHelper.prototype.ReportingPeriodicityEnum = {
		NONE : 0,
		MONTH : 100,
		QUARTER : 200,
		SEMESTER : 300,
		YEAR : 400
}

ViewDashboardHelper.prototype.ReportingIndicatorEnum = {
		PEI : 1010,
		EEI : 1030,
		INBOX_STATUS : 1050
}

ViewDashboardHelper.prototype.date2MysqlString = function (val){
	return val.getFullYear() + '-' + (val.getMonth() + 1) + '-' + val.getDay() ;
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

ViewDashboardHelper.prototype.fillSelectWithOptions = function ($select, options, selectedValue) {
	$select.empty(); // remove old options
    var list = options;
	$.each(list, function(index, option) {
	  $select.append($("<option></option>")
					 .attr("value", option.value).text(option.label));
	});
	$select.val(selectedValue);
}

ViewDashboardHelper.prototype.setVisibility = function ($element, isVisible) {
	$element.css('visibility', (isVisible ? 'visible' : 'hidden'));
	$element.css('display', (isVisible ? 'inline' : 'none'));
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


ViewDashboardHelper.prototype.getJson = function (endPoint, baseUrl, oauthToken) {
    var that = this;
    var callUrl = baseUrl + endPoint
	var requestFinished = $.Deferred();
	var itemInCache = that.getCacheItem(endPoint);

	if (itemInCache != null && !this.forceRemote) {
		that.forceRemote = false;
		requestFinished.resolve(itemInCache);
		return requestFinished.promise();
	}
	else {
		return $.ajax({
			url: callUrl,
			type: 'GET',
			datatype: 'json',
			success: function (data) {
				that.forceRemote = false;
				requestFinished.resolve(data);
				that.putInCache(endPoint, data);
			//	return requestFinished.promise();
			},
			error: function(jqXHR, textStatus, errorThrown) {
								throw new Error(callUrl + ' --  ' + errorThrown);
							},
			beforeSend: function (xhr) {
							xhr.setRequestHeader('Authorization', 'Bearer ' + oauthToken);
							//xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
						}
		});
	}
}

ViewDashboardHelper.prototype.postJson = function (endPoint, data, baseUrl, oauthToken) {
    var that = this;
    return $.ajax({
        url : baseUrl + endPoint,
        type : 'POST',
        datatype : 'json',
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(data),
        error: function(jqXHR, textStatus, errorThrown) {
			throw new Error(errorThrown);
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + oauthToken);
            xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
        }       
    }).fail(function () {
		throw new Error('Fail server');
    });
};

ViewDashboardHelper.prototype.putJson = function (endPoint, data, baseUrl, oauthToken) {
    var that = this;
    return $.ajax({
        url : baseUrl + endPoint,
        type : 'PUT',
        datatype : 'json',
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(data),
        error: function(jqXHR, textStatus, errorThrown) {
			throw new Error(errorThrown);
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + oauthToken);
            //xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
        }       
    }).fail(function () {
		throw new Error('Fail server');
    });
};

ViewDashboardHelper.prototype.getCacheItem = function (endPoint) {
	var retval = null;
	$.each(this.cache, function(index, objectItem) {
		if (objectItem.key == endPoint) {
			retval = objectItem.value;
		}
	});
	return retval;
}

ViewDashboardHelper.prototype.putInCache = function (endPoint, data) {
	var cacheItem = this.getCacheItem(endPoint);
	if (cacheItem == null) {
		this.cache.push ({ key: endPoint, value:data });
	}
	else {
		cacheItem.value = data;
	}
}



ViewDashboardHelper.prototype.periodInitDate = function (periodicity, period, year) {
	var retval = null;
	switch (periodicity * 1) {
		case this.ReportingPeriodicityEnum.MONTH:
			retval = new Date(year, period - 1, 1);
			break;
		case this.ReportingPeriodicityEnum.QUARTER:
			retval = new Date(year, 3 * (period-1), 1);
			break;
		case this.ReportingPeriodicityEnum.SEMESTER:
			retval = new Date(year, 6 * (period-1), 1);
			break;
		case this.ReportingPeriodicityEnum.YEAR:
			retval = new Date(year, 0, 1);
			break;
	}
	if (retval == null) {
		throw new Error("The periodicity " + periodicity + " is not supported.");
	}
	return retval;
}

ViewDashboardHelper.prototype.periodEndDate = function (periodicity, period, year) {
	var retval = null;
	switch (periodicity * 1) {
		case this.ReportingPeriodicityEnum.MONTH:
			retval = new Date(year, period, 0, 23,59,59);
			break;
		case this.ReportingPeriodicityEnum.QUARTER:
			retval = new Date(year, 3 * (period), 0, 23, 59, 59);
			break;
		case this.ReportingPeriodicityEnum.SEMESTER:
			retval = new Date(year, 6 * (period), 0, 23, 59, 59);
			break;
		case this.ReportingPeriodicityEnum.YEAR:
			retval = new Date(year, 11, 31, 23, 59, 59);
			break;
	}
	if (retval == null) {
		throw new Error("The periodicity " + periodicity + " is not supported.");
	}
	return retval;

}



ViewDashboardHelper.prototype.unescape = function (string) {
    var temp = document.createElement("div");
    temp.innerHTML = string;
    var result = temp.childNodes[0].nodeValue;
    temp.removeChild(temp.firstChild);
    return result;
}


