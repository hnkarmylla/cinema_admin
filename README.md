# Cinema Admin

Cinema Admin is a plain PHP application with no framework. It lists, adds, edits, and deletes movies and admin users. Each page uses mysqli prepared statements against MySQL.

## How a request is served

The running app is the PHP files at the repository root. A request hits a script such as `index.php`, `users.php`, `login.php`, `add_movie.php`, `edit_movie.php`, `add_user.php`, or `edit_user.php`. The script starts a PHP session, includes `config/db.php` when it needs the database, and prints HTML through `templates/header.php` and `templates/footer.php`. The header loads Materialize from a CDN.

`index.php` lists movies. `users.php` lists users. Deletes are `delete_movie.php` and `delete_user.php`. The `docs/` folder is not part of this request path.

## Database

`config/db.php` opens a mysqli connection. It currently uses host `localhost`, user `root`, an empty password, and database name `cinema_db`. Point the app at your own server by changing those four values in that file. Do not commit a real password.

This repository does not include a schema or SQL file. The movie queries use `id`, `title`, `genre`, `duration`, `rating`, and `release_date`. The user queries use `id`, `username`, `password`, `full_name`, `email`, and, on the list page, `created_at`.

## Login and session

`login.php` looks up a row in `users` by username. A stored password hash is checked with `password_verify`. A stored plaintext value that matches is accepted once, then replaced with `password_hash`. On success the script regenerates the session id and stores `user_id` and `username`. Pages other than login redirect to `login.php` when `user_id` is missing. `logout.php` clears the session and redirects to `login.php`.

## POST token

Movie and user deletes run only on POST, and only when `includes/csrf.php` accepts the posted `csrf_token`. Add and edit forms send the same token, and those scripts skip the write when the token does not match the session value. `delete_user.php` also skips the signed-in user's own id.

## Static mock

https://lollllz.github.io/cinema_admin/ is a static HTML, CSS, and JavaScript mock served from `docs/`. GitHub Pages does not run this PHP, and that mock does not talk to the database. The real app needs a PHP host and the database settings above.
