<?php

namespace app\gateway;
use app\database\Database;
use PDO;

require __DIR__ . "/../../vendor/autoload.php";

class UserGateway {

    private PDO $conn;

    public function __construct(Database $database){
        $this->conn = $database->getConnection();
    }

    public function getUser(string $email): array | false {
        $sql = "SELECT *
                FROM users
                WHERE email = :email";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createUser(string $email, string $password): int  {
        $sql = "INSERT INTO users (email, password_hash)
                VALUES (:email, :password_hash) ";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(":email", $email, PDO::PARAM_STR);
        $stmt->bindValue(":password_hash", $password, PDO::PARAM_STR);
        $stmt->execute();

        return $this->conn->lastInsertId();
    }
}