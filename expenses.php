<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $user_id = $_SESSION['user_id'];
    $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';
    $date_of_expense = isset($_POST['date']) ? trim($_POST['date']) : '';
    $category = isset($_POST['category']) ? trim($_POST['category']) : '';
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    function formatComment($comment) {
        $comment = strtolower($comment);
        return ucfirst($comment);
    }

    $comment = formatComment($comment);

    require_once 'database.php';

    $sql = 'SELECT id FROM expenses_category_default WHERE name = :category';
    $query = $db->prepare($sql);
    $query->bindValue(':category', $category, PDO::PARAM_STR);
    $query->execute();
    $category_data = $query->fetch(PDO::FETCH_ASSOC);

    $default_category_id = $category_data['id'];

    $sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE user_id = :user_id AND name = :category';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':category', $category, PDO::PARAM_STR);
    $query->execute();
    $assigned_category_data = $query->fetch(PDO::FETCH_ASSOC);

    if (!$assigned_category_data) {
        $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :category)';
        $query = $db->prepare($sql);
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->bindValue(':category', $category, PDO::PARAM_STR);
        $query->execute();
        $expense_category_assigned_to_user_id = $db->lastInsertId();
    } else {
        $expense_category_assigned_to_user_id = $assigned_category_data['id'];
    }

    $sql = 'SELECT id FROM payment_methods_default WHERE name = :payment_method';
    $query = $db->prepare($sql);
    $query->bindValue(':payment_method', $payment_method, PDO::PARAM_STR);
    $query->execute();
    $payment_method_data = $query->fetch(PDO::FETCH_ASSOC);

    $default_payment_method_id = $payment_method_data['id'];

    $sql = 'SELECT id FROM payment_methods_assigned_to_users WHERE user_id = :user_id AND name = :payment_method';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':payment_method', $payment_method, PDO::PARAM_STR);
    $query->execute();
    $assigned_payment_method_data = $query->fetch(PDO::FETCH_ASSOC);

    if (!$assigned_payment_method_data) {
        $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :payment_method)';
        $query = $db->prepare($sql);
        $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
        $query->bindValue(':payment_method', $payment_method, PDO::PARAM_STR);
        $query->execute();
        $payment_method_assigned_to_user_id = $db->lastInsertId();
    } else {
        $payment_method_assigned_to_user_id = $assigned_payment_method_data['id'];
    }

    $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES (:user_id, :expense_category_assigned_to_user_id, :payment_method_assigned_to_user_id, :amount, :date_of_expense, :expense_comment)';
    $query = $db->prepare($sql);
    $query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $query->bindValue(':expense_category_assigned_to_user_id', $expense_category_assigned_to_user_id, PDO::PARAM_INT);
    $query->bindValue(':payment_method_assigned_to_user_id', $payment_method_assigned_to_user_id, PDO::PARAM_INT);
    $query->bindValue(':amount', $amount, PDO::PARAM_STR);
    $query->bindValue(':date_of_expense', $date_of_expense, PDO::PARAM_STR);
    $query->bindValue(':expense_comment', $comment, PDO::PARAM_STR);
    $query->execute();

    echo json_encode(['status' => 'success', 'message' => 'Expense has been added successfully!']);

}
    
?>
