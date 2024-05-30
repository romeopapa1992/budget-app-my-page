<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);

    if (empty($email) || empty($password)) {
        echo 'Wszystkie pola są wymagane!';
        exit();
    }

    require_once 'database.php';

    $sql = 'SELECT * FROM users WHERE email = :email';
    $query = $db->prepare($sql);
    $query->bindValue(':email', $email);
    $query->execute();

    $user = $query->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['first_name'] = $user['first_name'];

        if ($remember_me) {
            setcookie('user_id', $user['id'], time() + (86400 * 30), "/"); // 30 dni
            setcookie('user_name', $user['first_name'], time() + (86400 * 30), "/");
            echo "Setting cookies"; // Debugging message
        }

       header('Location: balance.html');
       exit();
    } else {
        echo 'Nieprawidłowy email lub hasło!';
    }
} else {
    header('Location: signin.html');
    exit();
}
?>