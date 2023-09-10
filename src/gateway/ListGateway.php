<?php

namespace app\gateway;
use app\database\Database;
use PDO;

class ListGateway{

    private PDO $conn;
    public function __construct(private Database $database) {
        $this->conn = $database->getConnection();
    }   

    public function getList(int $topic_id): array{
        $sql = "SELECT * FROM questions
                WHERE topic_id = :topic_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":topic_id", $topic_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRevision(int $user_id, int $question_id = 0): array | false {
        if ($question_id === 0) {
            $sql = "SELECT * FROM revision
            WHERE  user_id = :user_id";
        } else {
            $sql = "SELECT * FROM revision
            WHERE question_id = :question_id AND user_id = :user_id";
        }

        $stmt = $this->conn->prepare($sql);

        if ($question_id !== 0) {
            $stmt->bindValue(":question_id", $question_id);
        }
        
        $stmt->bindValue(":user_id", $user_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReview(int $question_id) {
        $sql = "SELECT * FROM questions
        WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $question_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function postRevision(int $question_id, 
                                int $user_id,
                                int $repetition, 
                                float $ef, 
                                int $interval,
                                int $date): void  {

        $sql = "INSERT INTO revision (question_id, user_id, ef, rev_interval, repetitions, next_date) 
                VALUES (:question_id, :user_id, :ef, :rev_interval, :repetitions, :next_date)";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(
            ":question_id" => $question_id,
            ":user_id" => $user_id,
            ":ef" => $ef, 
            ":rev_interval" => $interval,
            ":next_date" => $date,
            ":repetitions" => $repetition
        ));
    }

    public function updateRevision(int $question_id, 
                                    int $user_id,
                                    int $repetition, 
                                    float $ef, 
                                    int $interval,
                                    int $date) {
        $sql = "UPDATE revision SET ef = :ef, rev_interval = :rev_interval, 
                                    repetitions = :repetitions, next_date = :next_date 
                WHERE question_id = :question_id AND user_id = :user_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(array(
            ":ef" => $ef,
            ":rev_interval" => $interval,
            ":next_date" => $date,
            ":repetitions" => $repetition,
            ":question_id" => $question_id,
            ":user_id" => $user_id
        ));
    }
}