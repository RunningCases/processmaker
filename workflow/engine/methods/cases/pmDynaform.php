<?php

$DYN_UID = $_GET["dyn_uid"];
G::LoadClass('pmDynaform');
$a = new pmDynaform(array("CURRENT_DYNAFORM" => $DYN_UID));
$a->printPmDynaform();
