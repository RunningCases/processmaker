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

            dashboardFrame    = document.getElementById('dashboardFrame');
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
    </script>
</html>