<?php 
include 'includes/header.php';
checkLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];
$tipologia = getTipologia();

// Statistiche rapide (SENZA DOCUMENTI)
$stmt = $db->prepare("SELECT COUNT(*) as totale FROM libri WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_libri = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM multimedia WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_multimedia = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM giochi WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_giochi = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM note WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_note = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM obiettivi WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_obiettivi = $stmt->fetch()['totale'];

// Completati
$stmt = $db->prepare("SELECT COUNT(*) as completati FROM libri WHERE user_id = ? AND stato = 'completato'");
$stmt->execute([$user_id]);
$libri_completati = $stmt->fetch()['completati'];

$stmt = $db->prepare("SELECT COUNT(*) as completati FROM giochi WHERE user_id = ? AND stato = 'completato'");
$stmt->execute([$user_id]);
$giochi_completati = $stmt->fetch()['completati'];

// Note in scadenza (per popup)
$stmt = $db->prepare("SELECT * FROM note WHERE user_id = ? AND data_scadenza IS NOT NULL AND data_scadenza BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 DAY) AND completata = 0");
$stmt->execute([$user_id]);
$note_scadenza = $stmt->fetchAll();
?>

<!-- POPUP SCADENZA NOTE -->
<?php if(count($note_scadenza) > 0): ?>
<div class="modal fade" id="scadenzaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> ⚠️ Note in Scadenza!</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Hai <strong><?php echo count($note_scadenza); ?></strong> note in scadenza:</p>
                <?php foreach($note_scadenza as $ns): ?>
                    <?php
                    $scad = new DateTime($ns['data_scadenza']);
                    $ora = new DateTime();
                    $diff = $ora->diff($scad);
                    $ore_rimaste = ($diff->days * 24) + $diff->h;
                    ?>
                    <div class="scadenza-item">
                        <div class="scadenza-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div class="scadenza-info">
                            <h6><?php echo htmlspecialchars($ns['titolo'] ?: 'Nota senza titolo'); ?></h6>
                            <p class="mb-0">
                                <?php if($ore_rimaste <= 24): ?>
                                    <span class="text-danger fw-bold">⏰ Scade tra <?php echo $ore_rimaste; ?> ore!</span>
                                <?php else: ?>
                                    <span class="text-warning">📅 Scade tra <?php echo $diff->days; ?> giorno/i</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="modal-footer">
                <a href="note.php" class="btn btn-outline-danger">Vai alle Note</a>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Ho Capito</button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modal = new bootstrap.Modal(document.getElementById('scadenzaModal'));
    modal.show();
});
</script>
<?php endif; ?>

<!-- Header Dashboard -->
<div class="dashboard-header mb-5">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="display-4 fw-bold mb-2">
                <i class="fas fa-tachometer-alt text-danger"></i> 
                <span class="dashboard-title">Dashboard</span>
            </h1>
            <p class="lead text-muted mb-0">
                <i class="fas fa-user-circle"></i> Benvenuto, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>!
                <span class="badge bg-<?php echo $tipologia == 'admin' ? 'danger' : 'secondary'; ?> ms-2">
                    <?php echo strtoupper($tipologia); ?>
                </span>
            </p>
            <p class="text-muted">
                <i class="fas fa-calendar-alt"></i> <?php echo date('d/m/Y'); ?>
            </p>
        </div>
        <div class="col-md-4 text-end">
            <div class="stat-badge">
                <i class="fas fa-check-circle"></i>
                <span><?php echo $libri_completati + $giochi_completati; ?> Completati</span>
            </div>
        </div>
    </div>
</div>

<!-- Cards con Immagini -->
<div class="row g-4 mb-5">
    <!-- Card Libri -->
    <div class="col-lg-4 col-md-6">
        <div class="stat-card-premium">
            <div class="card-image-overlay">
                <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=800&q=80" alt="Libri" class="card-bg-image">
                <div class="card-gradient" style="background: linear-gradient(135deg, rgba(139,0,0,0.85), rgba(220,20,60,0.9));"></div>
            </div>
            <div class="card-content">
                <div class="card-icon"><i class="fas fa-book"></i></div>
                <h3 class="card-title">Libri</h3>
                <div class="card-number"><?php echo $totale_libri; ?></div>
                <p class="card-subtitle"><?php echo $libri_completati; ?> completati</p>
                <a href="libri.php" class="btn-card-action"><span>Visualizza</span> <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Card Multimedia -->
    <div class="col-lg-4 col-md-6">
        <div class="stat-card-premium">
            <div class="card-image-overlay">
                <img src="https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=800&q=80" alt="Multimedia" class="card-bg-image">
                <div class="card-gradient" style="background: linear-gradient(135deg, rgba(102,51,153,0.85), rgba(220,20,60,0.9));"></div>
            </div>
            <div class="card-content">
                <div class="card-icon"><i class="fas fa-photo-video"></i></div>
                <h3 class="card-title">Multimedia</h3>
                <div class="card-number"><?php echo $totale_multimedia; ?></div>
                <p class="card-subtitle">Contenuti salvati</p>
                <a href="multimedia.php" class="btn-card-action"><span>Visualizza</span> <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Card Giochi -->
    <div class="col-lg-4 col-md-6">
        <div class="stat-card-premium">
            <div class="card-image-overlay">
                <img src="https://images.unsplash.com/photo-1511512578047-dfb367046420?w=800&q=80" alt="Gaming" class="card-bg-image">
                <div class="card-gradient" style="background: linear-gradient(135deg, rgba(26,26,26,0.9), rgba(220,20,60,0.85));"></div>
            </div>
            <div class="card-content">
                <div class="card-icon"><i class="fas fa-gamepad"></i></div>
                <h3 class="card-title">Giochi</h3>
                <div class="card-number"><?php echo $totale_giochi; ?></div>
                <p class="card-subtitle"><?php echo $giochi_completati; ?> completati</p>
                <a href="giochi.php" class="btn-card-action"><span>Visualizza</span> <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Card Note -->
    <div class="col-lg-6 col-md-6">
        <div class="stat-card-premium">
            <div class="card-image-overlay">
                <img src="https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=800&q=80" alt="Note" class="card-bg-image">
                <div class="card-gradient" style="background: linear-gradient(135deg, rgba(220,20,60,0.85), rgba(255,107,107,0.9));"></div>
            </div>
            <div class="card-content">
                <div class="card-icon"><i class="fas fa-sticky-note"></i></div>
                <h3 class="card-title">Note</h3>
                <div class="card-number"><?php echo $totale_note; ?></div>
                <p class="card-subtitle">Appunti e promemoria</p>
                <a href="note.php" class="btn-card-action"><span>Visualizza</span> <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>

    <!-- Card Obiettivi -->
    <div class="col-lg-6 col-md-6">
        <div class="stat-card-premium">
            <div class="card-image-overlay">
                <img src="https://images.unsplash.com/photo-1506784983877-45594efa4cbe?w=800&q=80" alt="Obiettivi" class="card-bg-image">
                <div class="card-gradient" style="background: linear-gradient(135deg, rgba(26,26,26,0.85), rgba(255,71,87,0.9));"></div>
            </div>
            <div class="card-content">
                <div class="card-icon"><i class="fas fa-bullseye"></i></div>
                <h3 class="card-title">Obiettivi</h3>
                <div class="card-number"><?php echo $totale_obiettivi; ?></div>
                <p class="card-subtitle">Traguardi da raggiungere</p>
                <a href="obiettivi.php" class="btn-card-action"><span>Visualizza</span> <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Mini Statistiche -->
<div class="row mb-5">
    <div class="col-md-3 col-6 mb-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon bg-danger"><i class="fas fa-layer-group"></i></div>
            <div class="mini-stat-info">
                <h4><?php echo $totale_libri + $totale_giochi + $totale_multimedia; ?></h4>
                <p>Totale Elementi</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon bg-success"><i class="fas fa-trophy"></i></div>
            <div class="mini-stat-info">
                <h4><?php echo $libri_completati + $giochi_completati; ?></h4>
                <p>Completati</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon bg-warning"><i class="fas fa-star"></i></div>
            <div class="mini-stat-info">
                <?php
                $stmt = $db->prepare("SELECT 
                    (SELECT COUNT(*) FROM libri WHERE user_id = ? AND preferito = 1) +
                    (SELECT COUNT(*) FROM giochi WHERE user_id = ? AND preferito = 1) as tot");
                $stmt->execute([$user_id, $user_id]);
                $preferiti = $stmt->fetch()['tot'];
                ?>
                <h4><?php echo $preferiti; ?></h4>
                <p>Preferiti</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon bg-info"><i class="fas fa-sticky-note"></i></div>
            <div class="mini-stat-info">
                <h4><?php echo $totale_note; ?></h4>
                <p>Note Attive</p>
            </div>
        </div>
    </div>
</div>

<!-- Azioni Rapide -->
<div class="card card-premium">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-bolt"></i> Azioni Rapide</h5>
    </div>
    <div class="card-body">
        <div class="quick-action-grid">
            <a href="libri.php" class="quick-action-box">
                <div class="qa-icon-large bg-danger"><i class="fas fa-book"></i></div>
                <h6>Libri</h6>
                <p>Gestisci la libreria</p>
            </a>
            <a href="giochi.php" class="quick-action-box">
                <div class="qa-icon-large bg-dark"><i class="fas fa-gamepad"></i></div>
                <h6>Giochi</h6>
                <p>I tuoi videogiochi</p>
            </a>
            <a href="multimedia.php" class="quick-action-box">
                <div class="qa-icon-large bg-success"><i class="fas fa-photo-video"></i></div>
                <h6>Multimedia</h6>
                <p>Contenuti salvati</p>
            </a>
            <a href="note.php" class="quick-action-box">
                <div class="qa-icon-large bg-warning"><i class="fas fa-sticky-note"></i></div>
                <h6>Note</h6>
                <p>Appunti e promemoria</p>
            </a>
            <a href="calendario.php" class="quick-action-box">
                <div class="qa-icon-large bg-info"><i class="fas fa-calendar-alt"></i></div>
                <h6>Calendario</h6>
                <p>I tuoi eventi</p>
            </a>
            <a href="statistiche.php" class="quick-action-box">
                <div class="qa-icon-large bg-dark"><i class="fas fa-chart-bar"></i></div>
                <h6>Statistiche</h6>
                <p>Analisi progressi</p>
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>