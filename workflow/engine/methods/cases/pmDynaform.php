<?php

$DYN_UID = $_GET["dyn_uid"];
G::LoadClass('pmDynaform');
$a = new pmDynaform($DYN_UID);
$a->printPmDynaform();
