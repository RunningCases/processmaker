<head>
	<title>Upload an Output Document</title>
	<script type="text/javascript" src="../../tiny_mce_popup.js" ></script>
	<script type="text/javascript" src="editor_plugin_src.js" ></script>
	<base target="_self" />
</head>
<body>
<?php
	$Action = isset($_GET["q"]) ? $_GET["q"] : "none";
	if($Action =="none"){
            displayUploadForm();
        }else if($Action=="upload"){
            uploadContentFile();
        }
?>
</body>
</html>

<?php

// displays the upload form
function displayUploadForm()
{
	echo '<form action="uploader.php?'.$_SERVER["QUERY_STRING"].'&q=upload" method="post" enctype="multipart/form-data" onsubmit="">'
                .'File Name: <br/>'
                .'<input type="file" size="40" name="upload_file" ID="File1"/><br/>'
                .'<input type="submit" name="Upload File" value="Upload File" style="width: 150px;" onclick="document.getElementById(\'progress_div\').style.visibility=\'visible\';"/>'
                .'  <div id="progress_div" style="visibility: hidden;"><img src="progress.gif" alt="wait..." style="padding-top: 5px;"></div>'
             .'</form>';
}
// uploads the file to the destination path, and returns a link with link path substituted for destination path
function uploadContentFile()
{
	$StatusMessage = "";
	$ActualFileName = "";
	$FileObject = $_FILES["upload_file"]; // find data on the file
    $DestPath = sys_get_temp_dir();
    $aux='';
    $chain = preg_replace("/\r\n+|\r+|\n+|\t+/i", ' ', file_get_contents(htmlentities($FileObject['tmp_name'])));
    $chain=preg_replace('#<head(.*?)>(.*?)</head>#is', ' ', $chain);
    $chain=strip_tags($chain, '<address><label><canvas><option><ol><u><textarea><em><h1><h2><h3><h4><h5><h6><section><tbody><tr><th><td><hr><center><br><b><img><p><a><table><caption><thead><div><ul><li><form><input><strong><span><small><button><figure>');
    $chain=str_replace ('"',"'",$chain);
    updateEditorContent(trim($chain));
    closeWindow();
}


function showPopUp($PopupText)
{
	echo "<script type=\"text/javascript\" language=\"javascript\">alert (\"$PopupText\");</script>";
}

function updateEditorContent($serializedHTML)
{
    echo "<script type=\"text/javascript\" language=\"javascript\">updateEditorContent(\"".$serializedHTML."\");</script>";
}

function closeWindow()
{
	echo '
            <script language="javascript" type="text/javascript">
                    closePluginPopup();
            </script>
         ';
}
?>

