<?php

namespace app\controller;

use app\algorithm\Algorithm;
use app\gateway\ListGateway;

class ListController{

    public function __construct(private ListGateway $list_gateway,
                                private Algorithm $algorithm){
        
    }

    public function handleList(string $method, mixed $param, int $user_id) {
        switch($method) {
            case "GET": 
                if ($param === "review") {
                    $revision_list = $this->list_gateway->getRevision($user_id);

                    for($i = 0; count($revision_list) > $i; $i++){
                        $questions[$i] = $this->list_gateway->getReview($revision_list[$i]['question_id'])[0];
                    }

                    for($i = 0; $i < count($revision_list); $i++){
                        extract($revision_list[$i]);
                        $qualitys = ["again", "hard", "good", "easy"];

                        foreach($qualitys as $quality){
                            $next_interval[$quality] = $this->algorithm->calculateNextReviewDate($repetitions, 
                                                                                    $ef,
                                                                                    $quality, 
                                                                                    $rev_interval)['interval'];
                            $next_interval[$quality] = "$next_interval[$quality]  day(s)";
                        }
                        $questions[$i]['interval'] = $next_interval;
                    }

                    echo json_encode($questions);
                } else {
                    $list = $this->list_gateway->getList($param);

                    for($i = 0; $i < count($list); $i++){
                       $list[$i]['interval'] = [
                            "again" => "1 minute",
                            "hard" => "< 10 minutes",
                            "good" => "1 day",
                            "easy" => "3 days"
                       ];
                    }
                    echo json_encode($list);
                }

                break;
            case "POST":
                $this->handlePostList($user_id);
                break;

            default:  
                echo json_encode(["message" => "method not allowed"]);
                header("Allow: POST, GET");
                http_response_code(405);
                exit;
                break;
        }
    }


    private function handlePostList(int $user_id) {
        $data = (array) json_decode(file_get_contents("php://input"), true);
        $question_id = $data['id'];
        $old_revision = $this->list_gateway->getRevision($user_id, $question_id);

        if (count($old_revision) === 0) {
            $newRev = $this->algorithm->calculateNextReviewDate(0, 2.5, $data["quality"], 0);
            $this->list_gateway->postRevision($question_id, 
                                            $user_id,
                                            $newRev['repetitions'],
                                            $newRev['ef'],
                                            $newRev['interval'],
                                            $newRev['date']);

            extract($newRev);
            $qualitys = ["again", "hard", "good", "easy"];

            foreach($qualitys as $quality){
                $next_interval[$quality] = $this->algorithm->calculateNextReviewDate($repetitions, 
                                                                        $ef,
                                                                        $quality, 
                                                                        $interval)['interval'];
                $next_interval[$quality] = "$next_interval[$quality]  day(s)";
            }

            echo json_encode($next_interval);
            
        } else {
            $old_revision = $this->list_gateway->getRevision( $user_id, $question_id);

            $newRev = $this->algorithm->calculateNextReviewDate($old_revision[0]['repetitions'], 
                                                                $old_revision[0]['ef'], 
                                                                $data["quality"], 
                                                                $old_revision[0]['rev_interval']);

            $this->list_gateway->updateRevision($question_id, 
                                                $user_id,
                                                $newRev['repetitions'],
                                                $newRev['ef'],
                                                $newRev['interval'],
                                                $newRev['date']);

            extract($newRev);
            $qualitys = ["again", "hard", "good", "easy"];

            foreach($qualitys as $quality){
                $next_interval[$quality] = $this->algorithm->calculateNextReviewDate($repetitions, 
                                                                        $ef,
                                                                        $quality, 
                                                                        $interval)['interval'];
                $next_interval[$quality] = "$next_interval[$quality]  day(s)";
            }

            echo json_encode($next_interval);
                              
        }
    }
}