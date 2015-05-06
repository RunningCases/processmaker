<html>
    <style type="text/css">
        .Footer .content {
            padding   :0px !important;
        }  
        *html body {
            overflow-y: hidden;
        }
    </style>
    <body onresize="autoResizeScreen()" onload="autoResizeScreen()">
    <iframe name="dashboardFrame" id="dashboardFrame" src ="" width="99%" height="768" frameborder="0">
      <p>Your browser does not support iframes.</p>
    </iframe>
    </body>
    <script>
        if ( (navigator.userAgent.indexOf("MSIE")!=-1) || (navigator.userAgent.indexOf("Trident")!=-1) ) {
            if ( typeof(winStrategicDashboard) == "undefined" || winStrategicDashboard.closed ) {
                winStrategicDashboard = window.open(
                    "../strategicDashboard/viewDashboard","winStrategicDashboard"
                );
            }
            document.getElementById('dashboardFrame').src = "../strategicDashboard/viewDashboardIE";
        } else {
            document.getElementById('dashboardFrame').src = "../strategicDashboard/viewDashboard";
        }
        if ( document.getElementById('pm_submenu') ) {
            document.getElementById('pm_submenu').style.display = 'none';
        }

        document.documentElement.style.overflowY = 'hidden';
        var oClientWinSize = getClientWindowSize();

        var autoResizeScreen = function () {
            var dashboardFrame;
            var containerList1, containerList2;

            dashboardFrame = document.getElementById('dashboardFrame');

            containerList1 = document.getElementById("pm_header");
            if (document.getElementById("mainMenuBG") &&
                document.getElementById("mainMenuBG").parentNode &&
                document.getElementById("mainMenuBG").parentNode.parentNode &&
                document.getElementById("mainMenuBG").parentNode.parentNode.parentNode &&
                document.getElementById("mainMenuBG").parentNode.parentNode.parentNode.parentNode
            ){
                containerList2 = document.getElementById("mainMenuBG").parentNode.parentNode.parentNode.parentNode;
            }
            if (containerList1 === containerList2) {
                height = oClientWinSize.height - containerList1.clientHeight;
                dashboardFrame.style.height = height;
                if (dashboardFrame.height ) {
                    dashboardFrame.height = height;
                }
            } else {
                if (dashboardFrame) {
                    height = getClientWindowSize().height-90;
                    if (typeof dashboardFrame.style != 'undefined') {
                        dashboardFrame.style.height = height;
                    }
                    if (typeof dashboardFrame.contentWindow.document != 'undefined') {
                        dashboardFrame = dashboardFrame.contentWindow.document.getElementById('dashboardFrame');
                        if (dashboardFrame && typeof dashboardFrame.style != 'undefined') {
                            dashboardFrame.style.height = height-5;
                        }
                    }
                } else {
                    setTimeout('autoResizeScreen()', 2000);
                }
            }
        }
    </script>
</html>