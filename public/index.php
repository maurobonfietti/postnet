<?php

require_once __DIR__ . '/../src/Controllers/CardController.php';
require_once __DIR__ . '/../src/Controllers/PaymentController.php';

if (php_sapi_name() === 'cli-server' && file_exists(__DIR__ . $_SERVER['REQUEST_URI'])) {
    return false;
}

header('Content-Type: application/json; charset=utf-8');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Method not allowed");
    }

    $path = trim($_SERVER['REQUEST_URI'], '/');

    switch ($path) {
        case 'register':
            $controller = new CardController();
            $response = $controller->register();
            break;
        case 'payment':
            $controller = new PaymentController();
            $response = $controller->doPayment();
            break;
        default:
            throw new Exception("Ruta no encontrada");
    }
} catch (Exception $e) {
    $response = ["status" => "error", "message" => $e->getMessage()];
    http_response_code(400);
}

echo json_encode($response);
