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
    // Prevent deleting self
    if ($id > 0 && $id !== (int) $_SESSION['user_id']) {
        $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: users.php");
exit();
