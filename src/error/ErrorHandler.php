<?php

namespace app\error;

use ErrorException;
use Throwable;

require __DIR__ . "/../../vendor/autoload.php";

class ErrorHandler {
    
    public static function handleError(

        int $errno,
        string $errstr,
        string $errfile,
        int $errline
        
    ): void {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }

    public static function handleException(Throwable $exception): void {
        http_response_code(500);
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }
}

