<?php

declare(strict_types=1);

namespace app;

use app\algorithm\Algorithm;
use app\auth\Auth;
use app\controller\DashboardController;
use app\controller\ListController;
use app\controller\SubjectController;
use app\controller\TopicController;
use app\controller\UserController;
use app\database\Database;
use app\gateway\DashboardGateway;
use app\gateway\ListGateway;
use app\gateway\SubjectGateway;
use app\gateway\TopicGateway;
use app\gateway\UserGateway;
use app\jwt\JWTCodec;
use app\router\Router;


require __DIR__ . "/../vendor/autoload.php";

set_error_handler('app\error\ErrorHandler::handleError');
set_exception_handler('app\error\ErrorHandler::handleException');

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Authorization, Content-Type");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

if($_SERVER["REQUEST_METHOD"] === "OPTIONS"){
    http_response_code(200);
    echo json_encode(["message"=> "ok"]);
    exit;
}

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . "/..");
$dotenv->load();

// database
$database = new Database($_ENV["DB_HOST"], $_ENV["DB_NAME"], $_ENV["DB_USER"], $_ENV["DB_PASS"]);

//algorithm
$algorithm = new Algorithm();

//gateways
$user_gateway = new UserGateway($database);
$dashboard_gateway = new DashboardGateway($database);
$subject_gateway = new SubjectGateway($database);
$topic_gateway = new TopicGateway($database);
$list_gateway = new ListGateway($database);

// JWTcodec && authorization
$codec = new JWTCodec($_ENV["SECRET_KEY"]);
$auth = new Auth($user_gateway, $codec);

//controlllers
$user_controller = new UserController($user_gateway, $codec);
$dashboard_controller = new DashboardController($dashboard_gateway);
$subject_controller = new SubjectController($subject_gateway);
$topic_controller = new TopicController($topic_gateway);
$list_controller = new ListController($list_gateway, $algorithm);

//router
$router = new Router ($_SERVER["REQUEST_URI"], 
                      $_SERVER["REQUEST_METHOD"], 
                      $user_controller,
                      $dashboard_controller,
                      $subject_controller,
                      $topic_controller,
                      $list_controller,
                      $auth);
$router->getPath();

