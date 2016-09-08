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
	var indicatorsAllowed = [1010, 1030];
	this.model.indicatorList(dashboardId, dummyDate, dummyDate).done(function (data) {
		var newArray = [];
		var list = data;
		$.each(list, function(index, originalObject) {
			if (indicatorsAllowed.indexOf(originalObject.DAS_IND_TYPE*1) >= 0) {
				var newObject = {label: originalObject.DAS_IND_TITLE,
								 value: originalObject.DAS_IND_UID
								}
				newArray.push(newObject);
			}
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
	this.endPeriodState.selValue = this.periodEquivalentFromDate (this.helper.ReportingPeriodicityEnum.MONTH, new Date());
}

TimeSeriesPresenter.prototype.changePeriodicityToQuarter = function (quarterList) {
	this.initPeriodState.list = quarterList;
	this.endPeriodState.list = quarterList;
	this.initPeriodState.visible = true;
	this.endPeriodState.visible = true;
	this.endPeriodState.selValue = this.periodEquivalentFromDate (this.helper.ReportingPeriodicityEnum.QUARTER, new Date());
}

TimeSeriesPresenter.prototype.changePeriodicityToSemester = function (semesterList) {
	this.initPeriodState.list = semesterList;
	this.endPeriodState.list = semesterList;
	this.initPeriodState.visible = true;
	this.endPeriodState.visible = true;
	this.endPeriodState.selValue = this.periodEquivalentFromDate (this.helper.ReportingPeriodicityEnum.SEMESTER, new Date());
}

TimeSeriesPresenter.prototype.changePeriodicityToYear = function (yearList) {
	this.initPeriodState.list = [];
	this.endPeriodState.list = [];
	this.initPeriodState.visible = false;
	this.endPeriodState.visible = false;
	this.endPeriodState.selValue = this.periodEquivalentFromDate (this.helper.ReportingPeriodicityEnum.YEAR, new Date());
}

TimeSeriesPresenter.prototype.historicData = function (indicator, periodicity, initPeriod, 
								initYear, endPeriod, endYear) {
	var that = this;
	var requestFinished = $.Deferred();
	var initDate = this.helper.periodInitDate(periodicity, initPeriod, initYear);
	var endDate = this.helper.periodEndDate(periodicity, endPeriod, endYear);
	this.model.historicData(indicator, periodicity, initDate, endDate).done(function (data) {
		var graphData = [];
		var list = data;
		$.each(list, function(index, originalObject) {
			var newObject = {datalabel: that.periodColumnName(periodicity, originalObject) + '/' + originalObject['YEAR'],
							 value: originalObject.VALUE,
							 period: that.periodColumnName(periodicity, originalObject),
							 year: originalObject.YEAR
							}
			graphData.push(newObject);
		});

		for (var y = initYear; y <= endYear; y++) {
			var periodRunFrom = (y == initYear) ? initPeriod : 1;
			var periodRunTo = (y == endYear) ? endPeriod : that.periodsInAYear(periodicity);
			for (var p = periodRunFrom; p <= periodRunTo; p++) {
				var results = $.grep(graphData, 
							function(obj) { 
								return (obj.year == y && obj.period == p);
							});
				if (results.length == 0) {
					var newObject = {	datalabel: p + '/' + y,
										value: 0,
										period: p,
										year: y
									};
					graphData.push(newObject);
				}
			}
		}
		graphData = graphData.sort(function (a, b) {
						return (a.year * 10 + a.period * 1) - (b.year * 10 + b.period * 1);
					});
		requestFinished.resolve(graphData);
	});
	return requestFinished.promise();
}

TimeSeriesPresenter.prototype.periodsInAYear = function (periodicity) {
	var retval = "";
	switch (periodicity * 1) {
		case this.helper.ReportingPeriodicityEnum.MONTH:
			retval = 12
			break;
		case this.helper.ReportingPeriodicityEnum.QUARTER:
			retval = 4;
			break;
		case this.helper.ReportingPeriodicityEnum.SEMESTER:
			retval = 2;
			break;
		case this.helper.ReportingPeriodicityEnum.YEAR:
			retval = 1;
			break;
	}
	if (retval == "") {
		throw new Error("The periodicity " + periodicity + " is not supported.");
	}
	return retval;
}


TimeSeriesPresenter.prototype.periodColumnName = function (periodicity, object) {
	var retval = "";
	switch (periodicity * 1) {
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

TimeSeriesPresenter.prototype.periodEquivalentFromDate = function (periodicity, date) {
	var retval = null;
	var year = date.getFullYear();

	switch (periodicity * 1) {
		case this.helper.ReportingPeriodicityEnum.MONTH:
			for (var i = 1; i < 12; i++) {
				var periodInitDate = this.helper.periodInitDate (periodicity, i, year);
				var periodEndDate = this.helper.periodEndDate (periodicity, i, year);
				if (periodInitDate <= date && periodEndDate >= date) {
					retval = i;
				}
			}
			break;
		case this.helper.ReportingPeriodicityEnum.QUARTER:
			for (var i = 1; i < 4; i++) {
				var periodInitDate = this.helper.periodInitDate (periodicity, i, year);
				var periodEndDate = this.helper.periodEndDate (periodicity, i, year);
				if (periodInitDate <= date && periodEndDate >= date) {
					retval = i;
				}
			}
			break;
		case this.helper.ReportingPeriodicityEnum.SEMESTER:
			for (var i = 1; i < 2; i++) {
				var periodInitDate = this.helper.periodInitDate (periodicity, i, year);
				var periodEndDate = this.helper.periodEndDate (periodicity, i, year);
				if (periodInitDate <= date && periodEndDate >= date) {
					retval = i;
				}
			}
			break;
		case this.helper.ReportingPeriodicityEnum.YEAR:
			retval = year
			break;
	}
	if (retval == null) {
		throw new Error("The periodicity " + periodicity + " is not supported.");
	}
	return retval;
}

