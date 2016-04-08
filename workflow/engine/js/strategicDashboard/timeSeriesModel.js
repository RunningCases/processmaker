var TimeSeriesModel = function (oauthToken, server, workspace, userId, strings) {
    this.server = server;
    this.workspace = workspace;
    this.baseUrl = server;
    this.oauthToken = oauthToken;
	this.helper = new ViewDashboardHelper();
	this.cache = {};
	this.forceRemote=false; //if true, the next call will go to the remote server
	this.userId = userId;
	this.strings = strings;

};

TimeSeriesModel.prototype.label = function(id) {
    return this.strings[id];
};

TimeSeriesModel.prototype.indicatorList = function(dashboardId,initDate, endDate) {
	var dummyDate = ''
    return this.helper.getJson('dashboard/' + dashboardId + '/indicator?dateIni=' + initDate + '&dateFin=' + endDate, this.baseUrl, this.oauthToken);
};

TimeSeriesModel.prototype.periodicityList = function() {
	var that = this;
	var json = [{label:that.label('ID_MONTH'), value:that.helper.ReportingPeriodicityEnum.MONTH},
					{label:that.label('ID_QUARTER'), value:that.helper.ReportingPeriodicityEnum.QUARTER},
					{label:that.label('ID_SEMESTER'), value:that.helper.ReportingPeriodicityEnum.SEMESTER},
					{label:that.label('ID_YEAR'), value:that.helper.ReportingPeriodicityEnum.YEAR}
				 ];
	return json;
};

TimeSeriesModel.prototype.monthList = function() {
	var that = this;
	var json = [{label:that.label("ID_MONTH_ABB_1"), value:"1"}, 
				{label:that.label("ID_MONTH_ABB_2"), value:"2"}, 
				{label:that.label("ID_MONTH_ABB_3"), value:"3"}, 
				{label:that.label("ID_MONTH_ABB_4"), value:"4"},
				{label:that.label("ID_MONTH_ABB_5"), value:"5"},
				{label:that.label("ID_MONTH_ABB_6"), value:"6"},
				{label:that.label("ID_MONTH_ABB_7"), value:"7"},
				{label:that.label("ID_MONTH_ABB_8"), value:"8"},
				{label:that.label("ID_MONTH_ABB_9"), value:"9"},
				{label:that.label("ID_MONTH_ABB_10"), value:"10"},
				{label:that.label("ID_MONTH_ABB_11"), value:"11"},
				{label:that.label("ID_MONTH_ABB_12"), value:"12"}
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
	var currentYear = this.defaultEndDate().getFullYear();
	var json = [];

	for (var i = currentYear; i > currentYear - 10; i--) {
		json.push ({label:i, value : i});
	}
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
    return this.helper.getJson(endPoint, this.baseUrl, this.oauthToken);
};



