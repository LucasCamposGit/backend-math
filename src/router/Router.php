<?php

namespace app\router;

use app\controller\DashboardController;
use app\controller\UserController;
use app\controller\SubjectController;
use app\controller\TopicController;
use app\auth\Auth;
use app\controller\ListController;

require __DIR__ . "/../../vendor/autoload.php";

class Router {
    
    public function __construct(private string $uri, 
                                private string $method, 
                                private UserController $user_controller,
                                private DashboardController $dashboard_controller,
                                private SubjectController $subject_controller,
                                private TopicController $topic_controller,
                                private ListController $list_controller,
                                private Auth $auth) {}

    public function getPath(): void {
        $this->uri = parse_url($this->uri, PHP_URL_PATH);
        $parts = explode("/", $this->uri);
        $route = $parts[2];

        switch($route) {

            case "dashboard":
                if (! $this->auth->authenticateAccessToken()) {
                    exit;
                }
                $user_id = $this->auth->user_id;
                $this->dashboard_controller->handleDashboard($this->method, $user_id);
                break;

            case "subject": 
                if(! $this->auth->authenticateAccessToken()) {
                    exit;
                }
                $param = $parts[3];
                $user_id = $this->auth->user_id;
                $this->subject_controller->handleSubject($this->method, $param, $user_id);
                break;

            case "topic":
                if(! $this->auth->authenticateAccessToken()){
                    exit;
                }
                $param = $parts[3];
                $user_id = $this->auth->user_id;
                $this->topic_controller->handleTopic($this->method, $param);
                break;
            
            case "list":
                if(! $this->auth->authenticateAccessToken()){
                    exit;
                }    
                $param = $parts[3];
                $user_id = $this->auth->user_id;
                $this->list_controller->handleList($this->method, $param, $user_id);
                break;
                
            case "signin": 
                $this->user_controller->handleSignin($this->method);
                break;

            case "login":
                $this->user_controller->handleLogin($this->method);
                break;
                
            default:
                $this->getResponseNotFound();
                break;
        }
    }

    function getResponseNotFound(): void {
        http_response_code(404);
        echo "not found";
        exit;
    }
}