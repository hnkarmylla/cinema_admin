<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Cinema Admin Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
            background-color: #121212;
            color: #ffffff;
        }

        main {
            flex: 1 0 auto;
        }

        .nav-wrapper {
            background-color: #1f1f1f !important;
            padding: 0 20px;
        }

        .brand-logo {
            font-weight: bold;
            color: #e50914 !important;
        }

        .card {
            background-color: #1e1e1e;
            border-radius: 8px;
            color: #fff;
        }

        .table-custom {
            background: #1e1e1e;
            border-radius: 8px;
            overflow: hidden;
        }

        .table-custom th,
        .table-custom td {
            color: #fff;
            border-bottom: 1px solid #333;
        }

        .btn-cinema {
            background-color: #e50914 !important;
        }

        .input-field label {
            color: #aaa !important;
        }

        .input-field input,
        .input-field select {
            color: #fff !important;
            border-bottom: 1px solid #555 !important;
        }
    </style>
</head>

<body>
    <nav class="z-depth-2">
        <div class="nav-wrapper">
            <a href="index.php" class="brand-logo"><i class="material-icons left">local_movies</i>Cinema Admin</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <ul id="nav-mobile" class="right hide-on-med-and-down">
                    <li><a href="index.php"><i class="material-icons left">movie</i>Movies</a></li>
                    <li><a href="users.php"><i class="material-icons left">people</i>Users</a></li>
                    <li><span class="grey-text text-lighten-1" style="margin-right: 15px;">Hi, <?php echo htmlspecialchars($_SESSION['username']); ?></span></li>
                    <li><a href="logout.php" class="btn btn-cinema waves-effect waves-light"><i class="material-icons left">exit_to_app</i>Logout</a></li>
                </ul>
            <?php endif; ?>
        </div>
    </nav>
    <main class="container style=" margin-top: 30px;">