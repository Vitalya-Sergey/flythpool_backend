<?php

// Подключение к базе данных
$pdo = new PDO('mysql:host=localhost;dbname=flyight_pool;charset=utf8', 'root', null, [
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$token = getallheaders()['Authorization'];

$stmt = $pdo->query(
    "SELECT document_number FROM users WHERE api_token = '$token'"
)->fetchAll()[0];
$document_number = $stmt['document_number'];

// echo json_encode($document_number);

$place = $pdo->query(
    "SELECT place_from, place_back FROM passengers WHERE document_number = '$document_number'"
)->fetchAll();

echo json_encode($place);

?>
