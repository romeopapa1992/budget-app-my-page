<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo 'Musisz być zalogowany, aby zobaczyć bilans.';
    exit();
}

$user_id = $_SESSION['user_id'];
$period = isset($_POST['period']) ? trim($_POST['period']) : 'current_month';
$startDate = isset($_POST['startDate']) ? trim($_POST['startDate']) : '';
$endDate = isset($_POST['endDate']) ? trim($_POST['endDate']) : '';

// Ustalenie zakresu dat na podstawie wybranego okresu
if ($period == 'current_month') {
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
} elseif ($period == 'previous_month') {
    $start_date = date('Y-m-01', strtotime('first day of last month'));
    $end_date = date('Y-m-t', strtotime('last day of last month'));
} elseif ($period == 'current_year') {
    $start_date = date('Y-01-01');
    $end_date = date('Y-12-31');
} elseif ($period == 'custom' && !empty($startDate) && !empty($endDate)) {
    $start_date = $startDate;
    $end_date = $endDate;
} else {
    echo 'Nieprawidłowy okres czasu!';
    exit();
}

require_once 'database.php';

// Pobranie sumy przychodów
$sql = 'SELECT SUM(amount) as total_income FROM incomes WHERE user_id = :user_id AND date_of_income BETWEEN :start_date AND :end_date';
$query = $db->prepare($sql);
$query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$query->bindValue(':start_date', $start_date, PDO::PARAM_STR);
$query->bindValue(':end_date', $end_date, PDO::PARAM_STR);
$query->execute();
$total_income = $query->fetch(PDO::FETCH_ASSOC)['total_income'];

// Pobranie sumy wydatków
$sql = 'SELECT SUM(amount) as total_expense FROM expenses WHERE user_id = :user_id AND date_of_expense BETWEEN :start_date AND :end_date';
$query = $db->prepare($sql);
$query->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$query->bindValue(':start_date', $start_date, PDO::PARAM_STR);
$query->bindValue(':end_date', $end_date, PDO::PARAM_STR);
$query->execute();
$total_expense = $query->fetch(PDO::FETCH_ASSOC)['total_expense'];

$balance = $total_income - $total_expense;

echo json_encode(['balance' => $balance, 'income' => $total_income, 'expense' => $total_expense]);
?>
