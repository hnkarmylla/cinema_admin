(function () {
  var STORAGE_KEY = "cinema_admin_demo_movies_v1";
  var SESSION_KEY = "cinema_admin_demo_user";

  var seed = [
    { id: 1, title: "Ashfall Station", genre: "Sci-Fi", duration: 132, rating: "PG-13", release: "2023-11-02" },
    { id: 2, title: "Paper Lanterns", genre: "Drama", duration: 104, rating: "PG", release: "2019-05-18" },
    { id: 3, title: "The Quiet Heist", genre: "Crime", duration: 121, rating: "R", release: "2021-08-27" },
    { id: 4, title: "Salt and Silver", genre: "Romance", duration: 98, rating: "PG-13", release: "2018-02-14" },
    { id: 5, title: "Orbit Choir", genre: "Musical", duration: 127, rating: "PG", release: "2025-01-09" },
    { id: 6, title: "Low Tide Letters", genre: "Mystery", duration: 110, rating: "PG-13", release: "2022-06-03" }
  ];

  var users = [
    { username: "mara.chen", name: "Mara Chen", email: "mara.chen@cinema.local", role: "Owner" },
    { username: "jonas.reid", name: "Jonas Reid", email: "jonas.reid@cinema.local", role: "Programmer" },
    { username: "aisha.noor", name: "Aisha Noor", email: "aisha.noor@cinema.local", role: "Box office" },
    { username: "leo.park", name: "Leo Park", email: "leo.park@cinema.local", role: "Floor" }
  ];

  var pendingFlash = "";
  var pendingDeleteId = null;

  function loadMovies() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      if (raw) {
        var parsed = JSON.parse(raw);
        if (Array.isArray(parsed)) return parsed;
      }
    } catch (err) {}
    return seed.map(function (movie) { return Object.assign({}, movie); });
  }

  function saveMovies(movies) {
    localStorage.setItem(STORAGE_KEY, JSON.stringify(movies));
  }

  function esc(value) {
    return String(value == null ? "" : value).replace(/[&<>"']/g, function (ch) {
      return { "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[ch];
    });
  }

  function yearOf(movie) {
    var release = String(movie.release || "");
    return release.length >= 4 ? release.slice(0, 4) : "";
  }

  function currentUser() {
    return sessionStorage.getItem(SESSION_KEY);
  }

  function go(hash, message) {
    pendingFlash = message || "";
    if (location.hash === hash) {
      render();
    } else {
      location.hash = hash;
    }
  }

  function showFlash() {
    var el = document.getElementById("flash");
    if (!pendingFlash) {
      el.hidden = true;
      el.textContent = "";
      return;
    }
    el.hidden = false;
    el.textContent = pendingFlash;
    pendingFlash = "";
  }

  function parseRoute() {
    var raw = (location.hash || "").replace(/^#\/?/, "");
    var parts = raw.split("/").filter(Boolean);
    return {
      screen: parts[0] || "",
      action: parts[1] || "",
      id: parts[2] || ""
    };
  }

  function nav(active) {
    return (
      '<button type="button" class="nav-btn' + (active === "movies" ? " is-active" : "") + '" data-go="#/movies">Movies</button>' +
      '<button type="button" class="nav-btn' + (active === "users" ? " is-active" : "") + '" data-go="#/users">Users</button>'
    );
  }

  function shell(active, user, body) {
    return (
      '<div class="shell">' +
        '<aside class="rail">' +
          '<p class="brand">Cinema Admin<small>Static demo</small></p>' +
          nav(active) +
          '<div class="spacer"></div>' +
          '<p class="who">Signed in as ' + esc(user) + '</p>' +
          '<button type="button" class="nav-btn" id="logout">Log out</button>' +
        "</aside>" +
        '<main class="main">' + body + "</main>" +
      "</div>" +
      '<nav class="bottom-nav">' +
        '<button type="button" data-go="#/movies"' + (active === "movies" ? ' class="is-active"' : "") + ">Movies</button>" +
        '<button type="button" data-go="#/users"' + (active === "users" ? ' class="is-active"' : "") + ">Users</button>" +
      "</nav>"
    );
  }

  function moviesView() {
    var movies = loadMovies();
    var cards = movies.map(function (movie) {
      var year = yearOf(movie);
      return (
        '<article class="card">' +
          '<div class="poster" aria-hidden="true"><span>' + esc(movie.title) + "</span></div>" +
          "<h2>" + esc(movie.title) + "</h2>" +
          '<p class="meta">' + esc(year) + " · " + esc(movie.genre) + " · " + esc(movie.duration) + " min</p>" +
          '<div class="card-actions">' +
            '<button type="button" class="btn ghost" data-go="#/movies/edit/' + esc(movie.id) + '">Edit</button>' +
            '<button type="button" class="btn quiet" data-delete="' + esc(movie.id) + '">Delete</button>' +
          "</div>" +
        "</article>"
      );
    }).join("");

    var grid = movies.length
      ? '<div class="grid">' + cards + "</div>"
      : '<p class="empty">No movies in this demo yet.</p>';

    return shell("movies", currentUser(),
      '<div class="screen-head">' +
        "<div><h1>Movies</h1><p class=\"sub\">Catalog in this browser only.</p></div>" +
        '<button type="button" class="btn primary" data-go="#/movies/new">Add movie</button>' +
      "</div>" + grid
    );
  }

  function formView(movie) {
    var editing = !!movie;
    var value = movie || { title: "", genre: "", duration: "", rating: "PG-13", release: "" };
    return shell("movies", currentUser(),
      '<div class="screen-head"><div><h1>' + (editing ? "Edit movie" : "Add movie") + "</h1>" +
        '<p class="sub">Saved only in this browser.</p></div></div>' +
      '<form class="panel" id="movie-form">' +
        '<input type="hidden" name="id" value="' + esc(editing ? movie.id : "") + '">' +
        "<label for=\"title\">Title</label>" +
        '<input id="title" name="title" required value="' + esc(value.title) + '">' +
        "<label for=\"genre\">Genre</label>" +
        '<input id="genre" name="genre" required value="' + esc(value.genre) + '">' +
        "<label for=\"duration\">Duration (minutes)</label>" +
        '<input id="duration" name="duration" type="number" min="1" required value="' + esc(value.duration) + '">' +
        "<label for=\"rating\">Rating</label>" +
        '<select id="rating" name="rating">' +
          ["G", "PG", "PG-13", "R"].map(function (rating) {
            return '<option' + (value.rating === rating ? " selected" : "") + ">" + rating + "</option>";
          }).join("") +
        "</select>" +
        "<label for=\"release\">Release date</label>" +
        '<input id="release" name="release" type="date" required value="' + esc(value.release) + '">' +
        '<div class="form-actions">' +
          '<button type="submit" class="btn primary">' + (editing ? "Save changes" : "Save movie") + "</button>" +
          '<button type="button" class="btn ghost" data-go="#/movies">Cancel</button>' +
        "</div>" +
      "</form>"
    );
  }

  function usersView() {
    var rows = users.map(function (user) {
      return "<tr><td>" + esc(user.username) + "</td><td>" + esc(user.name) +
        "</td><td>" + esc(user.email) + "</td><td>" + esc(user.role) + "</td></tr>";
    }).join("");
    return shell("users", currentUser(),
      '<div class="screen-head"><div><h1>Users</h1><p class="sub">Sample staff. Not stored anywhere.</p></div></div>' +
      '<div class="table-wrap"><table><thead><tr><th>Username</th><th>Name</th><th>Email</th><th>Role</th></tr></thead><tbody>' +
      rows + "</tbody></table></div>"
    );
  }

  function loginView() {
    return (
      '<div class="login-wrap"><form class="panel login-card" id="login-form">' +
        "<h1>Sign in</h1>" +
        '<p class="sub">Any username and password enters the demo.</p>' +
        '<label for="username">Username</label>' +
        '<input id="username" name="username" autocomplete="username" required>' +
        '<label for="password">Password</label>' +
        '<input id="password" name="password" type="password" autocomplete="current-password" required>' +
        '<div class="form-actions"><button type="submit" class="btn primary block">Log in</button></div>' +
      "</form></div>"
    );
  }

  function render() {
    var user = currentUser();
    var route = parseRoute();
    var app = document.getElementById("app");

    if (!user && route.screen !== "login") {
      location.hash = "#/login";
      return;
    }
    if (user && (route.screen === "" || route.screen === "login")) {
      location.hash = "#/movies";
      return;
    }

    showFlash();

    if (route.screen === "login") {
      app.innerHTML = loginView();
      return;
    }
    if (route.screen === "users") {
      app.innerHTML = usersView();
      return;
    }
    if (route.screen === "movies" && route.action === "new") {
      app.innerHTML = formView(null);
      return;
    }
    if (route.screen === "movies" && route.action === "edit") {
      var movies = loadMovies();
      var found = null;
      for (var i = 0; i < movies.length; i++) {
        if (String(movies[i].id) === String(route.id)) found = movies[i];
      }
      if (!found) {
        go("#/movies", "");
        return;
      }
      app.innerHTML = formView(found);
      return;
    }
    app.innerHTML = moviesView();
  }

  function openDialog(id) {
    var movies = loadMovies();
    var movie = null;
    for (var i = 0; i < movies.length; i++) {
      if (String(movies[i].id) === String(id)) movie = movies[i];
    }
    if (!movie) return;
    pendingDeleteId = movie.id;
    document.getElementById("dialog-copy").textContent = movie.title + " will leave this demo catalog.";
    document.getElementById("dialog").hidden = false;
    document.getElementById("dialog-cancel").focus();
  }

  function closeDialog() {
    pendingDeleteId = null;
    document.getElementById("dialog").hidden = true;
  }

  document.getElementById("dialog-cancel").addEventListener("click", closeDialog);
  document.getElementById("dialog").addEventListener("click", function (event) {
    if (event.target.id === "dialog") closeDialog();
  });
  document.getElementById("dialog-remove").addEventListener("click", function () {
    if (pendingDeleteId == null) return;
    var movies = loadMovies().filter(function (movie) {
      return String(movie.id) !== String(pendingDeleteId);
    });
    saveMovies(movies);
    closeDialog();
    go("#/movies", "Movie removed.");
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape" && !document.getElementById("dialog").hidden) closeDialog();
  });

  document.getElementById("app").addEventListener("click", function (event) {
    var goBtn = event.target.closest("[data-go]");
    if (goBtn) {
      go(goBtn.getAttribute("data-go"), "");
      return;
    }
    var del = event.target.closest("[data-delete]");
    if (del) openDialog(del.getAttribute("data-delete"));
    if (event.target.id === "logout") {
      sessionStorage.removeItem(SESSION_KEY);
      closeDialog();
      go("#/login", "");
    }
  });

  document.getElementById("app").addEventListener("submit", function (event) {
    if (event.target.id === "login-form") {
      event.preventDefault();
      var name = new FormData(event.target).get("username");
      sessionStorage.setItem(SESSION_KEY, String(name || "admin").trim() || "admin");
      go("#/movies", "");
      return;
    }
    if (event.target.id === "movie-form") {
      event.preventDefault();
      var data = new FormData(event.target);
      var movies = loadMovies();
      var id = String(data.get("id") || "");
      var record = {
        title: String(data.get("title") || "").trim(),
        genre: String(data.get("genre") || "").trim(),
        duration: Number(data.get("duration")) || 0,
        rating: String(data.get("rating") || ""),
        release: String(data.get("release") || "")
      };
      if (id) {
        movies = movies.map(function (movie) {
          if (String(movie.id) !== id) return movie;
          return Object.assign({}, movie, record);
        });
      } else {
        var next = movies.reduce(function (max, movie) {
          return Math.max(max, Number(movie.id) || 0);
        }, 0) + 1;
        record.id = next;
        movies.unshift(record);
      }
      saveMovies(movies);
      go("#/movies", "Movie saved.");
    }
  });

  window.addEventListener("hashchange", render);
  if (!location.hash) {
    location.hash = currentUser() ? "#/movies" : "#/login";
  } else {
    render();
  }
})();
