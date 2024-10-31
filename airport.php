<?php

// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=flyight_pool;charset=utf8', 'root', null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

// Получаем параметр query из GET-запроса
$query = isset($_GET['query']) ? $_GET['query'] : '';

// Если параметр не передан, возвращаем ошибку 400
if (empty($query)) {
    http_response_code(400);
    echo json_encode(["error" => "Параметр query обязателен, Request for parameters is required"]);
    exit;
}

// Приводим запрос к нижнему регистру для поиска без учета регистра
$queryLower = strtolower($query);

// SQL-запрос для поиска аэропортов по названию города или IATA-коду
$sql = "SELECT name, iata FROM airports WHERE LOWER(city) LIKE :query OR LOWER(name) LIKE :query OR LOWER(iata) = :iata";

// Подготовка и выполнение запроса
$stmt = $pdo->prepare($sql);
$stmt->execute([
    'query' => "%$queryLower%", 
    'iata' => $queryLower
]);

// Получаем результаты
$results = $stmt->fetchAll();

// Формируем ответ в нужном формате
$response = [
    "data" => [
        "items" => $results ?: [] // Если результатов нет, возвращаем пустой массив
    ]
];

// Устанавливаем заголовки и возвращаем ответ
http_response_code(200);
header('Content-Type: application/json');
echo json_encode($response);

?>
