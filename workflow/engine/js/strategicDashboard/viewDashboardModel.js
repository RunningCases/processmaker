var ViewDashboardModel = function (oauthToken, server, workspace, moneySymbol) {
    this.server = server;
    this.workspace = workspace;
    this.baseUrl =  server;
    this.oauthToken = oauthToken;
	this.helper = new ViewDashboardHelper();
    this.moneySymbol = moneySymbol;
};

ViewDashboardModel.prototype.userDashboards = function(userId) {
    return this.helper.getJson('dashboard/ownerData/' + userId, this.baseUrl, this.oauthToken);
};

ViewDashboardModel.prototype.dashboardIndicators = function(dashboardId, initDate, endDate) {
    return this.helper.getJson('dashboard/' + dashboardId + '/indicator?dateIni=' + initDate + '&dateFin=' + endDate, this.baseUrl, this.oauthToken);
};

ViewDashboardModel.prototype.peiData = function(indicatorId, compareDate, measureDate) {
	var endPoint = "ReportingIndicators/process-efficiency-data?" +
				"indicator_uid=" + indicatorId + 
				"&compare_date=" + compareDate +
				"&measure_date=" + measureDate + 
				"&language=en";
    return this.helper.getJson(endPoint, this.baseUrl, this.oauthToken);
}

ViewDashboardModel.prototype.statusData = function() {
    var endPoint = "ReportingIndicators/status-indicator";
    return this.helper.getJson(endPoint, this.baseUrl, this.oauthToken);
}

ViewDashboardModel.prototype.peiDetailData = function(process, initDate, endDate) {
	var endPoint = "ReportingIndicators/process-tasks?" +
				"process_list=" + process + 
				"&init_date=" + initDate + 
				"&end_date=" + endDate +
				"&language=en";
    return this.helper.getJson(endPoint, this.baseUrl, this.oauthToken);
}

ViewDashboardModel.prototype.ueiData = function(indicatorId, compareDate, measureDate ) {
	var endPoint = "ReportingIndicators/employee-efficiency-data?" +
				"indicator_uid=" + indicatorId + 
				"&compare_date=" + compareDate +
				"&measure_date=" + measureDate + 
				"&language=en";
    return this.helper.getJson(endPoint, this.baseUrl, this.oauthToken);
}

ViewDashboardModel.prototype.ueiDetailData = function(groupId, initDate, endDate) {
	var endPoint = "ReportingIndicators/group-employee-data?" +
				"group_uid=" + groupId + 
				"&init_date=" + initDate + 
				"&end_date=" + endDate +
				"&language=en";
    return this.helper.getJson(endPoint, this.baseUrl, this.oauthToken);
}

ViewDashboardModel.prototype.generalIndicatorData = function(indicatorId, initDate, endDate) {
	var method = "";
	var endPoint = "ReportingIndicators/general-indicator-data?" +
				"indicator_uid=" + indicatorId + 
				"&init_date=" + initDate + 
				"&end_date=" + endDate +
				"&language=en";
    return this.helper.getJson(endPoint, this.baseUrl, this.oauthToken);
}

ViewDashboardModel.prototype.getPositionIndicator = function(callBack) {
	var that = this;
    this.helper.getJson('dashboard/config', that.baseUrl, that.oauthToken).done(function (r) {
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
                that.helper.putJson('dashboard/config', data, that.baseUrl, that.oauthToken);
            } else {
                that.helper.postJson('dashboard/config', data, that.baseUrl, that.oauthToken);
            }
        }
    );
};

