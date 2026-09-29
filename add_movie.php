<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';
require_once __DIR__ . '/includes/csrf.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit']) && csrf_is_valid(isset($_POST['csrf_token']) ? $_POST['csrf_token'] : null)) {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $duration = (int)$_POST['duration'];
    $rating = trim($_POST['rating']);
    $release_date = $_POST['release_date'];

    $stmt = mysqli_prepare($conn, "INSERT INTO movies (title, genre, duration, rating, release_date) VALUES (?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "ssiss", $title, $genre, $duration, $rating, $release_date);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php");
        exit();
    }
    mysqli_stmt_close($stmt);
}
?>

<?php include 'templates/header.php'; ?>

<div class="row" style="margin-top: 30px;">
    <div class="col s12 m8 offset-m2">
        <div class="card p-4">
            <div class="card-content">
                <span class="card-title red-text">Add New Movie</span>
                <form action="add_movie.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="input-field">
                        <input type="text" name="title" required>
                        <label>Movie Title</label>
                    </div>
                    <div class="input-field">
                        <input type="text" name="genre" required>
                        <label>Genre (e.g., Action, Sci-Fi)</label>
                    </div>
                    <div class="input-field">
                        <input type="number" name="duration" required>
                        <label>Duration (Minutes)</label>
                    </div>
                    <div class="input-field">
                        <input type="text" name="rating" required>
                        <label>Rating (e.g., PG-13, R)</label>
                    </div>
                    <div class="input-field">
                        <input type="date" name="release_date" required style="color:#fff;">
                    </div>
                    <button type="submit" name="submit" class="btn btn-cinema waves-effect waves-light">Save Movie</button>
                    <a href="index.php" class="btn grey waves-effect waves-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>