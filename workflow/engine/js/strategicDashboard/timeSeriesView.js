
helper = new ViewDashboardHelper();
var ws = urlProxy.split('/');
var dashboardId = '3290922985542460a19e7c1021519011';
tsModel = new TimeSeriesModel(token, urlProxy, ws[3], pageUserId, dashboardId);
tsPresenter = new TimeSeriesPresenter(tsModel);

$(document).ready(function() {


	$('#periodicityList').change(function(){
		var id = $(this).val();
		tsPresenter.changePeriodicity(id);
		bindTimeSeriesLists(tsPresenter, ["indicatorList", "periodicityList"]);
	});

	$('#compareButton').click(function(){
		tsPresenter.historicData(
			$('#indicatorList').val(),
			$('#periodicityList').val(),
			$('#initPeriodList').val(),
			$('#initYearList').val(),
			$('#endPeriodList').val(),
			$('#endYearList').val()
			).done(function(data) {
				var graphParams1 = {
						canvas : {
							containerId:'compareGraph',
							width:300,
							height:300,
							stretch:true,
							noDataText: G_STRING.ID_DISPLAY_EMPTY
						},
						graph: {
								allowTransition: false,
								allowDrillDown: false,
								showTip: true,
								allowZoom: false,
								useShadows: false,
								gridLinesX: true,
								gridLinesY: true,
								area: {visible: false, css:"area"},
								axisX:{ showAxis: true, label: "Period" },
								axisY:{ showAxis: true, label: "Efficiency" },
								showErrorBars: false
							}
					};

				var graph1 = new LineChart(data, graphParams1, null, null);
				graph1.drawChart();
				$('#indicatorsView').hide();
			});
	});
});


var bindTimeSeriesLists = function (presenter, elementsToConserve) {
	var conserveStates =[];
    elementsToConserve =[];
	$.each (elementsToConserve, function (i, elem){
			conserveStates.push({id:elem, selValue: $('#' + elem).val()});
			});
	helper.fillSelectWithOptions ($('#indicatorList'), presenter.indicatorState.list, presenter.indicatorState.selValue);
	helper.fillSelectWithOptions ($('#periodicityList'), presenter.periodicityState.list, presenter.periodicityState.selValue);
	helper.fillSelectWithOptions ($('#initPeriodList'), presenter.initPeriodState.list, presenter.initPeriodState.selValue);
	helper.fillSelectWithOptions ($('#initYearList'), presenter.initYearState.list, presenter.initYearState.selValue);
	helper.fillSelectWithOptions ($('#endPeriodList'), presenter.endPeriodState.list, presenter.endPeriodState.selValue);
	helper.fillSelectWithOptions ($('#endYearList'), presenter.endYearState.list, presenter.endYearState.selValue);

	$.each (conserveStates, function (i, item){
			$('#' + item.id).val(item.selValue);
			});

	helper.setVisibility ($('#initPeriodList'), presenter.initPeriodState.visible);
	helper.setVisibility ($('#endPeriodList'), presenter.endPeriodState.visible);
}






