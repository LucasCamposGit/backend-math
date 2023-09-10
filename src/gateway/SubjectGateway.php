<?php

namespace app\gateway;
use app\database\Database;
use PDO;

class SubjectGateway {

    private PDO $conn;
    public function __construct(private Database $database){
        $this->conn = $database->getConnection();
    }

    public function getTopics(int $subject_id): array {
        $sql = "SELECT * FROM topics
                WHERE subject_id = :subject_id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":subject_id", $subject_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStatus(int $user_id, int $topic_id):array {
        $sql = "SELECT done FROM topics_progress
        WHERE user_id = :user_id AND topic_id = :topic_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $user_id);
        $stmt->bindValue(":topic_id", $topic_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestions(int $subject_id): int | false{
        $sql = "SELECT COUNT(*) FROM questions
                WHERE topic_id = $subject_id";
        
        $stmt = $this->conn->query($sql);
        return $stmt->fetchColumn();
    }

}