var TimeSeriesPresenter = function (model) {
	var that = this;
	this.helper = new ViewDashboardHelper();
    this.model = model;
};

TimeSeriesPresenter.prototype.initializePresenter = function (dashboardId) {
	var that = this;
	var requestFinished = $.Deferred();
	$.when (this.fillIndicatorList(dashboardId))
		.done(function () {
			that.periodicityState = {selValue: that.model.periodicityList()[0], 
										list: that.model.periodicityList(),
										label: that.model.label('ID_PERIODICITY') + ": "
									};

			that.initPeriodState = {selValue:that.model.monthList()[0].value, 
									list:that.model.monthList(),
									visible:true,
									label: that.model.label('ID_FROM') + ": "
									};

			that.initYearState = {selValue : that.model.yearList() [0].value,
									list : that.model.yearList(),
									label: that.model.label('ID_YEAR') + ": "
									};

			that.endPeriodState = {selValue : that.model.defaultEndDate().getMonth() + 1, 
									list : that.model.monthList(),
									visible:true,
									label: that.model.label('ID_TO') + ": "
									};

			that.endYearState = { selValue : that.model.yearList() [0].value,
									list : that.model.yearList(),
									label: that.model.label('ID_YEAR') + ": "
								};

			that.initDate = that.model.defaultInitDate();
			that.endDate = that.model.defaultEndDate();

			requestFinished.resolve(true);
	});
	return requestFinished.promise();
};

TimeSeriesPresenter.prototype.fillIndicatorList = function (dashboardId) {
	var requestFinished = $.Deferred();
	var that = this;
	var dummyDate = this.helper.date2MysqlString(new Date());
	that.indicatorList(dashboardId, dummyDate, dummyDate)
		.done(function(modelData){
			if (modelData== null || modelData.length == 0) {
				that.indicatorState = {selValue: null, 
										list: [],
										label: that.model.label('ID_INDICATOR') + ": "
				};
			}
			else {
				that.indicatorState = {selValue: modelData[0].value, 
										list: modelData,
										label: that.model.label('ID_INDICATOR') + ": "
										};
			}
			requestFinished.resolve(that.indicatorState);
	});
	return requestFinished.promise();
};

TimeSeriesPresenter.prototype.indicatorList = function (dashboardId) {
	var that = this;
	var requestFinished = $.Deferred();
	var dummyDate = this.helper.date2MysqlString(new Date());
	this.model.indicatorList(dashboardId, dummyDate, dummyDate).done(function (data) {
		var newArray = [];
		$.each(data, function(index, originalObject) {
			var newObject = {label: originalObject.DAS_IND_TITLE,
							 value: originalObject.DAS_IND_UID
							}
			newArray.push(newObject);
		});

		requestFinished.resolve(newArray);
	});
	return requestFinished.promise();
};

TimeSeriesPresenter.prototype.changePeriodicity = function (periodicity) {
	var that = this;
	var retval = this.monthList;

	switch (periodicity * 1) {
		case this.helper.ReportingPeriodicityEnum.MONTH:
			this.changePeriodicityToMonth(this.model.monthList());
			break;
		case this.helper.ReportingPeriodicityEnum.QUARTER:
			this.changePeriodicityToQuarter(this.model.quarterList());
			break;
		case this.helper.ReportingPeriodicityEnum.SEMESTER:
			this.changePeriodicityToSemester(this.model.semesterList());
			break;
		case this.helper.ReportingPeriodicityEnum.YEAR:
			this.changePeriodicityToYear(this.model.yearList());
			break;
		default:
			break;
	}
	return this;
}

TimeSeriesPresenter.prototype.changePeriodicityToMonth = function (monthList) {
	this.initPeriodState.list = monthList;
	this.endPeriodState.list = monthList;
	this.initPeriodState.visible = true;
	this.endPeriodState.visible = true;
}

TimeSeriesPresenter.prototype.changePeriodicityToQuarter = function (quarterList) {
	this.initPeriodState.list = quarterList;
	this.endPeriodState.list = quarterList;
	this.initPeriodState.visible = true;
	this.endPeriodState.visible = true;
}

TimeSeriesPresenter.prototype.changePeriodicityToSemester = function (semesterList) {
	this.initPeriodState.list = semesterList;
	this.endPeriodState.list = semesterList;
	this.initPeriodState.visible = true;
	this.endPeriodState.visible = true;
}

TimeSeriesPresenter.prototype.changePeriodicityToYear = function (yearList) {
	this.initPeriodState.list = [];
	this.endPeriodState.list = [];
	this.initPeriodState.visible = false;
	this.endPeriodState.visible = false;
}

TimeSeriesPresenter.prototype.historicData = function (indicator, periodicity, initPeriod, 
								initYear, endPeriod, endYear) {
	var that = this;
	var requestFinished = $.Deferred();
	var initDate = this.periodInitDate(periodicity, initPeriod, initYear);
	var endDate = this.periodEndDate(periodicity, endPeriod, endYear);
	this.model.historicData(indicator, periodicity, initDate, endDate).done(function (data) {
		var graphData = [];
		$.each(data, function(index, originalObject) {
			var newObject = {datalabel: that.periodValue(periodicity, originalObject) + '/' + originalObject['YEAR'],
							 value: originalObject.VALUE
							}
			graphData.push(newObject);
		});

		requestFinished.resolve(graphData);
	});
	return requestFinished.promise();
}


TimeSeriesPresenter.prototype.periodValue = function (periodicity, object) {
	var retval = "";
	switch (periodicity*1) {
		case this.helper.ReportingPeriodicityEnum.MONTH:
			retval = object.MONTH;
			break;
		case this.helper.ReportingPeriodicityEnum.QUARTER:
			retval = object.QUARTER;
			break;
		case this.helper.ReportingPeriodicityEnum.SEMESTER:
			retval = object.SEMESTER;
			break;
		case this.helper.ReportingPeriodicityEnum.YEAR:
			retval = object.YEAR;
			break;
	}
	if (retval == "") {
		throw new Error("The periodicity " + periodicity + " is not supported.");
	}
	return retval;
}

TimeSeriesPresenter.prototype.periodInitDate = function (periodicity, period, year) {
	var retval = null;
	switch (periodicity * 1) {
		case this.helper.ReportingPeriodicityEnum.MONTH:
			retval = new Date(year, period - 1, 1);
			break;
		case this.helper.ReportingPeriodicityEnum.QUARTER:
			retval = new Date(year, 3 * (period-1), 1);
			break;
		case this.helper.ReportingPeriodicityEnum.SEMESTER:
			retval = new Date(year, 6 * (period-1), 1);
			break;
		case this.helper.ReportingPeriodicityEnum.YEAR:
			retval = new Date(year, 0, 1);
			break;
	}
	if (retval == null) {
		throw new Error("The periodicity " + periodicity + " is not supported.");
	}
	return retval;
}

TimeSeriesPresenter.prototype.periodEndDate = function (periodicity, period, year) {
	var retval = null;
	switch (periodicity * 1) {
		case this.helper.ReportingPeriodicityEnum.MONTH:
			retval = new Date(year, period, 0);
			break;
		case this.helper.ReportingPeriodicityEnum.QUARTER:
			retval = new Date(year, 3 * (period), 0);
			break;
		case this.helper.ReportingPeriodicityEnum.SEMESTER:
			retval = new Date(year, 6 * (period), 0);
			break;
		case this.helper.ReportingPeriodicityEnum.YEAR:
			retval = new Date(year, 11, 31);
			break;
	}
	if (retval == null) {
		throw new Error("The periodicity " + periodicity + " is not supported.");
	}
	return retval;

}


