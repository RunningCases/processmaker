    var peiParams = {
        canvas : {
            containerId:'proEfficGenGraph',
            width:300,
            height:300,
            stretch:true
        },
        graph: {
            allowDrillDown:false,
            allowTransition:true,
            showTip: true,
            allowZoom: true, //verificar navegadores...
            gapWidth:0.2,
            useShadows: true, //for Firefox and Chrome
            thickness: 30,
            showLabels: true,
            colorPalette: ['#5486bf','#bf8d54','#acb30c','#7a0c0c','#bc0000','#906090','#007efb','#62284a','#0c7a7a']
        }
    };

    var ueiParams = {
        canvas : {
            containerId:'proEfficGenGraph',
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
            axisX:{ showAxis: true, label: G_STRING.ID_GROUPS },
            axisY:{ showAxis: true, label: G_STRING.ID_TIME_HOURS },
            showErrorBars: true
        }
    };


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
            axisY:{ showAxis: true, label: "Q" },
            gridLinesX:false,
            gridLinesY:true,
            showTip: true,
            allowZoom: true,
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
            axisY:{ showAxis: true, label: "Q" },
            gridLinesX:false,
            gridLinesY:true,
            showTip: true,
            allowZoom: true,
            useShadows: true,
            paddingTop: 50,
            colorPalette: ['#5486bf','#bf8d54','#acb30c','#7a0c0c','#bc0000','#906090','#007efb','#62284a','#0c7a7a','#74a9a9']
        }
    };
    var peiDetailParams = {
        canvas : {
            containerId:'proEfficGraph',
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
                showErrorBars: true

        }
    };

    var ueiDetailParams = {
        canvas : {
            containerId:'proEfficGraph',
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
                axisX:{ showAxis: true, label: G_STRING.ID_USERS },
                axisY:{ showAxis: true, label: G_STRING.ID_TIME_HOURS },
                showErrorBars: true

        }
    };

    //Adding data to
    function animateprogress (id, index, comparative, name, indUid, direction){
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

      if(name.length>20){
          name = name.substring( -20, 20 );
          name = name+"...";
      }      

      index = validaNull(index);

      document.getElementById(id+"Huge"+indUid).innerHTML = index;
      document.getElementById(id+"Small"+indUid).innerHTML = comparative;
      document.getElementById(id+"Span"+indUid).innerHTML = name;

      if(id == "proEffic" || id == "userEffic"){
        if(comparative<0){
          if(id == "proEffic"){
            $(document.getElementsByClassName("proGreen")).removeClass("panel-green").addClass("panel-red");
            $(document.getElementsByClassName("up")).removeClass("fa-chevron-up").addClass("fa-chevron-down");
          }
        } else if(comparative>=0){
          $(document.getElementsByClassName("proRed")).removeClass("panel-red").addClass("panel-green");
          if(comparative==0){
              $(document.getElementsByClassName("down")).removeClass("fa-chevron-down").addClass("fa-circle-o");
              $(document.getElementsByClassName("up")).removeClass("fa-chevron-up").addClass("fa-circle-o");
          } else {
              $(document.getElementsByClassName("down")).removeClass("fa-chevron-down").addClass("fa-chevron-up");
          }
        }
      }

      if(id == "generalGreat" || id == "generalLow"){
          var animacion = function () {
              var comp = parseInt(comparative);
              $(document.getElementById(id+indUid)).attr('aria-valuemax', comp);
              var indexToPaint = index*100/comp;
              if (i<=indexToPaint) 
              {
                  $(document.getElementById(id+indUid)).css('width', i+'%').attr('aria-valuenow', i);
                  i++;
                  fpAnimationFrame(animacion);
              }
              
              if(j<=index){
                  document.getElementById(id+"Huge"+indUid).innerHTML = j+"%";
                  j++;
                  fpAnimationFrame(animacion);
              }
              
              var direc = (direction == "1")? "<" : ">";
              if(id == "generalLow"){
                  document.getElementById(id+"Small"+indUid).innerHTML = "Goal "+direc+" "+comparative+"%";
              } else{//si esq es positivo mostramos Well Done y la clase q setea las letras en blanco
                  document.getElementById(id+"Small"+indUid).innerHTML = "("+direc+" "+comparative+" %) "+ G_STRING.ID_WELL_DONE;
              }
          }
        fpAnimationFrame(animacion); 
      }
    };
    
    //Button by dashbutton
    var dasButton = '<div class="btn-group pull-left"><button id="favorite" type="button" class="btn btn-success"><i class="fa fa-star fa-1x"></i></button><button id="dasB" type="button" class="btn btn-success">'+ G_STRING.ID_MANAGERS_DASHBOARDS +'</button></div>';

    //Items by each type:
    var proEffic = '<div class="col-lg-3 col-md-6 dashPro" id="proEfficItem" data-gs-min-width="3" data-gs-min-height="2" data-gs-max-height="2">\
                        <div class="proGreen panel panel-green grid-stack-item-content" style="min-width: 200px;">\
                            <a data-toggle="collapse" href="#efficiencyindex" aria-expanded="false" aria-controls="efficiencyindex">\
                                <div class="panel-heading">\
                                    <div class="row">\
                                        <div class="col-xs-3">\
                                            <div id="proEfficHuge" class="huge">1.22</div>\
                                        </div>\
                                        <div class="col-xs-9 text-right"><i class="up fa fa-chevron-up fa-3x"></i>\
                                            <div id="proEfficSmall" class="small">+ 1.5</div>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="panel-footer panel-active text-center" id="proEfficM"><span id="proEfficSpan">'+ G_STRING.ID_PRO_EFFICIENCY_INDEX +'</span></div>\
                            </a>\
                        </div>\
                    </div>';

    var userEffic = '<div class="col-lg-3 col-md-6 dashUsr" id="userEfficItem" data-gs-min-width="3" data-gs-min-height="2" data-gs-max-height="2">\
                        <div class="proRed panel panel-red grid-stack-item-content" style="min-width: 200px;">\
                            <a data-toggle="collapse" href="#userefficiency" aria-expanded="false" aria-controls="userefficiency">\
                                <div class="panel-heading">\
                                    <div class="row">\
                                        <div class="col-xs-3">\
                                            <div id="userEfficHuge" class="huge">0.8</div>\
                                        </div>\
                                        <div class="col-xs-9 text-right"><i class="down fa fa-chevron-down fa-3x"></i>\
                                            <div id="userEfficSmall" class="small">- 0.5</div>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="panel-footer text-center" id="userEfficM"><span id="userEfficSpan">'+ G_STRING.ID_EFFICIENCY_USER +'</span></div>\
                            </a>\
                        </div>\
                    </div>';

    var compCases = '<div class="col-lg-3 col-md-6" id="generalLowItem" data-gs-min-width="3" data-gs-min-height="2" data-gs-max-height="2">\
                        <div class="panel ie-panel panel-primary grid-stack-item-content" style="min-width: 200px;">\
                            <a data-toggle="collapse" href="#completedcases" aria-expanded="false" aria-controls="completedcases">\
                                <div class="panel-heading">\
                                    <div class="row">\
                                        <div class="col-xs-3">\
                                            <div id="generalLowHuge" class="huge">26%</div>\
                                        </div>\
                                        <div class="col-xs-9 text-right"><i class="fa fa-file-text-o fa-3x"></i>\
                                            <div id="generalLowSmall" class="small">Goal 100%</div>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="progress progress-xs progress-dark-base ie-progress-dark-base mar-no">\
                                    <div id="generalLow" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar-light" style="width: 0%"></div>\
                                </div>\
                                <div class="panel-footer ie-panel-footer text-center" id="generalLowM"><span id="generalLowSpan">'+ G_STRING.ID_COMPLETED_CASES +'</span></div>\
                            </a>\
                        </div>\
                    </div>';

    var numCases = '<div class="col-lg-3 col-md-6" id="generalGreatItem" data-gs-min-width="3" data-gs-min-height="2" data-gs-max-height="2">\
                        <div class="panel ie-panel panel-yellow grid-stack-item-content" style="min-width: 200px;">\
                            <a data-toggle="collapse" href="#numbercases" aria-expanded="false" aria-controls="numbercases">\
                                <div class="panel-heading">\
                                    <div class="row">\
                                        <div class="col-xs-3">\
                                            <div id="generalGreatHuge" class="huge">95%</div>\
                                        </div>\
                                        <div class="col-xs-9 text-right"><i class="fa fa-trophy fa-3x"></i>\
                                            <div class="small" id="generalGreatSmall">'+ G_STRING.ID_WELL_DONE +'</div>\
                                        </div>\
                                    </div>\
                                </div>\
                                <div class="progress progress-xs progress-dark-base ie-progress-dark-base mar-no">\
                                    <div id="generalGreat" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" class="progress-bar progress-bar-light" style="width: 0%"></div>\
                                </div>\
                                <div class="panel-footer ie-panel-footer text-center" id="generalGreatM"><span id="generalGreatSpan">'+ G_STRING.ID_NUMBER_CASES +'</span></div>\
                            </a>\
                        </div>\
                    </div>';

    //Data by Indicator elements:
    var proEfficDataGen = '<div class="process-div well" id="proEfficiencyData" data-gs-no-resize="true"  style="clear:both;position:relative;height:auto;">\
								<div class="panel-heading greenbg"><span id="proEfficTitle"> '+ G_STRING.ID_PRO_EFFICIENCY_INDEX +' </span></div>\
								<div class="text-center huge">\
									<div class="col-xs-3 vcenter">\
										<div id="proEfficIndex" class="green">26%</div>\
										<div class="small grey">'+ G_STRING.ID_EFFICIENCY_INDEX +'</div>\
									</div>\
									<div class="col-xs-3 vcenter">\
										<div id="proEfficCost" class="red">$1813.50</div>\
										<div class="small grey">'+ G_STRING.ID_INEFFICIENCY_COST +'</div>\
									</div>\
									<div class="col-xs-6" id="proEfficGenGraph" style="width:500px;height:300px; margin-left:80px;"><img src="../dist/img/graph.png" /></div>\
								</div>\
								<div class="clearfix"></div>\
							</div>';

    var proEfficData = '<div class="process-div well" id="proEfficiencyData" data-gs-no-resize="true" style="clear:both;position:relative;">\
							<div class="panel-heading greenbg">\
								<ol class="breadcrumb">\
									<li><a id="link" href="javascript:back();"><i class="fa fa-chevron-left fa-fw"></i><span id="proEfficTitle"> '+ G_STRING.ID_PRO_EFFICIENCY_INDEX +' </span></a></li>\
									<li id="proDetName">Process 1 name</li>\
								</ol>\
							</div>\
							<div class="text-center huge">\
								<div class="col-xs-3 vcenter">\
									<div id="proEfficIndex" class="green">26%</div>\
									<div class="small grey">'+ G_STRING.ID_EFFICIENCY_INDEX +'</div>\
								</div>\
								<div class="col-xs-3 vcenter">\
									<div id="proEfficCost" class="red">$1813.50</div>\
									<div class="small grey">'+ G_STRING.ID_INEFFICIENCY_COST +'</div>\
								</div>\
								<div class="col-xs-6" id="proEfficGraph" style="width:570px; height:300px; margin-left:70px; "><img src="../dist/img/graph.png" /></div>\
							</div>\
							<div class="clearfix"></div>\
						</div>';

    var proEfficDetail = '<div id="process" class="process-div well hideme" data-gs-no-resize="true" style="clear:both;position:relative;">\
                                <div class="col-lg-12 vcenter-task">\
                                    <a href="#" class="process-button">\
                                        <div class="col-xs-3 text-left title-process">\
                                            <div id="procGreyTitle" class="small grey">Process name 1</div>\
                                        </div>\
                                        <div class="col-xs-3 text-center ">\
                                            <div id="proIndex" class="blue">4.3 Days</div>\
                                            <div class="small grey">'+ G_STRING.ID_EFFICIENCY_INDEX +'</div>\
                                        </div>\
                                        <div class="col-xs-3 text-center ">\
                                            <div id="proCost" class="blue">1.3 Days</div>\
                                            <div class="small grey">'+ G_STRING.ID_EFFICIENCY_COST +'</div>\
                                        </div>\
                                        <div class="col-xs-3 text-right arrow"><i class="fa fa-chevron-right fa-fw"></i></div>\
                                    </a>\
                                </div>\
                                <div class="clearfix"></div>\
                            </div>';

    var proEfficTaskDetail = '<div id="task" class="process-div well hideme" data-gs-no-resize="true">\
                                <div class="panel-heading greenbg">\
                                    <div class="col-xs-12 text-center"><i class="fa fa-tasks fa-fw"></i> <span id="taskName">Task 1</span> </div>\
                                    <div class="clearfix"></div>\
                                </div>\
                                <div class="text-center huge">\
                                    <div class="col-xs-12 vcenter-task">\
                                        <div class="col-xs-4 ">\
                                            <div id="taskEffic" class="blue">0.95</div>\
                                            <div class="small grey fontMedium">'+ G_STRING.ID_EFFICIENCY_COST +'</div>\
                                        </div>\
                                        <div class="col-xs-4 ">\
                                            <div id="taskAver" class="blue">4.3</div>\
                                            <div class="small grey">Average Time</div>\
                                        </div>\
                                        <div class="col-xs-4 ">\
                                            <div id="taskDeviat" class="blue">1.3</div>\
                                            <div class="small grey">Deviation</div>\
                                        </div>\
                                    </div>\
                                    <div class="clearfix"></div>\
                                </div>\
                            </div>';

    var userTaskDetail = '<div id="user" class="process-div well hideme" data-gs-no-resize="true">\
                                <div class="panel-heading greenbg">\
                                    <div class="col-xs-12 text-center"><i class="fa fa-tasks fa-fw"></i> <span id="usrName">Task 1</span> </div>\
                                    <div class="clearfix"></div>\
                                </div>\
                                <div class="text-center huge">\
                                    <div class="col-xs-12 vcenter-task">\
                                        <div class="col-xs-5 ">\
                                            <div id="usrEffic" class="blue">0.95</div>\
                                            <div class="small grey fontMedium">'+ G_STRING.ID_EFFICIENCY_INDEX +'</div>\
                                        </div>\
                                        <div class="col-xs-7 "></div>\
                                        <div class="col-xs-7 ">\
                                            <div id="usrCost" class="blue">1.3</div>\
                                            <div class="small grey">'+ G_STRING.ID_INEFFICIENCY_COST +'</div>\
                                        </div>\
                                    </div>\
                                    <div class="clearfix"></div>\
                                </div>\
                            </div>';

    var generalDataLow = '<div class="process-div well" data-gs-no-resize="true" style="clear:both;position:relative;height:auto;">\
							<div class="panel-heading bluebg">\
								<ol class="breadcrumb">\
									<li id="generalLowTitle">'+ G_STRING.ID_COMPLETED_CASES +'</li>\
								</ol>\
							</div>\
							<div class="text-center huge">\
								<div class="col-xs-6" id="generalGraph1" style="width:600px; height:300px;"><img src="../dist/img/graph.png" /></div>\
								<div class="col-xs-6" id="generalGraph2" style="width:600px; height:300px;margin-left:60px;"><img src="../dist/img/graph.png" /></div>\
							</div>\
							<div class="clearfix"></div>\
						</div>';

    var generalDataGreat = '<div class="process-div well" data-gs-no-resize="true" style="clear:both;position:relative;height:auto;">\
								<div class="panel-heading yellowbg">\
									<ol class="breadcrumb">\
										<li id="generalGreatTitle">'+ G_STRING.ID_NUMBER_CASES +'</li>\
									</ol>\
								</div>\
								<div class="text-center huge">\
									<div class="col-xs-6" id="generalGraph1" style="width:600px; height:300px;"><img src="../dist/img/graph.png" /></div>\
									<div class="col-xs-6" id="generalGraph2" style="width:600px; height:300px; margin-left:60px;"><img src="../dist/img/graph.png" /></div>\
								</div>\
								<div class="clearfix"></div>\
							</div>';

    var oType;
    var actualDashId; 
 
    //fecha actual
    var date = new Date();
    var dateMonth = date.getMonth();
    var dateYear = date.getFullYear();
 
    var dateActual = "01-"+(dateMonth+1)+"-"+dateYear;
    var dateActualEnd = "30-"+(dateMonth)+"-"+dateYear;
    
    function validaNull(val){
        if(val === null || val == undefined || val == "?"){
            val = "?";
        } else {
            val = (parseFloat(val)).toFixed(2);
        }
        return val;
    };
 
    function back(){
        if(oType=="proEffic"){
          var oID = $('.dashPro').attr("id");
          var oIDs = oID.split('Item');
          var id = oIDs[0];
          var uid = oIDs[1];

          if($('.proGreen').hasClass('panel-red')){
              var comparative = -1;
          } else if($('.proRed').hasClass('panel-green')){
              var comparative = +1;
          }

          proxy.peiData(uid, dateActual, dateActualEnd,
                      function(dataIndicator){
                          indicatorsData(dataIndicator, "proEffic", uid, comparative );
                      });
        } else if(oType == "userEffic"){
          var oID = $('.dashUsr').attr("id");
          var oIDs = oID.split('Item');
          var id = oIDs[0];
          var uid = oIDs[1];

          if($('.proRed').hasClass('panel-red')){
              var comparative = -1;
          } else if($('.proRed').hasClass('panel-green')){
              var comparative = +1;
          }
          
          proxy.ueiData(uid, dateActual, dateActualEnd,
                      function(dataIndicator){
                          indicatorsData(dataIndicator, "userEffic", uid, comparative);
                      });
        }
    };

    /***** Adding Data by indicator *****/
    function indicatorsData(dataIndicator, type, indUid, comparative){
        $('#indicatorsDataGridStack').gridstack();
        var gridIndicators = $('#indicatorsDataGridStack').data('gridstack'),
        widgetDetailDom;
        gridIndicators.remove_all();

        var gridProcess = $('#relatedDataGridStack').data('gridstack');
        gridProcess.remove_all();

        oType = type;
        switch (type) {
          case 'proEffic': //Process Efficience Index 
          case 'userEffic':
              var widget = proEfficDataGen;
              var widgetDetail = proEfficDetail;
              type = 'proEffic';
              break;
          case 'generalGreat':
              var widget = generalDataGreat;
              var widgetDetail = "";
              break;
          case 'generalLow':
              var widget = generalDataLow;
              var widgetDetail = "";
              break;
        }

        //Drawing
        gridIndicators.add_widget($(widget), 0, 15, 20, 4.7, true); //General data

        if(oType == "proEffic" || oType == "userEffic"){
          if(comparative<0){
              $(document.getElementsByClassName("greenbg")).removeClass("greenbg").addClass("redbg");
              $(document.getElementsByClassName("green")).removeClass("green").addClass("red");
          } else if(comparative>=0) {
              $(document.getElementsByClassName("redbg")).removeClass("redbg").addClass("greenbg");
          }
        }

        if (oType == "generalGreat" || oType == "generalLow") {
            document.getElementById("relatedLabel").innerHTML = "";
            var graph1 = null;
            if (dataIndicator.graph1Type == '10') {
                generalBarParams1.graph.axisX.label = dataIndicator.graph1XLabel;
                generalBarParams1.graph.axisY.label = dataIndicator.graph1YLabel;
                graph1 = new BarChart(dataIndicator.graph1Data, generalBarParams1, null, null);
            } else {
                generalLineParams1.graph.axisX.label = dataIndicator.graph1XLabel;
                generalLineParams1.graph.axisY.label = dataIndicator.graph1YLabel;
                graph1 = new LineChart(dataIndicator.graph1Data, generalLineParams1, null, null);
            }
            graph1.drawChart();

            var graph2 = null;
            if (dataIndicator.graph2Type == '10') {
                generalBarParams2.graph.axisX.label = dataIndicator.graph2XLabel;
                generalBarParams2.graph.axisY.label = dataIndicator.graph2YLabel;
                graph2 = new BarChart(dataIndicator.graph2Data, generalBarParams2, null, null);
            } else {
                generalLineParams2.graph.axisX.label = dataIndicator.graph2XLabel;
                generalLineParams2.graph.axisY.label = dataIndicator.graph2YLabel;
                graph2 = new LineChart(dataIndicator.graph2Data, generalLineParams2, null, null);
            }
            graph2.drawChart();
        }

        //ProEffic or userEffic
        if(type == "proEffic" || type == "userEffic"){
            var inValue = validaNull(dataIndicator.efficiencyIndex);
            var inCost = validaNull(dataIndicator.inefficiencyCost);
            
            document.getElementById(type+"Index").innerHTML = inValue;
            document.getElementById(type+"Cost").innerHTML = "$" +inCost;
            
            //first level draw
            if(oType == "proEffic") {
                document.getElementById("relatedLabel").innerHTML = "<center><h3>"+ G_STRING.ID_RELATED_PROCESS +"</h3></center>";
                var graph = new Pie3DChart(dataIndicator.dataToDraw, peiParams, null, null);
                graph.drawChart();
            }

            if(oType == "userEffic") {
                document.getElementById("relatedLabel").innerHTML = "<center><h3>"+ G_STRING.ID_RELATED_GROUPS +"</h3></center>";
                var graph = new LineChart(dataIndicator.dataToDraw, ueiParams, null, null);
                graph.drawChart();
            }
                
            //Data by process
            for (i in dataIndicator.data){
              var proUid = dataIndicator.data[i].uid;
              var proDataName = dataIndicator.data[i].name;
              var proDataEfficiency = validaNull(dataIndicator.data[i].efficiencyIndex);
              var proDataEfficCost = validaNull(dataIndicator.data[i].inefficiencyCost);
              var x = 0;
              if(i % 2 == 0){
                x = 6;
              }

              widgetDetailDom = $(widgetDetail);
              widgetDetailDom.attr('id', proUid);
              
              gridProcess.add_widget(widgetDetailDom, x, 15, 6, 2, true);

              if(comparative<0){
                $(document.getElementsByClassName("green")).removeClass("green").addClass("blue");
              } else if(comparative>=0){
                //$(document.getElementsByClassName("blue")).removeClass("blue").addClass("green");
              }

                if(proDataName.length>25){
                    proDataName = proDataName.substring( -25, 25 );
                    proDataName = proDataName+"...";
                }

              //charging data by process
              //Process Title
              $("#procGreyTitle").attr('id', "procGreyTitle"+proUid);//changin the id
              document.getElementById("procGreyTitle"+proUid).innerHTML = "<B>"+proDataName+"</B>";
              //'+ G_STRING.ID_PRO_EFFICIENCY_INDEX +'
              $("#proIndex").attr('id', "proIndex"+proUid);//changin the id
              document.getElementById("proIndex"+proUid).innerHTML = proDataEfficiency;
              //Process Efficiency Cost
              $("#proCost").attr('id', "proCost"+proUid);//changin the id
              document.getElementById("proCost"+proUid).innerHTML = proDataEfficCost;

              widgetDetailDom.click(function(e){
                var proid = $(this).attr("id");
                var proname = $(this).find("#procGreyTitle"+proid).html();
                var proindex = validaNull($(this).find("#proIndex"+proid).html());
                var procost = validaNull($(this).find("#proCost"+proid).html());

                gridIndicators.remove_all();
                gridProcess.remove_all();

                //Drawing
                gridIndicators.add_widget($(proEfficData), 0, 15, 20, 4.7, true); //General data of the process
                if(comparative < 0){
                  $(document.getElementsByClassName("greenbg")).removeClass("greenbg").addClass("redbg");
                  $(document.getElementsByClassName("green")).removeClass("green").addClass("red");
                } else if(comparative > 0){
                  $(document.getElementsByClassName("redbg")).removeClass("redbg").addClass("greenbg");
                  //$(document.getElementsByClassName("red")).removeClass("red").addClass("green");
                }

                //adding data
                //var name = $("#"+oType+"Span"+indUid).html();
                document.getElementById(type+"Title").innerHTML = name;
                document.getElementById("proDetName").innerHTML = proname;
                document.getElementById(type+"Index").innerHTML = proindex;
                document.getElementById(type+"Cost").innerHTML = "$" +procost;

                //adding tasks
                if(oType == "proEffic"){
                  proxy.processTasksData(proid, dateActual, dateActualEnd,
                      function(dataTasks){
                          tasksData(dataTasks, gridProcess, proid);
                          hideScrollIfAllDivsAreVisible();
                      });
                } else {
                  proxy.userGroupData(proid, dateActual, dateActualEnd,
                      function(dataTasks){
                          tasksData(dataTasks, gridProcess, proid);
                          hideScrollIfAllDivsAreVisible();
                      });
                }
                return false;
              });
            }
            hideScrollIfAllDivsAreVisible();
        }
        //Adding the data by process
        var name = $("#"+oType+"Span"+indUid).html();
        document.getElementById(type+"Title").innerHTML = name;
    };

    function tasksData(dataTasks, gridIndicators, proid){
        var i = 0;
        if(oType == "proEffic"){
            document.getElementById("relatedLabel").innerHTML = "<center><h3>"+ G_STRING.ID_RELATED_TASKS +"</h3></center>";
            var graph = new LineChart(dataTasks.dataToDraw, peiDetailParams, null, null);
            graph.drawChart();
            for (i in dataTasks.tasksData){
                var taskUid = dataTasks.tasksData[i].uid;
                var taskName = dataTasks.tasksData[i].name;
                var taskEffic = validaNull(dataTasks.tasksData[i].efficienceIndex);
                var taskAverage = validaNull(dataTasks.tasksData[i].averageTime);
                var taskDeviation = validaNull(dataTasks.tasksData[i].deviationTime);
                var x = 0;
                if(i % 2 == 0){
                    x = 6;
                }
                
                var widgetDetailTaskDom = $(proEfficTaskDetail);
                widgetDetailTaskDom.attr('id', taskUid+"task");
                gridIndicators.add_widget(widgetDetailTaskDom, x, 15, 6, 2, true);//Drawing the task

                if($('.proGreen').hasClass('panel-red')){
                    $(document.getElementsByClassName("greenbg")).removeClass("greenbg").addClass("redbg");
                }else {
                   $(document.getElementsByClassName("redbg")).removeClass("redbg").addClass("greenbg");
                }

                if(taskName.length>55){
                  taskName = taskName.substring( -55, 55 );
                  taskName = taskName+"...";
                }
                
                //Adding data to task
                //Task Title
                $("#taskName").attr('id', "taskName"+taskUid);//changin the id
                document.getElementById("taskName"+taskUid).innerHTML = taskName;
                //Task Efficiency Index
                $("#taskEffic").attr('id', "taskEffic"+taskUid);//changin the id
                document.getElementById("taskEffic"+taskUid).innerHTML = taskEffic;
                //Task Average
                $("#taskAver").attr('id', "taskAver"+taskUid);//changin the id
                document.getElementById("taskAver"+taskUid).innerHTML = taskAverage;
                //Task Deviation
                $("#taskDeviat").attr('id', "taskDeviat"+taskUid);//changin the id
                document.getElementById("taskDeviat"+taskUid).innerHTML = taskDeviation;
            }
        } else {
            document.getElementById("relatedLabel").innerHTML = "<center><h3>"+ G_STRING.ID_RELATED_USERS +"</h3></center>";
            var graph = new LineChart(dataTasks.dataToDraw, ueiDetailParams, null, null);
            graph.drawChart();
            for (i in dataTasks.tasksData){
                var usrUid = dataTasks.tasksData[i].userUid;
                var usrName = dataTasks.tasksData[i].name;
                var usrEffic = validaNull(dataTasks.tasksData[i].efficiencyIndex);
                var usrCost = validaNull(dataTasks.tasksData[i].inefficiencyCost);
                var x = 0;
                if(i % 2 == 0){
                    x = 6;
                }
                
                var widgetDetailUsrDom = $(userTaskDetail);
                widgetDetailUsrDom.attr('id', usrUid+"task");
                gridIndicators.add_widget(widgetDetailUsrDom, x, 15, 6, 2, true);//Drawing the task

                if($('.proRed').hasClass('panel-green')){
                    $(document.getElementsByClassName("redbg")).removeClass("redbg").addClass("greenbg");
                } else{
                    $(document.getElementsByClassName("greenbg")).removeClass("greenbg").addClass("redbg");
                }

                if(usrName.length>55){
                    usrName = usrName.substring( -55, 55 );
                    usrName = usrName+"...";
                }
                
                //Adding data to task
                //Task Title
                $("#usrName").attr('id', "usrName"+usrUid);//changin the id
                document.getElementById("usrName"+usrUid).innerHTML = usrName;
                //Task Efficiency Index
                $("#usrEffic").attr('id', "usrEffic"+usrUid);//changin the id
                document.getElementById("usrEffic"+usrUid).innerHTML = usrEffic;
                //Task Deviation
                $("#usrCost").attr('id', "usrCost"+usrUid);//changin the id
                document.getElementById("usrCost"+usrUid).innerHTML = usrCost;
            }
        }

        
    };

    function hideScrollIfAllDivsAreVisible(){
        //For Debug: console.log('hidden  ' +  $('.hideme').length);
        if ($('.hideme').length <= 0) {
                $('#theImg').hide();
        }
        else {
                $('#theImg').show();
        }
    }

    $( document ).ready(function() {
        /* Show on scroll functionality... */
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

      if(dateMonth == 0){
          document.getElementById('year').selectedIndex = 1;
          document.getElementById('mounth').selectedIndex = 11;
      }else{
          document.getElementById('mounth').selectedIndex = dateMonth-1;
      }

      /*****calling the proxy*****/
      function getDashboardProxy(type) {
          var ws = urlProxy.split('/');
            if (type.toLowerCase()=='test') 
                return new DashboardProxyTest();

            if (type.toLowerCase()=='pro') 
                return new DashboardProxy(token,
                                        urlProxy,
                                        ws[3]);
      };
      proxy = getDashboardProxy('pro');
      proxy.userDashboards(usrId, 
            function(dataDashboards){
                if(dataDashboards.length > 0){
                    dashboardsButtons(dataDashboards);
                }else{
                    $(".indicators").append("<center><h3>"+ G_STRING.ID_GRID_PAGE_NO_DASHBOARD_MESSAGE +"</h3></center>");
                }
      });

      /*********************************/
      //ConfigurationObject
    objConfigDashboards = [
          {
            "dashUid": "15115651654654",
            "favorite": 1,
            "indicators":[
                {
                  /*"indUid": "15115651654654",
                  "indName": "Process Efficiency Index",*/
                  "id": "proEffic",
                  "favorite": 1,
                  "x": 0,
                  "y": 6,
                  "height": "50px",
                  "width": "20px" 
                }, {
                  /*"indUid": "45165165165161",
                  "indName": "Completed Cases",*/
                  "id": "compCases",
                  "favorite": 0,
                  "x": 6,
                  "y": 6,
                  "height": "50px",
                  "width": "20px" 
                }
            ]
          },
          {
            "dashUid": "5645565165465",
            "favorite": 0,
            "indicators":[
                {
                  /*"indUid": "15115651654654",
                  "indName": "Number Cases",*/
                  "id": "numCases",
                  "favorite": 0,
                  "x": 0,
                  "y": 6,
                  "height": "50px",
                  "width": "20px" 
                }, {
                  /*"indUid": "45165165165161",
                  "indName": "User Efficiency",*/
                  "id": "userEffic",
                  "favorite": 1,
                  "x": 4,
                  "y": 6,
                  "height": "50px",
                  "width": "20px"
                },
                {
                  /*"indUid": "45165165165161",
                  "indName": "Completed Cases",*/
                  "id": "compCases",
                  "favorite": 0,
                  "x": 8,
                  "y": 6,
                  "height": "50px",
                  "width": "20px" 
                }
            ]
          }
    ];

  //When some item is moved
    $('.grid-stack').on('change', function (e, items) {
        var widgets = [];
        _.map($('.grid-stack .grid-stack-item:visible'), function (el) {
            el = $(el);
            var item = el.data('_gridstack_node');
            var idWidGet = item.el[0].id.split('Item');
            if(favorite == actualDashId){
                favoriteData = 1;
            } else {
                favoriteData = 0;
            }
            if (typeof idWidGet[1] != "undefined") {
                var widgetsObj = {
                        'indicatorId': idWidGet[1],
                        'x': item.x,
                        'y': item.y,
                        'width': item.width,
                        'height': item.height <= 1 ? 2 : item.height
                }
                widgets.push(widgetsObj);
            }
        }); 
        
        if (widgets.length != 0) {
            var dashboard = {
                    'dashId': actualDashId,
                    'dashFavorite': favoriteData,
                    'dashData': widgets
            }
          proxy.setPositionIndicator(dashboard);  
        }
    });


    /*****Adding Buttons*****/
    function dashboardsButtons(dataDashboards){
      for( i in dataDashboards){
        var dashUid = dataDashboards[i].dashUid;
        var dashName = dataDashboards[i].dashName;
        var dashFavorite = dataDashboards[i].favorite;

        var domButton = $(dasButton);
        
        //adding a new button
        $( "#dasbuttons" ).append( domButton );

        //adding the UID like the id of the tag.
        $("#dasB").attr('id', dashUid);
        $("#favorite").attr('id', dashUid+'fav');
        
        if(dashName.length>20){
            dashNameButton = dashName.substring( -20, 20 );
            dashNameButton = dashNameButton+"...";
        } else{
            dashNameButton = dashName;
        }

        //addign the name
        document.getElementById(dashUid).innerHTML = dashNameButton;

        //if it is favorite adding the selected class
        if(dashFavorite == 1){
          actualDashId = dashUid;
          favorite = actualDashId;
          document.getElementById("titleH4").innerHTML = dashName;
          $("#"+dashUid+"fav").addClass("selected");

          //calling backend
          proxy.dashboardIndicators(dashUid, dateActual, dateActualEnd,
            function(widgetsObj) {
               indicators(widgetsObj);
          });
        }

        domButton.find("#"+dashUid+"fav").click(function() {
            dashUid = $(this).siblings('.btn').attr("id");
            favorite = dashUid;

            $(".selected").removeClass("selected");
            $(this).addClass("selected");
            //call backend to save the favorite selection
            var dashboard = {
                    'dashId': dashUid,
                    'dashFavorite': 1,
                    'dashData': ''
                }
            proxy.setPositionIndicator(dashboard);
        });

        domButton.find("#"+dashUid).click(function() {
            var btnid = $(this).attr("id");
            //first we have to get the divs empty
            $('#indicatorsGridStack').gridstack();
            var gridDashboards = $('#indicatorsGridStack').data('gridstack');
            gridDashboards.remove_all();

            $('#indicatorsDataGridStack').gridstack();
            var gridIndicators = $('#indicatorsDataGridStack').data('gridstack');
            gridIndicators.remove_all();

            //changing the Name of the Dashboard
            var btnName = $(this).html();
            document.getElementById("titleH4").innerHTML = btnName;

            actualDashId = btnid;
            //calling backend
            proxy.dashboardIndicators(btnid, dateActual, dateActualEnd,
              function(widgetsObj) {
                 indicators(widgetsObj);
            });
        });       

      }
    };



    /*****Adding the indicators*****/
    function indicators (widgetsObj){
      $('#indicatorsGridStack').gridstack();

      serialization = GridStackUI.Utils.sort(widgetsObj);
      var grid = $('#indicatorsGridStack').data('gridstack');
      //var width = 12 / widgetsObj.length;
      var i = 1;

      _.each(serialization, function (node) {
        if(node.x == 0){
            var x = 12 - (12/i);
        }else {
            var x = node.x;
        }
        if(node.y == 0){
            var y = 6;
        }else {
            var y = node.y;
        }
        
        if(node.height == 0){
            node.height = 2;
        }
        if(node.width == 0){
            node.width = 12 / widgetsObj.length;
        }
        
        node.comparative = validaNull(node.comparative);

        switch (node.id) {
        case "1010": //Process Efficience Index 
            var widget = proEffic;
            var id = "proEffic";
            break;
        case "1030": //Employee Efficience Index
            var widget = userEffic;
            var id = "userEffic";
            break;
        case "1020":
        case "1040":
        case "1050":
        case "1060":
        case "1070":
        case "1080":
            var indexI = parseFloat(node.index);
            var comparativeI = parseFloat(node.comparative);
            var condition = (node.direction == "1")? (indexI <= comparativeI) : (indexI >= comparativeI);
            if(condition == true){
                var widget = numCases; //Great
                var id = "generalGreat";
            } else {
                var widget = compCases; //Low
                var id = "generalLow";
            }
            break; 
        }
        
        //var comparative = (parseFloat(node.comparative)).toFixed(2);
        var widgetDom = $(widget);

        //Dibujando
        grid.add_widget(widgetDom, x, y, node.width, node.height, true); //dibuja los elementos
        
        $("#"+id+"Item").attr('id', id+"Item"+node.indUid);//changin the id of the divs
        $("#"+id+"Div").attr('id', id+"Div"+node.indUid);
        $("#"+id+"Huge").attr('id', id+"Huge"+node.indUid);
        $("#"+id+"Small").attr('id', id+"Small"+node.indUid);
        $("#"+id+"M").attr('id', id+"M"+node.indUid);
        $("#"+id+"Span").attr('id', id+"Span"+node.indUid);

        if(id =="generalGreat" || id == "generalLow"){
          $("#"+id).attr('id', id+node.indUid);
        }
        
        //Showing the data panels if is the favorite 
        if(node.favorite == 1){
              //changing the class
              if ($("#"+id+"M"+node.indUid).hasClass('panel-active')){
                //nada
              }else{
                //changing classes to show selection
                $(document.getElementsByClassName("panel-active")).removeClass("panel-active");
                $("#"+id+"M"+node.indUid).addClass("panel-active");
              }

              //Getting the data
              if(id == "proEffic"){
                  proxy.peiData(node.indUid, dateActual, dateActualEnd,
                    function(dataIndicator){
                        indicatorsData(dataIndicator, "proEffic", node.indUid, node.comparative);
                        hideScrollIfAllDivsAreVisible();
                    });
              } else if (id == "userEffic" ){
                  proxy.ueiData(node.indUid, dateActual, dateActualEnd,
                    function(dataIndicator){
                        indicatorsData(dataIndicator, "userEffic", node.indUid, node.comparative);
                        hideScrollIfAllDivsAreVisible();
                    });
              } else {
                  proxy.generalIndicatorData(node.indUid, dateActual, dateActualEnd,
                    function(dataIndicator){
                        var indexI = parseFloat(node.index);
                        var comparativeI = parseFloat(node.comparative);
                        var condition = (node.direction == "1")? (indexI <= comparativeI) : (indexI >= comparativeI);
                        if(condition == true){ //this are percentages
                          indicatorsData(dataIndicator, "generalGreat", node.indUid, node.comparative);
                          hideScrollIfAllDivsAreVisible();
                        } else{
                          indicatorsData(dataIndicator, "generalLow", node.indUid, node.comparative);
                          hideScrollIfAllDivsAreVisible();
                        }
                    });
              }
        } else {
            $("#"+id+"M"+node.indUid).removeClass("panel-active");
        }

        //Animating the Indicators
        animateprogress(id, node.index, node.comparative, node.indName, node.indUid, node.direction); //inserta datos en cada elemento
        i++;

        hideScrollIfAllDivsAreVisible();
        /********Changing the class when the indicator item is selected********/
        widgetDom.click(function(){
            var oID = $(this).attr("id");

            if(oID != undefined && oID.indexOf('Item') != -1){
                var comparative = 0;
                var oIDs = oID.split('Item');
                var id = oIDs[0];
                var uid = oIDs[1];

                if($(this).children().hasClass('panel-red')){
                  var comparative = -1;
                } else if($(this).children().hasClass('panel-green')){
                  var comparative = +1;
                }

                /*if ($("#"+id+"M"+uid).hasClass('panel-active')){
                    //nada
                }else{*/
                    //changing classes to show selection
                    $(document.getElementsByClassName("panel-active")).removeClass("panel-active");
                    $("#"+id+"M"+uid).addClass("panel-active");

                    //calling data of the indicator
                    if(id == "proEffic"){
                        proxy.peiData(uid, dateActual, dateActualEnd,
                          function(dataIndicator){
                              indicatorsData(dataIndicator, "proEffic", uid, comparative);
                                hideScrollIfAllDivsAreVisible();
                          });
                    } else if (id == "userEffic" ){
                        proxy.ueiData(uid, dateActual, dateActualEnd,
                          function(dataIndicator){
                              indicatorsData(dataIndicator, "userEffic", uid, comparative);
                                hideScrollIfAllDivsAreVisible();
                          });
                    } else {
                        proxy.generalIndicatorData(uid, dateActual, dateActualEnd,
                          function(dataIndicator){
                              var index = $("#"+id+"Huge"+uid).html();
                              index = parseInt(index);
                              var indexI = parseFloat(node.index);
                              var comparativeI = parseFloat(node.comparative);
                              var condition = (node.direction == "1")? (indexI <= comparativeI) : (indexI >= comparativeI);
                              if(condition == true){ //this are percentages
                                indicatorsData(dataIndicator, "generalGreat", uid, comparative);
                              } else{
                                indicatorsData(dataIndicator, "generalLow", uid, comparative);
                              }
                                hideScrollIfAllDivsAreVisible();
                          });
                    }
                    
                //}
            }
        });
      });
    };

    $(".btn-compare").click(function(){
        var yearComp = $( "#year option:selected" ).text();
        var mounthComp = $( "#mounth option:selected" ).val();

        dateActualEnd = "30-"+(mounthComp)+"-"+yearComp;

        //first we have to get the divs empty
        $('#indicatorsGridStack').gridstack();
        var gridDashboards = $('#indicatorsGridStack').data('gridstack');
        gridDashboards.remove_all();

        $('#indicatorsDataGridStack').gridstack();
        var gridIndicators = $('#indicatorsDataGridStack').data('gridstack');
        gridIndicators.remove_all();
          //For Debug: console.log(dateActualEnd);
        //calling backend
        proxy.dashboardIndicators(actualDashId, dateActual, dateActualEnd,
           function(widgetsObj) {
              indicators(widgetsObj);
        });
    });


 });

$(function () {
    var options = {
        cell_height: 75,
        vertical_margin: 12
    };
    $('.grid-stack').gridstack(options);
});
