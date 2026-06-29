<?php
// auth/register.php

$no_auth = true;
$no_layout = true;
$page_title = "Registrasi Admin";

require_once '../config/database.php';
require_once '../partials/header.php';

$error = '';
$success = '';

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = 'Semua field wajib diisi!';
    } elseif (strlen($password) < 5) {
        $error = 'Password minimal harus 5 karakter!';
    } elseif ($password !== $confirm_password) {
        $error = 'Konfirmasi password tidak cocok!';
    } else {
        // Cek apakah username sudah digunakan
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM tabel_pengguna WHERE username = ?");
        $stmt->execute([$username]);
        $exists = $stmt->fetchColumn();
        
        if ($exists) {
            $error = 'Username sudah terdaftar! Gunakan username lain.';
        } else {
            // Hash password dengan bcrypt
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            try {
                $stmt = $pdo->prepare("INSERT INTO tabel_pengguna (username, password) VALUES (?, ?)");
                $stmt->execute([$username, $hashed_password]);
                
                $_SESSION['success'] = 'Registrasi berhasil! Silakan masuk menggunakan akun baru Anda.';
                header("Location: login.php");
                exit();
            } catch (\PDOException $e) {
                $error = 'Registrasi gagal: ' . $e->getMessage();
            }
        }
    }
}
?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h3 class="auth-title"><i class="fas fa-user-plus me-2"></i>REGISTRASI</h3>
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
            
            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold text-muted">Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 border-blue text-muted"><i class="fas fa-lock small"></i></span>
                    <input type="password" name="password" id="password" class="form-control border-start-0 border-blue bg-light-focus" placeholder="Masukkan password" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label small fw-semibold text-muted">Konfirmasi Password</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 border-blue text-muted"><i class="fas fa-lock small"></i></span>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control border-start-0 border-blue bg-light-focus" placeholder="Ulangi password" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-semibold">
                Daftar Akun
            </button>
            
            <div class="text-center mt-3">
                <span class="small text-muted">Sudah punya akun? </span>
                <a href="login.php" class="text-decoration-none small fw-semibold text-primary">Login Disini</a>
            </div>
        </form>
    </div>
</div>
<?php
require_once '../partials/footer.php';
?>
