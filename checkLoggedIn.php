<?php
session_start();
$response = ['loggedIn' => isset($_SESSION['user_id'])];
echo json_encode($response);
?>
