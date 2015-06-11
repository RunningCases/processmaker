var TimeSeriesModel = function (oauthToken, server, workspace, userId, dashboardId) {
    this.server = server;
    this.workspace = workspace;
    this.baseUrl = "/api/1.0/" + workspace + "/";
    this.oauthToken = oauthToken;
	this.helper = new ViewDashboardHelper();
	this.cache = {};
	this.forceRemote=false; //if true, the next call will go to the remote server
	this.userId = userId;
	this.dashboardId = dashboardId;

};


TimeSeriesModel.prototype.indicatorList = function(dashboardId,initDate, endDate) {
	var dummyDate = ''
    return this.getJson('dashboard/' + dashboardId + '/indicator?dateIni=' + initDate + '&dateFin=' + endDate);
};


/*TimeSeriesModel.prototype.indicatorList = function() {
	var requestFinished = $.Deferred();
	var json = [ {"label":"PEI", "value":"1111"}, 
				 {"label":"EEI", "value":"2222"}
				];
	requestFinished.resolve(json);
	return requestFinished.promise();
};*/

TimeSeriesModel.prototype.periodicityList = function() {
	var that = this;
	var json = [{label:"Monthly", value:that.helper.ReportingPeriodicityEnum.MONTH},
					{label:"Quaterly", value:that.helper.ReportingPeriodicityEnum.QUARTER},
					{label:"Semester", value:that.helper.ReportingPeriodicityEnum.SEMESTER},
					{label:"Yearly", value:that.helper.ReportingPeriodicityEnum.YEAR}
				 ];
	return json;
};

TimeSeriesModel.prototype.monthList = function() {
	var json = [{label:"Jan", value:"1"}, 
								{label:"Feb", value:"2"}, 
								{label:"Mar", value:"3"}, 
								{label:"Apr", value:"4"},
								{label:"May", value:"5"},
								{label:"Jun", value:"6"},
								{label:"Jul", value:"7"}
								 ];
	return json;
};

TimeSeriesModel.prototype.quarterList = function() {
	var json = [{label:"1", value:"1"}, 
				{label:"2", value:"2"}, 
				{label:"3", value:"3"}, 
				{label:"4", value:"4"}];
	return json;
};

TimeSeriesModel.prototype.semesterList = function() {
	var json = [{label:"1", value:"1"}, {label:"2", value:"2"}];
	return json;
};

TimeSeriesModel.prototype.yearList = function() {
	var json = [{label:"2015", value:"2015"}, {label:"2014", value:"2014"}];
	return json;
};

TimeSeriesModel.prototype.defaultInitDate = function() {
	return new Date(new Date().getFullYear(), 0, 1);
};

TimeSeriesModel.prototype.defaultEndDate = function() {
	return new Date();
};

TimeSeriesModel.prototype.historicData = function(indicatorId, periodicity, initDate, endDate) {
	var endPoint = "ReportingIndicators/indicator-historic-data?" +
				"indicator_uid=" + indicatorId + 
				"&init_date=" + this.helper.date2MysqlString(initDate) +
				"&end_date=" + this.helper.date2MysqlString(endDate) + 
				"&periodicity=" + periodicity + 
				"&language=en";
    return this.getJson(endPoint);

};

TimeSeriesModel.prototype.getJson = function (endPoint) {
    var that = this;
    var callUrl = this.baseUrl + endPoint
	var requestFinished = $.Deferred();

	return $.ajax({
		url: callUrl,
		type: 'GET',
		datatype: 'json',
		success: function (data) {
			that.forceRemote = false;
			requestFinished.resolve(data);
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



