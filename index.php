<?php include 'includes/header.php'; ?>

<div class="hero-section text-center py-5">
    <h1 class="display-4"><i class="fas fa-backpack"></i> Benvenuto in MyBackpack</h1>
    <p class="lead">Il tuo zaino digitale per organizzare libri, multimedia, giochi, documenti, note e altro ancora!</p>
    
    <?php if(!isset($_SESSION['user_id'])): ?>
    <div class="mt-4">
        <a href="register.php" class="btn btn-primary btn-lg me-2">Registrati</a>
        <a href="login.php" class="btn btn-outline-primary btn-lg">Accedi</a>
    </div>
    <?php else: ?>
    <div class="mt-4">
        <a href="dashboard.php" class="btn btn-primary btn-lg">Vai alla Dashboard</a>
    </div>
    <?php endif; ?>
</div>

<div class="row mt-5">
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-book fa-3x text-primary mb-3"></i>
                <h5 class="card-title">Gestisci i Libri</h5>
                <p class="card-text">Organizza la tua biblioteca personale, segna i libri letti e assegna voti.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-photo-video fa-3x text-success mb-3"></i>
                <h5 class="card-title">Contenuti Multimedia</h5>
                <p class="card-text">Salva link a video, audio e immagini utili per lo studio.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-gamepad fa-3x text-danger mb-3"></i>
                <h5 class="card-title">Giochi</h5>
                <p class="card-text">Tieni traccia dei giochi che vuoi completare.</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-6 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-file-alt fa-3x text-warning mb-3"></i>
                <h5 class="card-title">Documenti</h5>
                <p class="card-text">Organizza i tuoi documenti scolastici e personali.</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card h-100 text-center">
            <div class="card-body">
                <i class="fas fa-sticky-note fa-3x text-info mb-3"></i>
                <h5 class="card-title">Note Rapide</h5>
                <p class="card-text">Prendi appunti veloci e salvali nel tuo zaino digitale.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>