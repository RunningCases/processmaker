<?php
use Illuminate\Support\Facades\Log;
try {
  $category = (isset($_GET["category"]))? $_GET["category"] : null;
  /* Render page */
  $oHeadPublisher = headPublisher::getSingleton();
  $pmDynaform = new PmDynaform(array());
  $G_MAIN_MENU        = "processmaker";
  $G_ID_MENU_SELECTED = "ID_SCHEDULER_MNU_01";
  $dateTime = new \ProcessMaker\Util\DateTime();
  if (!empty($_SESSION['USER_LOGGED'])) {
    $arrayTimeZoneId = \DateTimeZone::listIdentifiers();
    $js = "" .
    "<script type='text/javascript'>\n" .
    "var timezoneArray = " . G::json_encode($arrayTimeZoneId) . ";\n" .
    "</script>\n";
    echo($js);  

  }

  $js = "" .
  "<script type=\"text/javascript\" src=/js/ext/translation.".SYS_LANG.".".G::browserCacheFilesGetUid() .".js></script>\n".
    "<script type='text/javascript'>\n" .
    "var server = '" . System::getHttpServerHostnameRequestsFrontEnd() . "';\n" .
    "var credentials = " . G::json_encode($pmDynaform->getCredentials()) . ";\n" .
    "var category = '" . $category . "';\n" .
    "var lang = '" . SYS_LANG . "';\n" .
    "</script>\n";
  echo($js);  

  $file = file_get_contents(PATH_HOME . 'public_html/lib/taskscheduler/index.html');
  echo $file;
} catch (Exception $e) {

}
?>