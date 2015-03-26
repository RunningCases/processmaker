function drawAxisX(data, canvas, scale, parameter){
	if (!parameter.graph.axisX.showAxis) return;
	//graph Dimensions inside the container
	var graphDim = new GraphDim(parameter);
	var xAxis = d3.svg.axis().scale(scale).orient('bottom').tickSize(parameter.graph.axisX.ticks);

	var x_axis = canvas.append('g')
		   .attr('class', 'axis')
		   .attr('transform','translate(0,' + graphDim.bottom + ')')
		   .call(xAxis)
		   .selectAll('text')
			  .style('text-anchor','end')
			  .attr('class', 'x-ticks-label')
			  .attr('transform','rotate(-45)');	 

			  
	if (parameter.graph.axisX.showLabel) {
		var labelPosX = graphDim.left + graphDim.width/2;
		var labelPosY = graphDim.bottom + 55 
		canvas.append("text")
				.attr("transform", "translate(" + labelPosX  + " ," + labelPosY + ")")
				.attr('class','axis-label')
				.style("text-anchor", "middle")
				.text(parameter.graph.axisX.label);
	}
}

function drawAxisY(data, canvas, scale, parameter){
	if (!parameter.graph.axisY.showAxis) return;
	var graphDim = new GraphDim(parameter);
	var yAxis = d3.svg.axis().scale(scale).orient('left').ticks(parameter.graph.axisY.ticks); 
	var y_axis = canvas.append('g')
					.attr('class','axis')
					.attr('transform','translate(' + graphDim.top + ',0)')
					.call(yAxis)
					   .selectAll('text')
						  .attr('class', 'y-ticks-label');
						

	if (parameter.graph.axisY.showLabel) {
		canvas.append("text")
		    .attr('class','axis-label')
			.attr("transform", "rotate(-90)")
			.attr("y", 0)
			.attr("x", -(graphDim.left + graphDim.height/2))
			.attr("dy", "1.5em")
			.style("text-anchor", "end")
			.text(parameter.graph.axisY.label);
	}
}


var BarChart = function (data, params, previousDataPoint, breadcrumbs) {
	this.originalData = data;
	this.previousDataPoint = previousDataPoint;
	this.params = params;
	this.$container = $('#' + this.params.canvas.containerId);
	//Breadcrumb stack:
	this.breadCrumbStack =  (breadcrumbs == null ) ? [] : breadcrumbs;
	pushToStack(previousDataPoint, this);
};

BarChart.prototype.drawChart = function() {
	var $container = $('#' + this.params.canvas.containerId); // canvas[0] to convert d3 to jquery object
	$container.empty();
	$('.tooltipdiv').remove();
	stretchCanvas(null, this.$container, this.params);
	this.canvas = createCanvas(this.params.canvas.containerId, this.params.canvas);
	this.drawBars(this.originalData, this.canvas, this.params);
	refreshBreadCrumbs(this);
};

BarChart.prototype.addBarTransition = function (bars, scaleX, scaleY) {

	/************* IN TRANSITION ****************/
	  bars.attr("stroke-width", 4)
			.transition()
			.duration(300)
			.attr("width", scaleX.rangeBand())
			.attr("y", function (d) { return scaleY(d.value) });

	/*************** EXIT TRANSITION *****************/
	    bars.exit()
		.transition()
		.duration(300)
		.ease("exp")
			.attr("width", 0)
			.remove();
}

BarChart.prototype.drawBars = function(data, canvas, param) {
	if (data == null || data.length == 0) {
		this.$container.html( "<div class='pm-charts-no-draw'>No data to draw ...</div>" );
	}
	var parameter = createDefaultParamsForGraph(param);
	//graph part of the parameters passed to this object
	var graphParam = createDefaultParamsForLineChart(param.graph);
	parameter.graph = graphParam;

	//graph Dimensions inside the container
	var graphDim = new GraphDim(param);

	/*var totalPaddingTop = DEFAULT_PADDING + parameter.graph.paddingTop;
	var	h = parameter.canvas.height - totalPaddingTop -  DEFAULT_PADDING;
	var w = parameter.canvas.width - 2 * DEFAULT_PADDING;
	var chartRight = w + DEFAULT_PADDING;
	var chartBottom = h + totalPaddingTop;
*/
	var tooltip = new ToolTip();
	//HACK: to avoid  context change in closure we store object's reference(this) here.
	//		JavaScript things...
	var currObj = this;

	var xScaleLabels = data.map(function(data){
		return data.datalabel;
	});

	var maxValue = d3.max(data,function(d){ return d.value*1.0; });

	var xScale = d3.scale
					.ordinal()
					.domain(xScaleLabels)
					.rangeRoundBands([graphDim.left,graphDim.right], 0.15);

	var yScale = d3.scale
					.linear()
					.domain( [0, maxValue] )
					.range([graphDim.bottom, graphDim.top])
					.nice();

	var chart = canvas.append('g');

	drawAxisX(data, chart, xScale, parameter);
	drawAxisY(data, chart, yScale, parameter);
	drawLinesX(data, chart, xScale, parameter);
	drawLinesY(data, chart, yScale, parameter);

	var bars = chart.selectAll('rect').data(data);
	if (parameter.graph.allowZoom) { addZoomToCanvas(chart); }
	addGradient(chart, "gradientForBars")

	bars.enter()
	   .append('rect')
	   .attr({
			   'x': function(d,i) {
				   return xScale(d.datalabel); 
			   },
			   'y': function(d) {
				   return (parameter.graph.allowTransition) ? 0 : yScale(d.value);
			   },
			   'width': (parameter.graph.allowTransition) ? 0: xScale.rangeBand(),
			   'height': function(d) {
				   return graphDim.bottom - yScale(d.value);
			   },
			   'fill': 'url(#gradientForBars)'
		})
		.attr("clip-path", "url(#rectClip)")
		.on("mouseover", function(d,i) {
			d3.select(this)
				.attr('fill', currObj.params.graph.colorPalette[i%currObj.params.graph.colorPalette.length]);

			tooltip.show(function () {
				return {value: d.value, datalabel: d.datalabel}
			});
		})
		.on('mouseout',function(d){
		d3.select(this)
				.attr('fill','url(#gradientForBars)');
		tooltip.hide();
		});


	if (parameter.graph.allowTransition) {this.addBarTransition(bars, xScale, yScale);}


	if (parameter.graph.useShadows){
		addShadow(canvas, "110%", 2);
		chart.selectAll('rect')
			   .attr("filter", "url(#drop-shadow)"); 
	}



	if (this.params.graph.allowDrillDown) {
		this.addOnClick(data, canvas);
		if (this.breadCrumbStack.length > 0) {
			var clip = chart.append("defs")
				.append("svg:clipPath")
				.attr("id", "clip")
				.append("svg:rect")
				.attr("id", "clip-rect")
				.attr("x", "0")
				.attr("y", "0")
				.attr("width", 50)
				.attr("height", 50)
				.transition()
				.duration(2000)
				.attr("width", 500)
				.attr("height", 500);
			d3.select("svg g").attr('clip-path', 'url(#clip)');
		}
	}

}

//function used to implement the drill-down
BarChart.prototype.addOnClick = function (arrayData, canvas) {
	//HACK: to avoid  context change in closue we store object's reference(this) here.
	//		JavaScript things...
	var currObj = this;
	canvas.selectAll("rect")
		.data(arrayData)
		.on("click", function (pointData) {
			if (pointData.callBack != null && pointData.callBack.length != '') {
				var $container = $(canvas[0]).parent();
				$container.empty();
				$('.tooltipdiv').remove();
				var funCallBack = eval(pointData.callBack);
				funCallBack(pointData, currObj);
				//pushToStack(pointData, currObj);
			}
		});
};



var DEFAULT_PADDING = 50;

function defaultAxis(axisParam) {
	var retval = {};
	addValueForProperty(retval, axisParam, 'showLabel',  true);
	addValueForProperty(retval, axisParam, 'showAxis',  true);
	addValueForProperty(retval, axisParam, 'label',  'X');
	addValueForProperty(retval, axisParam, 'ticks',  10);
	return retval;
}

function addValueForProperty(targetObject, baseObject, property, defaultValue) {
	if (property in baseObject)	{
		targetObject[property] = baseObject[property];
	}
	else {
		targetObject[property] = defaultValue;
	}

}

function createDefaultParamsForGraph(param) {
	if (param.canvas == null) {throw new Error('You need specify canvas configuration parameters.');}
	if (param.graph == null) {throw new Error('You need specify graph configuration parameters.');}
	if (param.canvas.width == null) {throw new Error('No canvas width specified.');}
	if (param.canvas.height == null) {throw new Error('No canvas height specified.');}

    var retval = {
        canvas: {
            width: ("width" in  param.canvas) ? param.canvas.width : 100 ,
            height: ("height" in  param.canvas) ? param.canvas.height : 100 ,
            exportTo: ("exportTo" in  param.canvas) ? param.canvas.exportTo : [],
            stretch: ("stretch" in  param.canvas) ? param.canvas.stretch : true
        },
        graph: {
			allowTransition: ("allowTransition" in  param.graph) ? param.graph.allowTransition : false,
            allowZoom: ("allowZoom" in  param.graph) ? param.graph.allowZoom : false,
            useShadows: ("useShadows" in  param.graph) ? param.graph.useShadows : false,
            showTip: ("showTip" in  param.graph) ? param.graph.showTip : false,
            paddingTop: ("paddingTop" in  param.graph) ? param.graph.paddingTop : 50,
			axisX: ("axisX" in param.graph) ? defaultAxis(param.graph.axisX) : defaultAxis({}),
			axisY: ("axisY" in param.graph) ? defaultAxis(param.graph.axisY) : defaultAxis({}),
			colorPalette: ("colorPalette" in param.graph) ? param.graph.colorPalette : ["#62C1A3", "#FB906B", "#8DA1CB", "#E88AC2", "#E4C18F", "#B3B3B3",  "#3180BA", "#50B14D", "#9A51A4", "#F87709", "#A35920","#A6D954", "#FED92F", "#ED2617"] 
        },
        linesx: true,
        linesy: true
    };
	return retval;
			/*axisX: ("axisX" in param.graph) ? param.graph.axisX : { showAxis: true, label: "X", showLabel: true, ticks: 5 },
			axisY: ("axisY" in param.graph) ? param.graph.axisY : { showAxis: true, label: "Y", showLable: true, ticks: 5 },*/
}
function createDefaultParamsForGraphRign(param) {

	if (param.canvas == null) {throw new Error('You need specify canvas configuration parameters.');}
	if (param.graph == null) {throw new Error('You need specify graph configuration parameters.');}
	if (param.canvas.width == null) {throw new Error('No canvas width specified.');}
	if (param.canvas.height == null) {throw new Error('No canvas height specified.');}

    var retval = {
        canvas: {
            width: ("width" in  param.canvas) ? param.canvas.width : 200 ,
            height: ("width" in  param.canvas) ? param.canvas.height : 200 ,
            exportTo: ("exportTo" in  param.canvas) ? param.canvas.exportTo : [],
            stretch: ("stretch" in  param.canvas) ? param.canvas.stretch : true
        },
        graph: {
        	ringColor :("ringColor" in param.graph) ? param.graph.ringColor : '#74cc84',
        	labelColor :("labelColor" in param.graph) ? param.graph.labelColor : 'red',
        	diameter : ("diameter" in param.graph) ? param.graph.diameter : 200,
        	gapWidth :("gapWidth" in param.graph) ? param.graph.gapWidth :50,
        	useShadows: ("useShadows" in  param.graph) ? param.graph.useShadows : false,
			allowTransition: ("allowTransition" in  param.graph) ? param.graph.allowTransition : false,
            allowZoom: ("allowZoom" in  param.graph) ? param.graph.allowZoom : false
        }
    };
	return retval;
}
function createDefaultParamsForGraphVelocimeter(param) {

	if (param.canvas == null) {throw new Error('You need specify canvas configuration parameters.');}
	if (param.graph == null) {throw new Error('You need specify graph configuration parameters.');}
	if (param.canvas.width == null) {throw new Error('No canvas width specified.');}
	if (param.canvas.height == null) {throw new Error('No canvas height specified.');}

    var retval = {
        canvas: {
            width: ("width" in  param.canvas) ? param.canvas.width : 700 ,
            height: ("width" in  param.canvas) ? param.canvas.height : 200 ,
            exportTo: ("exportTo" in  param.canvas) ? param.canvas.exportTo : [],
            stretch: ("stretch" in  param.canvas) ? param.canvas.stretch : true
        },
        graph: {
        	useShadows: ("useShadows" in  param.graph) ? param.graph.useShadows : false,
            allowZoom: ("allowZoom" in  param.graph) ? param.graph.allowZoom : false
        }
    };
	return retval;
}
function createDefaultParamsForGraphPie(param) {
	if (param.canvas == null) {throw new Error('You need specify canvas configuration parameters.');}
	if (param.graph == null) {throw new Error('You need specify graph configuration parameters.');}
	if (param.canvas.width == null) {throw new Error('No canvas width specified.');}
	if (param.canvas.height == null) {throw new Error('No canvas height specified.');}

	//if (param.graph.axisX == null) {param.graph.axisX = { showAxis: true, label: "X" };}
	//if (param.graph.axisY == null) {param.graph.axisX = { showAxis: true, label: "Y" };}

	

    var retval = {
        canvas: {
            width: ("width" in  param.canvas) ? param.canvas.width : 100 ,
            height: ("height" in  param.canvas) ? param.canvas.height : 100 ,
            exportTo: ("exportTo" in  param.canvas) ? param.canvas.exportTo : [],
            stretch: ("stretch" in  param.canvas) ? param.canvas.stretch : true
        },
        graph: {
			allowTransition: ("allowTransition" in  param.graph) ? param.graph.allowTransition : false,
            //axisX: ("axisX" in  param.graph) ? param.graph.axisX : false,
            //axisY: ("axisY" in  param.graph) ? param.graph.axisY : false,
			allowDrillDown: ("allowDrillDown" in  param.graph) ? param.graph.allowDrillDown : false,
            allowZoom: ("allowZoom" in  param.graph) ? param.graph.allowZoom : false,
            useShadows: ("useShadows" in  param.graph) ? param.graph.useShadows : false,
            showTip: ("showTip" in  param.graph) ? param.graph.showTip : false,
            thickness: ("thickness" in  param.graph) ? param.graph.thickness : 50,
            showLabels: ("showLabels" in  param.graph) ? param.graph.showLabels : false,
            colorPalette: ("colorPalette" in param.graph) ? param.graph.colorPalette : ["#62C1A3", "#FB906B", "#8DA1CB", "#E88AC2", "#E4C18F", "#B3B3B3",  "#3180BA", "#50B14D", "#9A51A4", "#F87709", "#A35920","#A6D954", "#FED92F", "#ED2617"] 
        }
        //linesx: true,
        //linesy: true
    };
	return retval;
}
function createDefaultParamsForLineChart(param) {
	var graphParam = {	
		axisX: ("axisX" in param) ? defaultAxis(param.axisX) : defaultAxis({}),
		axisY: ("axisY" in param) ? defaultAxis(param.axisY) : defaultAxis({})
	};
	addValueForProperty(graphParam, param, 'allowTransition', false);
	addValueForProperty(graphParam, param, 'allowZoom', false);
	addValueForProperty(graphParam, param, 'useShadows', false);
	addValueForProperty(graphParam, param, 'showTip', false);
	addValueForProperty(graphParam, param, 'paddingTop', 0);
	addValueForProperty(graphParam, param, 'area', { visible: false, css: "area"});
	addValueForProperty(graphParam, param, 'marker', { visible: true, ratio: 5, css: "default"});
	addValueForProperty(graphParam, param, 'line', { visible: true, css: "line1"});
	addValueForProperty(graphParam, param, 'gridLinesX', true);
	addValueForProperty(graphParam, param, 'gridLinesY', true);
	addValueForProperty(graphParam, param, 'showErrorBars', false);
	return graphParam;
}

function stretchCanvas(canvas, $container, params) {
	if (params.canvas.stretch) {
		if ($container.width() == null || $container.height() == null) {
			throw new Error('stretchCanvas: The container ' + $container.attr('id') + ' must have a width and height assigned.')
		}
		var widthToUse = ($container.width() == null || $container.width() == 0) ? params.canvas.width : $container.width();
		var heightToUse = ($container.height() == null || $container.height() == 0) ? params.canvas.height : $container.height();



		//for ring
		var diameterToUse = d3.min([widthToUse, heightToUse], 
									function (d) {return d;}
								);
		params.canvas.width = widthToUse;
		params.canvas.height = heightToUse;
		params.graph.diameter = diameterToUse; 
	}

	//TODO a better way is to stretch using SVG native functions. The following code does not work correctly
	/*canvas.attr('width', '100%')
			.attr('height', '98%')
			.attr("viewBox", "0 0 " + .$container.width()+ " " + $containr.height())
			.attr("preserveAspectRatio", "xMidYMid meet")
			.attr("pointer-events", "all");
	return canvas;*/
}

function redrawChart(chart) {
	chart.attr("transform",
		  "translate(" + d3.event.translate + ")"
		  + " scale(" + d3.event.scale + ")");
}

function createCanvas (selectorId, param) {
	d3.select('#'+selectorId).select('svg').remove();
	var canvas = d3.select('#'+selectorId)
					.append('svg') 
					.attr('width', param.width)
					.attr('height', param.height);
	return canvas;
}

function addZoomToCanvas(canvas) {
		/*canvas.call(d3.behavior.zoom().on("zoom", function () {
			  canvas.attr("transform",
			  "translate(" + d3.event.translate + ")"
			  + " scale(" + d3.event.scale + ")");
				}));*/
	var zoom = d3.behavior.zoom()
		    .scaleExtent([1, 3])
		    .on("zoom", function(){
		    	canvas.attr("transform", "translate(" + d3.event.translate + ")scale(" + d3.event.scale + ")");
		    });	
	canvas.call(zoom);
		
}

function addExportOptions(container, exportOptions) {
	var arr = [];
	$(exportOptions).each (function() {
		arr.push({val:this.toLowerCase(), text: this})
	});

	/*var arr = [
	  {val : 'pdf', text: 'PDF'},
	  {val : 'png', text: 'PNG'},
	  {val : 'svg', text: 'SVG'}
	];*/

	var sel = $('<select>').appendTo(container);
	sel.append($("<option>").attr('value','').text('Export To ...'));

	$(arr).each(function() {
		sel.append($("<option>").attr('value',this.val).text(this.text));
	});
	
	sel.css({
				'position': 'absolute',
				'top' : 0,
				'left' : 0
			})

	sel.change(function() {
		var exportType = this.options[this.selectedIndex].value;
		var svgElement = container.get(0).getElementsByTagName('svg')[0];
		var svgSerialized = (new XMLSerializer)
							.serializeToString(svgElement);

		var form = document.getElementById("svgform");
		form['output_format'].value = exportType;
		form['data'].value = svgSerialized ;
		form.submit();

		/*var newForm = jQuery('<form>', {
			'action': 'http://d3export.housegordon.org/download.pl',
			'method': 'post',
			'target': '_top'
		})

		newForm.append(jQuery('<input>', {
			'name': 'output_format',
			'value': exportType,
			'type': 'hidden'
		}));

		newForm.append(jQuery('<input>', {
			'name': 'data',
			'value': svgSerialized,
			'type': 'hidden'
		}));

		newForm.get(0).submit();*/
	});
	container.append(sel);
}

function addDefsSection(canvas) {
	var defs = canvas.select('defs');
	if (defs.empty()) {
		defs = canvas.append('defs');
	}
	return defs;
}

function addGradient(canvas, gradientId) {

	defs = addDefsSection (canvas);

	var gradient = defs.append("linearGradient")
				.attr("id", gradientId)
				.attr("y1", 10)
				.attr("y2", 800)
				.attr("x1", "0")
				.attr("x2", "0")
				.attr("gradientUnits", "userSpaceOnUse");

		gradient.append("stop")
		  .attr("offset", "0")
		  .attr("stop-color", "#99d5cf")

		gradient.append("stop")
			.attr("offset", "0.5")
			.attr("stop-color", "#009688") 
}

function linesX(data,selector,parameter){

	var width = parameter.width;
	var svg = d3.select('#'+selector+' svg');

	var arra = [],
            lens;
        for (key in data) {
            arra.push(key);
        }
        lens = arra.length;
    var barLabelsx = data.map(function(data){
		   return data.datalabel;
		});

    var sx = d3.scale.ordinal().domain(barLabelsx).rangePoints([50, width-50]);

        function make_x_axis() {
            return d3.svg.axis()
                .scale(sx)
                .orient("bottom")
                .ticks(lens)
        }
	
    var group3 = svg.append("g");
	group3.append("g")
            .attr("class", "grid")
            .attr("transform", "translate(0," + 240 + ")")
            .call(make_x_axis()
                .tickSize(-220, 0, 0)
                .tickFormat("")
            );
}

function linesY(data, selector, parameter){

}
/*
function addToolTip(canvas) {
	var div = d3.select("body")
                .append("div")
                .attr("class", "tooltipdiv")
                .style("opacity", 0)
                .style("width","auto")
                .style("height","auto");
    return div;
}*/


/*function addToolTip(canvas, template) {
	if (template === undefined) {
		var template = "<strong>Value:</strong> <span style='color:orange'>%value%</span><br><strong>Datalabel:</strong> <span style='color:orange'>%datalabel%</span>";
	}
	var tip = d3.tip()
		.attr('class', 'd3-tip')
		.offset([-10, 0])
		.html(function(d) {
			var replacements = {'%value%' : d.value, '%datalabel%' : d.datalabel};
			return template.replace(/%\w+%/g, function(all) { return replacements[all] || all; });
		});
	canvas.call(tip);
	return tip;
}*/

function addToolTipPie(canvas) {
	
	var tip = d3.tip()
		.attr('class', 'd3-tip')
		.offset([-10, 0])
		.html(function(d) {
			return "<strong>Value:</strong> <span style='color:#57B1DB'>"+d.data.value+"</span><br><strong>Data Label:</strong> <span style='color:#57B1DB'>"+d.data.label+"</span>";
		});
	canvas.call(tip);
	return tip;
}

function addToolTipPie2D(canvas, stretch) {
	
	var tip = d3.tip()
		.attr('class', 'd3-tip')
		.offset(function () {
          if(stretch) { return [240,0] }
          	else { return [0,0] }
        })
		.html(function(d,i) {
			return "<strong>Value:</strong> <span style='color:#57B1DB'>"+d.value+"</span><br><strong>Data Label:</strong> <span style='color:#57B1DB'>"+d.data.cat+"</span>";
		});
	canvas.call(tip);
	return tip;
}


function addShadow(canvas, shadowHeightPercent, shadowWidth)
{
	var defs = addDefsSection (canvas);

	var filter = defs.append("filter")
	  .attr("id", "drop-shadow")
	  .attr("height", shadowHeightPercent);

	// SourceAlpha refers to opacity of graphic that this filter will be applied to
	// convolve that with a Gaussian with standard deviation 3 and store result
	// in blur
	filter.append("feGaussianBlur")
	  .attr("in", "SourceAlpha")
	  .attr("stdDeviation", shadowWidth)
	  .attr("result", "blur");

	// translate output of Gaussian blur to the right and downwards with 2px
	// store result in offsetBlur
	filter.append("feOffset")
	  .attr("in", "blur")
	  .attr("dx", 1.5)
	  .attr("dy", 2)
	  .attr("result", "offsetBlur");

	// overlay original SourceGraphic over translated blurred opacity by using
	// feMerge filter. Order of specifying inputs is important!
	var feMerge = filter.append("feMerge");

	feMerge.append("feMergeNode")
	  .attr("in", "offsetBlur")
	feMerge.append("feMergeNode")
	  .attr("in", "SourceGraphic");
}


function drawLinesX(data, chart, sx, parameter){
		if (!parameter.graph.gridLinesX) return;
	var DEFAULT_PADDING = 50;
	var totalPaddingTop = DEFAULT_PADDING + parameter.graph.paddingTop;
	var	height = parameter.canvas.height - totalPaddingTop -  DEFAULT_PADDING;
	var width = parameter.canvas.width - 2 * DEFAULT_PADDING;
	var chartRight = width + DEFAULT_PADDING;
	var chartBottom = height + totalPaddingTop;

	var arra = [],
            lens;
        for (key in data) {
            arra.push(key);
        }
        lens = arra.length;
    var barLabelsx = data.map(function(data){
		   return data.datalabel;
		});

	function make_x_axis() {
		return d3.svg.axis()
			.scale(sx)
			.orient("bottom")
			.ticks(parameter.graph.axisX.ticks)
	}
	
    var group3 = chart.append("g");
	group3.append("g")
            .attr("class", "grid")
			.attr("transform", "translate(0,"+totalPaddingTop+")")
            .call(make_x_axis()
                .tickSize(height, 0, 0)
                .tickFormat("")
            );
}

function drawLinesY(data, chart, sy, parameter){
		if (!parameter.graph.gridLinesY) return;
	var DEFAULT_PADDING = 50;
	var totalPaddingTop = DEFAULT_PADDING + parameter.graph.paddingTop;
	var	height = parameter.canvas.height - totalPaddingTop -  DEFAULT_PADDING;
	var width = parameter.canvas.width - 2 * DEFAULT_PADDING;
	var chartRight = width + DEFAULT_PADDING;
	var chartBottom = height + totalPaddingTop;

	var maxValue = d3.max(data,function(d){ return d.value*1.0; });
	/*var sy = d3.scale.linear().domain( [0,maxValue] ).range( [chartBottom,15] ).nice();*/
	function make_y_axis() {
        return d3.svg.axis()
            .scale(sy)
            .orient("left")
            /*.attr('transform',function(d, i){
               	return "translate(0," + (i * 25 + 12) + ")"
            })*/
            .ticks(parameter.graph.axisY.ticks)
    }

    var group3 = chart.append("g");
    group3.append("g")
            .attr("class", "grid")
			.attr("transform", "translate(" + DEFAULT_PADDING + ",0)")
            .call(make_y_axis()
                .tickSize(-width, 0, 0)
                .tickFormat("")
            );
}

function pushToStack(selectedDataPoint, graphObject) {
	var objToAdd = {
		previousDataPoint : selectedDataPoint,
		graph : graphObject
	}
	graphObject.breadCrumbStack.push(objToAdd);
}

function refreshBreadCrumbs(graphObject) {
	//if there is just one element do nothing.
	if (graphObject.breadCrumbStack.length <= 1 ) {
		return;
	}

	graphObject.$container.children('.graph-breadcrumb-div').remove();
	graphObject.$container.append('<div class="graph-breadcrumb-div" style=" height:20px; padding-left:80px;border:1px solid;"></div>');
	var breadCrumbsDiv = graphObject.$container.children('.graph-breadcrumb-div').first();

	for (var i = 0; i < graphObject.breadCrumbStack.length; i++) {
		var item = graphObject.breadCrumbStack[i];
		var $newLink = $(document.createElement("a"));
		$newLink.attr("class", "graph-breadcrumb-link");
		$newLink.text((item.previousDataPoint == null)
						? "init"
						: item.previousDataPoint.datalabel);
		$newLink.attr('href','#');
		$newLink.attr('data-index', i);

		$newLink.click(function() {
			var index = $(this).data('index');
			for (var i = index; i < graphObject.breadCrumbStack.length; i++)  {
				graphObject.breadCrumbStack.pop();
			}
			//getting array's last element
			var element = graphObject.breadCrumbStack.slice(-1)[0];
			element.graph.drawChart();
		});
		$newLink.appendTo(breadCrumbsDiv);
		if (i < graphObject.breadCrumbStack.length -1) {
			breadCrumbsDiv.append(" >> ");
		}
	}
}

//graph dimensions
var GraphDim = function (params) {
	var DEFAULT_PADDING = 50;
	this.top = DEFAULT_PADDING;
	this.left = DEFAULT_PADDING;
	this.height = params.canvas.height - 2 * DEFAULT_PADDING - 20;
	this.width = params.canvas.width - 2 * DEFAULT_PADDING;
	this.bottom = this.height + this.top;
	this.right = this.width + this.left;
};



var LineChart = function (data, params, previousDataPoint, breadcrumbs) {
	this.originalData = data;
	this.previousDataPoint = previousDataPoint;
	this.params = params;
	this.$container = $('#' + this.params.canvas.containerId);
	//Breadcrumb stack:
	this.breadCrumbStack =  (breadcrumbs == null ) ? [] : breadcrumbs;
	pushToStack(previousDataPoint, this);
};

LineChart.prototype.drawChart = function() {
	var $container = $('#' + this.params.canvas.containerId); // canvas[0] to convert d3 to jquery object
	$container.empty();
	$('.tooltipdiv').remove();

	//TODO better if this is done inside the building function(drawLines)
	stretchCanvas(null, this.$container, this.params);

	this.canvas = createCanvas(this.params.canvas.containerId, this.params.canvas);
	this.drawLines(this.originalData, this.canvas, this.params);
	refreshBreadCrumbs(this);
};

LineChart.prototype.addTransitionToCircle = function(circle, ratio) {
	circle.transition()
			.duration(1000)
			.attr({
				r : ratio
			});
};

LineChart.prototype.drawLines = function (data, canvas, param) {
	if (data == null || data.length == 0) {
		this.$container.html( "<div class='pm-charts-no-draw'>No data to draw ...</div>" );
	}

	var parameter = createDefaultParamsForGraph(param);
	//graph part of the parameters passed to this object
	var graphParam = createDefaultParamsForLineChart(param.graph);
	parameter.graph = graphParam;

	//graph Dimensions inside the container
	var graphDim = new GraphDim(param);

	var tooltip = new ToolTip();
	//HACK: to avoid  context change in closure we store object's reference(this) here.
	//		JavaScript things...
	var currObj = this;

	var xScaleLabels = data.map(function(data){
		return data.datalabel;
	});

	var maxValue = d3.max(data,function(d){ return d.value*1.0; });

	var xScale = d3.scale
					.ordinal()
					.domain(xScaleLabels)
					.rangePoints([graphDim.left, graphDim.right], 0);

	var yScale = d3.scale
					.linear()
					.domain([0, maxValue])
					.range([graphDim.bottom, graphDim.top]).nice();

	var chart = canvas.append("g");

	drawAxisX(data, chart, xScale, parameter);
	drawAxisY(data, chart, yScale, parameter);
	drawLinesX(data, chart, xScale, parameter);
	drawLinesY(data, chart, yScale, parameter);

	var area = d3.svg.area()
		.x(function(d) {  return xScale(d.datalabel); })
		.y0(graphDim.bottom)
		.y1(function(d) { return yScale(d.value); });

	var lineForValue = d3.svg.line()
		.x(function(d) { return xScale(d.datalabel); })
		.y(function(d) { return yScale(d.value); });


	if (parameter.graph.allowZoom) { addZoomToCanvas(chart);}

    data.forEach(function(d) {
        d.value = +d.value;
    });

	var data0;

	if (parameter.canvas.exportTo != null && parameter.canvas.exportTo.length > 0) {
		addExportOptions($('#' + this.params.canvas.containerId), this.params.canvas.exportTo);
	}

	if (parameter.graph.allowTransition) {
		data0 = data.map(function (d) { return {datalabel : d.datalabel, value : 0}; });
	}
	else {
		data0 = data;
	}

    // Add the valueline path.
    chart.append("path")
        .attr("d", lineForValue(data0))
		.attr("class", parameter.graph.line.css)
		.transition()
		.duration(3000)
		.attr("d", lineForValue(data));

	if (parameter.graph.area.visible) {
		var pathArea =  chart.append("path");

		pathArea
			.datum(data0)
			.attr("class", parameter.graph.area.css)
			.attr("d", area);
	}

	chart.selectAll("circle")
		.data(data0)
		.enter()
		.append("circle")
		.attr("class", parameter.graph.marker.css)
		.each(function(d){
			d3.select(this).attr({
				cx: xScale(d.datalabel),		
				cy: yScale(d.value),
				 r: parameter.graph.marker.ratio		
			});
		});

	if (parameter.graph.showErrorBars) {
		chart.selectAll(".errorBar")
			.data (data0)
			.enter()
			.append("path")
			.attr("class", "errorBar")
			.each(function (d) {
					var delta = d.dispersion / 2;
					var xVal = xScale(d.datalabel);
					var yVal0 = yScale(d.value - delta);
					var yVal1 = yScale(d.value + delta);
					d3.select(this)
						.attr ("d", "M" + xVal + "," + yVal0 
									+ "L" + xVal + "," + yVal1);
					
			});

		chart.selectAll(".errorBarLowerMark")
			.data (data0)
			.enter()
			.append("path")
			.attr("class", "errorBarLowerMark")
			.each(function (d) {
					var delta = d.dispersion / 2;
					var xVal = xScale(d.datalabel);
					var yVal0 = yScale(d.value - delta);
					var yVal1 = yScale(d.value + delta);
					d3.select(this)
						.attr ("d", "M" + (xVal - 5) + "," + yVal0 
								+ "L" + (xVal + 5) + "," + yVal0)
			});

		chart.selectAll(".errorBarUpperMark")
			.data (data0)
			.enter()
			.append("path")
			.attr("class", "errorBarUpperMark")
			.each(function (d) {
					var delta = d.dispersion / 2;
					var xVal = xScale(d.datalabel);
					var yVal0 = yScale(d.value - delta);
					var yVal1 = yScale(d.value + delta);
					d3.select(this)
						.attr ("d", "M" + (xVal - 5) + "," + yVal1 
							+ "L" + (xVal + 5) + "," + yVal1)
			});
		
	}

	if (parameter.graph.allowTransition) {
		if (parameter.graph.area.visible) {
			pathArea
				.datum(data)
				.transition()
				.duration(3000)
				.attr("d", area);
		}

		chart.selectAll("circle")
			.data(data)
			.each(function(d){
				d3.select(this)
				.transition()
				.duration(3000)
				.attr('class', parameter.graph.marker.css)
				.attr({
					cx: xScale(d.datalabel),		
					cy: yScale(d.value)
				});
			});
	}

		//colocamos este código después de la transición para asegurarnos
		//que se usan los datos correctos.
		if (parameter.graph.showTip) {
			chart.selectAll('circle')
				.data(data)
				.on('mouseover', function (d) {
					tooltip.show(function () {
						if (parameter.graph.showErrorBars)
							return {value: d.value + ' (sdv = ' + d.dispersion + ')', datalabel: d.datalabel}
						else
							return {value: d.value, datalabel: d.datalabel}
					});
					currObj.addTransitionToCircle(d3.select(this), parameter.graph.marker.ratio);
				 })
				.on('mouseout', function () {
					currObj.addTransitionToCircle(d3.select(this), parameter.graph.marker.ratio);
					if(parameter.graph.showTip) {
						tooltip.hide();
					}
				 })
		}
	
		if (this.params.graph.allowDrillDown) {
			this.addOnClick(data, canvas);
			if (this.breadCrumbStack.length > 0) {
				var clip = chart.append("defs")
					.append("svg:clipPath")
					.attr("id", "clip")
					.append("svg:rect")
					.attr("id", "clip-rect")
					.attr("x", "0")
					.attr("y", "0")
					.attr("width", 50)
					.attr("height", 50)
					.transition()
					.duration(2000)
					.attr("width", 500)
					.attr("height", 500);
				d3.select("svg g").attr('clip-path', 'url(#clip)');
			}
		}
};

//function used to implement the drill-down
LineChart.prototype.addOnClick = function (arrayData, canvas) {
	//HACK: to avoid  context change in closue we store object's reference(this) here.
	//		JavaScript things...
	var currObj = this;
	canvas.selectAll("circle")
		.data(arrayData)
		.on("click", function (pointData) {
			if (pointData.callBack != null && pointData.callBack.length != '') {
				var $container = $(canvas[0]).parent();
				$container.empty();
				$('.d3-tip').remove();
				var funCallBack = eval(pointData.callBack);
				funCallBack(pointData, currObj);
			}
		});
};


var PieChart = function (data, params, previousDataPoint, breadcrumbs) {
    this.originalData = data;
    this.previousDataPoint = previousDataPoint;
    this.params = params;
    this.$container = $('#' + this.params.canvas.containerId);
    //Breadcrumb stack:
    this.breadCrumbStack =  (breadcrumbs == null ) ? [] : breadcrumbs;
    pushToStack(previousDataPoint, this);
};

PieChart.prototype.drawChart = function () {
    var $container = $('#' + this.params.canvas.containerId); // canvas[0] to convert d3 to jquery object
    $container.empty();
    $('.tooltipdiv').remove();

	//TODO better if this is done inside the building function(drawLines)
	stretchCanvas(null, this.$container, this.params);

    this.canvas = createCanvas(this.params.canvas.containerId, this.params.canvas);
    this.drawPie2D(this.originalData, this.canvas, this.params);
    refreshBreadCrumbs(this);
};

PieChart.prototype.drawPie2D = function (dataset, canvas, param) {
	if (dataset == null || dataset.length == 0) {
		this.$container.html( "<div class='pm-charts-no-draw'>No data to draw ...</div>" );
	}

    var parameter = createDefaultParamsForGraphPie(param);
    var width = parameter.canvas.width,
        height = parameter.canvas.height;

    var tooltip = new ToolTip();

    if (parameter.graph.showLabels) {
        height = height - 50;
        width = width - 150;
    }

    var margin = 50,
        radius = Math.min(width - margin, height - margin) / 2,
    // Pie layout will use the "val" property of each data object entry
        pieChart = d3.layout.pie().sort(null).value(function (d) {
            return d.value;
        }),
        arc = d3.svg.arc().outerRadius(radius);
    var colors2 = parameter.graph.colorPalette;


    // Synthetic data generation ------------------------------------------------
    var data = Data(dataset);

    function Data(data) {
        return data.map(function (d, i) {
            var newcolor;
            if (i == parameter.graph.colorPalette.length) {
                newcolor = "#000000";
            } else {
                newcolor = colors2[i % parameter.graph.colorPalette.length];
            }
            var children = [];
            var color = colors2[i];
/*            children.push({
                datalabel: "datalabel" + ((i + 1) * 100 + i),
                value: Math.random(),
                color: d3.rgb(color).darker(1 / (i + 1))
            });*/
            return {
                datalabel: (d.datalabel),
                value: (d.value * 1),
                //color: colors2[i%(parameter.graph.colorPalette.length-1)],
                color: newcolor,
                children: children,
                callBack: d.callBack
            };
        });
    }

    var totalValues = 0;


    for (var i = 0; i < data.length; i++) {
        totalValues = totalValues + data[i].value;
    }


    // --------------------------------------------------------------------------
    // SVG elements init
    //var svg = d3.select("body").append("svg").attr("width", width).attr("height", height),
    var chart = canvas.append('g');
    var svg = chart,
        defs = svg.append("svg:defs"),
    // Declare a main gradient with the dimensions for all gradient entries to refer
        mainGrad = defs.append("svg:radialGradient")
            .attr("gradientUnits", "userSpaceOnUse")
            .attr("cx", 0).attr("cy", 0).attr("r", radius).attr("fx", 0).attr("fy", 0)
            .attr("id", "master")
    // The pie sectors container
    arcGroup = svg.append("svg:g")
        .attr("class", "arcGroup")
        //.attr("filter", "url(#shadow)")
        .attr("transform", "translate(" + (width / 2) + "," + (height / 2) + ")"),
        // Header text
        header = svg.append("text").text("")
            .attr("transform", "translate(10, 20)").attr("class", "header");


    if (parameter.graph.allowZoom) {
        addZoomToCanvas(chart);
    }


    // Redraw the graph given a certain level of data

    function updateGraph(currObject, datalabel) {
        var currData = data;

        // Simple header text
        if (datalabel != undefined) {
            currData = findChildenByCat(datalabel);
            d3.select(".header").text("");
        } else {
            d3.select(".header").text("");
        }

        // Create a gradient for each entry (each entry identified by its unique category)
        var gradients = defs.selectAll(".gradient").data(currData, function (d) {
            return d.datalabel;
        });
        gradients.enter().append("svg:radialGradient")
            .attr("id", function (d, i) {
                return "gradient" + d.datalabel;
            })
            .attr("class", "gradient")
            .attr("xlink:href", "#master");

        gradients.append("svg:stop").attr("offset", "0%").attr("stop-color", getColor);
        gradients.append("svg:stop").attr("offset", "90%").attr("stop-color", getColor);
        gradients.append("svg:stop").attr("offset", "100%").attr("stop-color", getDarkerColor);


        // Create a sector for each entry in the enter selection
        var paths = arcGroup.selectAll("path")
            .data(pieChart(currData), function (d) {
                return d.data.datalabel;
            });

        paths.enter().append("svg:path").attr("class", "sector");


        if (currObject.params.graph.allowDrillDown) {
            currObject.addOnClick(data, paths, canvas);
            if (currObject.breadCrumbStack.length > 0) {
                var clip = chart.append("defs")
                    .append("svg:clipPath")
                    .attr("id", "clip")
                    .append("svg:rect")
                    .attr("id", "clip-rect")
                    .attr("x", "0")
                    .attr("y", "0")
                    .attr("width", 50)
                    .attr("height", 50)
                    .transition()
                    .duration(2000)
                    .attr("width", 500)
                    .attr("height", 500);
                d3.select("svg g").attr('clip-path', 'url(#clip)');
            }
        }

        // Each sector will refer to its gradient fill
        paths.attr("fill", function (d, i) {
            return "url(#gradient" + d.data.datalabel + ")";
        })
            .transition().duration(1000).attrTween("d", tweenIn).each("end", function () {
                this._listenToEvents = true;
            });

        // Mouse interaction handling
        /*
         paths.on("click", function(d){
         if(this._listenToEvents){
         // Reset inmediatelly
         d3.select(this).attr("transform", "translate(0,0)")
         // Change level on click if no transition has started
         paths.each(function(){
         this._listenToEvents = false;
         });
         updateGraph(d.data.children? d.data.datalabel : undefined);
         }
         })
         */
        paths.on("mouseover", function (d) {
            // Mouseover effect if no transition has started
            if (this._listenToEvents) {
                // Calculate angle bisector
                var ang = d.startAngle + (d.endAngle - d.startAngle) / 2;
                // Transformate to SVG space
                ang = (ang - (Math.PI / 2)) * -1;

                // Calculate a 10% radius displacement
                var x = Math.cos(ang) * radius * 0.1;
                var y = Math.sin(ang) * radius * -0.1;

                d3.select(this).transition()
                    .duration(250).attr("transform", "translate(" + x + "," + y + ")");
            }

            if (parameter.graph.showTip) {
                tooltip.show(function () {
                    return {
                        value: d.value,
                        datalabel: d.data.datalabel
                    }
                });
            }
        })
            .on("mouseout", function (d) {
                // Mouseout effect if no transition has started                
                if (this._listenToEvents) {
                    d3.select(this).transition()
                        .duration(150).attr("transform", "translate(0,0)");
                }
                if (parameter.graph.showTip) {
                    tooltip.hide();
                }
            });

        // Collapse sectors for the exit selection
        paths.exit().transition()
            .duration(1000)
            .attrTween("d", tweenOut).remove();
    }

    // "Fold" pie sectors by tweening its current start/end angles
    // into 2*PI
    function tweenOut(data) {
        data.startAngle = data.endAngle = (2 * Math.PI);
        var interpolation = d3.interpolate(this._current, data);
        this._current = interpolation(0);
        return function (t) {
            return arc(interpolation(t));
        };
    }


    // "Unfold" pie sectors by tweening its start/end angles
    // from 0 into their final calculated values
    function tweenIn(data) {
        var interpolation = d3.interpolate({
            startAngle: 0,
            endAngle: 0
        }, data);
        this._current = interpolation(0);
        return function (t) {
            return arc(interpolation(t));
        };
    }


    // Helper function to extract color from data object
    function getColor(data, index) {
        return data.color;
    }


    // Helper function to extract a darker version of the color
    function getDarkerColor(data, index) {
        return d3.rgb(getColor(data, index)).darker(0.7);
    }


    function findChildenByCat(datalabel) {
        for (i = -1; i++ < data.length - 1;) {
            if (data[i].datalabel == datalabel) {
                return data[i].children;
            }
        }
        return data;
    }

    function getPercent(d) {

        return (Math.round(100 * d / totalValues * 10) / 10 + '%');
    }

    // Start by updating graph at root level
    updateGraph(this);

    if (parameter.graph.showLabels) {
        var thickness = 119 * Math.log(parameter.canvas.height) - 645;
        var group5 = chart.append("g")
            .attr("class", "group5")
            .attr("transform", "translate(0," + thickness + ")");
        for (var i = 0; i < data.length; i++) {
            var newcolor;
            if (i == parameter.graph.colorPalette.length) {
                newcolor = "#000000";
            } else {
                newcolor = colors2[i % parameter.graph.colorPalette.length];
            }
            group5.append("circle")
                .attr("r", 9)
                .attr("fill", newcolor)
                .attr("cx", width)
                .attr("cy", (i * 25));
        }

        group5.selectAll("text")
            .data(data)
            .enter()
            .append("text")
            .attr("x", width + 30)
            .text(function (d, i) {
                return (d.datalabel + "-" + getPercent(d.value))
            })
            .attr("transform", function (d, i) {
                return "translate(0," + (i * 25 + 5) + ")"
            });
    }

}

//function used to implement the drill-down
PieChart.prototype.addOnClick = function (arrayData, paths, canvas) {
    //HACK: to avoid  context change in closue we store object's reference(this) here.
    //		JavaScript things...
    var currObj = this;
    //paths.enter().append("svg:path").attr("class", "sector");
    //canvas.selectAll("path.sector")
    paths.on("click", function (pieData) {
        //piedData has the data encapsulated inside the data property.
        var pointData = pieData.data;
        if (pointData.callBack != null && pointData.callBack.length != '') {
            var $container = $(canvas[0]).parent();
            $container.empty();
            $('.tooltipdiv').remove();
            var funCallBack = eval(pointData.callBack);
            funCallBack(pointData, currObj);
        }
    });
};

var Pie3DChart = function (data, params, previousDataPoint, breadcrumbs) {
    this.originalData = data;
    this.previousDataPoint = previousDataPoint;
    this.params = params;
    this.$container = $('#' + this.params.canvas.containerId);
    //Breadcrumb stack:
    this.breadCrumbStack =  (breadcrumbs == null ) ? [] : breadcrumbs;
    pushToStack(previousDataPoint, this);
};

Pie3DChart.prototype.drawChart = function () {
    var $container = $('#' + this.params.canvas.containerId); // canvas[0] to convert d3 to jquery object
    $container.empty();
    $('.tooltipdiv').remove();
	
	//TODO better if this is done inside the building function(drawLines)
	stretchCanvas(null, this.$container, this.params);

    this.canvas = createCanvas(this.params.canvas.containerId, this.params.canvas);
    this.drawPie3D(this.originalData, this.canvas, this.params);
    refreshBreadCrumbs(this);
};


Pie3DChart.prototype.drawPie3D = function (data, canvas, param) {

	if (data == null || data.length == 0) {
		this.$container.html( "<div class='pm-charts-no-draw'>No data to draw ...</div>" );
	}

    var duration_transition = 0;
    var parameter = createDefaultParamsForGraphPie(param);
    var totalValues = 0;
    var h = parameter.canvas.height,
        w = parameter.canvas.width

    for (var i = 0; i < data.length; i++) {
        totalValues = totalValues + data[i].value * 1;
    }

    if (parameter.graph.showLabels) {
        h = h - 50;
        w = w - 150;
    }

    var x_center = w / 2;
    var y_center = (h / 2 - 50);
    var rx = w / 2 - parameter.graph.thickness;
    var ry = h / 2 / 2;

    var color = parameter.graph.colorPalette;
    var chart = canvas.append('g')
        .attr("transform", "translate(0,0)");

    var group4 = chart.append("g")
        .attr("class", "group4")
        .attr("id", "salesDonut")
        .attr("transform", "translate(7,0)");

    if (parameter.graph.allowTransition) {
        var duration_transition = 100;
    }

    var tooltip = null;
    if (parameter.graph.showTip) {
        tooltip = new ToolTip();
    }


    Donut3D.draw("salesDonut", Data(), x_center, y_center, rx, ry, param.graph.thickness,
                param.graph.gapWidth, duration_transition,
                tooltip, parameter, canvas, this);



    function getPercent(d) {
        return (Math.round(100 * d / totalValues * 10) / 10 + '%');
    }

    function Data() {
        return data.map(function (d, i) {
            var newcolor;
            if (i == parameter.graph.colorPalette.length) {
                newcolor = "#000000";
            } else {
                newcolor = color[i % parameter.graph.colorPalette.length];
            }
            return {
                label: (d.datalabel),
                datalabel: (d.datalabel),
                value: (d.value * 1),
                callBack: d.callBack,
                //color: (color[i%(parameter.graph.colorPalette.length)])
                color: newcolor
            };
        });
    }


    if (parameter.graph.allowZoom) {
        addZoomToCanvas(chart);
    }

    if (parameter.graph.showLabels) {

        var thickness = 119 * Math.log(parameter.canvas.height) - 645;

        var group5 = chart.append("g")
            .attr("class", "group5")
            .attr("transform", "translate(0," + thickness + ")");

        for (var i = 0; i < data.length; i++) {
            var newcolor;
            if (i == parameter.graph.colorPalette.length) {
                newcolor = "#000000";
            } else {
                newcolor = color[i % parameter.graph.colorPalette.length];
            }
            group5.append("circle")
                .attr("r", 9)
                //.attr("height", 15)
                .attr("fill", newcolor)
                .attr("cx", w)
                .attr("cy", (i * 25));
            //.attr("transform", "translate("+param.canvas.width+"," + (i * 25) + ")");
        }

        group5.selectAll("text")
            .data(data)
            .enter()
            .append("text")
            .attr("x", w + 30)
            .attr("class", "pie-label")
            //.attr("y",i*10+50)
            .text(function (d, i) {
                return d.datalabel +  " - " + getPercent(d.value * 1)
            })
            .on("mouseover", function (d, i) {
                d3.select("#salesDonut")
                    .select(".topSlice" + i)
                    .style("fill", d3.hsl("#E87886").darker(0.8))
                    .style("stroke", d3.hsl("#E87886").darker(0.8));
            })
            .on("mouseout", function (d, i) {

                var newcolor;
                if (i == parameter.graph.colorPalette.length) {
                    newcolor = "#000000";
                } else {
                    newcolor = color[i % parameter.graph.colorPalette.length];
                }

                d3.select("#salesDonut")
                    .select(".topSlice" + i)
                    .style("fill", d3.hsl(newcolor))
                    .style("stroke", d3.hsl(newcolor));
            })
            .attr("transform", function (d, i) {
                return "translate(0," + (i * 25 + 5) + ")"
            });

    }


    if (parameter.graph.useShadows) {
        addShadow(group4, "130%", 5);
        group4.select('.slices')
            .attr("filter", "url(#drop-shadow)");
    }
}


var RingChart = function (data, params, previousDataPoint, breadcrumbs) {
    this.originalData = data;
    this.previousDataPoint = previousDataPoint;
    this.params = params;
    this.$container = $('#' + this.params.canvas.containerId);
    //Breadcrumb stack:
    this.breadCrumbStack =  (breadcrumbs == null ) ? [] : breadcrumbs;
    pushToStack(previousDataPoint, this);
};

RingChart.prototype.drawChart = function () {
    var $container = $('#' + this.params.canvas.containerId); // canvas[0] to convert d3 to jquery object
    $container.empty();
    $('.tooltipdiv').remove();

	//TODO better if this is done inside the building function(drawLines)
	stretchCanvas(null, this.$container, this.params);

    this.canvas = createCanvas(this.params.canvas.containerId, this.params.canvas);
    this.drawRing(this.originalData, this.canvas, this.params);
    refreshBreadCrumbs(this);
};

RingChart.prototype.drawRing = function(data, canvas, param){
	if (data == null || data.length == 0) {
		this.$container.html( "<div class='pm-charts-no-draw'>No data to draw ...</div>" );
	}

	//d3.select('#'+parent).select('svg').remove();
    var parameter = createDefaultParamsForGraphRign(param);
    var h = parameter.canvas.height,
        w = parameter.canvas.width;
    var value = data[0].value;
	var ringColor = parameter.graph.ringColor;
	var labelColor = parameter.graph.labelColor;
	var label = data[0].datalabel;
	var diameter1 = parameter.graph.diameter;

    var currObject = this;

    if (parameter.graph.allowZoom) { addZoomToCanvas(canvas); }

    var rp1 = radialProgress(canvas)
				//alert(diameter);
                .label(label)
                //.onClick(onClick1)
                .diameter(diameter1)
                .value(value)
                .render();

	function radialProgress(canvas) {
    var _data=null,
        _duration= 0,
        _selection,
        _margin = {top:20, right:0, bottom:30, left:20},
        __width = parameter.graph.diameter,
        __height = parameter.graph.diameter,
        _diameter,
        _label="",
        _fontSize=10;

    if(parameter.graph.allowTransition){_duration= 1000;}


    var _mouseClick;

    var _value= 0,
        _minValue = 0,
        _maxValue = 100;

    var  _currentArc= 0, _currentArc2= 0, _currentValue=0;

    var _arc = d3.svg.arc()
        .startAngle(0 * (Math.PI/180)); //just radians

    var _arc2 = d3.svg.arc()
        .startAngle(0 * (Math.PI/180))
        .endAngle(0); //just radians


    _selection=canvas;


    function component() {

        _selection.each(function (data) {

            // Select the svg element, if it exists.
            var svg = d3.select(this).selectAll("svg").data([data]);
            var enter = svg.enter().append("svg").attr("class","radial-svg").append("g");

            measure();
            
            /*if (parameter.graph.useShadows){
                addShadow(enter, "130%", 2);
            }*/

            svg.attr("width", __width)
                .attr("height", __height);


            var background = enter.append("g").attr("class","component")
                .attr("cursor","pointer");
                //.on("click",onMouseClick);


            _arc.endAngle(360 * (Math.PI/180))

            background.append("rect")
                .attr("class","background")
                .attr("width", _width)
                .attr("height", _height);

            background.append("path")
                .attr("transform", "translate(" + _width/2 + "," + _width/2 + ")")
                .attr("d", _arc);

			if (currObject.params.graph.showLabel) {
				background.append("text")
					.attr("class", "label")
					.attr("transform", "translate(" + _width/2 + "," + (_height + 25) + ")")
					.attr("fill", labelColor)
					.text(_label);
			}


           var g = svg.select("g")
                .attr("transform", "translate(" + _margin.left + "," + _margin.top + ")");


            _arc.endAngle(_currentArc);
            enter.append("g").attr("class", "arcs");
            var path = svg.select(".arcs").selectAll(".arc").data(data);
            path.enter().append("path")
                .attr("class","arc")
                .attr("fill", ringColor)
                //.style("filter", "url(#drop-shadow-ring)")
                .attr("transform", "translate(" + _width/2 + "," + _width/2 + ")")
                .attr("d", _arc)
                .on ('click', function (){
                    if (currObject.params.graph.allowDrillDown) {
                        var pointData = currObject.originalData[0];
                        if (pointData.callBack != null && pointData.callBack.length != '') {
                            var $container = $(canvas[0]).parent();
                            $container.empty();
                            $('.tooltipdiv').remove();
                            var funCallBack = eval(pointData.callBack);
                            funCallBack(pointData, currObject);
                        }

                        if (currObject.breadCrumbStack.length > 0) {
                            var clip = canvas.append("defs")
                                .append("svg:clipPath")
                                .attr("id", "clip")
                                .append("svg:rect")
                                .attr("id", "clip-rect")
                                .attr("x", "0")
                                .attr("y", "0")
                                .attr("width", 50)
                                .attr("height", 50)
                                .transition()
                                .duration(2000)
                                .attr("width", 500)
                                .attr("height", 500);
                            d3.select("svg g").attr('clip-path', 'url(#clip)');
                        }
                    }
                });

            

            //Another path in case we exceed 100%
            var path2 = svg.select(".arcs").selectAll(".arc2").data(data);
            path2.enter().append("path")
                .attr("class","arc2")
                //.style("filter", "url(#drop-shadow)")
                .attr("transform", "translate(" + _width/2 + "," + _width/2 + ")")
                .attr("d", _arc2);


            enter.append("g").attr("class", "labels");
            var label = svg.select(".labels").selectAll(".label").data(data);
            label.enter().append("text")
                .attr("class","label")
                .attr("y",_width/2+_fontSize/3)
                .attr("x",_width/2)
                .attr("cursor","pointer")
                .attr("width",_width)
                // .attr("x",(3*_fontSize/2))
                .text(function (d) { return Math.round((_value-_minValue)/(_maxValue-_minValue)*100) + "%" })
                .style("font-size",_fontSize+"px")
                .on("click",onMouseClick);

            	
                if(parameter.graph.useShadows)
                {
                addShadow(enter, "150%", 5);
                
                //----------------------From Internet Explorer 10
                var path1 = svg.selectAll('.arc')
                .attr("filter", "url(#drop-shadow)");
                //svg.select(".labels").selectAll('.label')
                //.style("text-shadow", "5px 4px 4px black"); 
                //---------------------------------------------End
                }
                

            path.exit().transition().duration(500).attr("x",1000).remove();


            layout(svg);

            function layout(svg) {

                var ratio=(_value-_minValue)/(_maxValue-_minValue);
                var endAngle=Math.min(360*ratio,360);
                endAngle=endAngle * Math.PI/180;

                path.datum(endAngle);
                path.transition().duration(_duration)
                    .attrTween("d", arcTween);

                if (ratio > 1) {
                    path2.datum(Math.min(360*(ratio-1),360) * Math.PI/180);
                    path2.transition().delay(_duration).duration(_duration)
                        .attrTween("d", arcTween2);
                }

                label.datum(Math.round(ratio*100));
                label.transition().duration(_duration)
                    .tween("text",labelTween);

            }

        });

        function onMouseClick(d) {
            if (typeof _mouseClick == "function") {
                //DL: original call back function commented
                //_mouseClick.call();
                alert(1);
            }
        }
    }

    function labelTween(a) {
        var i = d3.interpolate(_currentValue, a);
        _currentValue = i(0);

        return function(t) {
            _currentValue = i(t);
            this.textContent = Math.round(i(t)) + "%";
        }
    }

    function arcTween(a) {
        var i = d3.interpolate(_currentArc, a);

        return function(t) {
            _currentArc=i(t);
            return _arc.endAngle(i(t))();
        };
    }

    function arcTween2(a) {
        var i = d3.interpolate(_currentArc2, a);

        return function(t) {
            return _arc2.endAngle(i(t))();
        };
    }


    function measure() {
        _width=_diameter - _margin.right - _margin.left - _margin.top - _margin.bottom;
        //_width = _diameter;
        _height=_width;
        _fontSize=_width*.2;
        _arc.outerRadius(_width/2);
        _arc.innerRadius(_width/2 * (parameter.graph.gapWidth/100));
        _arc2.outerRadius(_width/2 * .85);
        _arc2.innerRadius(_width/2 * .85 - (_width/2 * .15));
    }


    component.render = function() {
        measure();
        component();
        return component;
    }

    component.value = function (_) {
        if (!arguments.length) return _value;
        _value = [_];
        _selection.datum([_value]);
        return component;
    }


    component.margin = function(_) {
        if (!arguments.length) return _margin;
        _margin = _;
        return component;
    };

    component.diameter = function(_) {
        if (!arguments.length) return _diameter
        _diameter =  _;
        return component;
    };

    component.minValue = function(_) {
        if (!arguments.length) return _minValue;
        _minValue = _;
        return component;
    };

    component.maxValue = function(_) {
        if (!arguments.length) return _maxValue;
        _maxValue = _;
        return component;
    };

    component.label = function(_) {
        if (!arguments.length) return _label;
        _label = _;
        return component;
    };

    component._duration = function(_) {
        if (!arguments.length) return _duration;
        _duration = _;
        return component;
    };

    component.onClick = function (_) {
        if (!arguments.length) return _mouseClick;
        _mouseClick=_;
        return component;
    }

    return component;

}


}

var ToolTip = function (template) {
    this.template = template;
    if (template == null) {
       this.template = "<strong>Value: </strong><span style='color:orange'>%value%</span><br/><strong>Data Label: </strong><span style='color:orange'>%datalabel%</span>";
    }
    this.div = d3.select("body")
                    .append("div")
                    .attr("class", "tooltipdiv")
                    .style("opacity", 0)
                    .style("width","auto")
                    .style("height","auto");
}

ToolTip.prototype.show = function (funPointData)  {
    var replacements = {'%value%' : funPointData().value, '%datalabel%' : funPointData().datalabel};
    var tipHtml = this.template.replace(/%\w+%/g, function(all) { return replacements[all] || all;});

    this.div
        .transition()
        .duration(200)
        .style("opacity", .9);

    this.div
        .html(tipHtml)
        .style("left", (d3.event.pageX-50) + "px")
        .style("top", (d3.event.pageY-55) + "px");
}

ToolTip.prototype.hide = function (){
    this.div
        .transition()
        .duration(500)
        .style("opacity", 0);
}



function drawVelocimeter(selector,param){
	window.onload=function(){
    
    var gauges = [];
    //var dashContainer;
    var readings = [];	// pretend readings are supplied (named by gauge).
    var i = 0;
    var interv0 = 0;
    var xDim = 0;

            var greenColor = "#107618";
            var yellowColor = "#FFC900";
            var redColor = "#EC4922";
            var darkColor = "#101010";
            var blueColor = "#1030B0";
            var dimBlueColor = "#101560";
            var lightColor = "#EEEEEE";
	    var greyColor = "303030";
	    var darkGreyColor = "101010";
	    var blackColor = "000000";
	    var lightBlueColor = "7095F0";

    //InitDim();
    createDashboard(selector, param);
    //interv0 = setInterval(updateGauges, 1000);	// set a basic interval period

    function createDashboard(selector, param) {
        createDash(selector, param);
        createGauge(dashContainer, 25, "inbox", "Inbox", 72, 145,90, {
            from: 0, to: 25 }, {
            from: 25, to: 50 }, {
            from: 50, to: 100});
        createGauge(dashContainer, 50, "cases", "Cases", 72, 505,90, {// third is +size bias.
            from: 0, to: 25 }, {
            from: 25, to: 50 }, {
	        from: 50, to: 100});
        createGauge(dashContainer, 100, "drafts", "Drafts", 72, 860,90, {
            from: 0, to: 25 }, {
            from: 25, to: 50 }, {
            from: 50, to: 100});
    }

    function updateGauges() {
        if (i >= 0) {	// initially use a faster interval and sweep the gauge
            {
                for (var key in gauges) {
                    gauges[key].redraw(i);
                }
                if (i === 0) {
                    clearInterval(interv0);
                    interv0 = setInterval(updateGauges, 75);
                }
                i = i + 5;
                if (i > 100) {
                    i = -1;
                    clearInterval(interv0);
                    interv0 = setInterval(updateGauges, 1000);	// restore a normal interval
                }
            }
        } else {
	        // pass a data array to dashboard.js's UpdateDashboard(values for named gauges)
            for (var key in gauges) {
		readings[key] = readings[key] + 10*Math.random()-5;
		if (readings[key]<0)
			readings[key] = 0;
		if (readings[key]>100)
			readings[key] = 100;
                gauges[key].redraw(readings[key]);
            }
        }
    }

    // code below here could go in a dashboard.js

    function dimChange() {
        dimDash(this.selectedIndex);
        for (var key in gauges) {
            gauges[key].dimDisplay(this.selectedIndex);	// just use the index; could use the indexed entry value.
        }
    }

    function InitDim() {
        var dimOptions = { "Day": 0, "Night": 1 };

        var selectUI = d3.select("#dimmable").append("form").append("select").on("change", dimChange);
        selectUI.selectAll("option").data(d3.keys(dimOptions)).enter().append("option").text(function (d) { return d; });

        selectUI.selectAll("option").data(d3.values(dimOptions)).attr("value", function (d) { return d; });

        var checkOption = function (e) {
            if (e === xDim) { return d3.select(this).attr("selected", "selected"); }
        };

        selectUI.selectAll("option").each(checkOption);
    }

    // some of createGauge is specific to the example (size=120), some belongs in Gauge.
    function createDash(selector, param)
    {
        if (param.canvas.stretch) {
            this.body = d3.select("#"+selector)
                .append("svg:svg")
                .attr('width', '100%')
                .attr('height', '98%')
                .attr("viewBox", "0 0 " + param.canvas.width + " " + 180)
                .attr("preserveAspectRatio", "xMidYMid meet")
                .attr("pointer-events", "all");

        }else{
            this.body = d3.select("#"+selector)
                .append("svg:svg")
                .attr("class", "dash")
                .attr("width", param.canvas.width)//this.config.size)
                .attr("height", param.canvas.height);// this.config.size);
        } 

        dashContainer =  this.body.append("svg:g").attr("class", "dashContainer")
                .attr("width",404)
                .attr("height",202);

        if (param.graph.allowZoom) {
            addZoomToCanvas(dashContainer);
        }
    }

    function dimDash(value) {
            var dasharea =d3.select("#dashboardContainer").selectAll("ellipse");
            dasharea.style("fill",value<0.5 ? blueColor: dimBlueColor);
    }

    function createGauge(myContainer, value, name, label, sizebias, containerOffsetx, containerOffsety, redZone, yellowZone, greenZone) {
        var config = {
            size: 120 + sizebias,
	    cx: containerOffsetx,
	    cy: containerOffsety,
            label: label,
            minorTicks: 5
        };

        config.redZones = [];	// allows for example upper and lower limit zones
        config.redZones.push(redZone);

        config.yellowZones = [];
        config.yellowZones.push(yellowZone);

        config.greenZones = [];
        config.greenZones.push(greenZone);
        
        gauges[name] = new Gauge(myContainer, name, config,value);
        gauges[name].render();
	   readings[name] = 50;
    }

    // code from gauge.js, below
    //
    function Gauge(myContainer, name, configuration, value) {
        this.name = name;
	    this.myContainer = myContainer;

        var self = this; // some internal d3 functions do not "like" the "this" keyword, hence setting a local variable

        this.configure = function (configuration) {
            this.config = configuration;

            this.config.size = this.config.size * 0.9;

            this.config.raduis = this.config.size * 0.97 / 2;
            this.config.cx = this.config.cx;// + this.config.size / 4;
            this.config.cy = this.config.cy;// + this.config.size / 2;

            this.config.min = configuration.min || 0;
            this.config.max = configuration.max || 100;
            this.config.range = this.config.max - this.config.min;

            this.config.majorTicks = configuration.majorTicks || 5;
            this.config.minorTicks = configuration.minorTicks || 2;

            this.config.bezelColor = configuration.bezelColor || lightColor;
            this.config.bezelDimColor = configuration.bezelDimColor || greyColor;
            this.config.greenColor = configuration.greenColor || greenColor;
            this.config.yellowColor = configuration.yellowColor || yellowColor;
            this.config.redColor = configuration.redColor || redColor;
            this.config.faceColor = configuration.faceColor || lightColor;
            this.config.dimFaceColor = configuration.dimFaceColor || darkGreyColor;
            this.config.lightColor = configuration.lightColor || "#EEEEEE";
            this.config.greyColor = configuration.greyColor || "101010";
            this.config.lightBlueColor = configuration.lightBlueColor || "6085A0";

        };
        //alert(value);
        
        this.render = function () {
            this.body = this.myContainer//dashContainer//d3.select("#" + this.placeholderName)
                .append("svg:svg")
                .attr("class", "gauge")
                .attr("x", this.myContainer.x)//this.config.cx-this.config.size/4)
                .attr("y", this.myContainer.y)//this.config.cy-this.config.size/4)
                .attr("width", this.myContainer.width)//this.config.size)
                .attr("height", this.myContainer.height)//this.config.size);
  
            this.body.append("svg:circle")	// outer shell
                .attr("cx", this.config.cx)
                .attr("cy", this.config.cy)
                .attr("r", this.config.raduis)
                .style("fill", "#ccc")
                .style("stroke", blackColor )
                .style("stroke-width", "0.5px");

            this.body.append("svg:circle")	// bezel
                .attr("cx", this.config.cx)
                .attr("cy", this.config.cy)
                .attr("r", 0.9 * this.config.raduis)
                .style("fill", (xDim < 0.5 ? this.config.bezelColor : this.config.bezelDimColor))
                .style("stroke", "#e0e0e0")
                .style("stroke-width", "2px");

            var faceContainer = this.body.append("svg:g").attr("class", "faceContainer");	// for day/night changes
            var bandsContainer = this.body.append("svg:g").attr("class", "bandsContainer");	// for day/night changes
            var ticksContainer = this.body.append("svg:g").attr("class", "ticksContainer");	// for day/night changes
            this.redrawDimmableFace(xDim);//0);

            var pointerContainer = this.body.append("svg:g").attr("class", "pointerContainer");
            //alert(value);
            this.drawPointer(value);
            pointerContainer.append("svg:circle")
                .attr("cx", this.config.cx)
                .attr("cy", this.config.cy)
                .attr("r", 0.12 * this.config.raduis)
                .style("fill", "#4684EE")
                .style("stroke", "#666")
                .style("opacity", 1);
        };

	this.drawBands = function(bandsContainer) { 
            for (var index in this.config.greenZones) {
                this.drawBand(bandsContainer,this.config.greenZones[index].from, this.config.greenZones[index].to, self.config.greenColor);
            }

            for (var index in this.config.yellowZones) {
                this.drawBand(bandsContainer,this.config.yellowZones[index].from, this.config.yellowZones[index].to, self.config.yellowColor);
            }

            for (var index in this.config.redZones) {
                this.drawBand(bandsContainer,this.config.redZones[index].from, this.config.redZones[index].to, self.config.redColor);
            }
	};

        this.redrawDimmableFace = function (value) {
            this.drawFace(value < 0.5 ? self.config.faceColor : self.config.dimFaceColor,	// facecolor
			  value < 0.5 ? self.config.greyColor : lightBlueColor);
        }

        this.drawTicks = function (ticksContainer,color) {

            var fontSize = Math.round(this.config.size / 16);
            var majorDelta = this.config.range / (this.config.majorTicks - 1);
            for (var major = this.config.min; major <= this.config.max; major += majorDelta) {
                var minorDelta = majorDelta / this.config.minorTicks;
                for (var minor = major + minorDelta; minor < Math.min(major + majorDelta, this.config.max); minor += minorDelta) {
                    var minorpoint1 = this.valueToPoint(minor, 0.75);
                    var minorpoint2 = this.valueToPoint(minor, 0.85);

		    ticksContainer.append("svg:line")
                        .attr("x1", minorpoint1.x)
                        .attr("y1", minorpoint1.y)
                        .attr("x2", minorpoint2.x)
                        .attr("y2", minorpoint2.y)
                        .style("stroke", color)
                        .style("stroke-width", "1px");
                }

                var majorpoint1 = this.valueToPoint(major, 0.7);
                var majorpoint2 = this.valueToPoint(major, 0.85);

		ticksContainer.append("svg:line")
                    .attr("x1", majorpoint1.x)
                    .attr("y1", majorpoint1.y)
                    .attr("x2", majorpoint2.x)
                    .attr("y2", majorpoint2.y)
                    .style("stroke", color)
                    .style("stroke-width", "2px");

                if (major == this.config.min || major == this.config.max) {
                    var point = this.valueToPoint(major, 0.63);

		    ticksContainer.append("svg:text")
                        .attr("x", point.x)
                        .attr("y", point.y)
                        .attr("dy", fontSize / 3)
                        .attr("text-anchor", major == this.config.min ? "start" : "end")
                        .text(major)
                        .style("font-size", fontSize + "px")
                        .style("fill", color)
                        .style("stroke-width", "0px");
                }
            }
        };


        this.redraw = function (value) {
            this.drawPointer(value);
        };

        this.dimDisplay = function (value) {
            this.redrawDimmableFace(value);
        };

        this.drawBand = function (bandsContainer, start, end, color) {
            if (0 >= end - start) return;

            bandsContainer.append("svg:path")
                .style("fill", color)
                .attr("d", d3.svg.arc()
                .startAngle(this.valueToRadians(start))
                .endAngle(this.valueToRadians(end))
                .innerRadius(0.70 * this.config.raduis)
                .outerRadius(0.85 * this.config.raduis))
                .attr("transform", function () {
                return "translate(" + self.config.cx + ", " + self.config.cy + ") rotate(270)";
            });
        };

        this.drawFace = function (colorFace,colorTicks) {
            var arc0 = d3.svg.arc()
                .startAngle(0) //this.valueToRadians(0))
                .endAngle(2 * Math.PI)
                .innerRadius(0.00 * this.config.raduis)
                .outerRadius(0.9 * this.config.raduis);

            var faceContainer = this.body.selectAll(".faceContainer");
            var bandsContainer = this.body.selectAll(".bandsContainer");
            var ticksContainer = this.body.selectAll(".ticksContainer");
            var pointerContainer = this.body.selectAll(".pointerContainer");
            var face = faceContainer.selectAll("path");
            if (face == 0)
	    {
                faceContainer
                  .append("svg:path")
                  .attr("d", arc0) //d3.svg.arc()
                  .style("fill", colorFace)
                  .style("fill-opacity", 0.7)
                  .attr("transform",
                      "translate(" + self.config.cx + ", " + self.config.cy + ")");

		this.drawBands(bandsContainer);
	        this.drawTicks(ticksContainer,colorTicks);
                var fontSize = Math.round(this.config.size / 9);
                faceContainer.append("svg:text")
                    .attr("x", this.config.cx)
                    .attr("y", this.config.cy - this.config.size/6 - fontSize / 2 )
                    .attr("dy", fontSize / 2)
                    .attr("text-anchor", "middle")
                    .text(this.config.label)
                    .style("font-size", fontSize + "px")
                    .style("fill", colorTicks)
                    .style("stroke-width", "0px");
	    }
            else
	    {
                face.style("fill", colorFace);
		var facetxt = faceContainer.selectAll("text");
		facetxt.style("fill", colorTicks);
        var ptrtxt = pointerContainer.selectAll("text");
        ptrtxt.style("fill", colorTicks);
		var ticks = ticksContainer.selectAll("line");
		ticks.style("stroke", colorTicks);
		var texts = ticksContainer.selectAll("text");
		texts.style("fill", colorTicks);
        
	    }
	};

        this.drawPointer = function (value) {
            var delta = this.config.range / 13;

            var head = this.valueToPoint(value, 0.85);
            var head1 = this.valueToPoint(value - delta, 0.12);
            var head2 = this.valueToPoint(value + delta, 0.12);

            var tailValue = value - (this.config.range * (1 / (270 / 360)) / 2);
            var tail = this.valueToPoint(tailValue, 0.28);
            var tail1 = this.valueToPoint(tailValue - delta, 0.12);
            var tail2 = this.valueToPoint(tailValue + delta, 0.12);

            var data = [head, head1, tail2, tail, tail1, head2, head];

            var line = d3.svg.line()
                .x(function (d) {
                return d.x;
            })
                .y(function (d) {
                return d.y;
            })
                .interpolate("basis");

            var pointerContainer = this.body.select(".pointerContainer");

            var pointer = pointerContainer.selectAll("path").data([data]);

            pointer.enter()
                .append("svg:path")
                .attr("d", line)
                .style("fill", "#dc3912")
                .style("stroke", "#c63310")
                .style("fill-opacity", 0.7);

            pointer.transition()
                .attr("d", line)
            //.ease("linear")
            .duration(i>=0 ? 50 : 500);

            var fontSize = Math.round(this.config.size / 10);
            pointerContainer.selectAll("text")
                .data([value])
                .text(Math.round(value))
                .enter()
                .append("svg:text")
                .attr("x", this.config.cx)
                .attr("y",  this.config.cy + this.config.size/6 + fontSize)
                .attr("dy", fontSize / 2)
                .attr("text-anchor", "middle")
                .text(Math.round(value))
                .style("font-size", fontSize + "px")
                .style("fill", "#000")
                .style("stroke-width", "0px");
        };

        this.valueToDegrees = function (value) {
            return value / this.config.range * 270 - 45;
        };

        this.valueToRadians = function (value) {
            return this.valueToDegrees(value) * Math.PI / 180;
        };

        this.valueToPoint = function (value, factor) {
            var len = this.config.raduis * factor;
            var inRadians = this.valueToRadians(value);
            var point = {
                x: this.config.cx - len * Math.cos(inRadians),
                y: this.config.cy - len * Math.sin(inRadians)
            };

            return point;
        };

        // initialization
        this.configure(configuration);
    }

}
}

!function(){
	var Donut3D={};
	
	function pieTop(d, rx, ry, ir ){
		d.endAngle = d.endAngle - 0.001;
		if(d.endAngle - d.startAngle == 0 ) return "M 0 0";
		var sx = rx*Math.cos(d.startAngle),
			sy = ry*Math.sin(d.startAngle),
			ex = rx*Math.cos(d.endAngle),
			ey = ry*Math.sin(d.endAngle);
			
		var ret =[];
		ret.push("M",sx,sy,"A",rx,ry,"0",(d.endAngle-d.startAngle > Math.PI? 1: 0),"1",ex,ey,"L",ir*ex,ir*ey);
		ret.push("A",ir*rx,ir*ry,"0",(d.endAngle-d.startAngle > Math.PI? 1: 0), "0",ir*sx,ir*sy,"z");
		return ret.join(" ");
	}

	function pieOuter(d, rx, ry, h ){
		var startAngle = (d.startAngle > Math.PI ? Math.PI : d.startAngle);
		var endAngle = (d.endAngle > Math.PI ? Math.PI : d.endAngle);
		
		var sx = rx*Math.cos(startAngle),
			sy = ry*Math.sin(startAngle),
			ex = rx*Math.cos(endAngle),
			ey = ry*Math.sin(endAngle);
			
			var ret =[];
			ret.push("M",sx,h+sy,"A",rx,ry,"0 0 1",ex,h+ey,"L",ex,ey,"A",rx,ry,"0 0 0",sx,sy,"z");
			return ret.join(" ");
	}

	function pieInner(d, rx, ry, h, ir ){
		var startAngle = (d.startAngle < Math.PI ? Math.PI : d.startAngle);
		var endAngle = (d.endAngle < Math.PI ? Math.PI : d.endAngle);
		
		var sx = ir*rx*Math.cos(startAngle),
			sy = ir*ry*Math.sin(startAngle),
			ex = ir*rx*Math.cos(endAngle),
			ey = ir*ry*Math.sin(endAngle);

			var ret =[];
			ret.push("M",sx, sy,"A",ir*rx,ir*ry,"0 0 1",ex,ey, "L",ex,h+ey,"A",ir*rx, ir*ry,"0 0 0",sx,h+sy,"z");
			return ret.join(" ");
	}

	function getPercent(d){
		return (d.endAngle-d.startAngle > 0.2 ? 
				Math.round(1000*(d.endAngle-d.startAngle)/(Math.PI*2))/10+'%' : '');
	}	
	
	Donut3D.transition = function(id, data, rx, ry, h, ir){
		function arcTweenInner(a) {
		  var i = d3.interpolate(this._current, a);
		  this._current = i(0);
		  return function(t) { return pieInner(i(t), rx+0.5, ry+0.5, h, ir);  };
		}
		function arcTweenTop(a) {
		  var i = d3.interpolate(this._current, a);
		  this._current = i(0);
		  return function(t) { return pieTop(i(t), rx, ry, ir);  };
		}
		function arcTweenOuter(a) {
		  var i = d3.interpolate(this._current, a);
		  this._current = i(0);
		  return function(t) { return pieOuter(i(t), rx-.5, ry-.5, h);  };
		}
		function textTweenX(a) {
		  var i = d3.interpolate(this._current, a);
		  this._current = i(0);
		  return function(t) { return 0.6*rx*Math.cos(0.5*(i(t).startAngle+i(t).endAngle));  };
		}
		function textTweenY(a) {
		  var i = d3.interpolate(this._current, a);
		  this._current = i(0);
		  return function(t) { return 0.6*rx*Math.sin(0.5*(i(t).startAngle+i(t).endAngle));  };
		}
		
		var _data = d3.layout.pie().sort(null).value(function(d) {return d.value;})(data);
		
		d3.select("#"+id).selectAll(".innerSlice").data(_data)
			.transition().duration(750).attrTween("d", arcTweenInner); 
			
		d3.select("#"+id).selectAll(".topSlice").data(_data)
			.transition().duration(750).attrTween("d", arcTweenTop); 
			
		d3.select("#"+id).selectAll(".outerSlice").data(_data)
			.transition().duration(750).attrTween("d", arcTweenOuter); 	
			
		d3.select("#"+id).selectAll(".percent").data(_data).transition().duration(750)
			.attrTween("x",textTweenX).attrTween("y",textTweenY).text(getPercent); 	
	}
	
	Donut3D.draw=function(id, data, x /*center x*/, y/*center y*/, 
			rx/*radius x*/, ry/*radius y*/, h/*height*/, ir/*inner radius*/, dt/*duration transition*/,
			tip/*tooltip*/, parameter /*chart conf. parameters*/,
			canvas /*place where the pie is drawn*/, currObj /*Pie3D object*/){
	
		var _data = d3.layout.pie().sort(null).value(function(d) {return d.value;})(data);
		
		var slices = d3.select("#"+id).append("g").attr("transform", "translate(" + x + "," + y + ")")
			.attr("class", "slices");
			
		slices.selectAll(".innerSlice").data(_data).enter().append("path")
			.transition().delay(function(d, i) { return i * dt; }).duration(dt*5)
			.attr("class", "innerSlice")
			.style("fill", function(d) { return d3.hsl(d.data.color).darker(0.7); })
			.attr("d",function(d){ return pieInner(d, rx+0.5,ry+0.5, h, ir);})
			.each(function(d){this._current=d;});

		//.on('mouseover', tip.show)
		//	.on('mouseout', tip.hide)
		// TODO set tooltip here
		slices.selectAll(".topSlice").data(_data).enter().append("path")
			.on('mouseover', function (d) {
				if (parameter.graph.showTip) {
					tip.show(function () {
						return {
							value: d.data.value,
							datalabel: d.data.datalabel
						}
					});
				}
			})
			.on('mouseout', function () {
				if (parameter.graph.showTip) {
					tip.hide();
				}
			})
			.on('click', function (d) {
				if (parameter.graph.allowDrillDown) {
					var pointData = d.data;
					if (pointData.callBack != null && pointData.callBack.length != '') {
						var $container = $(canvas[0]).parent();
						$container.empty();
						$('.tooltipdiv').remove();
						var funCallBack = eval(pointData.callBack);
						funCallBack(pointData, currObj);
					}
				}
			})
			.transition().delay(function(d, i) { return i * dt; }).duration(dt*5)
			.attr("class", function(d,i){return "topSlice"+i})
			.style("fill", function(d) { return d.data.color; })
			.style("stroke", function(d) { return d.data.color; })
			.attr("d",function(d){ return pieTop(d, rx, ry, ir);})			
			.each(function(d){this._current=d;});
		
		slices.selectAll(".outerSlice").data(_data).enter().append("path")
			.transition().delay(function(d, i) { return i * dt; }).duration(dt*5)
			.attr("class", "outerSlice")
			.style("fill", function(d) { return d3.hsl(d.data.color).darker(0.7); })
			.attr("d",function(d){ return pieOuter(d, rx-.5,ry-.5, h);})
			.each(function(d){this._current=d;});

		slices.selectAll(".percent").data(_data).enter().append("text")
			.transition().delay(function(d, i) { return i * dt; }).duration(dt*5)
			.attr("class", "percent")
			.attr("x",function(d){ return 0.6*rx*Math.cos(0.5*(d.startAngle+d.endAngle));})
			.attr("y",function(d){ return 0.6*ry*Math.sin(0.5*(d.startAngle+d.endAngle));})
			.text(getPercent).each(function(d){this._current=d;});				

	}
	
	this.Donut3D = Donut3D;
}();

