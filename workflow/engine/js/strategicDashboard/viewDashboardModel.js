
var ViewDashboardModel = function (oauthToken, server, workspace) {
    this.server = server;
    this.workspace = workspace;
    this.baseUrl =  "/api/1.0/" + workspace + "/";
    this.oauthToken = oauthToken;
	this.helper = new ViewDashboardHelper();
};

ViewDashboardModel.prototype.userDashboards = function(userId) {
    return this.getJson('dashboard/ownerData/' + userId);
};

ViewDashboardModel.prototype.dashboardIndicators = function(dashboardId, initDate, endDate) {
    return this.getJson('dashboard/' + dashboardId + '/indicator?dateIni=' + initDate + '&dateFin=' + endDate);
};

ViewDashboardModel.prototype.peiData = function(indicatorId, measureDate, compareDate) {
	var endPoint = "ReportingIndicators/process-efficiency-data?" +
				"indicator_uid=" + indicatorId + 
				"&measure_date=" + measureDate + 
				"&compare_date=" + compareDate +
				"&language=en";
    return this.getJson(endPoint);
}

ViewDashboardModel.prototype.statusData = function(indicatorId, measureDate, compareDate) {
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

ViewDashboardModel.prototype.ueiData = function(indicatorId, measureDate, compareDate) {
	var endPoint = "ReportingIndicators/employee-efficiency-data?" +
				"indicator_uid=" + indicatorId + 
				"&measure_date=" + measureDate + 
				"&compare_date=" + compareDate +
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
    return $.ajax({
        url: callUrl,
        type: 'GET',
        datatype: 'json',
        error: function(jqXHR, textStatus, errorThrown) {
							throw new Error(callUrl + ' --  ' + errorThrown);
                        },
        beforeSend: function (xhr) {
						xhr.setRequestHeader('Authorization', 'Bearer ' + that.oauthToken);
						//xhr.setRequestHeader('Access-Control-Allow-Origin', '*');
					}
    });
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

