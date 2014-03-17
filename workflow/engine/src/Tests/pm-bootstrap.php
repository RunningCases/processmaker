<?php
//
// pm-bootstrap.php
//

/*
 * PmBootstrap for Test Unit Suite
 *
 * @author Erik Amaru Ortiz <aortiz.erik@gmail.com, erik@colosa.com>
 */

$config = parse_ini_file(__DIR__ . DIRECTORY_SEPARATOR . "config.ini");

$workspace = $config['workspace'];
$lang = $config['lang'];
$processMakerHome = $config['pm_home_dir'];
$rootDir = realpath($processMakerHome) . DIRECTORY_SEPARATOR;

require $rootDir . "framework/src/Maveriks/Util/ClassLoader.php";

$loader = Maveriks\Util\ClassLoader::getInstance();
$loader->add($rootDir . 'framework/src/', "Maveriks");
$loader->add($rootDir . 'workflow/engine/src/', "ProcessMaker");
$loader->add($rootDir . 'workflow/engine/src/');

// add vendors to autoloader
$loader->add($rootDir . 'vendor/bshaffer/oauth2-server-php/src/', "OAuth2");
$loader->addClass("Bootstrap", $rootDir . 'gulliver/system/class.bootstrap.php');

$loader->addModelClassPath($rootDir . "workflow/engine/classes/model/");

$app = new Maveriks\WebApplication();
$app->setRootDir($rootDir);
$app->loadEnvironment($workspace);
