<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $user_id = $_SESSION['user_id'];
    $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
    $date_of_income = isset($_POST['date']) ? trim($_POST['date']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    function formatComment($comment) {
        $comment = strtolower($comment);
        return ucfirst($comment);
    }

    $comment = formatComment($comment);

    require_once 'database.php';

    $sql = 'SELECT id FROM incomes_category_default WHERE name = :category';
    $query = $db->prepare($sql);
    $query->bindValue(':category', $category, PDO::PARAM_STR);
    $query->execute();
    $category_data = $query->fetch(PDO::FETCH_ASSOC);

    $default_category_id = $category_data['id'];

    $sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE user_id = :user_id AND name = :category';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':category', $category, PDO::PARAM_STR);
    $query->execute();
    $assigned_category_data = $query->fetch(PDO::FETCH_ASSOC);

    if (!$assigned_category_data) {
        $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :category)';
        $query = $db->prepare($sql);
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->bindValue(':category', $category, PDO::PARAM_STR);
        $query->execute();

        $income_category_assigned_to_user_id = $db->lastInsertId();
    } else {
        $income_category_assigned_to_user_id = $assigned_category_data['id'];
    }

    $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) VALUES (:user_id, :income_category_assigned_to_user_id, :amount, :date_of_income, :income_comment)';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':income_category_assigned_to_user_id', $income_category_assigned_to_user_id, PDO::PARAM_INT);
    $query->bindValue(':amount', $amount, PDO::PARAM_STR);
    $query->bindValue(':date_of_income', $date_of_income, PDO::PARAM_STR);
    $query->bindValue(':income_comment', $comment, PDO::PARAM_STR);
    $query->execute();

    echo json_encode(['status' => 'success', 'message' => 'Income has been added successfully!']);
}
?> 