<?php 
include 'includes/header.php';
checkLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];
$tipologia = getTipologia();

// Statistiche
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

$stmt = $db->prepare("SELECT COUNT(*) as completati FROM libri WHERE user_id = ? AND stato = 'completato'");
$stmt->execute([$user_id]);
$libri_completati = $stmt->fetch()['completati'];

$stmt = $db->prepare("SELECT COUNT(*) as completati FROM giochi WHERE user_id = ? AND stato = 'completato'");
$stmt->execute([$user_id]);
$giochi_completati = $stmt->fetch()['completati'];

// Note in scadenza
$stmt = $db->prepare("SELECT * FROM note WHERE user_id = ? AND data_scadenza IS NOT NULL AND data_scadenza BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 DAY) AND completata = 0");
$stmt->execute([$user_id]);
$note_scadenza = $stmt->fetchAll();

// Preferiti
$stmt = $db->prepare("SELECT 
    (SELECT COUNT(*) FROM libri WHERE user_id = ? AND preferito = 1) +
    (SELECT COUNT(*) FROM giochi WHERE user_id = ? AND preferito = 1) as tot");
$stmt->execute([$user_id, $user_id]);
$preferiti = $stmt->fetch()['tot'];
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
                        <div class="scadenza-icon"><i class="fas fa-bell"></i></div>
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

<!-- ==================== HEADER ==================== -->
<div class="dash-header">
    <div class="row align-items-center">
        <div class="col-md-7">
            <h1 class="dash-title">
                <span class="dash-title-red">Dashboard</span>
            </h1>
            <p class="dash-welcome">
                Bentornato, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                <span class="badge bg-<?php echo $tipologia == 'admin' ? 'danger' : 'secondary'; ?> ms-2"><?php echo strtoupper($tipologia); ?></span>
            </p>
        </div>
        <div class="col-md-5">
            <!-- AZIONI RAPIDE - Pulsanti compatti -->
            <div class="quick-buttons">
                <span class="quick-label"><i class="fas fa-bolt"></i> Aggiungi:</span>
                <a href="libri.php#form-libro" class="qbtn qbtn-libro" title="Aggiungi Libro">
                    <i class="fas fa-book"></i>
                </a>
                <a href="giochi.php#form-gioco" class="qbtn qbtn-gioco" title="Aggiungi Gioco">
                    <i class="fas fa-gamepad"></i>
                </a>
                <a href="multimedia.php#form-multimedia" class="qbtn qbtn-media" title="Aggiungi Multimedia">
                    <i class="fas fa-photo-video"></i>
                </a>
                <a href="note.php#form-nota" class="qbtn qbtn-nota" title="Nuova Nota">
                    <i class="fas fa-sticky-note"></i>
                </a>
                <a href="statistiche.php" class="qbtn qbtn-stat" title="Statistiche">
                    <i class="fas fa-chart-bar"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MINI STATS ==================== -->
<div class="row g-3 mb-5">
    <div class="col-md-3 col-6">
        <div class="mini-stat">
            <div class="mini-stat-icon-dash"><i class="fas fa-layer-group"></i></div>
            <div>
                <h3><?php echo $totale_libri + $totale_giochi + $totale_multimedia; ?></h3>
                <p>Elementi</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="mini-stat">
            <div class="mini-stat-icon-dash" style="background: linear-gradient(135deg, #28a745, #20c997);"><i class="fas fa-trophy"></i></div>
            <div>
                <h3><?php echo $libri_completati + $giochi_completati; ?></h3>
                <p>Completati</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="mini-stat">
            <div class="mini-stat-icon-dash" style="background: linear-gradient(135deg, #ffc107, #ff9800);"><i class="fas fa-star"></i></div>
            <div>
                <h3><?php echo $preferiti; ?></h3>
                <p>Preferiti</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="mini-stat">
            <div class="mini-stat-icon-dash" style="background: linear-gradient(135deg, #6c757d, #495057);"><i class="fas fa-sticky-note"></i></div>
            <div>
                <h3><?php echo $totale_note; ?></h3>
                <p>Note</p>
            </div>
        </div>
    </div>
</div>

<!-- ==================== CARD LIBRI ==================== -->
<div class="dash-section-card mb-5">
    <div class="dash-card-bg">
        <img src="https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=1200&q=80" alt="Biblioteca">
        <div class="dash-card-gradient dash-gradient-libri"></div>
    </div>
    <div class="dash-card-content">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="dash-card-icon"><i class="fas fa-book"></i></div>
                <h2 class="dash-card-title">La Mia Libreria</h2>
                <p class="dash-card-desc">Organizza i tuoi libri, segna quelli letti e scopri nuovi titoli</p>
                <div class="dash-card-stats">
                    <span><strong><?php echo $totale_libri; ?></strong> totali</span>
                    <span class="dash-card-divider">|</span>
                    <span><strong><?php echo $libri_completati; ?></strong> letti</span>
                    <span class="dash-card-divider">|</span>
                    <span><strong><?php echo $libri_preferiti; ?></strong> <i class="fas fa-heart text-danger"></i></span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="dash-card-number"><?php echo $totale_libri; ?></div>
                <a href="libri.php" class="btn-dash-action">
                    Vai alla Libreria <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ==================== CARD MULTIMEDIA ==================== -->
<div class="dash-section-card mb-5">
    <div class="dash-card-bg">
        <img src="https://images.unsplash.com/photo-1574717024653-61fd2cf4d44d?w=1200&q=80" alt="Multimedia">
        <div class="dash-card-gradient dash-gradient-multimedia"></div>
    </div>
    <div class="dash-card-content">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="dash-card-icon"><i class="fas fa-photo-video"></i></div>
                <h2 class="dash-card-title">Contenuti Multimediali</h2>
                <p class="dash-card-desc">Video, audio, podcast e contenuti utili per lo studio e il tempo libero</p>
                <div class="dash-card-stats">
                    <span><strong><?php echo $totale_multimedia; ?></strong> contenuti salvati</span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="dash-card-number"><?php echo $totale_multimedia; ?></div>
                <a href="multimedia.php" class="btn-dash-action">
                    Vai ai Multimedia <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ==================== CARD GIOCHI ==================== -->
<div class="dash-section-card mb-5">
    <div class="dash-card-bg">
        <img src="https://images.unsplash.com/photo-1511512578047-dfb367046420?w=1200&q=80" alt="Gaming">
        <div class="dash-card-gradient dash-gradient-giochi"></div>
    </div>
    <div class="dash-card-content">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="dash-card-icon"><i class="fas fa-gamepad"></i></div>
                <h2 class="dash-card-title">La Mia Collezione Giochi</h2>
                <p class="dash-card-desc">Tieni traccia dei giochi completati, in corso e della tua wishlist</p>
                <div class="dash-card-stats">
                    <span><strong><?php echo $totale_giochi; ?></strong> totali</span>
                    <span class="dash-card-divider">|</span>
                    <span><strong><?php echo $giochi_completati; ?></strong> completati</span>
                    <span class="dash-card-divider">|</span>
                    <span><strong><?php echo $giochi_preferiti; ?></strong> <i class="fas fa-heart text-danger"></i></span>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="dash-card-number"><?php echo $totale_giochi; ?></div>
                <a href="giochi.php" class="btn-dash-action">
                    Vai ai Giochi <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ==================== CARD NOTE ==================== -->
<div class="dash-section-card mb-5">
    <div class="dash-card-bg">
        <img src="https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=1200&q=80" alt="Note">
        <div class="dash-card-gradient dash-gradient-note"></div>
    </div>
    <div class="dash-card-content">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="dash-card-icon"><i class="fas fa-sticky-note"></i></div>
                <h2 class="dash-card-title">Note & Promemoria</h2>
                <p class="dash-card-desc">Appunti veloci, promemoria con scadenza e organizzazione delle idee</p>
                <div class="dash-card-stats">
                    <span><strong><?php echo $totale_note; ?></strong> note</span>
                    <?php if(count($note_scadenza) > 0): ?>
                        <span class="dash-card-divider">|</span>
                        <span class="text-warning"><strong><?php echo count($note_scadenza); ?></strong> ⚠️ in scadenza</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <div class="dash-card-number"><?php echo $totale_note; ?></div>
                <a href="note.php" class="btn-dash-action">
                    Vai alle Note <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>