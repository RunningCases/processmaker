<?php
/*
 * ProcessMaker Web Application Bootstrap
 */
try {
    $rootDir = realpath(__DIR__ . "/../../") . DIRECTORY_SEPARATOR;

    if (! is_dir($rootDir . 'vendor')) {
        if (file_exists($rootDir . 'composer.phar')) {
            throw new Exception(
                "ERROR: Vendors are missing!" . PHP_EOL .
                "Please execute the following command to install vendors:" .PHP_EOL.PHP_EOL.
                "$>php composer.phar install"
            );
        } else {
            throw new Exception(
                "ERROR: Vendors are missing!" . PHP_EOL .
                "Please execute the following commands to prepare/install vendors:" .PHP_EOL.PHP_EOL.
                "$>curl -sS https://getcomposer.org/installer | php" . PHP_EOL .
                "$>php composer.phar install"
            );
        }
    }

    if (! file_exists($rootDir . 'vendor' . DIRECTORY_SEPARATOR . "autoload.php")) {
        throw new Exception(
            "ERROR: Problems with Vendors!" . PHP_EOL .
            "Please execute the following command to repair vendors:" .PHP_EOL.PHP_EOL.
            "$>php composer.phar update"
        );
    }

    require $rootDir . "framework/src/Maveriks/Util/ClassLoader.php";

    $loader = Maveriks\Util\ClassLoader::getInstance();
    $loader->add($rootDir . 'framework/src/', "Maveriks");
    $loader->add($rootDir . 'workflow/engine/src/', "ProcessMaker");
    $loader->add($rootDir . 'workflow/engine/src/');
    $loader->add($rootDir . 'workflow/engine/classes/model/');

    // and vendors to autoloader
    $loader->add($rootDir . 'vendor/luracast/restler/vendor', "Luracast");
    $loader->add($rootDir . 'vendor/bshaffer/oauth2-server-php/src/', "OAuth2");

    $app = new Maveriks\WebApplication();

    $app->setRootDir($rootDir);
    $app->setRequestUri($_SERVER['REQUEST_URI']);
    $stat = $app->route();

    switch ($stat)
    {
        case Maveriks\WebApplication::RUNNING_WORKFLOW:
            include "sysGeneric.php";
            break;

        case Maveriks\WebApplication::RUNNING_API:
            $app->run(Maveriks\WebApplication::SERVICE_API);
            break;
    }

} catch (Exception $e) {
    $view = new Maveriks\Pattern\Mvc\PhtmlView($rootDir . "framework/src/templates/Exception.phtml");
    $view->set("message", $e->getMessage());
    $view->set("exception", $e);

    $response = new Maveriks\Http\Response($view->getOutput(), 503);
    $response->send();
}
