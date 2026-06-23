<?php
// partials/footer.php
?>
<?php if (!isset($no_layout)): ?>
        <!-- Footer Visual -->
        <footer class="footer py-3 bg-white mt-5 no-print" style="border-top: 1px solid var(--border-blue); border-radius: 12px;">
            <div class="container-fluid text-center">
                <span class="text-muted small">
                    © 2026 SDN 105361 Lubuk Cemara. Dikembangkan oleh <strong>Riza Haidar Aly (2022020362)</strong>.
                </span>
            </div>
        </footer>
    </div> <!-- Tutup #content -->
</div> <!-- Tutup .wrapper -->
<?php endif; ?>

<!-- Bootstrap 5 Bundle JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Logika Sidebar Toggle Dinamis -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const sidebarCollapse = document.getElementById("sidebarCollapse");
    const sidebar = document.getElementById("sidebar");
    
    if (sidebarCollapse && sidebar) {
        sidebarCollapse.addEventListener("click", function() {
            sidebar.classList.toggle("active");
        });
    }
});
</script>
</body>
</html>
