<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        echo 'Wszystkie pola są wymagane!';
        exit();
    }

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    require_once 'database.php';
    
    $sql = 'INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)';
    $query = $db->prepare($sql);
    $query->bindValue(':first_name', $first_name);
    $query->bindValue(':last_name', $last_name);
    $query->bindValue(':email', $email);
    $query->bindValue(':password', $hashed_password);

    if ($query->execute()) {
        echo 'Rejestracja zakończona sukcesem! SUKCES!';
    } else {
        echo 'Wystąpił błąd podczas rejestracji!';
    }
} else {
    header('Location: index.html');
    exit();
}
?>
