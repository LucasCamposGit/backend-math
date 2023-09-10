<?php

namespace app\controller;
use app\gateway\UserGateway;
use app\jwt\JWTCodec;

require __DIR__ . "/../../vendor/autoload.php";


class UserController {

    public function __construct(private UserGateway $user_gateway, private JWTCodec $codec){
        
    }
    
    public function handleSignin(string $method) {

        if($method !== "POST") {

            http_response_code(405);
            header("Allow: POST");
            exit;
        } else {
            
            $data = (array) json_decode(file_get_contents("php://input"), true);

            if (! $this->validateEmail($data)  || ! $this->validatePassword($data)) {
                exit;
            }

            $psw  = $this->hashPassword($data["password"]);

            $id = $this->user_gateway->createUser($data["email"], $psw);

            echo json_encode(["message" => "user {$id} created"]);
        }
    }

    public function handleLogin(string $method) {

        if ($method !== "POST") {

            http_response_code(405);
            header("Allow: POST");
            exit;
        } else {

            $data = (array) json_decode(file_get_contents("php://input"), true);
            
            if( ! array_key_exists("email", $data) || ! array_key_exists("password", $data)) {

                http_response_code(400);
                echo json_encode(["message" => "missing login credentials"]);
                exit;
            } 
            
            $user = $this->user_gateway->getUser($data["email"]); 

            if (empty($user)) {
                http_response_code(400);
                echo json_encode(["message" => "wrong email"]);
                exit;
            }

            $password_credential = base64_encode($data["password"]);

            if ( ! password_verify($password_credential, $user["password_hash"])){

                http_response_code(400);
                echo json_encode(["message" => "wrong password"]);
                exit;
            }

            $payload = [
                "sub" => $user["id"],
                "exp" => time() + 84600
            ];

            $access_token = $this->codec->encode($payload); 

            echo json_encode(["id" => $user["id"], "access token" => $access_token]);
        }
    }

    public function validateEmail(array $data): bool {

        if(empty($data["email"])) {
            echo json_encode(["message" => "invalid email"]);
            return false;
        }

        $email = $data["email"];

        $response = $this->user_gateway->getUser($email);

        if ($response !== false) {
            echo json_encode(["message" => "email already exists"]);
            return false;
        }

        $pattern = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        if($email === ""  || !preg_match($pattern, $email)) {
            echo json_encode(["message" => "invalid email"]);
            return false;
        }

        return true;

    }

    public function validatePassword(array $data): bool {

        if(empty($data["password"])) {
            http_response_code(422);
            echo json_encode(["message" => "invalid password"]);
            return false;
        }

        $password = $data["password"];
        $pattern = '/^(?=.*[A-Za-z])(?=.*\d).{6,}$/';

        if(!preg_match($pattern, $password)) {
            http_response_code(422);
            echo json_encode(["message" => "password must contain atleast 6 digits, numbers and letters"]);
            return false;
        }

        return true;
    }

    public function hashPassword(string $password): string {
        $password = base64_encode($password);
        $password = password_hash($password, PASSWORD_DEFAULT);
        return $password;
    }
}
