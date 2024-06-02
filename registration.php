<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
    
    require_once 'database.php';

    // Check if email already exists
    $sql_check = 'SELECT COUNT(*) FROM users WHERE email = :email';
    $query_check = $db->prepare($sql_check);
    $query_check->bindValue(':email', $email);
    $query_check->execute();
    $count = $query_check->fetchColumn();

    if ($count > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Email already exists']);
        exit();
    }
    
    $sql = 'INSERT INTO users (first_name, last_name, email, password) VALUES (:first_name, :last_name, :email, :password)';
    $query = $db->prepare($sql);
    $query->bindValue(':first_name', $first_name);
    $query->bindValue(':last_name', $last_name);
    $query->bindValue(':email', $email);
    $query->bindValue(':password', $hashed_password);

    if ($query->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed']);
    }
} else {
    header('Location: index.html');
    exit();
}
?>