<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    echo "Formularz został wysłany.<br>";
    echo "Email: " . htmlspecialchars($_POST['email']) . "<br>";
    echo "Password: " . htmlspecialchars($_POST['password']) . "<br>";

    $email = $_POST['email'];
    $password = $_POST['password'];

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
        echo 'Logowanie zakończone sukcesem! Witaj, ' . htmlspecialchars($user['first_name']) . '!';
    } else {
        echo 'Nieprawidłowy email lub hasło!';
    }
} else {
    header('Location: signin.html');
    exit();
}

?>
