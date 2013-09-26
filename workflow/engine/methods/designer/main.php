<?php

$G_MAIN_MENU = 'processmaker';
$G_ID_MENU_SELECTED = 'DESIGNER';
$G_PUBLISH = new Publisher();

$G_PUBLISH->AddContent( 'view', 'designer/main' );

G::RenderPage('publish', 'minimal');