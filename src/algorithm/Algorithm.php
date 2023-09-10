<?php

namespace app\algorithm;

class Algorithm {
    
    public function calculateNextReviewDate(int $repetitions, 
                                            float $easiness, 
                                            string $quality, 
                                            int $interval, 
                                            $today = null): array {

        $minimumInterval = 1;
        $maximumInterval = 36500;

        if ($today === null) {
            $today = time();
        }

        switch($quality) {
            case "again":
                $newEasiness = max(1.3, $easiness - 0.8);
                $repetitions = 0;
                break;
            case "hard":
                $newEasiness = max(1.3, $easiness - 0.2);
                break; 
            case "good":
                $newEasiness = $easiness;
                break;
            case "easy":
                $newEasiness = $easiness + 0.3;
                break;
            default: 
                return $today;
        }

        if($repetitions == 0 || $repetitions == 1) {
            switch($quality) {
                case "good":
                    $newInterval = 2;
                    break;
                case "easy":
                    $newInterval = 4;
                    break;
                default:
                    $newInterval = $minimumInterval;
                    break;
            }
        } else if ($repetitions == 2){
            switch($quality) {
                case "good":
                    $newInterval = 4;
                    break;
                case "easy":
                    $newInterval = 7;
                    break;
                default:
                    $newInterval = $minimumInterval;
                    break;
            }
        } else {
            if ($quality === "again" || $quality === "hard") {
                $newInterval = round($interval / 2);
            } else {
                $newInterval = round($interval * $newEasiness);
            }
        }

        $newInterval = min($newInterval, $maximumInterval);

        $nextReviewDate = $today + $newInterval * 86400;

        return [
            "date" => $nextReviewDate,
            "ef" => $newEasiness,
            "repetitions" => $repetitions + 1,
            "interval" => $newInterval
        ];
        
    }
}