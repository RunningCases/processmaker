var ViewDashboardModel = function (oauthToken, server, workspace) {
    this.server = server;
    this.workspace = workspace;
    this.baseUrl =  "/api/1.0/" + workspace + "/";
    //this.baseUrl =  "http://127.0.0.1:8080/api/1.0/workflow/";
    this.oauthToken = oauthToken;
	this.helper = new ViewDashboardHelper();
	this.cache = [];
	this.forceRemote=false; //if true, the next call will go to the remote server
};

ViewDashboardModel.prototype.userDashboards = function(userId) {
    return this.getJson('dashboard/ownerData/' + userId);
};

ViewDashboardModel.prototype.dashboardIndicators = function(dashboardId, initDate, endDate) {
    return this.getJson('dashboard/' + dashboardId + '/indicator?dateIni=' + initDate + '&dateFin=' + endDate);
};

ViewDashboardModel.prototype.peiData = function(indicatorId, compareDate, measureDate) {
	var endPoint = "ReportingIndicators/process-efficiency-data?" +
				"indicator_uid=" + indicatorId + 
				"&compare_date=" + compareDate +
				"&measure_date=" + measureDate + 
				"&language=en";
    return this.getJson(endPoint);
}

ViewDashboardModel.prototype.statusData = function() {
    var endPoint = "ReportingIndicators/status-indicator";
    return this.getJson(endPoint);
}

ViewDashboardModel.prototype.peiDetailData = function(process, initDate, endDate) {
	var endPoint = "ReportingIndicators/process-tasks?" +
				"process_list=" + process + 
				"&init_date=" + initDate + 
				"&end_date=" + endDate +
				"&language=en";
    return this.getJson(endPoint);
}

ViewDashboardModel.prototype.ueiData = function(indicatorId, compareDate, measureDate ) {
	var endPoint = "ReportingIndicators/employee-efficiency-data?" +
				"indicator_uid=" + indicatorId + 
				"&compare_date=" + compareDate +
				"&measure_date=" + measureDate + 
				"&language=en";
    return this.getJson(endPoint);
}

ViewDashboardModel.prototype.ueiDetailData = function(groupId, initDate, endDate) {
	var endPoint = "ReportingIndicators/group-employee-data?" +
				"group_uid=" + groupId + 
				"&init_date=" + initDate + 
				"&end_date=" + endDate +
				"&language=en";
    return this.getJson(endPoint);
}

ViewDashboardModel.prototype.generalIndicatorData = function(indicatorId, initDate, endDate) {
	var method = "";
	var endPoint = "ReportingIndicators/general-indicator-data?" +
				"indicator_uid=" + indicatorId + 
				"&init_date=" + initDate + 
				"&end_date=" + endDate +
				"&language=en";
    return this.getJson(endPoint);
}

ViewDashboardModel.prototype.getPositionIndicator = function(callBack) {
    this.getJson('dashboard/config').done(function (r) {
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

ViewDashboardModel.prototype.setPositionIndicator = function(data) {
    var that = this;
    
    this.getPositionIndicator( 
        function(response){
            if (response.length != 0) {
                that.putJson('dashboard/config', data);
            } else {
                that.postJson('dashboard/config', data);
            }
        }
    );
};

ViewDashboardModel.prototype.getJson = function (endPoint) {
    var that = this;
    var callUrl = this.baseUrl + endPoint
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
							xhr.setRequestHeader('Authorization', 'Bearer ' + that.oauthToken);
							//xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
						}
		});
	}
}

ViewDashboardModel.prototype.postJson = function (endPoint, data) {
    var that = this;
    return $.ajax({
        url : this.baseUrl + endPoint,
        type : 'POST',
        datatype : 'json',
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(data),
        error: function(jqXHR, textStatus, errorThrown) {
			throw new Error(errorThrown);
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + that.oauthToken);
            xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
        }       
    }).fail(function () {
		throw new Error('Fail server');
    });
};

ViewDashboardModel.prototype.putJson = function (endPoint, data) {
    var that = this;
    return $.ajax({
        url : this.baseUrl + endPoint,
        type : 'PUT',
        datatype : 'json',
        contentType: "application/json; charset=utf-8",
        data: JSON.stringify(data),
        error: function(jqXHR, textStatus, errorThrown) {
			throw new Error(errorThrown);
        },
        beforeSend: function (xhr) {
            xhr.setRequestHeader('Authorization', 'Bearer ' + that.oauthToken);
            //xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
        }       
    }).fail(function () {
		throw new Error('Fail server');
    });
};

ViewDashboardModel.prototype.getCacheItem = function (endPoint) {
	var retval = null;
	$.each(this.cache, function(index, objectItem) {
		if (objectItem.key == endPoint) {
			retval = objectItem.value;
		}
	});
	return retval;
}

ViewDashboardModel.prototype.putInCache = function (endPoint, data) {
	var cacheItem = this.getCacheItem(endPoint);
	if (cacheItem == null) {
		this.cache.push ({ key: endPoint, value:data });
	}
	else {
		cacheItem.value = data;
	}
}
