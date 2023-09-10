<?php

namespace app\controller;

use app\gateway\TopicGateway;

class TopicController {

    public function __construct(private TopicGateway $topic_gateway){
        
    }

    public function handleTopic(string $method, int $topic_id){
        if($method !== "GET") {
            echo json_encode(["message" => "method not allowed"]);
            header("Allow: POST");
            http_response_code(405);
            exit;
        }

        $topic = $this->topic_gateway->getTopic($topic_id);
        echo json_encode($topic);

    }
}