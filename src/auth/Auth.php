<?php

namespace app\auth;

use app\error\InvalidSignatureException;
use app\error\TokenExpiredException;
use app\gateway\UserGateway;
use app\jwt\JWTCodec;
use Exception;

class Auth {

    public int $user_id;

    public function __construct(private UserGateway $user_gateway,
                                private JWTCodec $codec){
        
    }

    public function authenticateAccessToken(): bool {
        if (! preg_match("/^Bearer\s+(.*)$/", $_SERVER["HTTP_AUTHORIZATION"], $matches)) {
            http_response_code(400);
            echo json_encode(["message" => "incomplete authorization header"]);
            return false;
        }

        try {
            $data = $this->codec->decode($matches[1]);
        } catch(InvalidSignatureException) {
            
            http_response_code(401);
            echo json_encode(["message" => "invalid signature"]);
            return false;  
        } catch(TokenExpiredException){

            http_response_code(401);
            echo json_encode(["message" => "token has expired"]);
            return false;
        } catch (Exception $e){

            http_response_code(400);
            echo json_encode(["message" => $e->getMessage()]);
            return false;
        }

        $this->user_id = $data["sub"];
        
        return true;
    }


}