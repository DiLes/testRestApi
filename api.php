<?php

// Функция проверки базовой аутентификации
function verifyUser() {
    if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
        header('WWW-Authenticate: Basic realm="Access Restricted"');
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(["success" => false, "errors" => ["Access denied"]]);
        exit;
    }

    $validUsers = [
        'admin' => 'password', // Список пользователей и паролей
    ];

    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];

    if (!isset($validUsers[$username]) || $validUsers[$username] !== $password) {
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(["success" => false, "errors" => ["Invalid credentials"]]);
        exit;
    }
}

// Хранилища данных
$warehouseStocks = []; // Остатки по складам
$generalStocks = [];   // Общий остаток для ненайденных складов

// Функция обработки импорта остатков
function processStockImport() {
    global $warehouseStocks, $generalStocks;

    $rawInput = file_get_contents('php://input');
    $decodedInput = json_decode($rawInput, true);

    if (!is_array($decodedInput)) {
        http_response_code(400);
        echo json_encode(["success" => false, "errors" => ["Invalid request format"]]);
        return;
    }

    $errorMessages = [];

    foreach ($decodedInput as $warehouse) {
        $warehouseId = $warehouse['uuid'] ?? null;
        $stockList = $warehouse['stocks'] ?? null;

        if (!$warehouseId || !is_array($stockList)) {
            $errorMessages[] = "Invalid warehouse data format.";
            continue;
        }

        foreach ($stockList as $stock) {
            $stockId = $stock['uuid'] ?? null;
            $quantity = $stock['quantity'] ?? null;

            if (!$stockId || !is_numeric($quantity)) {
                $errorMessages[] = "Invalid stock data for warehouse: $warehouseId.";
                continue;
            }

            if (isset($warehouseStocks[$warehouseId])) {
                $warehouseStocks[$warehouseId][$stockId] = ($warehouseStocks[$warehouseId][$stockId] ?? 0) + $quantity;
            } else {
                $generalStocks[$stockId] = ($generalStocks[$stockId] ?? 0) + $quantity;
            }
        }
    }

    if (!empty($errorMessages)) {
        http_response_code(400);
        echo json_encode(["success" => false, "errors" => $errorMessages]);
    } else {
        echo json_encode(["success" => true]);
    }
}

// Основной обработчик запросов
header('Content-Type: application/json');

verifyUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    processStockImport();
} else {
    http_response_code(405);
    echo json_encode(["success" => false, "errors" => ["Method not allowed"]]);
}

?>
