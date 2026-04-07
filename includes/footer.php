<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/lhtm_system/');
}
?>

</main>

<!-- Footer - Identical on ALL Pages -->
<footer class="footer bg-dark text-white py-5 mt-5">
    <div class="container">
        <div class="row">
            <!-- Company Info -->
            <div class="col-lg-4 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">
                    <i class="bi bi-github text-warning"></i> 
                    Livestock<span class="text-warning">Health</span>
                </h5>
                <p class="text-white-50">
                    Smart farming solution for modern agriculture. Track, monitor, and improve livestock health with our comprehensive digital platform.
                </p>
                <div class="social-links">
                    <a href="#" class="text-white-50 me-2" data-bs-toggle="tooltip" title="Facebook">
                        <i class="bi bi-facebook fs-5"></i>
                    </a>
                    <a href="#" class="text-white-50 me-2" data-bs-toggle="tooltip" title="Twitter">
                        <i class="bi bi-twitter fs-5"></i>
                    </a>
                    <a href="#" class="text-white-50 me-2" data-bs-toggle="tooltip" title="Instagram">
                        <i class="bi bi-instagram fs-5"></i>
                    </a>
                    <a href="#" class="text-white-50 me-2" data-bs-toggle="tooltip" title="LinkedIn">
                        <i class="bi bi-linkedin fs-5"></i>
                    </a>
                    <a href="#" class="text-white-50" data-bs-toggle="tooltip" title="YouTube">
                        <i class="bi bi-youtube fs-5"></i>
                    </a>
                </div>
            </div>
            
            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">Quick Links</h5>
                <ul class="list-unstyled">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <!-- Links for Logged-in Users -->
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>dashboard.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-speedometer2 me-2"></i> Dashboard
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>animals/index.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-github me-2"></i> Animals
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>health/index.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-file-medical me-2"></i> Health Records
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>alerts/index.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-bell me-2"></i> Alerts
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>reports/index.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-file-text me-2"></i> Reports
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- Links for Guests -->
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>index.php#features" class="text-white-50 text-decoration-none">
                                <i class="bi bi-star me-2"></i> Features
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>index.php#about" class="text-white-50 text-decoration-none">
                                <i class="bi bi-info-circle me-2"></i> About Us
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>auth/register.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-person-plus me-2"></i> Register
                            </a>
                        </li>
                        <li class="mb-2">
                            <a href="<?= BASE_URL ?>auth/index.php" class="text-white-50 text-decoration-none">
                                <i class="bi bi-box-arrow-in-right me-2"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">Contact Info</h5>
                <ul class="list-unstyled text-white-50">
                    <li class="mb-3">
                        <i class="bi bi-geo-alt text-warning me-2"></i> 
                        123 Farm Street, Nakuru, Kenya
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-envelope text-warning me-2"></i> 
                        <a href="mailto:support@livestockhealth.com" class="text-white-50 text-decoration-none">
                            support@livestockhealth.com
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-telephone text-warning me-2"></i> 
                        <a href="tel:+254712345678" class="text-white-50 text-decoration-none">
                            +254 713505483
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="bi bi-clock text-warning me-2"></i> 
                        Mon - Fri: 8:00 AM - 6:00 PM
                    </li>
                </ul>
            </div>
            
            <!-- Newsletter & Security -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="text-uppercase mb-4">Newsletter</h5>
                <p class="text-white-50">Subscribe for updates and farming tips.</p>
                <div class="input-group mb-3">
                    <input type="email" class="form-control bg-dark text-white border-secondary" 
                           placeholder="Your email" id="newsletterEmail">
                    <button class="btn btn-warning" type="button" onclick="alert('Newsletter feature coming soon!')">
                        <i class="bi bi-send"></i>
                    </button>
                </div>
                
                <hr class="bg-secondary">
                
                <!-- Security Badge -->
                <div class="d-flex align-items-center text-white-50 mb-2">
                    <i class="bi bi-shield-check text-warning fs-4 me-2"></i>
                    <span>Secure & Encrypted</span>
                </div>
                
                <!-- System Status -->
                <div class="d-flex align-items-center text-white-50">
                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                    <span>System Status: <span class="text-success">Online</span></span>
                </div>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <hr class="bg-secondary">
                    <div class="small text-white-50">
                        <i class="bi bi-person-circle me-1"></i>
                        Logged in as: <strong class="text-warning"><?= htmlspecialchars($_SESSION['name'] ?? '') ?></strong>
                        <br>
                        <span class="badge bg-<?= $_SESSION['role'] === 'admin' ? 'danger' : 'primary' ?> mt-1">
                            <?= ucfirst($_SESSION['role'] ?? 'user') ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Footer Bottom -->
        <div class="footer-bottom mt-4 pt-4 border-top border-secondary">
            <div class="row">
                <div class="col-md-6 text-md-start text-center mb-3 mb-md-0">
                    <p class="text-white-50 small mb-0">
                        &copy; <?= date('Y') ?> Livestock Health Monitoring And Tracking System. All rights reserved.
                    </p>
                </div>
                <div class="col-md-6 text-md-end text-center">
                    <a href="<?= BASE_URL ?>privacy.php" class="text-white-50 text-decoration-none small me-3">
                        Privacy Policy
                    </a>
                    <a href="<?= BASE_URL ?>terms.php" class="text-white-50 text-decoration-none small me-3">
                        Terms of Service
                    </a>
                    <a href="<?= BASE_URL ?>cookies.php" class="text-white-50 text-decoration-none small">
                        Cookie Policy
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- AOS Animation -->
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<!-- Custom JS -->
<script src="<?= BASE_URL ?>assets/js/script.js"></script>

<script>
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true
    });
    
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
    
    // Auto-hide alerts
    setTimeout(function() {
        document.querySelectorAll('.alert:not(.alert-permanent)').forEach(function(alert) {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
    
    // Newsletter subscription
    document.getElementById('newsletterEmail')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            alert('Newsletter feature coming soon!');
        }
    });
</script>

</body>
</html>