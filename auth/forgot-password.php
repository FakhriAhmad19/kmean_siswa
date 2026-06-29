<?php
// auth/forgot-password.php

$no_auth = true;
$no_layout = true;
$page_title = "Lupa Password";

require_once '../config/database.php';
require_once '../partials/header.php';

$error = '';
$success = '';

// Kunci Reset Keamanan default untuk verifikasi simulasi lokal
define('SECURITY_RESET_KEY', 'sdn105361');

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: ../index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $security_key = trim($_POST['security_key']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($username) || empty($security_key) || empty($new_password) || empty($confirm_password)) {
        $error = 'Semua field wajib diisi!';
    } elseif ($security_key !== SECURITY_RESET_KEY) {
        $error = 'Kunci reset keamanan salah! Silakan hubungi operator sekolah.';
    } elseif (strlen($new_password) < 5) {
        $error = 'Password baru minimal harus 5 karakter!';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Konfirmasi password baru tidak cocok!';
    } else {
        // Cek apakah username terdaftar
        $stmt = $pdo->prepare("SELECT * FROM tabel_pengguna WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $error = 'Username tidak terdaftar di sistem!';
        } else {
            // Hash password baru dengan bcrypt
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            
            try {
                $stmt = $pdo->prepare("UPDATE tabel_pengguna SET password = ? WHERE id_user = ?");
                $stmt->execute([$hashed_password, $user['id_user']]);
                
                $_SESSION['success'] = 'Password berhasil direset! Silakan masuk dengan password baru Anda.';
                header("Location: login.php");
                exit();
            } catch (\PDOException $e) {
                $error = 'Gagal mereset password: ' . $e->getMessage();
            }
        }
    }
}
?>
<div class="auth-wrapper">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h3 class="auth-title"><i class="fas fa-key me-2"></i>RESET PASSWORD</h3>
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
                <label for="username" class="form-label small fw-semibold text-muted">Username Akun</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 border-blue text-muted"><i class="fas fa-user small"></i></span>
                    <input type="text" name="username" id="username" class="form-control border-start-0 border-blue bg-light-focus" placeholder="Masukkan username" required value="<?= htmlspecialchars($username ?? ''); ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="security_key" class="form-label small fw-semibold text-muted">Kunci Reset Keamanan</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 border-blue text-muted"><i class="fas fa-shield-alt small"></i></span>
                    <input type="password" name="security_key" id="security_key" class="form-control border-start-0 border-blue bg-light-focus" placeholder="Kunci Reset (Default: sdn105361)" required>
                </div>
                <div class="form-text small text-muted">Hubungi operator sekolah untuk mendapatkan kunci reset.</div>
            </div>
            
            <div class="mb-3">
                <label for="new_password" class="form-label small fw-semibold text-muted">Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 border-blue text-muted"><i class="fas fa-lock small"></i></span>
                    <input type="password" name="new_password" id="new_password" class="form-control border-start-0 border-blue bg-light-focus" placeholder="Masukkan password baru" required>
                </div>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="form-label small fw-semibold text-muted">Konfirmasi Password Baru</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 border-blue text-muted"><i class="fas fa-lock small"></i></span>
                    <input type="password" name="confirm_password" id="confirm_password" class="form-control border-start-0 border-blue bg-light-focus" placeholder="Ulangi password baru" required>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary-custom w-100 py-2 fw-semibold">
                Reset Password
            </button>
            
            <div class="text-center mt-3">
                <a href="login.php" class="text-decoration-none small fw-semibold text-primary">Kembali ke Halaman Login</a>
            </div>
        </form>
    </div>
</div>
<?php
require_once '../partials/footer.php';
?>
