<?php

namespace app\controller;
use app\gateway\DashboardGateway;

require __DIR__ . "/../../vendor/autoload.php";


class DashboardController{

    
    public function __construct(private DashboardGateway $dashboard_gateway){
    }

    public function handleDashboard(string $method, int $id): void {
        if ($method !== "POST") {
            echo json_encode(["message" => "invalid method"]);
            header("Allow: POST");
            http_response_code(405);
            exit;
        }

        $subjects = $this->dashboard_gateway->getSubject();
        $question_progress = $this->dashboard_gateway->getQuestionProgress($id);
        $questions = $this->dashboard_gateway->getQuestions();
        $solved_questions = $this->getSolved($question_progress);
        $total_of_questions = count($questions);

        $response = [
            "subjects" => $subjects,
            "solved" => $solved_questions,
            "total_of_questions" => $total_of_questions
        ];

        echo json_encode($response);
    }

    public function getSolved(array $question_progress): int {
        $solved = 0;
        
        for($i = 0; $i < count($question_progress); $i++){
            if ($question_progress[$i]["done"]) {
                $solved = $solved + 1;
            } 
        }

        return $solved;
    } 

}