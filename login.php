<?php
session_start();
require_once 'config/db.php';

$error = '';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Prepared statement for secure login query
        $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            $stored = $user['password'];
            $info = password_get_info($stored);
            $isHash = isset($info['algoName']) && $info['algoName'] !== 'unknown';
            $authenticated = false;
            $upgradePlaintext = false;

            if ($isHash && password_verify($password, $stored)) {
                $authenticated = true;
            } elseif (!$isHash && is_string($stored) && strlen($stored) === strlen($password) && hash_equals($stored, $password)) {
                // Existing rows may still hold a plaintext password. Accept it once and replace it.
                $authenticated = true;
                $upgradePlaintext = true;
            }

            if ($authenticated) {
                if ($upgradePlaintext) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    if (is_string($newHash) && $newHash !== '') {
                        $update = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
                        if ($update) {
                            $userId = (int) $user['id'];
                            mysqli_stmt_bind_param($update, "si", $newHash, $userId);
                            mysqli_stmt_execute($update);
                            mysqli_stmt_close($update);
                        }
                    }
                }
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password credentials.";
            }
        } else {
            $error = "User not found.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<?php include 'templates/header.php'; ?>

<div class="row" style="margin-top: 60px;">
    <div class="col s12 m6 offset-m3">
        <div class="card p-4">
            <div class="card-content">
                <span class="card-title center red-text text-accent-4"><strong>ADMIN LOGIN</strong></span>

                <?php if ($error): ?>
                    <div class="card-panel red darken-3 white-text"><?php echo $error; ?></div>
                <?php endif; ?>

                <form action="login.php" method="POST">
                    <div class="input-field">
                        <input type="text" name="username" id="username" required>
                        <label for="username">Username</label>
                    </div>
                    <div class="input-field">
                        <input type="password" name="password" id="password" required>
                        <label for="password">Password</label>
                    </div>
                    <button type="submit" name="login" class="btn btn-cinema waves-effect waves-light width-100" style="width: 100%;">Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>