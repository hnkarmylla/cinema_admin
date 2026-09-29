<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';
require_once __DIR__ . '/includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id']) && csrf_is_valid(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    $id = (int) $_POST['id'];
    if ($id > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM movies WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: index.php");
exit();
