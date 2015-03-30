
var getKeyValue = 
function getKeyValue(obj, key, undefined) {
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

var setKeyValue = 
function setKeyValue(obj, key, value) {
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

var merge = 
function merge(objFrom, objTo, propMap) {
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
      if (!Array.isArray(toKey)) {
        toKey = [toKey];
      }

      for(x = 0; x < toKey.length; x++) {
        def = null;
        transform = null;
        key = toKey[x];
        keyIsArray = Array.isArray(key);

        if (typeof(key) === "object" && !keyIsArray) {
          def = key.default || null;
          transform = key.transform || null;
          key = key.key;
	  //evaluate if the new key is an array
	  keyIsArray = Array.isArray(key);
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

        value = getKeyValue(objFrom, fromKey);
        
        if (transform) {
          value = transform(value, objFrom, objTo);
        }
        
        if (typeof value !== 'undefined') {
          setKeyValue(objTo, key, value);
        }
        else if (typeof def !== 'undefined') {
          setKeyValue(objTo, key, def);
        }
      }
    }
  }
  
  return objTo;
}; 

var DashboardProxy = function (oauthToken, server, workspace) {
    this.server = server;
    this.workspace = workspace;
    this.baseUrl = "/api/1.0/" + workspace + "/";
    this.oauthToken = oauthToken;
};

DashboardProxy.prototype.userDashboards = function(userId, callBack) {
    this.getJson('dashboard/ownerData/' + userId,
            function (r) {
                var returnList = [];
                $.each(r, function(index, originalObject) {
                    var map = {
                        "DAS_TITLE" : "dashName",
                        "DAS_UID" : "dashUid",
                        "DAS_FAVORITE" : "favorite",
                    };

                    var newObject = merge(originalObject, {}, map);
                    returnList.push(newObject);
                });
		callBack(returnList);
            });
};

DashboardProxy.prototype.dashboardIndicators = function(dashboardId, initDate, endDate, callBack) {
    this.getJson('dashboard/' + dashboardId + '/indicator?dateIni=' + initDate + '&dateFin=' + endDate,
            function (r) {
                var returnList = [];
                $.each(r, function(index, originalObject) {
                    var map = {
                        "DAS_IND_UID" : "indUid",
                        "DAS_IND_TITLE" : "indName",
                        "DAS_IND_TYPE" : "id",
                        "DAS_IND_VARIATION" : "comparative",
                        "DAS_IND_DIRECTION" : "direction",
                        "DAS_IND_VALUE" : "index",
                        "DAS_IND_X" : "x",
                        "DAS_IND_Y" : "y",
                        "DAS_IND_WIDTH" : "width",
                        "DAS_IND_HEIGHT" : "height",
                        "DAS_UID_PROCESS" : "process"
                    };

                    var newObject = merge(originalObject, {}, map);
                    //TODO do not burn this value. Data must come from the endpoint
                    newObject.favorite = ((returnList.length == 1) ? 1 : 0);
                    returnList.push(newObject);
                });
				callBack(returnList);
            });
};

DashboardProxy.prototype.peiData = function(indicatorId, measureDate, compareDate,  callBack) {
	var endPoint = "ReportingIndicators/process-efficiency-data?" +
				"indicator_uid=" + indicatorId + 
				"&measure_date=" + measureDate + 
				"&compare_date=" + compareDate +
				"&language=en";
    this.getJson(endPoint,
            function (r) {
                var graphData = [];
                $.each(r.data, function(index, originalObject) {
                    var map = {
                        "name" : "datalabel",
                        "inefficiencyCost" : "value"
                    };
                    var newObject = merge(originalObject, {}, map);
					var shortLabel = (newObject.datalabel == null) 
												? "" 
												: newObject.datalabel.substring(0,15);
					newObject.datalabel = shortLabel;
                    graphData.push(newObject);
                });
				r.dataToDraw = graphData.splice(0,7);
				callBack(r);
            });
}

DashboardProxy.prototype.processTasksData = function(process, initDate, endDate, callBack) {
	var endPoint = "ReportingIndicators/process-tasks?" +
				"process_list=" + process + 
				"&init_date=" + initDate + 
				"&end_date=" + endDate +
				"&language=en";
    this.getJson(endPoint,
            function (r) {
                var graphData = [];
                $.each(r, function(index, originalObject) {
                    var map = {
                        "name" : "datalabel",
                        "averageTime" : "value",
                        "deviationTime" : "dispersion"
                    };
                    var newObject = merge(originalObject, {}, map);
					newObject.datalabel = newObject.datalabel.substring(0, 7);
                    graphData.push(newObject);
                });
				var retval = {};
				retval.dataToDraw = graphData.splice(0,7);
				retval.tasksData = r;
				callBack(retval);
            });
}

DashboardProxy.prototype.ueiData = function(indicatorId, measureDate, compareDate,  callBack) {
	var endPoint = "ReportingIndicators/employee-efficiency-data?" +
				"indicator_uid=" + indicatorId + 
				"&measure_date=" + measureDate + 
				"&compare_date=" + compareDate +
				"&language=en";
    this.getJson(endPoint,
            function (r) {
                var graphData = [];
                $.each(r.data, function(index, originalObject) {
                    var map = {
                        "name" : "datalabel",
                        "averageTime" : "value",
                        "deviationTime" : "dispersion"
                    };
                    var newObject = merge(originalObject, {}, map);
					var shortLabel = (newObject.datalabel == null) 
												? "" 
												: newObject.datalabel.substring(0,7);

					newObject.datalabel = shortLabel;
					graphData.push(newObject);
                });
				r.dataToDraw = graphData.splice(0,7);
				callBack(r);
            });

    /*var retval = {
                "efficiencyIndex":1.23,
                "efficiencyVariation":0.23,
                "inefficiencyCost":"$ 20112.23",
                "employeeGroupsDataToDraw": 
                                    [
                                    {"value":"96", "datalabel":"User 1"},
                                    {"value":"84", "datalabel":"User 2"},
                                    {"value":"72", "datalabel":"User 3"},
                                    {"value":"18", "datalabel":"User 4"},
                                    {"value":"85", "datalabel":"User 5"}
                                    ],

                "employeeGroupsData": [
                                    {"name": "User 1", "efficiencyIndex":"0.45", "innefficiencyCost":"$ 3404"},
                                    {"name": "User 2", "efficiencyIndex":"1.45", "innefficiencyCost":"$ 1404"},
                                    {"name": "User 3", "efficiencyIndex":"0.25", "innefficiencyCost":"$ 3304"},
                                    {"name": "User 4", "efficiencyIndex":"1.95", "innefficiencyCost":"$ 404"},
                                    {"name": "User 5", "efficiencyIndex":"1.25", "innefficiencyCost":"$ 13404"},
                                    {"name": "User 6", "efficiencyIndex":"0.75", "innefficiencyCost":"$ 4"}
                                    ]
            } 
	return retval;*/
}

DashboardProxy.prototype.userGroupData = function(groupId, initDate, endDate, callBack) {
	var endPoint = "ReportingIndicators/group-employee-data?" +
				"group_uid=" + groupId + 
				"&init_date=" + initDate + 
				"&end_date=" + endDate +
				"&language=en";
    this.getJson(endPoint,
            function (r) {
				 var graphData = [];
                $.each(r, function(index, originalObject) {
                    var map = {
                        "name" : "datalabel",
                        "averageTime" : "value",
                        "deviationTime" : "dispersion"
                    };
                    var newObject = merge(originalObject, {}, map);
					newObject.datalabel = newObject.datalabel.substring(0, 7);
                    graphData.push(newObject);
                });
				var retval = {};
				retval.dataToDraw = graphData.splice(0,7);
				retval.tasksData = r;
				callBack(retval);
            });
}

DashboardProxy.prototype.generalIndicatorData = function(indicatorId, initDate, endDate, callBack) {
	var method = "";
	var endPoint = "ReportingIndicators/general-indicator-data?" +
				"indicator_uid=" + indicatorId + 
				"&init_date=" + initDate + 
				"&end_date=" + endDate +
				"&language=en";
    this.getJson(endPoint,
            function (r) {
                $.each(r.graph1Data, function(index, originalObject) {
					var label = (('YEAR' in originalObject) ? originalObject.YEAR : "") ;
					label += (('MONTH' in originalObject) ? "/" + originalObject.MONTH : "") ;
					label += (('QUARTER' in originalObject) ?  "/" + originalObject.QUARTER : "");
					label += (('SEMESTER' in originalObject) ?  "/" + originalObject.SEMESTER : "");
					originalObject.datalabel = label;
				});

                $.each(r.graph2Data, function(index, originalObject) {
					var label = (('YEAR' in originalObject) ? originalObject.YEAR : "") ;
					label += (('MONTH' in originalObject) ? "/" + originalObject.MONTH : "") ;
					label += (('QUARTER' in originalObject) ?  "/" + originalObject.QUARTER : "");
					label += (('SEMESTER' in originalObject) ?  "/" + originalObject.SEMESTER : "") ;
					originalObject.datalabel = label;
				});
				callBack(r);
            });



    /*var retval = {
				"index" : "23",
                "graph1Data": [
                                    {"value":"96", "datalabel":"User 1"},
                                    {"value":"84", "datalabel":"User 2"},
                                    {"value":"72", "datalabel":"User 3"},
                                    {"value":"18", "datalabel":"User 4"},
                                    {"value":"85", "datalabel":"User 5"}
                               ],
                "graph2Data":	[
                                    {"value":"196", "datalabel":"User 1"},
                                    {"value":"184", "datalabel":"User 2"},
                                    {"value":"172", "datalabel":"User 3"},
                                    {"value":"118", "datalabel":"User 4"},
                                    {"value":"185", "datalabel":"User 5"}
                               ]
            } 
	return retval;*/
}

DashboardProxy.prototype.userTasksData = function(processId, monthCompare, yearCompare) {
    var retval = {
                "tasksDataToDraw": [
                                    {"value":"96", "datalabel":"Task 1"},
                                    {"value":"84", "datalabel":"Task 2"},
                                    {"value":"72", "datalabel":"Task 3"},
                                    {"value":"18", "datalabel":"Task 4"},
                                    {"value":"85", "datalabel":"Task 5"}
                                   ],

                "tasksData": [
                                    {"Name": "Task 1", "efficiencyIndex":"0.45", "deviationTime":"0.45", "averageTime":"34 days"},
                                    {"Name": "Task 2", "efficiencyIndex":"1.45", "deviationTime":"1.45", "averageTime":"14 days"},
                                    {"Name": "Task 3", "efficiencyIndex":"0.25", "deviationTime":"0.25", "averageTime":"3 days"},
                                    {"Name": "Task 4", "efficiencyIndex":"1.95", "deviationTime":"1.95", "averageTime":"4 days"},
                                    {"Name": "Task 5", "efficiencyIndex":"1.25", "deviationTime":"1.25", "averageTime":"14 days"},
                                    {"Name": "Task 6", "efficiencyIndex":"0.75", "deviationTime":"0.75", "averageTime":"4 days"}
                                    ]

            } 
	return retval;
}

DashboardProxy.prototype.getPositionIndicator = function(callBack) {
    this.getJson('dashboard/config', function (r) {
        var graphData = [];
        $.each(r, function(index, originalObject) {
            var map = {
                "widgetId" : originalObject.widgetId,
                "x" : originalObject.x,
                "y" : originalObject.y,
                "width" : originalObject.width,
                "height" : originalObject.height

            };
            graphData.push(map);
        });
        callBack(graphData);
    });
};

DashboardProxy.prototype.setPositionIndicator = function(data, callBack) {
    var that = this;
    
    this.getPositionIndicator( 
        function(response){
            if (response.length != 0) {
                that.putJson('dashboard/config', data, function (r) {
                });
            } else {
                that.postJson('dashboard/config', data, function (r) {
                });
            }
        }
    );
};



DashboardProxy.prototype.getJson = function (endPoint, callBack) {
    var that = this;
    var callUrl = this.baseUrl + endPoint
	//For Debug: console.log('Llamando:');
	//For Debug: console.log(callUrl)
    $.ajax({
        url: callUrl,
        type: 'GET',
        datatype: 'json',
        success: function(response) { callBack(response);  },
        error: function(jqXHR, textStatus, errorThrown) {
							throw new Error(textStatus);
                        },
        beforeSend: function (xhr) {
						xhr.setRequestHeader('Authorization', 'Bearer ' + that.oauthToken);
						xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
					}

    });
}

DashboardProxy.prototype.postJson = function (endPoint, data, callBack) {
    var that = this;
    $.ajax({
        url : this.baseUrl + endPoint,
        type : 'POST',
        datatype : 'json',
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(data),
        success: function(response) {
            callBack(response);  
        },
        error: function(jqXHR, textStatus, errorThrown) {
			throw new Error(textStatus);
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + that.oauthToken);
            xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
        }       
    }).fail(function () {
		throw new Error('Fail server');
    });
};


DashboardProxy.prototype.putJson = function (endPoint, data, callBack) {
    var that = this;
    $.ajax({
        url : this.baseUrl + endPoint,
        type : 'PUT',
        datatype : 'json',
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(data),
        success: function(response) {
            callBack(response);  
        },
        error: function(jqXHR, textStatus, errorThrown) {
			throw new Error(textStatus);
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + that.oauthToken);
            xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
        }       
    }).fail(function () {
		throw new Error('Fail server');
    });
};
