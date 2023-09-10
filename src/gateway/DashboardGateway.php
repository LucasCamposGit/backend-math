<?php

namespace app\gateway;
use app\database\Database;
use PDO;

require __DIR__ . "/../../vendor/autoload.php";

class DashboardGateway {
    
    private PDO $conn;

    public function __construct(private Database $database) {
        $this->conn = $database->getConnection();
    }

    public function getSubject(): array {
        $sql = "SELECT * FROM subject";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestionProgress(int $id): array {
        $sql = "SELECT * FROM question_progress
                WHERE user_id = :user_id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":user_id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getQuestions(): array {
        $sql = "SELECT * FROM questions";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}