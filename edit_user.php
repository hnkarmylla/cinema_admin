<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';
require_once __DIR__ . '/includes/csrf.php';

$id = $_GET['id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update']) && csrf_is_valid(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    $id = $_POST['id'];
    $username = trim($_POST['username']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);

    $stmt = mysqli_prepare($conn, "UPDATE users SET username=?, full_name=?, email=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "sssi", $username, $full_name, $email, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: users.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT id, username, full_name, email FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$u = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>

<?php include 'templates/header.php'; ?>

<div class="row" style="margin-top: 30px;">
    <div class="col s12 m8 offset-m2">
        <div class="card p-4">
            <div class="card-content">
                <span class="card-title red-text">Edit User</span>
                <form action="edit_user.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                    <div class="input-field">
                        <input type="text" name="username" value="<?php echo htmlspecialchars($u['username']); ?>" required>
                    </div>
                    <div class="input-field">
                        <input type="text" name="full_name" value="<?php echo htmlspecialchars($u['full_name']); ?>" required>
                    </div>
                    <div class="input-field">
                        <input type="email" name="email" value="<?php echo htmlspecialchars($u['email']); ?>" required>
                    </div>
                    <button type="submit" name="update" class="btn btn-cinema waves-effect waves-light">Update User</button>
                    <a href="users.php" class="btn grey waves-effect waves-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>