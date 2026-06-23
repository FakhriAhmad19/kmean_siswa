<?php
// auth/login.php

$no_auth = true;
$no_layout = true;
$page_title = "Masuk";

require_once '../config/database.php';
require_once '../partials/header.php';

$error = '';

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password tidak boleh kosong!';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM tabel_pengguna WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            header("Location: ../index.php");
            exit();
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h3 class="auth-title"><i class="fas fa-chart-pie me-2"></i>LOGIN</h3>
            <p class="text-muted small">SDN 105361 Lubuk Cemara</p>
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger py-2 px-3 small border-0 rounded-3 mb-3 d-flex align-items-center gap-2">
                <i class="fas fa-exclamation-circle"></i>
                <div><?= $error; ?></div>
            </div>
        <?php endif; ?>
        
        <form action="" method="POST" autocomplete="off">
            <div class="mb-3">
                <label for="username" class="form-label small fw-semibold text-muted">Username</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 border-blue text-muted"><i class="fas fa-user small"></i></span>
                    <input type="text" name="username" id="username" class="form-control border-start-0 border-blue bg-light-focus" placeholder="Masukkan username" required value="<?= htmlspecialchars($username ?? ''); ?>">
                </div>
            </div>
            
            <div class="mb-4">
                <label for="password" class="form-label small fw-semibold text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 border-blue text-muted"><i class="fas fa-lock small"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 border-blue bg-light-focus" placeholder="Masukkan password" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-semibold">
                Login
            </button>
            
            <div class="d-flex justify-content-between mt-3 px-1">
                <a href="#" class="text-decoration-none text-muted small hover-primary" onclick="alert('Fitur lupa password dinonaktifkan. Hubungi Database Administrator.')">Lupa Password</a>
                <a href="#" class="text-decoration-none text-muted small hover-primary" onclick="alert('Registrasi dinonaktifkan. Akun admin default: admin/admin.')">Registrasi</a>
            </div>
        </form>
    </div>
</div>
<?php
require_once '../partials/footer.php';
?>
