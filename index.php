<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once 'config/db.php';

// Fetch all movies using prepared statement
$stmt = mysqli_prepare($conn, "SELECT * FROM movies ORDER BY id DESC");
mysqli_stmt_execute($stmt);
$movies = mysqli_stmt_get_result($stmt);
?>

<?php include 'templates/header.php'; ?>

<div class="row" style="margin-top: 20px;">
    <div class="col s8">
        <h4 class="white-text"><i class="material-icons left red-text">movie</i>Movie Catalog</h4>
    </div>
    <div class="col s4 right-align" style="margin-top: 15px;">
        <a href="add_movie.php" class="btn btn-cinema waves-effect waves-light"><i class="material-icons left">add</i>Add New Movie</a>
    </div>
</div>

<table class="striped responsive-table table-custom">
    <thead>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Genre</th>
            <th>Duration</th>
            <th>Rating</th>
            <th>Release Date</th>
            <th class="center">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = mysqli_fetch_assoc($movies)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><strong><?php echo htmlspecialchars($row['title']); ?></strong></td>
                <td><?php echo htmlspecialchars($row['genre']); ?></td>
                <td><?php echo htmlspecialchars($row['duration']); ?> mins</td>
                <td><span class="new badge red darken-2" data-badge-caption=""><?php echo htmlspecialchars($row['rating']); ?></span></td>
                <td><?php echo htmlspecialchars($row['release_date']); ?></td>
                <td class="center">
                    <a href="edit_movie.php?id=<?php echo $row['id']; ?>" class="btn-small blue waves-effect waves-light"><i class="material-icons">edit</i></a>
                    <button type="button" class="btn-small red waves-effect waves-light" data-remove data-action="delete_movie.php" data-id="<?php echo (int) $row['id']; ?>" data-title="Remove this movie?" aria-label="Remove this movie"><i class="material-icons">delete</i></button>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php include 'templates/footer.php'; ?>