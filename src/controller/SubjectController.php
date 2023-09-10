<?php

namespace app\controller;

use app\gateway\SubjectGateway;

class SubjectController{

    public function __construct(private SubjectGateway $subject_gateway){
        
    }

    public function handleSubject(string $method, int $subject_id, int $user_id) {
        if($method !== "GET") {
            echo json_encode(["message" => "method not allowed"]);
            header("Allow: POST");
            http_response_code(405);
            exit;
        }

        $topics = $this->subject_gateway->getTopics($subject_id);

        for($i = 0; $i < count($topics); $i++ ){
            
            $status = $this->subject_gateway->getStatus($user_id, $topics[$i]['id']);
            $questions_number = $this->subject_gateway->getQuestions($topics[$i]['id']);

            $topics[$i]['questions_number'] = $questions_number;
            $topics[$i]['status'] = $status ? boolval($status[0]['done']) : false;
        }

        echo json_encode($topics);
    }


}