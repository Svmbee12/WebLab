<?php
session_start();

// Database файл (JSON ашиглана)
$users_file = 'users.json';

// Хэрэглэгчдийг унших функц
function getUsers() {
  global $users_file;
  if (file_exists($users_file)) {
    $data = file_get_contents($users_file);
    return json_decode($data, true);
  }
  return [];
}

// Хэрэглэгч хадгалах функц
function saveUser($username, $password, $email) {
  global $users_file;
  $users = getUsers();
  $users[$username] = [
    'password' => password_hash($password, PASSWORD_DEFAULT),
    'email' => $email,
    'created_at' => date('Y-m-d H:i:s')
  ];
  file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT));
}

// Login шалгах
if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $users = getUsers();
  
  if (isset($users[$username]) && password_verify($password, $users[$username]['password'])) {
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $username;
    header('Location: ?page=home');
    exit();
  } else {
    $login_error = "Нэвтрэх нэр эсвэл нууц үг буруу байна!";
  }
}

// Sign Up шалгах
if (isset($_POST['signup'])) {
  $username = $_POST['signup_username'];
  $email = $_POST['signup_email'];
  $password = $_POST['signup_password'];
  $confirm_password = $_POST['confirm_password'];
  $users = getUsers();
  
  if (isset($users[$username])) {
    $signup_error = "Энэ хэрэглэгчийн нэр аль хэдийн бүртгэлтэй байна!";
  } elseif ($password !== $confirm_password) {
    $signup_error = "Нууц үг таарахгүй байна!";
  } elseif (strlen($password) < 6) {
    $signup_error = "Нууц үг хамгийн багадаа 6 тэмдэгт байх ёстой!";
  } else {
    saveUser($username, $password, $email);
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $username;
    header('Location: ?page=home');
    exit();
  }
}

// Logout хийх
if (isset($_GET['logout'])) {
  session_destroy();
  header('Location: ?page=login');
  exit();
}

// GET параметрээр хуудас солих
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Хэрэв нэвтрээгүй бол login хуудас руу шилжүүлэх
if (!isset($_SESSION['logged_in']) && $page != 'login') {
  $page = 'login';
}

// CSS код
$css = "
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  text-decoration: none;
  list-style: none;
  font-family: sans-serif;
}

:root {
  --color-background: 11, 22, 34;
  --color-foreground: 21, 31, 46;
  --color-card: 21, 31, 46;
  --color-card-hover: 30, 42, 56;
  --color-text: 159, 173, 189;
  --color-text-light: 114, 138, 161;
  --color-shadow-blue: 0, 5, 15;
  --accent-gold: #ffc107;
}

body {
  background: rgb(var(--color-background)) !important;
  color: rgb(var(--color-text));
  font-family: 'Poppins', sans-serif;
  padding-top: 70px;
}

.bg-dark {
  background: rgb(var(--color-background)) !important;
}

.navbar,
.navbar * {
  background: rgb(21, 31, 46) !important;
  border: none !important;
  box-shadow: none !important;
  border-bottom: 2px solid #ffffff;
}

.navbar-collapse {
  background: rgb(21, 31, 46) !important;
}

.logo_img {
  width: 40px;
  height: 40px;
  object-fit: contain;
}

.hero-section {
  background: 
    linear-gradient(180deg, rgba(15,22,31,0.85), rgba(22, 65, 113, 0.95)),
    url('img/hero-bg.jpg') center/cover no-repeat !important;
  color: rgb(240, 243, 246) !important;
  text-align: center;
}

.card {
  background: rgb(var(--color-card)) !important;
  border: none;
  box-shadow: 0 8px 24px rgba(var(--color-shadow-blue), 0.4);
  transition: 0.3s;
}

.card:hover {
  background: rgb(var(--color-card-hover)) !important;
  transform: translateY(-6px);
}

h1 span,
.text-warning,
.card-title {
  color: var(--accent-gold) !important;
}

.muted {
  color: rgb(var(--color-text-light));
}

footer {
  background: rgb(6, 12, 19) !important;
  color: rgb(44, 120, 206);
  padding: 20px 0;
  text-align: center;
}

/* Login/Signup Container */
.auth-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  background: linear-gradient(135deg, rgb(11, 22, 34) 0%, rgb(15, 30, 45) 100%);
}

.auth-wrapper {
  display: flex;
  max-width: 1000px;
  width: 100%;
  background: rgb(var(--color-card));
  border-radius: 20px;
  overflow: hidden;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
  position: relative;
}

.auth-forms-container {
  flex: 1;
  position: relative;
  overflow: hidden;
}

.auth-side {
  padding: 50px 40px;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  opacity: 0;
  transition: all 0.6s ease-in-out;
  transform: translateX(-100%);
}

.auth-side.active {
  opacity: 1;
  transform: translateX(0);
  position: relative;
}

.auth-side h2 {
  color: var(--accent-gold);
  margin-bottom: 10px;
  font-size: 28px;
}

.auth-side p {
  color: rgb(var(--color-text-light));
  margin-bottom: 30px;
}

.auth-side .form-control {
  background: rgba(var(--color-background), 0.8) !important;
  border: 1px solid rgba(var(--color-text-light), 0.3);
  color: rgb(var(--color-text));
  padding: 12px 15px;
  border-radius: 8px;
  margin-bottom: 15px;
}

.auth-side .form-control:focus {
  background: rgba(var(--color-background), 0.9) !important;
  border-color: var(--accent-gold);
  color: rgb(var(--color-text));
  box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25);
}

.auth-side .btn-warning {
  width: 100%;
  padding: 12px;
  font-weight: bold;
  border-radius: 8px;
  margin-top: 10px;
  background-color: var(--accent-gold);
  border-color: var(--accent-gold);
}

.auth-side .btn-warning:hover {
  background-color: #e0a800;
  border-color: #d39e00;
}

.auth-toggle {
  flex: 1;
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  padding: 50px 40px;
  text-align: center;
  color: white;
}

.auth-toggle h3 {
  font-size: 24px;
  margin-bottom: 15px;
  color: var(--accent-gold);
}

.auth-toggle p {
  margin-bottom: 25px;
  color: rgb(var(--color-text));
}

.auth-toggle .btn-outline-warning {
  padding: 10px 40px;
  border: 2px solid var(--accent-gold);
  background: transparent;
  color: var(--accent-gold);
  border-radius: 25px;
  font-weight: bold;
  transition: 0.3s;
}

.auth-toggle .btn-outline-warning:hover {
  background: var(--accent-gold);
  color: #1a1a2e;
}

.alert-danger {
  background: rgba(220, 53, 69, 0.2) !important;
  border: 1px solid rgba(220, 53, 69, 0.5);
  color: #ff6b6b;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 15px;
}

.alert-success {
  background: rgba(40, 167, 69, 0.2) !important;
  border: 1px solid rgba(40, 167, 69, 0.5);
  color: #51cf66;
  border-radius: 8px;
  padding: 12px;
  margin-bottom: 15px;
}

@media (max-width: 768px) {
  .auth-wrapper {
    flex-direction: column;
  }
  
  .auth-toggle {
    order: -1;
    padding: 30px 20px;
  }
  
  .auth-side {
    padding: 30px 20px;
  }
}
";
?>
<!DOCTYPE html>
<html lang="mn">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo ($page == 'home') ? 'OtakuHub - Home' : (($page == 'about') ? 'About - OtakuHub' : (($page == 'login') ? 'Login - OtakuHub' : 'Anime - OtakuHub')); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style><?php echo $css; ?></style>
</head>
<body class="bg-dark text-light">

  <?php if ($page != 'login') { ?>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-black fixed-top">
    <div class="container">
      <a class="navbar-brand fw-bold text-warning d-flex align-items-center" href="?page=home">
        <span style="font-size: 24px; margin-right: 10px;">🎌</span> OtakuHub
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navMenu">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link <?php echo ($page == 'home') ? 'active' : ''; ?>" href="?page=home">Home</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($page == 'about') ? 'active' : ''; ?>" href="?page=about">About</a></li>
          <li class="nav-item"><a class="nav-link <?php echo ($page == 'anime') ? 'active' : ''; ?>" href="?page=anime">Anime</a></li>
          <li class="nav-item"><span class="nav-link text-warning">👤 <?php echo $_SESSION['username']; ?></span></li>
          <li class="nav-item"><a class="nav-link text-danger" href="?logout=true">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <?php } ?>

  <?php
  // Хуудсын агуулгыг харуулах
  if ($page == 'login') {
    // LOGIN/SIGNUP PAGE
    ?>
    <div class="auth-container">
      <div class="auth-wrapper">
        
        <div class="auth-forms-container">
          <!-- LOGIN FORM -->
          <div class="auth-side active" id="loginForm">
            <h2>🎌 Нэвтрэх</h2>
            <p>OtakuHub руу тавтай морил!</p>
            
            <?php if (isset($login_error)) { ?>
              <div class="alert alert-danger"><?php echo $login_error; ?></div>
            <?php } ?>
            
            <form method="POST">
              <input type="text" class="form-control" name="username" placeholder="Нэвтрэх нэр" required>
              <input type="password" class="form-control" name="password" placeholder="Нууц үг" required>
              <button type="submit" name="login" class="btn btn-warning">Нэвтрэх</button>
            </form>
          </div>

          <!-- SIGNUP FORM -->
          <div class="auth-side" id="signupForm">
            <h2>📝 Бүртгүүлэх</h2>
            <p>Шинэ данс үүсгээрэй!</p>
            
            <?php if (isset($signup_error)) { ?>
              <div class="alert alert-danger"><?php echo $signup_error; ?></div>
            <?php } ?>
            
            <form method="POST">
              <input type="text" class="form-control" name="signup_username" placeholder="Нэвтрэх нэр" required>
              <input type="email" class="form-control" name="signup_email" placeholder="И-мэйл" required>
              <input type="password" class="form-control" name="signup_password" placeholder="Нууц үг (минимум 6 тэмдэгт)" required>
              <input type="password" class="form-control" name="confirm_password" placeholder="Нууц үг давтах" required>
              <button type="submit" name="signup" class="btn btn-warning">Бүртгүүлэх</button>
            </form>
          </div>
        </div>

        <!-- TOGGLE PANEL -->
        <div class="auth-toggle" id="togglePanel">
          <div id="toSignup">
            <h3>Шинэ хэрэглэгч үү?</h3>
            <p>Бүртгүүлээд OtakuHub-ын бүх боломжуудыг ашигла!</p>
            <button class="btn btn-outline-warning" onclick="showSignup()">Бүртгүүлэх</button>
          </div>
          <div id="toLogin" style="display: none;">
            <h3>Аль хэдийн бүртгэлтэй юу?</h3>
            <p>Нэвтрээд үргэлжлүүлээрэй!</p>
            <button class="btn btn-outline-warning" onclick="showLogin()">Нэвтрэх</button>
          </div>
        </div>

      </div>
    </div>

    <script>
      function showSignup() {
        document.getElementById('loginForm').classList.remove('active');
        document.getElementById('signupForm').classList.add('active');
        document.getElementById('toSignup').style.display = 'none';
        document.getElementById('toLogin').style.display = 'block';
      }

      function showLogin() {
        document.getElementById('signupForm').classList.remove('active');
        document.getElementById('loginForm').classList.add('active');
        document.getElementById('toLogin').style.display = 'none';
        document.getElementById('toSignup').style.display = 'block';
      }
    </script>
    <?php
  } elseif ($page == 'home') {
    // HOME PAGE
    ?>
    <section class="hero-section text-center d-flex flex-column justify-content-center align-items-center vh-100 bg-gradient">
      <h1 class="display-4 fw-bold">Welcome to <span class="text-warning">OtakuHub</span></h1>
      <p class="lead">Your ultimate hub for anime & manga adventures</p>
      <a href="?page=anime" class="btn btn-warning btn-lg mt-3">Explore Anime</a>
    </section>

    <section class="container py-5">
      <h2 class="text-center mb-4">🔥 Golden Trio</h2>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card bg-secondary text-light h-100">
            <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx20-dE6UHbFFg1A5.jpg" class="card-img-top" alt="Naruto">
            <div class="card-body text-center">
              <h5 class="card-title">Naruto</h5>
              <a href="https://anilist.co/anime/20/Naruto" class="btn btn-outline-warning btn-sm">View</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-secondary text-light h-100">
            <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx21-ELSYx3yMPcKM.jpg" class="card-img-top" alt="One Piece">
            <div class="card-body text-center">
              <h5 class="card-title">One Piece</h5>
              <a href="https://anilist.co/anime/21/ONE-PIECE" class="btn btn-outline-warning btn-sm">View</a>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card bg-secondary text-light h-100">
            <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx269-d2GmRkJbMopq.png" class="card-img-top" alt="Bleach">
            <div class="card-body text-center">
              <h5 class="card-title">Bleach</h5>
              <a href="https://anilist.co/anime/257/Bleach" class="btn btn-outline-warning btn-sm">View</a>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer>
      <p>&copy; 2024 OtakuHub. All rights reserved.</p>
    </footer>
    <?php
  } elseif ($page == 'about') {
    // ABOUT PAGE
    ?>
    <div class="container py-5 mt-5">
      <h2 class="text-warning mb-3">OtakuHub гэж юу вэ?</h2>
      <p>OtakuHub бол аниме болон манга сонирхогчдын нэгдсэн төв юм. Энд та дуртай аниме, мангагаа хайж олох, шинэ контентыг олж мэдэх боломжтой.</p>

      <h2 class="text-warning mt-5">Вэбсайт хэрхэн ажилладаг вэ?</h2>
      <p>OtakuHub нь янз бүрийн эх сурвалжаас мэдээлэл цуглуулж нэг дор харуулдаг. Та энд trending, popular болон upcoming аниме нарыг олж харах боломжтой.</p>

      <h2 class="text-warning mt-5">Disclaimer</h2>
      <p>OtakuHub нь стриминг үйлчилгээ биш бөгөөд зөвхөн мэдээлэл өгөх зорилготой. Бүх контент нь гадны эх сурвалжаас авсан мэдээлэл юм.</p>
    </div>

    <footer>
      <p>&copy; 2024 OtakuHub. All rights reserved.</p>
    </footer>
    <?php
  } elseif ($page == 'anime') {
    // ANIME PAGE
    ?>
    <main class="container py-5 mt-5">

      <!-- Trending Section -->
      <section class="mb-5">
        <h2 class="text-warning mb-4">🔥 Trending</h2>
        <div class="row g-4">
          <div class="col-md-3 col-sm-6">
            <div class="card bg-secondary text-light h-100 shadow-sm">
              <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx153800-8SpzdHOaZCoU.jpg" class="card-img-top" alt="One Punch Man S3">
              <div class="card-body text-center">
                <h5 class="card-title">One Punch Man S3</h5>
                <a href="https://anilist.co/anime/153800/One-Punch-Man-3/" target="_blank" class="btn btn-outline-warning btn-sm">View</a>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card bg-secondary text-light h-100 shadow-sm">
              <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx182896-mvxTVHGdDB4q.jpg" class="card-img-top" alt="Boku no Hero Academia">
              <div class="card-body text-center">
                <h5 class="card-title">Boku no Hero Academia FINAL</h5>
                <a href="https://anilist.co/anime/182896/" target="_blank" class="btn btn-outline-warning btn-sm">View</a>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card bg-secondary text-light h-100 shadow-sm">
              <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx177937-Tzgg6rAdhCoH.jpg" class="card-img-top" alt="SPY×FAMILY Season 3">
              <div class="card-body text-center">
                <h5 class="card-title">SPY×FAMILY Season 3</h5>
                <a href="https://anilist.co/anime/177937/" target="_blank" class="btn btn-outline-warning btn-sm">View</a>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card bg-secondary text-light h-100 shadow-sm">
              <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx166613-YzuAjRNJKo1K.png" class="card-img-top" alt="Jigokuraku">
              <div class="card-body text-center">
                <h5 class="card-title">Jigokuraku 2nd Season</h5>
                <a href="https://anilist.co/anime/166613/" target="_blank" class="btn btn-outline-warning btn-sm">View</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Popular This Season -->
      <section class="mb-5">
        <h2 class="text-warning mb-4">⭐ Popular This Season</h2>
        <div class="row g-4">
          <div class="col-md-3 col-sm-6">
            <div class="card bg-secondary text-light h-100 shadow-sm">
              <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx153800-8SpzdHOaZCoU.jpg" class="card-img-top" alt="One Punch Man 3">
              <div class="card-body text-center">
                <h5 class="card-title">One Punch Man 3</h5>
                <a href="https://anilist.co/anime/153800/" target="_blank" class="btn btn-outline-warning btn-sm">View</a>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card bg-secondary text-light h-100 shadow-sm">
              <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx177937-Tzgg6rAdhCoH.jpg" class="card-img-top" alt="SPY×FAMILY">
              <div class="card-body text-center">
                <h5 class="card-title">SPY×FAMILY Season 3</h5>
                <a href="https://anilist.co/anime/177937/" target="_blank" class="btn btn-outline-warning btn-sm">View</a>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card bg-secondary text-light h-100 shadow-sm">
              <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx182896-mvxTVHGdDB4q.jpg" class="card-img-top" alt="My Hero Academia">
              <div class="card-body text-center">
                <h5 class="card-title">My Hero Academia FINAL</h5>
                <a href="https://anilist.co/anime/182896/" target="_blank" class="btn btn-outline-warning btn-sm">View</a>
              </div>
            </div>
          </div>
          <div class="col-md-3 col-sm-6">
            <div class="card bg-secondary text-light h-100 shadow-sm">
              <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx166613-YzuAjRNJKo1K.png" class="card-img-top" alt="Jigokuraku">
              <div class="card-body text-center">
                <h5 class="card-title">Jigokuraku 2nd Season</h5>
                <a href="https://anilist.co/anime/166613/" target="_blank" class="btn btn-outline-warning btn-sm">View</a>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Upcoming Next Season -->
      <section class="mb-5">
        <h2 class="text-warning mb-4">⏳ Upcoming Next Season</h2>
        <div class="row g-4">
          <div class="col-md-3 col-sm-6">
            <div class="card bg-secondary text-light h-100 shadow-sm">
              <img src="https://s4.anilist.co/file/anilistcdn/media/anime/cover/large/bx172463-NU2G6VHIHivv.png" class="card-img-top" alt="Jujutsu Kaisen">
              <div class="card-body text-center">
                <h5 class="card-title">Jujutsu Kaisen: Shimetsu Kaiyuu</h5>
                <a href="https://anilist.co/anime/172463/" target="_blank" class="btn btn-outline-warning btn-sm">View</a>
              </div>
            </div>
          </div>
        </div>
      </section>

    </main>

    <footer>
      <p>&copy; 2024 OtakuHub. All rights reserved.</p>
    </footer>
    <?php
  }
  ?>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>