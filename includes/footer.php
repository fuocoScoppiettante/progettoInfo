</div>

<?php if(isset($_SESSION['user_id'])): ?>
<footer class="footer bg-dark text-white mt-5">
    <div class="container py-5">
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="footer-logo mb-3">
                    <div class="logo-icon-footer">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>MyBackpack</h5>
                </div>
                <p class="text-light">Il tuo zaino digitale per organizzare tutto.</p>
                <p class="text-muted small">Progetto Scolastico 2024 - Istituto Tecnico</p>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="footer-title"><i class="fas fa-headset"></i> Assistenza</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-envelope text-danger"></i> <a href="mailto:support@mybackpack.it" class="text-light">support@mybackpack.it</a></li>
                    <li class="mb-2"><i class="fas fa-phone text-danger"></i> <a href="tel:+393331234567" class="text-light">+39 333 123 4567</a></li>
                    <li class="mb-2"><i class="fas fa-clock text-danger"></i> <span class="text-light">Lun-Ven: 9:00-18:00</span></li>
                </ul>
            </div>
            <div class="col-md-4 mb-4">
                <h5 class="footer-title"><i class="fas fa-share-alt"></i> Social</h5>
                <div class="social-links">
                    <a href="https://www.instagram.com" target="_blank" class="btn btn-social"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.facebook.com" target="_blank" class="btn btn-social"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://twitter.com" target="_blank" class="btn btn-social"><i class="fab fa-x-twitter"></i></a>
                    <a href="https://www.youtube.com" target="_blank" class="btn btn-social"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>
        <hr class="my-4 border-danger">
        <p class="text-center mb-0">&copy; 2024 MyBackpack - Tutti i diritti riservati</p>
    </div>
</footer>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script Notifiche Scadenza Note -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-dismiss alerts
    document.querySelectorAll('.alert').forEach(function(alert) {
        setTimeout(function() {
            if(bootstrap.Alert.getOrCreateInstance(alert)) {
                bootstrap.Alert.getOrCreateInstance(alert).close();
            }
        }, 5000);
    });
});
</script>

</body>
</html>