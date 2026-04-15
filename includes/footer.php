</div>

<footer class="footer bg-dark text-white mt-5">
    <div class="container py-5">
        <div class="row">
            <div class="col-12 col-md-6 col-lg-4 mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-backpack"></i> MyBackpack
                </h5>
                <p class="text-light">
                    Il tuo zaino digitale per organizzare libri, contenuti multimediali, 
                    giochi, documenti e note rapide. Tutto in un unico posto, sempre accessibile.
                </p>
                <p class="small mb-0" style="color: red">
                    <i class="fas fa-code"></i> Progetto Informatica
                </p>
                <p class="small" style="color: red">
                    <i class="fas fa-graduation-cap"></i> Istituto Tecnico - Classe 5°
                </p>
            </div>

            <div class="col-12 col-md-6 col-lg-3 mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-headset"></i> Assistenza
                </h5>
                <ul class="list-unstyled">
                    <li class="mb-3">
                        <i class="fas fa-envelope text-primary"></i>
                        <a href="mailto:support@mybackpack.it" class="text-light text-decoration-none ms-2 d-inline-block">
                            MyBackpack@gmail.com
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-phone text-success"></i>
                        <a href="tel:+393331234567" class="text-light text-decoration-none ms-2">
                            +39 333 123 4567
                        </a>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-clock text-warning"></i>
                        <span class="text-light ms-2">Lun-Ven: 10:00-18:00</span>
                    </li>
                    <li class="mb-3">
                        <i class="fas fa-map-marker-alt text-danger"></i>
                        <span class="text-light ms-2">Italia</span>
                    </li>
                </ul>
            </div>

            <div class="col-12 col-md-12 col-lg-5 mb-4">
                <h5 class="mb-3">
                    <i class="fas fa-share-alt"></i> Social
                </h5>
                <div class="social-links d-flex flex-wrap gap-2">
                    <a href="https://www.instagram.com/mybackpack_official" target="_blank" 
                       class="btn btn-outline-light btn-social flex-grow-1 flex-sm-grow-0" 
                       title="Seguici su Instagram">
                        <i class="fab fa-instagram"></i> Instagram
                    </a>
                    <a href="https://www.linkedin.com/company/mybackpack" target="_blank" 
                       class="btn btn-outline-light btn-social flex-grow-1 flex-sm-grow-0" 
                       title="Seguici su LinkedIn">
                        <i class="fab fa-linkedin-in"></i> LinkedIn
                    </a>
                </div>
            </div>
        </div>

        <hr class="my-4 bg-secondary">

        <div class="row align-items-center">
            <div class="col-12 col-md-6 text-center text-md-start mb-3 mb-md-0">
                <p class="mb-0">
                    &copy; 2026 <strong>MyBackpack</strong> - Tutti i diritti riservati
                </p>
            </div>
            <div class="col-12 col-md-6 text-center text-md-end">
                <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-md-end gap-2 gap-sm-3">
                    <a href="#" class="text-light text-decoration-none" data-bs-toggle="modal" data-bs-target="#privacyModal">
                        <i class="fas fa-shield-alt"></i> Privacy Policy
                    </a>
                    <a href="#" class="text-light text-decoration-none" data-bs-toggle="modal" data-bs-target="#termsModal">
                        <i class="fas fa-file-contract"></i> Termini di Servizio
                    </a>
                    <a href="#" class="text-light text-decoration-none" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;">
                        <i class="fas fa-arrow-up"></i> Torna su
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-wave d-none d-md-block">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 100" preserveAspectRatio="none">
            <path fill="#212529" d="M0,50 Q360,0 720,50 T1440,50 L1440,100 L0,100 Z"></path>
        </svg>
    </div>
</footer>

<div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="privacyModalLabel">
                    <i class="fas fa-shield-alt"></i> Privacy Policy
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Informativa sulla Privacy</h6>
                <p>MyBackpack rispetta la tua privacy e protegge i tuoi dati personali.</p>
                
                <h6 class="mt-3">Dati Raccolti</h6>
                <ul>
                    <li>Username e email per la registrazione</li>
                    <li>Contenuti inseriti (libri, note, ecc.)</li>
                    <li>Dati di utilizzo del servizio</li>
                </ul>
                
                <h6 class="mt-3">Utilizzo dei Dati</h6>
                <p>I tuoi dati vengono utilizzati esclusivamente per fornire il servizio e migliorare l'esperienza utente.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="termsModalLabel">
                    <i class="fas fa-file-contract"></i> Termini di Servizio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>Condizioni Generali</h6>
                <p>Utilizzando MyBackpack, accetti i seguenti termini di servizio.</p>
                
                <h6 class="mt-3">Uso del Servizio</h6>
                <ul>
                    <li>È vietato l'uso improprio della piattaforma</li>
                    <li>I contenuti caricati sono di tua responsabilità</li>
                </ul>
                
                <h6 class="mt-3">Account Utente</h6>
                <p>Sei responsabile della sicurezza del tuo account e della password.</p>
                
                <h6 class="mt-3">Limitazioni</h6>
                <p>Il servizio è fornito gratuitamente per scopi educativi e personali.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
</script>

</body>
</html>