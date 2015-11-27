<?php
session_start();
if (!isset($_SESSION['USER_LOGGED'])) {
	die( '<script type="text/javascript">
		try
		{
			alert("lostSession");
				var dataToSend = {
					"action": "credentials",
					"operation": "refreshPmSession",
					"type": "processCall",
					"funParams": [
						"",
						""
					],
					"expectReturn": false
				};
				var x = parent.postMessage(JSON.stringify(dataToSend), "*");
		}catch (err)
		{
			parent.location = parent.location;
		}
	</script>');
}
if($_GET['form']){
	header( 'location:' . $_SESSION['server'] . $_SESSION['PMCase'] );
}else if($_GET['processmap']){
	header( 'location:' . $_SESSION['server'] . $_SESSION['PMProcessmap'] );
}else if($_GET['uploaded']){
	header( 'location:' . $_SESSION['server'] . $_SESSION['PMUploadedDocuments'] );
} else if($_GET['generated']){
	header( 'location:' . $_SESSION['server'] . $_SESSION['PMGeneratedDocuments'] );
}
