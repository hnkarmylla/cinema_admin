<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';

$id = $_GET['id'] ?? null;

if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $duration = (int)$_POST['duration'];
    $rating = trim($_POST['rating']);
    $release_date = $_POST['release_date'];

    $stmt = mysqli_prepare($conn, "UPDATE movies SET title=?, genre=?, duration=?, rating=?, release_date=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssissi", $title, $genre, $duration, $rating, $release_date, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: index.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT * FROM movies WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$movie = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>

<?php include 'templates/header.php'; ?>

<div class="row" style="margin-top: 30px;">
    <div class="col s12 m8 offset-m2">
        <div class="card p-4">
            <div class="card-content">
                <span class="card-title red-text">Edit Movie</span>
                <form action="edit_movie.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $movie['id']; ?>">
                    <div class="input-field">
                        <input type="text" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
                    </div>
                    <div class="input-field">
                        <input type="text" name="genre" value="<?php echo htmlspecialchars($movie['genre']); ?>" required>
                    </div>
                    <div class="input-field">
                        <input type="number" name="duration" value="<?php echo htmlspecialchars($movie['duration']); ?>" required>
                    </div>
                    <div class="input-field">
                        <input type="text" name="rating" value="<?php echo htmlspecialchars($movie['rating']); ?>" required>
                    </div>
                    <div class="input-field">
                        <input type="date" name="release_date" value="<?php echo htmlspecialchars($movie['release_date']); ?>" required style="color:#fff;">
                    </div>
                    <button type="submit" name="update" class="btn btn-cinema waves-effect waves-light">Update Movie</button>
                    <a href="index.php" class="btn grey waves-effect waves-light">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'templates/footer.php'; ?>