

var ViewDashboardPresenter = function (model) {
	this.helper = new ViewDashboardHelper();
	this.helper.assert(model != null, "A model must be passed for the presenter work.")
    this.model = model;
};

ViewDashboardPresenter.prototype.getUserDashboards = function (userId) {
	var that = this;
	var requestFinished = $.Deferred();
	this.model.userDashboards(userId)
			.done(function(modelData){
			   	var viewModel = that.userDashboardsViewModel(modelData)
				requestFinished.resolve(viewModel);
			});
	return requestFinished.promise();
};

ViewDashboardPresenter.prototype.userDashboardsViewModel = function(data) {
	var that = this;
	//if null data is returned we default to an empty array
	if (data == null) { data = []; }
	var returnList = [];
	$.each(data, function(index, originalObject) {
		var map = {
			"DAS_TITLE" : "title",
			"DAS_UID" : "id",
			"DAS_FAVORITE" : "isFavorite"
		};
		var newObject = that.helper.merge(originalObject, {}, map);
		returnList.push(newObject);
	});
	return returnList;
};

ViewDashboardPresenter.prototype.getDashboardIndicators = function (dashboardId,initDate, endDate) {
	if (dashboardId == null) {throw new Error ("getDashboardIndicators -> dashboardId can't be null");};
	var that = this;
	var requestFinished = $.Deferred();
	this.model.dashboardIndicators (dashboardId,  initDate, endDate)
		.done(function (modelData) {
			var viewModel = that.dashboardIndicatorsViewModel(modelData)
			requestFinished.resolve(viewModel);
		});
	return requestFinished.promise();
};

ViewDashboardPresenter.prototype.dashboardIndicatorsViewModel = function(data) {
	var that = this;
	var returnList = [];
	var i = 1;
	$.each(data, function(index, originalObject) {
		var map = {
			"DAS_IND_UID" : "id",
			"DAS_IND_TITLE" : "title",
			"DAS_IND_TYPE" : "type",
			"DAS_IND_VARIATION" : "comparative",
			"DAS_IND_DIRECTION" : "direction",
			"DAS_IND_VALUE" : "value",
			"DAS_IND_X" : "x",
			"DAS_IND_Y" : "y",
			"DAS_IND_WIDTH" : "width",
			"DAS_IND_HEIGHT" : "height",
			"DAS_UID_PROCESS" : "process",
			"PERCENTAGE_OVERDUE" : "percentageOverdue",
			"PERCENTAGE_AT_RISK" : "percentageAtRisk",
			"PERCENTAGE_ON_TIME" : "percentageOnTime"
		};

		var newObject = that.helper.merge(originalObject, {}, map);
		newObject.toDrawX =  newObject.x;
		//newObject.toDrawX =  (newObject.x == 0) ? 12 - 12/i : newObject.x;

		newObject.toDrawY = (newObject.y == 0) ? 6 : newObject.y;
		newObject.toDrawHeight = (newObject.y == 0) ? 2 : newObject.height;
		newObject.toDrawWidth = (newObject.y == 0) ? 12 / data.length : newObject.width;
		newObject.comparative = ((newObject.comparative > 0)? "+": "") +  that.helper.stringIfNull(newObject.comparative);
		newObject.directionSymbol = (newObject.direction == "1") ? "<" : ">";
		newObject.isWellDone = (newObject.direction == "1") 
								? parseFloat(newObject.value) <= parseFloat(newObject.comparative)
								: parseFloat(newObject.value) >= parseFloat(newObject.comparative);
        
		newObject.category = (newObject.type == "1010" || newObject.type == "1030")
									? "special"
									: "normal";

		//round goals for normal indicators
		newObject.comparative = (newObject.category == "normal")
								? Math.round(newObject.comparative) + ""
								: newObject.comparative;

		newObject.value = (newObject.category == "normal")
								? Math.round(newObject.value) + ""
								: Math.round(newObject.value*100)/100 + ""

		newObject.favorite = 0;
		newObject.percentageOverdue = Math.round(newObject.percentageOverdue);
		newObject.percentageAtRisk = Math.round(newObject.percentageAtRisk);
		//to be sure that percentages sum up to 100 (the rounding will lost decimals)%
		newObject.percentageOnTime = 100 - newObject.percentageOverdue - newObject.percentageAtRisk;
		newObject.overdueVisibility = (newObject.percentageOverdue > 0)? "visible" : "hidden";
		newObject.atRiskVisiblity = (newObject.percentageAtRisk > 0)? "visible" : "hidden";
		newObject.onTimeVisibility = (newObject.percentageOnTime > 0)? "visible" : "hidden";
		returnList.push(newObject);
		i++;
	});

	//sort the array for drawing in toDrawX order
	returnList.sort(function (a, b) {
		return ((a.toDrawX <= b.toDrawX) ? -1 : 1);
	});
	if (returnList.length > 0)  {
		returnList[0].favorite = 1;
	}
	return returnList;
};

/*++++++++ FIRST LEVEL INDICATOR DATA +++++++++++++*/
ViewDashboardPresenter.prototype.getIndicatorData = function (indicatorId, indicatorType, initDate, endDate) {
	var that = this;
	var requestFinished = $.Deferred();
	switch (indicatorType) {
		case "1010":
			this.model.peiData(indicatorId, initDate, endDate)
				.done(function(modelData) {
						var viewModel = that.peiViewModel(modelData)
						requestFinished.resolve(viewModel);
				});
			break;
		case "1030":
			this.model.ueiData(indicatorId, initDate, endDate)
				.done(function(modelData) {
						var viewModel = that.ueiViewModel(modelData)
						requestFinished.resolve(viewModel);
				});
			break;
		case "1050":
			this.model.statusData(indicatorId)
				.done(function(modelData) {
					var viewModel = that.statusViewModel(indicatorId, modelData)
					requestFinished.resolve(viewModel);
				});
			break;
		default:
			this.model.generalIndicatorData(indicatorId, initDate, endDate)
				.done(function(modelData) {
						var viewModel = that.indicatorViewModel(modelData)
						requestFinished.resolve(viewModel);
				});
			break;
	}
	return requestFinished.promise();
};

ViewDashboardPresenter.prototype.peiViewModel = function(data) {
	var that = this;
	var graphData = [];
	$.each(data.data, function(index, originalObject) {
		var map = {
			"name" : "datalabel",
			"efficiencyIndex" : "value"
		};
		var newObject = that.helper.merge(originalObject, {}, map);
		var shortLabel = (newObject.datalabel == null) 
									? "" 
									: newObject.datalabel.substring(0,15);

		newObject.datalabel = shortLabel;
		graphData.push(newObject);
		originalObject.inefficiencyCostToShow = Math.round(originalObject.inefficiencyCost);
		originalObject.efficiencyIndexToShow = Math.round(originalObject.efficiencyIndex * 100) / 100;
		originalObject.indicatorId = data.id;
		originalObject.json = JSON.stringify(originalObject);
	});

	var retval = {};
	//TODO selecte de 7 worst cases no the first 7
	retval = data;
	retval.dataToDraw = graphData.splice(0,7);
	//TODO aumentar el símbolo de moneda $
	retval.inefficiencyCostToShow = Math.round(retval.inefficiencyCost);
	retval.efficiencyIndexToShow = Math.round(retval.efficiencyIndex * 100) / 100;
	return retval;
};

ViewDashboardPresenter.prototype.ueiViewModel = function(data) {
	var that = this;
	var graphData = [];
	$.each(data.data, function(index, originalObject) {
		var map = {
			"name" : "datalabel",
			"averageTime" : "value",
			"deviationTime" : "dispersion"
		};
		var newObject = that.helper.merge(originalObject, {}, map);
		var shortLabel = (newObject.datalabel == null) 
									? "" 
									: newObject.datalabel.substring(0,7);

		newObject.datalabel = shortLabel;
		graphData.push(newObject);
		originalObject.inefficiencyCostToShow = Math.round(originalObject.inefficiencyCost);
		originalObject.efficiencyIndexToShow = Math.round(originalObject.efficiencyIndex * 100) / 100;
		originalObject.indicatorId = data.id;
		originalObject.json = JSON.stringify(originalObject);
	});

	var retval = {};
	//TODO selecte de 7 worst cases no the first 7
	retval = data;
	retval.dataToDraw = graphData.splice(0,7);
	//TODO aumentar el símbolo de moneda $
	retval.inefficiencyCostToShow = Math.round(retval.inefficiencyCost);
	retval.efficiencyIndexToShow = Math.round(retval.efficiencyIndex * 100) / 100;
	return retval;
};

ViewDashboardPresenter.prototype.statusViewModel = function(indicatorId, data) {
	var that = this;
	data.id = indicatorId;
	var graph1Data = [];
	var graph2Data = [];
	var graph3Data = [];
	$.each(data.dataList, function(index, originalObject) {
		var title = (originalObject.taskTitle == null)
                                ? ""
                                : originalObject.taskTitle.substring(0,15);
		var newObject1 = {
			datalabel : title,
			value : originalObject.percentageTotalOverdue
		};
		var newObject2 = {
			datalabel : title,
			value : originalObject.percentageTotalAtRisk
		};
		var newObject3 = {
			datalabel : title,
			value : originalObject.percentageTotalOnTime
		};

		graph1Data.push(newObject1);
		graph2Data.push(newObject2);
		graph3Data.push(newObject3);
		//we add the indicator id for reference
		originalObject.indicatorId = indicatorId;
	});

	var retval = data;
	//TODO selecte de 7 worst cases no the first 7
	retval.graph1Data = graph1Data.splice(0,7)
	retval.graph2Data = graph2Data.splice(0,7)
	retval.graph3Data = graph3Data.splice(0,7)
	
	return retval;
};

ViewDashboardPresenter.prototype.indicatorViewModel = function(data) {
	var that = this;
	$.each(data.graph1Data, function(index, originalObject) {
		var label = (('YEAR' in originalObject) ? originalObject.YEAR : "") ;
		label += (('MONTH' in originalObject) ? "/" + originalObject.MONTH : "") ;
		label += (('QUARTER' in originalObject) ?  "/" + originalObject.QUARTER : "");
		label += (('SEMESTER' in originalObject) ?  "/" + originalObject.SEMESTER : "");
		originalObject.datalabel = label;
	});

	$.each(data.graph2Data, function(index, originalObject) {
		var label = (('YEAR' in originalObject) ? originalObject.YEAR : "") ;
		label += (('MONTH' in originalObject) ? "/" + originalObject.MONTH : "") ;
		label += (('QUARTER' in originalObject) ?  "/" + originalObject.QUARTER : "");
		label += (('SEMESTER' in originalObject) ?  "/" + originalObject.SEMESTER : "") ;
		originalObject.datalabel = label;
	});
	return data;
};
/*-------FIRST LEVEL INDICATOR DATA */

/*++++++++ SECOND LEVEL INDICATOR DATA +++++++++++++*/
ViewDashboardPresenter.prototype.getSpecialIndicatorSecondLevel = function (entityId, indicatorType, initDate, endDate) {
	var that = this;
	var requestFinished = $.Deferred();
	//entityid is the process id or group id
	switch (indicatorType) {
		case "1010":
			this.model.peiDetailData(entityId, initDate, endDate)
				.done(function (modelData) {
					var viewModel = that.returnIndicatorSecondLevelPei(modelData);
					requestFinished.resolve(viewModel);
				});
			break;
		case "1030":
			this.model.ueiDetailData(entityId, initDate, endDate)
				.done(function (modelData) {
					var viewModel = that.returnIndicatorSecondLevelUei(modelData);
					requestFinished.resolve(viewModel);
				});
			break;
		default:
			throw new Error("Indicator type " + indicatorType + " has not detail data implemented of special indicator kind.");
	}
	return requestFinished.promise();
};

ViewDashboardPresenter.prototype.returnIndicatorSecondLevelPei = function(modelData) {
	//modelData arrives in format [{users/tasks}]
	//returns object {dataToDraw[], entityData[] //user/tasks data}
	var that = this;
	var graphData = [];
	$.each(modelData, function(index, originalObject) {
		var map = {
			"name" : "datalabel",
			"averageTime" : "value",
			"deviationTime" : "dispersion"
		};
		var newObject = that.helper.merge(originalObject, {}, map);
		newObject.datalabel = ((newObject.datalabel == null) ? "" : newObject.datalabel.substring(0, 7));
		originalObject.inefficiencyCostToShow = Math.round(originalObject.inefficiencyCost);
		originalObject.efficiencyIndexToShow = Math.round(originalObject.efficiencyIndex * 100) / 100;
		graphData.push(newObject);
	});
	var retval = {};
	retval.dataToDraw = graphData.splice(0,7);
	retval.entityData = modelData;
	return retval;
};

ViewDashboardPresenter.prototype.returnIndicatorSecondLevelUei = function(modelData) {
	//modelData arrives in format [{users/tasks}]
	//returns object {dataToDraw[], entityData[] //user/tasks data}
	var that = this;
	var graphData = [];
	$.each(modelData, function(index, originalObject) {
		var map = {
			"name" : "datalabel",
			"averageTime" : "value",
			"deviationTime" : "dispersion"
		};
		var newObject = that.helper.merge(originalObject, {}, map);
		newObject.datalabel = ((newObject.datalabel == null) ? "" : newObject.datalabel.substring(0, 7));
		originalObject.inefficiencyCostToShow = Math.round(originalObject.inefficiencyCost);
		originalObject.efficiencyIndexToShow = Math.round(originalObject.efficiencyIndex * 100) / 100;
		graphData.push(newObject);
	});
	var retval = {};
	retval.dataToDraw = graphData.splice(0,7);
	retval.entityData = modelData;
	return retval;
};
/*-------SECOND LEVEL INDICATOR DATA*/




 




