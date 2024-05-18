<?php
require_once 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo 'Wszystkie pola są wymagane!';
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $sql = 'INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)';
    $stmt = $db->prepare($sql);

    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo 'Rejestracja zakończona sukcesem!';
    } else {
        echo 'Wystąpił błąd podczas rejestracji!';
    }
} else {
    echo 'Nieprawidłowa metoda żądania!';
}
?>