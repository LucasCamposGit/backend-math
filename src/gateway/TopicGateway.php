<?php

namespace app\gateway;
use app\database\Database;
use PDO;

class TopicGateway{

    private PDO $conn;
    public function __construct(private Database $database) {
        $this->conn = $database->getConnection();
    }   

    public function getTopic(int $id): array{
        $sql = "SELECT * FROM topics
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":id", $id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


}