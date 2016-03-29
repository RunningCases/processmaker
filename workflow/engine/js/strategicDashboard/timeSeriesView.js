
helper = new ViewDashboardHelper();
tsModel = new TimeSeriesModel(token, urlProxy, workspace, pageUserId, G_STRING);
tsPresenter = new TimeSeriesPresenter(tsModel);

$(document).ready(function() {
	$('#indicatorsView').show();
	$('#compareDiv').hide();

	$('#periodicityList').change(function(){
		var id = $(this).val();
		tsPresenter.changePeriodicity(id);
		bindTimeSeriesLists(tsPresenter, ["indicatorList", "periodicityList"]);
	});

	$('#compareButton').click(function(){
		$('#comparisonBreadcrumb').find('li').remove()
		$('#comparisonBreadcrumb')
					.append ('<li><a class="bread-back-selector2" href="#"><i class="fa fa-chevron-left fa-fw"></i>Return to the Indicator\'s View</a>');
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
				$('#indicatorsView').hide();
				$('#scrollImg').hide();
				$('#compareDiv').show();
				var graph1 = new LineChart(data, graphParams1, null, null);
				graph1.drawChart();
			});
	});

	$('body').on('click','.bread-back-selector2', function() {
		$('#indicatorsView').show();
		$('#scrollImg').show();
		$('#compareDiv').hide();
	});
});

var bindTimeSeriesLists = function (presenter, elementsToConserve) {
	var conserveStates =[];

	if (elementsToConserve === null || elementsToConserve === undefined) {
		elementsToConserve =[];
	}

	$.each (elementsToConserve, function (i, elem){
			conserveStates.push({id:elem, selValue: $('#' + elem).val()});
	});
	helper.fillSelectWithOptions ($('#indicatorList'), presenter.indicatorState.list, presenter.indicatorState.selValue);
	helper.fillSelectWithOptions ($('#periodicityList'), presenter.periodicityState.list, presenter.periodicityState.selValue);
	helper.fillSelectWithOptions ($('#initPeriodList'), presenter.initPeriodState.list, presenter.initPeriodState.selValue);
	helper.fillSelectWithOptions ($('#initYearList'), presenter.initYearState.list, presenter.initYearState.selValue);
	helper.fillSelectWithOptions ($('#endPeriodList'), presenter.endPeriodState.list, presenter.endPeriodState.selValue);
	helper.fillSelectWithOptions ($('#endYearList'), presenter.endYearState.list, presenter.endYearState.selValue);

	$('#indicatorLabel').text(presenter.indicatorState.label);
	$('#periodicityLabel').text(presenter.periodicityState.label);
	$('#initPeriodLabel').text(presenter.initPeriodState.label);
	$('#endPeriodLabel').text(presenter.endPeriodState.label);


	$.each (conserveStates, function (i, item){
			$('#' + item.id).val(item.selValue);
	});

	helper.setVisibility ($('#initPeriodList'), presenter.initPeriodState.visible);
	helper.setVisibility ($('#endPeriodList'), presenter.endPeriodState.visible);
}






