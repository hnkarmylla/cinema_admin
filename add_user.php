<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';
require_once __DIR__ . '/includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && csrf_is_valid(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    $username = trim($_POST['username']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);

    $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, full_name, email) VALUES (?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssss", $username, $password, $full_name, $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: users.php");
    exit();
}
?>

<?php include 'templates/header.php'; ?>

<div class="row" style="margin-top: 30px;">
    <div class="col s12 m8 offset-m2">
        <div class="card p-4">
            <div class="card-content">
                <span class="card-title red-text">Add New Admin User</span>
                <form action="add_user.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="input-field">
                        <input type="text" name="username" required>
                        <label>Username</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="password" required>
                        <label>Password</label>
                    </div>
                    <div class="input-field">
                        <input type="text" name="full_name" required>
                        <label>Full Name</label>
                    </div>
                    <div class="input-field">
                        <input type="email" name="email" required>
                        <label>Email Address</label>
                    </div>
                    <button type="submit" name="submit" class="btn btn-cinema waves-effect waves-light">Create User</button>
                    <a href="users.php" class="btn grey waves-effect waves-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>