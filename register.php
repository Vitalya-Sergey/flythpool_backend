<?php 

/**
 * подключение к БД , dbname = название своeй бд
 */
$bd = new PDO(
    'mysql:host=localhost;dbname=flythpool;charset=utf8', 
    'root', 
    null, 
    [PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

/**
 * Данные которые передали через form-data , хранятся в виде first_name - abiba
 */
$formData = $_POST;

/**
 * Массив ошибок , хранятся в виде first_name - поле не заполнено
 */
$listErrors = [];

/**
 * Поля которые должны прийти
 */
$fields = ['first_name', 'last_name', 'phone', 'document_number', 'password'];

// Задание 1: Проверка наличия всех полей
foreach ($fields as $field) {
    if (empty($formData[$field])) {
        $listErrors[$field] = "Поле '$field' не заполнено";
    }
}

// Задание 2: Проверка, что пользователь не существует по номеру телефона
if (!empty($formData['phone'])) {
    $stmt = $bd->prepare("SELECT * FROM users WHERE phone = ?");
    $stmt->execute([$formData['phone']]);
    $user = $stmt->fetch();

    if ($user) {
        $listErrors['phone'] = "Пользователь с таким номером телефона уже существует";
    }
}

// Условие выполнится , если массив ошибок не пустой
if (!empty($listErrors)) {
    // Массив ошибок не пустой - отправляем его (json_encode нужен чтобы корректно передать массив)
    
    http_response_code(422);

    echo json_encode([
        "error" => [
            "code" => 422,
            "message" => "Validation error",
            "errors" => $listErrors,
        ]
    ]);
    exit; // Завершаем выполнение скрипта
}

// Условие выполнится , если массив ошибок пустой
if (empty($listErrors)) {
    // Задание 3: Запись данных в таблицу users
    $stmt = $bd->prepare("INSERT INTO users (first_name, last_name, phone, document_number, password) VALUES (?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $formData['first_name'],
        $formData['last_name'],
        $formData['phone'],
        $formData['document_number'],
        $formData['password']
    ]);

    http_response_code(201);
}

?>
