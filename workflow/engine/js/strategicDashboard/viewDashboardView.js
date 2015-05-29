/**************************************************************/
var WidgetBuilder = function () {
	this.helper = new ViewDashboardHelper();
}

WidgetBuilder.prototype.getIndicatorWidget = function (indicator) {
	var retval = null;
	switch(indicator.type) {
		case "1010": retval = this.buildSpecialIndicatorButton(indicator); break;
		case "1030": retval = this.buildSpecialIndicatorButton(indicator); break;
		case "1050": retval = this.buildStatusIndicatorButton(indicator); break;
		case "1020": 
		case "1040": 
		case "1060": 
		case "1070": 
		case "1080": 
					 retval = this.buildIndicatorButton(indicator); break;
	}
	if(retval == null) {throw new Error(indicator.type + " has not associated a widget.");}
	return retval;
};

WidgetBuilder.prototype.buildSpecialIndicatorButton = function (indicator) {
	_.templateSettings.variable = "indicator";
	var template = _.template ($("script.specialIndicatorButtonTemplate").html());
	var $retval =  $(template(indicator));
	
	if(indicator.comparative < 0){
		$retval.find(".ind-container-selector").removeClass("panel-green").addClass("panel-red");
		$retval.find(".ind-symbol-selector").removeClass("fa-chevron-up").addClass("fa-chevron-down");
	}

	if(indicator.comparative > 0){
		$retval.find(".ind-container-selector").removeClass("panel-red").addClass("panel-green");
		$retval.find(".ind-symbol-selector").removeClass("fa-chevron-down").addClass("fa-chevron-up");
	}

	if(indicator.comparative == 0){
		$retval.find(".ind-symbol-selector").removeClass("fa-chevron-up");
		$retval.find(".ind-symbol-selector").removeClass("fa-chevron-down");
		$retval.find(".ind-symbol-selector").addClass("fa-circle-o");
		$retval.find(".ind-container-selector").removeClass("panel-red").addClass("panel-green");
	}
	return $retval;
}

WidgetBuilder.prototype.buildStatusIndicatorButton = function (indicator) {
	_.templateSettings.variable = "indicator";
	var template = _.template ($("script.statusIndicatorButtonTemplate").html());
	var $retval =  $(template(indicator));
	return $retval;
}

WidgetBuilder.prototype.buildIndicatorButton = function (indicator) {
	_.templateSettings.variable = "indicator";
	var template = _.template ($("script.statusIndicatorButtonTemplate").html());
	var $retval =  $(template(indicator));
	var $comparative = $retval.find('.ind-comparative-selector');
	var $title = $retval.find('.ind-title-selector');
	if (indicator.isWellDone) {
		$comparative.text("(" + indicator.directionSymbol + " " + indicator.comparative + "%)-"+ G_STRING.ID_WELL_DONE);
		$retval.find(".ind-container-selector").removeClass("panel-low").addClass("panel-high");
	}
	else {
		$comparative.text("Goal: " + indicator.directionSymbol + " " + indicator.comparative + "%");
		$retval.find(".ind-container-selector").removeClass("panel-high").addClass("panel-low");
	}
	return $retval;
}

WidgetBuilder.prototype.buildSpecialIndicatorFirstView = function (indicatorData) {
	if (indicatorData == null) { throw new Error ("indicatorData is null."); }	
	if (!indicatorData.hasOwnProperty("id")) { throw new Error ("indicatorData has no id."); }	

	_.templateSettings.variable = "indicator";
	var template = _.template ($("script.specialIndicatorMainPanel").html());
	var $retval =  $(template(indicatorData));
	var indicatorPrincipalData = this.getIndicatorLoadedById(indicatorData.id)
	$retval.find('.breadcrumb').find('li').remove()
	$retval.find('.breadcrumb').append ('<li><b>'+indicatorPrincipalData.title+'</b></li>')
	$retval.find(".sind-index-selector").text(G_STRING.ID_EFFICIENCY_INDEX);
	$retval.find(".sind-cost-selector").text(G_STRING.ID_INEFFICIENCY_COST);
	this.setColorForInefficiency($retval.find(".sind-cost-number-selector"), indicatorData);
	return $retval;
}

WidgetBuilder.prototype.buildSpecialIndicatorFirstViewDetail = function (oneItemDetail) {
	//detailData =  {indicatorId, uid, name, averateTime...}
	if (oneItemDetail == null){throw new Error("oneItemDetail is null ");}
	if (!typeof(oneItemDetail) === 'object'){throw new Error( "detailData is not and object ->" + oneItemDetail);}
	if (!oneItemDetail.hasOwnProperty("name")){throw new Error("buildSpecialIndicatorFirstViewDetail -> detailData has not the name param. Has it the correct Type? ->" + oneItemDetail);}

	_.templateSettings.variable = "detailData";
	var template = _.template ($("script.specialIndicatorDetail").html());
	var $retval =  $(template(oneItemDetail));
	$retval.find(".detail-efficiency-selector").text(G_STRING.ID_EFFICIENCY_INDEX);
	$retval.find(".detail-cost-selector").text(G_STRING.ID_INEFFICIENCY_COST);
	this.setColorForInefficiency($retval.find(".detail-cost-number-selector"), oneItemDetail);
	return $retval;
}

WidgetBuilder.prototype.buildStatusIndicatorFirstView = function (indicatorData) {
	if (indicatorData == null) { throw new Error ("indicatorData is null."); }
	if (!indicatorData.hasOwnProperty("id")) { throw new Error ("indicatorData has no id."); }

	_.templateSettings.variable = "indicator";
	var template = _.template ($("script.statusIndicatorMainPanel").html());
	var $retval =  $(template(indicatorData));
	var indicatorPrincipalData = this.getIndicatorLoadedById(indicatorData.id)
	$retval.find('.breadcrumb').find('li').remove()
	$retval.find('.breadcrumb').append ('<li><b>'+indicatorPrincipalData.title+'</b></li>')
	return $retval;
}

WidgetBuilder.prototype.buildStatusIndicatorFirstViewDetail = function (oneItemDetail) {
	//detailData =  {indicatorId, uid, name, averateTime...}
	if (oneItemDetail == null){throw new Error("oneItemDetail is null ");}
	if (!typeof(oneItemDetail) === 'object'){throw new Error( "detailData is not and object ->" + oneItemDetail);}
	if (!oneItemDetail.hasOwnProperty("taskTitle")){throw new Error("detailData has not the name param. Has it the correct Type? ->" + oneItemDetail);}

	_.templateSettings.variable = "detailData";
	var template = _.template ($("script.statusDetail").html());
	var $retval =  $(template(oneItemDetail));
	return $retval;
}

WidgetBuilder.prototype.buildSpecialIndicatorSecondView = function (secondViewData) {
	//presenterData= object {dataToDraw[], entityData[] //user/tasks data}
	_.templateSettings.variable = "indicator";
	var template = _.template ($("script.specialIndicatorMainPanel").html());
	var $retval =  $(template(window.currentEntityData));
	//var indicatorPrincipalData = this.getIndicatorLoadedById(indicatorId);
	//$retval.find(".sind-title-selector").text(indicatorPrincipalData.title);
	$retval.find(".sind-index-selector").text(G_STRING.ID_EFFICIENCY_INDEX);
	$retval.find(".sind-cost-selector").text(G_STRING.ID_INEFFICIENCY_COST);

	$retval.find('.breadcrumb').find('li').remove();
	$retval.find('.breadcrumb').append ('<li><a class="bread-back-selector" href="#"><i class="fa fa-chevron-left fa-fw"></i>' + window.currentIndicator.title +  '</a></li>');
	$retval.find('.breadcrumb').append ('<li><b>' + window.currentEntityData.name + '</b></li>');
	this.setColorForInefficiency($retval.find(".sind-cost-number-selector"), window.currentEntityData);
	return $retval;
};

WidgetBuilder.prototype.buildSpecialIndicatorSecondViewDetailPei = function (oneItemDetail) {
	if (oneItemDetail == null){throw new Error("oneItemDetail is null ");}
	if (!typeof(oneItemDetail) === 'object'){throw new Error( "detailData is not and object ->" + oneItemDetail);}
	if (!oneItemDetail.hasOwnProperty("name")){throw new Error("buildSpecialIndicatorFirstViewDetail -> detailData has not the name param. Has it the correct Type? ->" + oneItemDetail);}

	_.templateSettings.variable = "detailData";
	var template = _.template ($("script.specialIndicatorSecondViewDetailPei").html());
	var $retval =  $(template(oneItemDetail));
	$retval.find(".detail-efficiency-selector").text(G_STRING.ID_EFFICIENCY_INDEX);
	$retval.find(".detail-cost-selector").text(G_STRING.ID_INEFFICIENCY_COST);
	this.setColorForInefficiency($retval.find(".detail-cost-number-selector"), oneItemDetail);
	return $retval;
}

WidgetBuilder.prototype.buildSpecialIndicatorSecondViewDetailUei = function (oneItemDetail) {
	if (oneItemDetail == null){throw new Error("oneItemDetail is null ");}
	if (!typeof(oneItemDetail) === 'object'){throw new Error( "detailData is not and object ->" + oneItemDetail);}
	if (!oneItemDetail.hasOwnProperty("name")){throw new Error("buildSpecialIndicatorFirstViewDetail -> detailData has not the name param. Has it the correct Type? ->" + oneItemDetail);}

	_.templateSettings.variable = "detailData";
	var template = _.template ($("script.specialIndicatorSecondViewDetailUei").html());
	var $retval =  $(template(oneItemDetail));
	$retval.find(".detail-efficiency-selector").text(G_STRING.ID_EFFICIENCY_INDEX);
	$retval.find(".detail-cost-selector").text(G_STRING.ID_INEFFICIENCY_COST);
	this.setColorForInefficiency($retval.find(".detail-cost-number-selector"), oneItemDetail);
	return $retval;
}

WidgetBuilder.prototype.buildSpecialIndicatorSecondViewDetaiUei = function (oneItemDetail) {
	if (oneItemDetail == null){throw new Error("oneItemDetail is null ");}
	if (!typeof(oneItemDetail) === 'object'){throw new Error( "detailData is not and object ->" + oneItemDetail);}
	if (!oneItemDetail.hasOwnProperty("name")){throw new Error("buildSpecialIndicatorFirstViewDetail -> detailData has not the name param. Has it the correct Type? ->" + oneItemDetail);}

	_.templateSettings.variable = "detailData";
	var template = _.template ($("script.specialIndicatorSencondViewDetailUei").html());
	var $retval =  $(template(oneItemDetail));
	$retval.find(".detail-efficiency-selector").text(G_STRING.ID_EFFICIENCY_INDEX);
	$retval.find(".detail-cost-selector").text(G_STRING.ID_INEFFICIENCY_COST);
	this.setColorForInefficiency($retval.find(".detail-cost-number-selector"), oneItemDetail);
	return $retval;
}

WidgetBuilder.prototype.getIndicatorLoadedById = function (searchedIndicatorId) {
	var retval = null;
	for (key in window.loadedIndicators) {
		var indicator = window.loadedIndicators[key];
		if (indicator.id == searchedIndicatorId) {
			retval = indicator;		
		}
	}
	if (retval == null) { throw new Error(searchedIndicatorId + " was not found in the loaded indicators.");}
	return retval;
}

WidgetBuilder.prototype.getDashboardLoadedById = function (searchedDashboardId) {
	var retval = null;
	for (key in window.loadedDashboards) {
		var dashboard = window.loadedDashboards[key];
		if (dashboard.id == searchedDashboardId) {
			retval = dashboard;		
		}
	}
	if (retval == null) { throw new Error(searchedIndicatorId + " was not found in the loaded indicators.");}
	return retval;
}

WidgetBuilder.prototype.buildGeneralIndicatorFirstView = function (indicatorData) {
	_.templateSettings.variable = "indicator";
	var template = _.template ($("script.generalIndicatorMainPanel").html());
	var $retval =  $(template(indicatorData));
	$retval.find(".ind-title-selector").text(window.currentIndicator.title);
	return $retval;
}


WidgetBuilder.prototype.setColorForInefficiency = function ($widget, indicatorData) {
	//turn red/gree the font according if is positive or negative: var $widget = $retval.find(".sind-cost-number-selector");
	$widget.removeClass("red");
	$widget.removeClass("green");
	if (indicatorData.inefficiencyCost >= 0) {
		$widget.addClass("green");
	}
	else {
		$widget.addClass("red");
	}
}

/**********************************************************************/
helper = new ViewDashboardHelper();
var ws = urlProxy.split('/');
model = new ViewDashboardModel(token, urlProxy, ws[3]);
presenter = new ViewDashboardPresenter(model);

window.loadedIndicators = []; //updated in das-title-selector.click->fillIndicatorWidgets, ready->fillIndicatorWidgets
window.loadedDashboards = [];
window.currentEntityData = null;
window.currentIndicator = null;//updated in ind-button-selector.click ->loadIndicator, ready->loadIndicator
window.currentDashboardId = null;
window.currentDetailFunction = null;
window.currentDetailList = null;

$(document).ready(function() {
	$('#indicatorsGridStack').gridstack();
	$('#indicatorsDataGridStack').gridstack();
	$('#relatedDetailGridStack').gridstack();

	$('#sortListButton').click(function() {
		var btn = $(this);
		if (btn.hasClass('fa-chevron-up')) {
			btn.removeClass('fa-chevron-up');
			btn.addClass('fa-chevron-down');
		}
		else {
			btn.removeClass('fa-chevron-down');
			btn.addClass('fa-chevron-up');
		}

		window.currentDetailFunction (presenter.orderDataList (
													window.currentDetailList, 
													selectedOrderOfDetailList()));
		//send scroll +1 and -1 to activate the show/hide event. 
		//both scrolls are sent cause if the scroll at the end
		//scroll +1 has no effect but -1 yes
		$(window).scrollTop($(window).scrollTop() + 1);
		$(window).scrollTop($(window).scrollTop() - 1);
		return false;
	});
	
	/* Show on scroll functionality */
	$(window).scroll( function() {
		/* Check the location of each desired element */
		$('.hideme').each( function(i){
			var bottom_of_object = $(this).offset().top + $(this).outerHeight();
			var bottom_of_window = $(window).scrollTop() + $(window).height();
			/* If the object is completely visible in the window, fade it in */
			if (bottom_of_window + 100 > bottom_of_object) {
				$(this).animate({'opacity':'1'}, 500);
				$(this).removeClass('hideme');
			}
		}); 
		hideScrollIfAllDivsAreVisible();
	});
 
	var isHover = false;
	$('#scrollImg').mouseover(function() {
		isHover = true;
		var interval =  window.setInterval(function () {
				var newPos = $(window).scrollTop() + 100;
				$(window).scrollTop(newPos);
				if (isHover == false) {
					window.clearInterval(interval);
				}
		}, 200);
	});
	
	$('#scrollImg').mouseleave(function() {
		isHover = false;
	});

	
  //When some item is moved
    $('.grid-stack').on('change', function (e, items) {
        var widgets = [];
        _.map($('.grid-stack .grid-stack-item:visible'), function (el) {
            el = $(el);
            var item = el.data('_gridstack_node');
            var idWidGet = el.data("indicator-id");
            /*if(favorite == actualDashId){
                favoriteData = 1;
            } else {
                favoriteData = 0;
            }*/
            if (typeof idWidGet != "undefined" && el.hasClass('ind-button-selector')) {
                var widgetsObj = {
                        'indicatorId': idWidGet,
                        'x': item.x,
                        'y': item.y,
                        'width': item.width,
                        'height': item.height <= 1 ? 2 : item.height
                }
                widgets.push(widgetsObj);
            }
        }); 
        
		var favoriteDasbhoardId = $('.das-icon-selector.selected').parent().data('dashboard-id');
		if (favoriteDasbhoardId == null || favoriteDasbhoardId == 'undefined') {throw new Error ('No favorite dashboard detected');}

        if (widgets.length != 0) {
            var dashboard = {
                    'dashId': window.currentDashboardId,
                    'dashFavorite': ((window.currentDashboardId == favoriteDasbhoardId) ? 1 : 0),
                    'dashData': widgets
            }
          model.setPositionIndicator(dashboard);  
        }
    });
	
	$('body').on('click', '.das-icon-selector', function() {
		var dashboardId = $(this).parent().data('dashboard-id');
		$('.das-icon-selector').removeClass("selected");
		$(this).addClass('selected');
		var dashboard = {
                    'dashId': dashboardId,
                    'dashFavorite': 1,
                    'dashData': ''
                }
		model.setPositionIndicator(dashboard);  
	});


	/*-------------------------------clicks----------------------------*/
	$('body').on('click','.btn-compare', function() {
		presenter.getDashboardIndicators(window.currentDashboardId, defaultInitDate(), defaultEndDate())
				.done(function(indicatorsVM) {
					fillIndicatorWidgets(indicatorsVM);
					loadIndicator(getFavoriteIndicator().id, defaultInitDate(), defaultEndDate());
				});
	});

	$('#dashboardsList').on('click','.das-title-selector', function() {
		var dashboardId = $(this).parent().data('dashboard-id');
		window.currentDashboardId  = dashboardId;
		presenter.getDashboardIndicators(dashboardId, defaultInitDate(), defaultEndDate())
				.done(function(indicatorsVM) {
					fillIndicatorWidgets(indicatorsVM);
					loadIndicator(getFavoriteIndicator().id, defaultInitDate(), defaultEndDate());
					setActiveDashboard();
				});
	});

	$('#indicatorsGridStack').on('click','.ind-button-selector', function() {
		var indicatorId = $(this).data('indicator-id');
		loadIndicator(indicatorId, defaultInitDate(), defaultEndDate());
	});

	$('body').on('click','.bread-back-selector', function() {
		var indicatorId = window.currentIndicator.id;
		loadIndicator(indicatorId, defaultInitDate(), defaultEndDate());
		return false;
	});

	$('#relatedDetailGridStack').on('click','.detail-button-selector', function() {
		var detailId = $(this).data('detail-id');
		window.currentEntityData = {"entityId":$(this).data('detail-id'),
							"indicatorId":$(this).data('indicator-id'),
                            "efficiencyIndexToShow":$(this).data('detail-index'),
                            "inefficiencyCostToShow":$(this).data('detail-cost-to-show'),
                            "inefficiencyCost":$(this).data('detail-cost'),
                            "name":$(this).data('detail-name')
		};
		presenter.getSpecialIndicatorSecondLevel(detailId, window.currentIndicator.type, defaultInitDate(), defaultEndDate())
			.done(function (viewModel) {
				fillSpecialIndicatorSecondView(viewModel);
			});
	});
	initialDraw();
});

var hideScrollIfAllDivsAreVisible = function(){
	if ($('.hideme').length <= 0) {
			$('#scrollImg').hide();
	}
	else {
			$('#scrollImg').css('visibility', 'visible');
			$('#scrollImg').show();
	}
}

var hideTitleAndSortDiv = function(){
	if (window.currentIndicator == null) {
		$('#relatedLabel').hide();
	} 
	switch (window.currentIndicator.type) {
		case "1010":
		case "1030":
			if($('.detail-button-selector').length == 0) {
				$('#relatedLabel').hide();
				//$('#relatedLabel').find('h3').text(G_STRING['ID_NO_DATA_TO_DISPLAY']);
			}
			else {
				$('#relatedLabel').css('visibility', 'visible');
				$('#relatedLabel').show();
			}

			break;
		default:
			$('#relatedLabel').hide();
			break;
	}
}

var selectedOrderOfDetailList = function () {
	return ($('#sortListButton').hasClass('fa-chevron-up') ? "up" : "down");
}

var selectDefaultMonthAndYear = function () {
	var compareDate = new Date();
	compareDate.setDate(1);
	compareDate.setMonth(compareDate.getMonth() - 1);
	var compareMonth = compareDate.getMonth() + 1;
	var compareYear = compareDate.getFullYear();
	$('#month').val(compareMonth);
	$('#year').val(compareYear);
}

var setActiveDashboard = function () {
	var builder = new WidgetBuilder();
	var dashboard = builder.getDashboardLoadedById(window.currentDashboardId);
	if (dashboard == null) {
		return;
	}
	$('#titleH4').text(dashboard.title);
}

var initialDraw = function () {
	selectDefaultMonthAndYear();
	presenter.getUserDashboards(pageUserId)
		.then(function(dashboardsVM) {
				fillDashboardsList(dashboardsVM);
				if (window.currentDashboardId == null) {return;}
				/**** window initialization  with favorite dashboard*****/
				presenter.getDashboardIndicators(window.currentDashboardId, defaultInitDate(), defaultEndDate())
						.done(function(indicatorsVM) {
							fillIndicatorWidgets(indicatorsVM);
							loadIndicator(getFavoriteIndicator().id, defaultInitDate(), defaultEndDate());
							setActiveDashboard();
						});
			});
}

var loadIndicator = function (indicatorId, initDate, endDate) {
	if (indicatorId == null || indicatorId === undefined) {return;}
    var builder = new WidgetBuilder();
    window.currentIndicator = builder.getIndicatorLoadedById(indicatorId);
	presenter.getIndicatorData(indicatorId, window.currentIndicator.type, initDate, endDate)
			.done(function (viewModel) {
				switch (window.currentIndicator.type)  {
					case "1010":
					case "1030":
						fillSpecialIndicatorFirstView(viewModel);
						break;
					case "1050":
						fillStatusIndicatorFirstView(viewModel);
						break;
					default:
						fillGeneralIndicatorFirstView(viewModel);
						break;
				}
				hideScrollIfAllDivsAreVisible();
				hideTitleAndSortDiv();
			});
}

var setIndicatorActiveMarker = function () {
	$('.panel-footer').each (function () {
		$(this).removeClass('panel-active');
		var indicatorId = $(this).parents('.ind-button-selector').data('indicator-id');
		if (window.currentIndicator.id == indicatorId)  {
			$(this).addClass('panel-active');
		}
	});
}

var getFavoriteIndicator = function() {
	var retval = (window.loadedIndicators.length > 0)
					? window.loadedIndicators[0] 
					: null;
	for (key in window.loadedIndicators) {
		var indicator = window.loadedIndicators[key];
		if (indicator.favorite == 1) {
			retval = indicator;
		}
	}
	if (retval==null) {throw new Error ('No favorites found.');}
	return retval;
}

var defaultInitDate = function() {
    var date = new Date();
    var dateMonth = date.getMonth();
    var dateYear = date.getFullYear();
	var initDate = $('#year').val() + '-' + $('#month').val() + '-' + '01';
	return initDate;
}

var defaultEndDate = function () {
    var date = new Date();
    var dateMonth = date.getMonth();
    var dateYear = date.getFullYear();
	return dateYear + "-" + (dateMonth + 1) + "-30";
}

var fillDashboardsList = function (presenterData) {
	if (presenterData == null || presenterData.length == 0) {
		$('#dashboardMessage').text(G_STRING['ID_GRID_PAGE_NO_DASHBOARD_MESSAGE']);
		$('#titleH4').text(G_STRING['ID_GRID_PAGE_NO_DASHBOARD_MESSAGE']);
		$('#compareIndicators').hide();
	}
	_.templateSettings.variable = "dashboard";
	var template = _.template ($("script.dashboardButtonTemplate").html())
	window.loadedDashboards = presenterData;
	for (key in presenterData) {
		var dashboard = presenterData[key];
		$('#dashboardsList').append(template(dashboard));
		if (dashboard.isFavorite == 1) {
			window.currentDashboardId = dashboard.id;
			$('#dashboardButton-' + dashboard.id)
				.find('.das-icon-selector')
				.addClass('selected');
		}
	}
	
};

var fillIndicatorWidgets = function (presenterData) {
	if (presenterData == null || presenterData === undefined) {return;}
	var widgetBuilder = new WidgetBuilder();
    var grid = $('#indicatorsGridStack').data('gridstack');
	grid.remove_all();
	window.loadedIndicators = presenterData;
	$.each(presenterData, function(key, indicator) {
		var $widget = widgetBuilder.getIndicatorWidget(indicator);
		grid.add_widget($widget, indicator.toDrawX, indicator.toDrawY, indicator.toDrawWidth, indicator.toDrawHeight, true);
		var $title = $widget.find('.ind-title-selector');
		if (indicator.favorite == "1") {
			$title.addClass("panel-active");
		}
	});
}

var fillStatusIndicatorFirstView = function (presenterData) {
	var widgetBuilder = new WidgetBuilder();
	var panel = $('#indicatorsDataGridStack').data('gridstack');
	panel.remove_all();
	$('#relatedDetailGridStack').data('gridstack').remove_all();

	var $widget = widgetBuilder.buildStatusIndicatorFirstView(presenterData);
	panel.add_widget($widget, 0, 15, 20, 4.7, true);

	var graphParams1 = {
		canvas : {
			containerId:'graph1',
			width:300,
			height:300,
			stretch:true,
			noDataText: G_STRING.ID_DISPLAY_EMPTY
		},
		graph: {

			allowDrillDown:true,
			allowTransition:true,
			showTip: true,
			allowZoom: false,
			showLabels: true
		}
	};

	var graph1 = new PieChart(presenterData.graph1Data, graphParams1, null, null);
	graph1.drawChart();
	var graphParams2 = graphParams1;
	graphParams2.canvas.containerId = "graph2";
	var graph2 = new PieChart(presenterData.graph2Data, graphParams2, null, null);
	graph2.drawChart();
	var graphParams3 = graphParams1;
	graphParams3.canvas.containerId = "graph3";
	var graph3 = new PieChart(presenterData.graph3Data, graphParams3, null, null);
	graph3.drawChart();

	var indicatorPrincipalData = widgetBuilder.getIndicatorLoadedById(presenterData.id)
	setIndicatorActiveMarker();
}

var fillStatusIndicatorFirstViewDetail = function(presenterData) {
	var widgetBuilder = new WidgetBuilder();
	var gridDetail = $('#relatedDetailGridStack').data('gridstack');
	//gridDetail.remove_all();
	$.each(presenterData.dataList, function(index, dataItem) {
		var $widget = widgetBuilder.buildStatusIndicatorFirstViewDetail(dataItem);
		var x = (index % 2 == 0) ? 6 : 0;
		gridDetail.add_widget($widget, x, 15, 6, 2, true);
	});
	if (window.currentIndicator.type == "1010") {
		$('#relatedLabel').find('h3').text(G_STRING['ID_RELATED_PROCESS']);
	}
	if (window.currentIndicator.type == "1030") {
		$('#relatedLabel').find('h3').text(G_STRING['ID_RELATED_GROUPS']);
	}
	if (window.currentIndicator.type == "1050") {
		$('#relatedLabel').find('h3').text(G_STRING['ID_RELATED_PROCESS']);
	}
}

var fillSpecialIndicatorFirstView = function(presenterData) {
	var widgetBuilder = new WidgetBuilder();
	var panel = $('#indicatorsDataGridStack').data('gridstack');
	panel.remove_all();
	$('#relatedDetailGridStack').data('gridstack').remove_all();

	var $widget = widgetBuilder.buildSpecialIndicatorFirstView(presenterData);
	panel.add_widget($widget, 0, 15, 20, 4.7, true);
	  var peiParams = {
        canvas : {
            containerId:'specialIndicatorGraph',
            width:300,
            height:300,
            stretch:true,
			noDataText: G_STRING.ID_NO_INEFFICIENT_PROCESSES
        },
        graph: {
            allowDrillDown:false,
            allowTransition:true,
            showTip: true,
            allowZoom: false,
            gapWidth:0.3,
            useShadows: true,
            thickness: 30,
            showLabels: true
        }
    };

    var ueiParams = {
		canvas : {
			containerId:'specialIndicatorGraph',
			width:500,
			height:300,
			stretch:true,
			noDataText: G_STRING.ID_NO_INEFFICIENT_USER_GROUPS
		},
		graph: {
			allowDrillDown:false,
			allowTransition:true,
			axisX:{ showAxis: true, label: G_STRING['ID_GROUPS']},
			axisY:{ showAxis: true, label: G_STRING['ID_COSTS']},
			gridLinesX:false,
			gridLinesY:true,
			showTip: true,
			allowZoom: false,
			useShadows: true,
			paddingTop: 50,
			colorPalette: ['#5486bf','#bf8d54','#acb30c','#7a0c0c','#bc0000','#906090','#007efb','#62284a','#0c7a7a','#74a9a9']
		}
    };

	var indicatorPrincipalData = widgetBuilder.getIndicatorLoadedById(presenterData.id)

	if (indicatorPrincipalData.type == "1010") {
		var graph = new Pie3DChart(presenterData.dataToDraw, peiParams, null, null);
		graph.drawChart();
		//the pie chart goes to much upwards,so a margin is added:
		$('#specialIndicatorGraph').css('margin-top','60px');
	}

	if (indicatorPrincipalData.type == "1030") {
		var graph = new BarChart(presenterData.dataToDraw, ueiParams, null, null);
		graph.drawChart();
	}
	

	this.fillSpecialIndicatorFirstViewDetail(presenter.orderDataList(presenterData.data, selectedOrderOfDetailList()));
	setIndicatorActiveMarker();
}

var fillSpecialIndicatorFirstViewDetail = function (list) {
	//presenterData = { id: "indId", efficiencyIndex: "0.11764706", efficiencyVariation: -0.08235294,
	// 					inefficiencyCost: "-127.5000", inefficiencyCostToShow: -127, efficiencyIndexToShow: 0.12
	// 					data: {indicatorId, uid, name, averateTime...}, dataToDraw: [{datalabe, value}] }
	var widgetBuilder = new WidgetBuilder();
	var gridDetail = $('#relatedDetailGridStack').data('gridstack');
	gridDetail.remove_all();
	
	window.currentDetailList = list;	
	window.currentDetailFunction = fillSpecialIndicatorFirstViewDetail;

	$.each(list, function(index, dataItem) {
		var $widget = widgetBuilder.buildSpecialIndicatorFirstViewDetail(dataItem);
		var x = (index % 2 == 0) ? 6 : 0;
		//the first 2 elements are not hidden
		if (index < 2) {
			$widget.removeClass("hideme");
		}
		gridDetail.add_widget($widget, x, 15, 6, 2, true);
	});
	if (window.currentIndicator.type == "1010") {
		$('#relatedLabel').find('h3').text(G_STRING['ID_RELATED_PROCESS']);
	}
	if (window.currentIndicator.type == "1030") {
		$('#relatedLabel').find('h3').text(G_STRING['ID_RELATED_GROUPS']);
	}
	hideScrollIfAllDivsAreVisible();
}

var fillSpecialIndicatorSecondView = function(presenterData) {
	//presenterData= object {dataToDraw[], entityData[] //user/tasks data}
	var widgetBuilder = new WidgetBuilder();
	var panel = $('#indicatorsDataGridStack').data('gridstack');
	panel.remove_all();
	var $widget = widgetBuilder.buildSpecialIndicatorSecondView(presenterData);
	panel.add_widget($widget, 0, 15, 20, 4.7, true);
	var detailParams = {
		canvas : {
			containerId:'specialIndicatorGraph',
			width:300,
			height:300,
			stretch:true
		},
		graph: {
			allowTransition: false,
			allowDrillDown: true,
			showTip: true,
			allowZoom: false,
			useShadows: false,
			gridLinesX: true,
			gridLinesY: true,
			area: {visible: false, css:"area"},
			axisX:{ showAxis: true, label: G_STRING['ID_USER'] },
			axisY:{ showAxis: true, label: G_STRING['ID_COSTS'] },
			showErrorBars: true,
			colorPalette: ['#5486bf','#bf8d54','#acb30c','#7a0c0c','#bc0000','#906090','#007efb','#62284a','#0c7a7a','#74a9a9']

		}
	};

	var indicatorPrincipalData = widgetBuilder.getIndicatorLoadedById(window.currentEntityData.indicatorId);

	if (window.currentIndicator.type == "1010") {
		detailParams.graph.axisX.label = G_STRING['ID_TASK'] ;
		detailParams.canvas.noDataText = G_STRING['ID_NO_INEFFICIENT_TASKS'] ;
		var graph = new BarChart(presenterData.dataToDraw, detailParams, null, null);
		graph.drawChart();
	}

	if (window.currentIndicator.type == "1030") {
		detailParams.canvas.noDataText = G_STRING['ID_NO_INEFFICIENT_USERS'] ;
		var graph = new BarChart(presenterData.dataToDraw, detailParams, null, null);
		graph.drawChart();
	}
	this.fillSpecialIndicatorSecondViewDetail(presenter.orderDataList(presenterData.entityData, selectedOrderOfDetailList()));
}

var fillSpecialIndicatorSecondViewDetail = function (list) {
	//presenterData =  { entityData: Array[{name,uid,inefficiencyCost,
	// 									inefficiencyIndex, deviationTime,
	// 									averageTime}],
	// 						dataToDraw: Array[{datalabel, value}] }
	var widgetBuilder = new WidgetBuilder();
	var gridDetail = $('#relatedDetailGridStack').data('gridstack');
	gridDetail.remove_all();

	window.currentDetailList = list;	
	window.currentDetailFunction = fillSpecialIndicatorSecondViewDetail;

	$.each(list, function(index, dataItem) {
		if (window.currentIndicator.type == "1010") {
			var $widget = widgetBuilder.buildSpecialIndicatorSecondViewDetailPei(dataItem);
		}

		if (window.currentIndicator.type == "1030") {
			var $widget = widgetBuilder.buildSpecialIndicatorSecondViewDetailUei(dataItem);
		}

		var x = (index % 2 == 0) ? 6 : 0;
		//the first 2 elements are not hidden
		if (index < 2) {
			$widget.removeClass("hideme");
		}
		gridDetail.add_widget($widget, x, 15, 6, 2, true);
	});

	if (window.currentIndicator.type == "1010") {
		$('#relatedLabel').find('h3').text(G_STRING['ID_RELATED_TASKS']);
	}
	if (window.currentIndicator.type == "1030") {
		$('#relatedLabel').find('h3').text(G_STRING['ID_RELATED_USERS']);
	}
	hideScrollIfAllDivsAreVisible();
}

var fillGeneralIndicatorFirstView = function (presenterData) {
	var widgetBuilder = new WidgetBuilder();
	var panel = $('#indicatorsDataGridStack').data('gridstack');
	panel.remove_all();
	$('#relatedDetailGridStack').data('gridstack').remove_all();

	var $widget = widgetBuilder.buildGeneralIndicatorFirstView(presenterData);
	panel.add_widget($widget, 0, 15, 20, 4.7, true);

	$('#relatedLabel').find('h3').text('');

	var generalLineParams1 = {
		canvas : {
			containerId:'generalGraph1',
			width:300,
			height:300,
			stretch:true
		},
		graph: {
			allowTransition: false,
			allowDrillDown: true,
			showTip: true,
			allowZoom: false,
			useShadows: false,
			gridLinesX: true,
			gridLinesY: true,
			area: {visible: false, css:"area"},
			axisX:{ showAxis: true, label: G_STRING.ID_PROCESS_TASKS },
			axisY:{ showAxis: true, label: G_STRING.ID_TIME_HOURS },
			showErrorBars: false
		}
	};

	var generalLineParams2 = {
		canvas : {
			containerId:'generalGraph2',
			width:300,
			height:300,
			stretch:true
		},
		graph: {
			allowTransition: false,
			allowDrillDown: true,
			showTip: true,
			allowZoom: false,
			useShadows: false,
			gridLinesX: true,
			gridLinesY: true,
			area: {visible: false, css:"area"},
			axisX:{ showAxis: true, label: G_STRING.ID_PROCESS_TASKS },
			axisY:{ showAxis: true, label: G_STRING.ID_TIME_HOURS },
			showErrorBars: false
		}
	};

	var generalBarParams1 = {
		canvas : {
			containerId:'generalGraph1',
			width:300,
			height:300,
			stretch:true
		},
		graph: {
			allowDrillDown:false,
			allowTransition:true,
			axisX:{ showAxis: true, label: G_STRING.ID_YEAR },
			axisY:{ showAxis: true, label: G_STRING.ID_TIME_HOURS },
			gridLinesX:false,
			gridLinesY:true,
			showTip: true,
			allowZoom: false,
			useShadows: true,
			paddingTop: 50,
			colorPalette: ['#5486bf','#bf8d54','#acb30c','#7a0c0c','#bc0000','#906090','#007efb','#62284a','#0c7a7a','#74a9a9']
		}
	};

	var generalBarParams2 = {
		canvas : {
			containerId:'generalGraph2',
			width:300,
			height:300,
			stretch:true
		},
		graph: {
			allowDrillDown:false,
			allowTransition:true,
			axisX:{ showAxis: true, label: G_STRING.ID_YEAR },
			axisY:{ showAxis: true, label: G_STRING.ID_TIME_HOURS  },
			gridLinesX:false,
			gridLinesY:true,
			showTip: true,
			allowZoom: false,
			useShadows: true,
			paddingTop: 50,
			colorPalette: ['#5486bf','#bf8d54','#acb30c','#7a0c0c','#bc0000','#906090','#007efb','#62284a','#0c7a7a','#74a9a9']
		}
	};

	var graph1 = null;
	if (presenterData.graph1Type == '10') {
		generalBarParams1.graph.axisX.label = presenterData.graph1XLabel;
		generalBarParams1.graph.axisY.label = presenterData.graph1YLabel;
		graph1 = new BarChart(presenterData.graph1Data, generalBarParams1, null, null);
	} else {
		generalLineParams1.graph.axisX.label = presenterData.graph1XLabel;
		generalLineParams1.graph.axisY.label = presenterData.graph1YLabel;
		graph1 = new LineChart(presenterData.graph1Data, generalLineParams1, null, null);
	}
	graph1.drawChart();

	var graph2 = null;
	if (presenterData.graph2Type == '10') {
		generalBarParams2.graph.axisX.label = presenterData.graph2XLabel;
		generalBarParams2.graph.axisY.label = presenterData.graph2YLabel;
		graph2 = new BarChart(presenterData.graph2Data, generalBarParams2, null, null);
	} else {
		generalLineParams2.graph.axisX.label = presenterData.graph2XLabel;
		generalLineParams2.graph.axisY.label = presenterData.graph2YLabel;
		graph2 = new LineChart(presenterData.graph2Data, generalLineParams2, null, null);
	}
	graph2.drawChart();

	setIndicatorActiveMarker();
}

var animateProgress = function (indicatorItem, widget){
      var getRequestAnimationFrame = function () {
        return window.requestAnimationFrame ||
        window.webkitRequestAnimationFrame ||   
        window.mozRequestAnimationFrame ||
        window.oRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        function ( callback ){
          window.setTimeout(enroute, 1 / 60 * 1000);
        };
      };

      var fpAnimationFrame = getRequestAnimationFrame();   
      var i = 0;
      var j = 0;

	  var indicator = indicatorItem;
	  var animacion = function () {
		  var intComparative = parseInt(indicator.comparative);
		  var divId = "#indicatorButton" + indicator.id;
		  var $valueLabel = widget
				.find('.ind-value-selector');
		  var $progressBar = widget
				.find('.ind-progress-selector');

		  if (!($valueLabel.length > 0)) {throw new Error ('"No ind-value-selector found for " + divId');}
		  this.helper.assert($progressBar.length > 0, "No ind-progress-selector found for " + divId);
		  $progressBar.attr('aria-valuemax', intComparative);
		  var indexToPaint = Math.min(indicator.value * 100 / intComparative, 100);
		  
		  if (i <= indexToPaint) {
			  $progressBar.css('width', i+'%').attr('aria-valuenow', i);
			  i++;
			  fpAnimationFrame(animacion);
		  }
		  
		  if(j <= indicator.value){
			  $valueLabel.text(j + "%");
			  j++;
			  fpAnimationFrame(animacion);
		  }
		  
	  }
	fpAnimationFrame(animacion); 
};

/*var dashboardButtonTemplate = ' <div class="btn-group pull-left"> \ 
								<button id="favorite" type="button" class="btn btn-success"><i class="fa fa-star fa-1x"></i></button> \
								<button id="dasB" type="button" class="btn btn-success">'+ G_STRING.ID_MANAGERS_DASHBOARDS +'</button> \
							</div>';*/



