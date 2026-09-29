<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';

$stmt = mysqli_prepare($conn, "SELECT id, username, full_name, email, created_at FROM users ORDER BY id DESC");
mysqli_stmt_execute($stmt);
$users = mysqli_stmt_get_result($stmt);
?>

<?php include 'templates/header.php'; ?>

<div class="row" style="margin-top: 20px;">
    <div class="col s8">
        <h4 class="white-text"><i class="material-icons left red-text">people</i>Admin Users</h4>
    </div>
    <div class="col s4 right-align" style="margin-top: 15px;">
        <a href="add_user.php" class="btn btn-cinema waves-effect waves-light"><i class="material-icons left">person_add</i>Add Admin User</a>
    </div>
</div>

<table class="striped responsive-table table-custom">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Created At</th>
            <th class="center">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($u = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($u['username']); ?></strong></td>
                <td><?php echo htmlspecialchars($u['full_name']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td><?php echo htmlspecialchars($u['created_at']); ?></td>
                <td class="center">
                    <a href="edit_user.php?id=<?php echo $u['id']; ?>" class="btn-small blue waves-effect waves-light"><i class="material-icons">edit</i></a>
                    <a href="delete_user.php?id=<?php echo $u['id']; ?>" onclick="return confirm('Delete this user?');" class="btn-small red waves-effect waves-light"><i class="material-icons">delete</i></a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'templates/footer.php'; ?>