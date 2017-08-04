<?php
require_once(__DIR__ . '/../../bootstrap/autoload.php');
register_shutdown_function(
    create_function(
        "",
        "
        if (class_exists(\"Propel\")) {
            Propel::close();
        }
        "
    )
);

ini_set("session.cookie_httponly", 1);

if (isset($_SERVER['UNENCODED_URL'])) {
    $_SERVER['REQUEST_URI'] = $_SERVER['UNENCODED_URL'];
}

try {
    $rootDir = realpath(__DIR__ . "/../../") . DIRECTORY_SEPARATOR;

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

        case Maveriks\WebApplication::RUNNING_OAUTH2:
            $app->run(Maveriks\WebApplication::SERVICE_OAUTH2);
            break;

        case Maveriks\WebApplication::RUNNING_INDEX:
            $response = new Maveriks\Http\Response(file_get_contents("index.html"), 302);
            $response->send();
            break;

        case Maveriks\WebApplication::RUNNING_DEFAULT:
            $response = new Maveriks\Http\Response("", 302);
            //TODO compose this def url with configuration data from env.ini
            $response->setHeader("location", "/sys/en/neoclassic/login/login");
            $response->send();
            break;
    }

} catch (Exception $e) {
    $view = new Maveriks\Pattern\Mvc\PhtmlView($rootDir . "framework/src/templates/Exception.phtml");
    $view->set("message", $e->getMessage());
    $view->set("exception", $e);

    $response = new Maveriks\Http\Response($view->getOutput(), 503);
    $response->send();
}

