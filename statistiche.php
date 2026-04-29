<?php 
include 'includes/header.php';
checkLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];
$tipologia = getTipologia();

// STATISTICHE LIBRI
$stmt = $db->prepare("SELECT COUNT(*) as tot FROM libri WHERE user_id = ?");
$stmt->execute([$user_id]);
$tot_libri = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM libri WHERE user_id = ? AND stato = 'completato'");
$stmt->execute([$user_id]);
$libri_letti = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM libri WHERE user_id = ? AND stato = 'in_lettura'");
$stmt->execute([$user_id]);
$libri_in_lettura = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM libri WHERE user_id = ? AND stato = 'da_leggere'");
$stmt->execute([$user_id]);
$libri_da_leggere = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM libri WHERE user_id = ? AND preferito = 1");
$stmt->execute([$user_id]);
$libri_preferiti = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT genere, COUNT(*) as count FROM libri WHERE user_id = ? AND genere IS NOT NULL GROUP BY genere ORDER BY count DESC LIMIT 5");
$stmt->execute([$user_id]);
$generi_libri = $stmt->fetchAll();

$stmt = $db->prepare("SELECT AVG(voto) as media FROM libri WHERE user_id = ? AND voto IS NOT NULL");
$stmt->execute([$user_id]);
$media_voto_libri = round($stmt->fetch()['media'] ?? 0, 1);

// STATISTICHE GIOCHI
$stmt = $db->prepare("SELECT COUNT(*) as tot FROM giochi WHERE user_id = ?");
$stmt->execute([$user_id]);
$tot_giochi = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM giochi WHERE user_id = ? AND stato = 'completato'");
$stmt->execute([$user_id]);
$giochi_completati = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM giochi WHERE user_id = ? AND stato = 'in_corso'");
$stmt->execute([$user_id]);
$giochi_in_corso = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM giochi WHERE user_id = ? AND stato = 'da_giocare'");
$stmt->execute([$user_id]);
$giochi_da_giocare = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM giochi WHERE user_id = ? AND preferito = 1");
$stmt->execute([$user_id]);
$giochi_preferiti = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT genere, COUNT(*) as count FROM giochi WHERE user_id = ? AND genere IS NOT NULL GROUP BY genere ORDER BY count DESC LIMIT 5");
$stmt->execute([$user_id]);
$generi_giochi = $stmt->fetchAll();

$stmt = $db->prepare("SELECT piattaforma, COUNT(*) as count FROM giochi WHERE user_id = ? AND piattaforma IS NOT NULL GROUP BY piattaforma ORDER BY count DESC");
$stmt->execute([$user_id]);
$piattaforme = $stmt->fetchAll();

$stmt = $db->prepare("SELECT AVG(voto) as media FROM giochi WHERE user_id = ? AND voto IS NOT NULL");
$stmt->execute([$user_id]);
$media_voto_giochi = round($stmt->fetch()['media'] ?? 0, 1);

// STATISTICHE MULTIMEDIA
$stmt = $db->prepare("SELECT COUNT(*) as tot FROM multimedia WHERE user_id = ?");
$stmt->execute([$user_id]);
$tot_multimedia = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT tipo, COUNT(*) as count FROM multimedia WHERE user_id = ? GROUP BY tipo ORDER BY count DESC");
$stmt->execute([$user_id]);
$tipi_multimedia = $stmt->fetchAll();

$stmt = $db->prepare("SELECT categoria, COUNT(*) as count FROM multimedia WHERE user_id = ? AND categoria IS NOT NULL GROUP BY categoria ORDER BY count DESC LIMIT 5");
$stmt->execute([$user_id]);
$cat_multimedia = $stmt->fetchAll();

// STATISTICHE NOTE
$stmt = $db->prepare("SELECT COUNT(*) as tot FROM note WHERE user_id = ?");
$stmt->execute([$user_id]);
$tot_note = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM note WHERE user_id = ? AND completata = 1");
$stmt->execute([$user_id]);
$note_completate = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM note WHERE user_id = ? AND data_scadenza IS NOT NULL AND data_scadenza < NOW() AND completata = 0");
$stmt->execute([$user_id]);
$note_scadute = $stmt->fetch()['tot'];

// STATISTICHE OBIETTIVI
$stmt = $db->prepare("SELECT COUNT(*) as tot FROM obiettivi WHERE user_id = ?");
$stmt->execute([$user_id]);
$tot_obiettivi = $stmt->fetch()['tot'];

$stmt = $db->prepare("SELECT COUNT(*) as tot FROM obiettivi WHERE user_id = ? AND completato = 1");
$stmt->execute([$user_id]);
$obiettivi_completati = $stmt->fetch()['tot'];

// TOTALE GENERALE
$totale_elementi = $tot_libri + $tot_giochi + $tot_multimedia + $tot_note + $tot_obiettivi;
$totale_completati = $libri_letti + $giochi_completati + $note_completate + $obiettivi_completati;
?>

<!-- Header -->
<div class="page-header mb-4">
    <h2 class="display-5 fw-bold">
        <i class="fas fa-chart-bar text-gradient"></i> Statistiche
    </h2>
    <p class="text-muted">Panoramica completa del tuo zaino digitale</p>
</div>

<!-- Riepilogo Generale -->
<div class="row mb-5">
    <div class="col-md-3 col-6 mb-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon bg-danger"><i class="fas fa-layer-group"></i></div>
            <div class="mini-stat-info">
                <h4><?php echo $totale_elementi; ?></h4>
                <p>Elementi Totali</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon bg-success"><i class="fas fa-check-double"></i></div>
            <div class="mini-stat-info">
                <h4><?php echo $totale_completati; ?></h4>
                <p>Completati</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon bg-warning"><i class="fas fa-star"></i></div>
            <div class="mini-stat-info">
                <h4><?php echo $libri_preferiti + $giochi_preferiti; ?></h4>
                <p>Preferiti</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-6 mb-3">
        <div class="mini-stat-card">
            <div class="mini-stat-icon bg-dark"><i class="fas fa-percentage"></i></div>
            <div class="mini-stat-info">
                <h4><?php echo $totale_elementi > 0 ? round(($totale_completati / $totale_elementi) * 100) : 0; ?>%</h4>
                <p>Completamento</p>
            </div>
        </div>
    </div>
</div>

<!-- STATISTICHE LIBRI -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card stat-detail-card">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-book"></i> Statistiche Libri</h5>
            </div>
            <div class="card-body">
                <!-- Numeri -->
                <div class="row text-center mb-4">
                    <div class="col-3">
                        <div class="stat-number text-primary"><?php echo $tot_libri; ?></div>
                        <small class="text-muted">Totali</small>
                    </div>
                    <div class="col-3">
                        <div class="stat-number text-success"><?php echo $libri_letti; ?></div>
                        <small class="text-muted">Letti</small>
                    </div>
                    <div class="col-3">
                        <div class="stat-number text-warning"><?php echo $libri_in_lettura; ?></div>
                        <small class="text-muted">In lettura</small>
                    </div>
                    <div class="col-3">
                        <div class="stat-number text-secondary"><?php echo $libri_da_leggere; ?></div>
                        <small class="text-muted">Da leggere</small>
                    </div>
                </div>
                
                <!-- Progress -->
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Percentuale letti</span>
                        <strong><?php $perc_libri = $tot_libri > 0 ? round(($libri_letti/$tot_libri)*100) : 0; echo $perc_libri; ?>%</strong>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 10px;">
                        <div class="progress-bar bg-success" style="width: <?php echo $perc_libri; ?>%"></div>
                    </div>
                </div>
                
                <!-- Voto Medio -->
                <div class="stat-row">
                    <span><i class="fas fa-star text-warning"></i> Voto medio</span>
                    <strong><?php echo $media_voto_libri; ?>/5</strong>
                </div>
                <div class="stat-row">
                    <span><i class="fas fa-heart text-danger"></i> Preferiti</span>
                    <strong><?php echo $libri_preferiti; ?></strong>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Generi Libri -->
    <div class="col-lg-6 mb-4">
        <div class="card stat-detail-card">
            <div class="card-header-custom">
                <h5 class="mb-0"><i class="fas fa-tags"></i> Generi Libri</h5>
            </div>
            <div class="card-body">
                <?php if(count($generi_libri) > 0): ?>
                    <?php foreach($generi_libri as $g): ?>
                        <?php $perc = $tot_libri > 0 ? round(($g['count']/$tot_libri)*100) : 0; ?>
                        <div class="genere-bar mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-600"><?php echo htmlspecialchars($g['genere']); ?></span>
                                <span class="badge bg-danger"><?php echo $g['count']; ?></span>
                            </div>
                            <div class="progress" style="height: 8px; border-radius: 10px;">
                                <div class="progress-bar bg-danger" style="width: <?php echo $perc; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted text-center">Nessun genere disponibile</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- STATISTICHE GIOCHI -->
<div class="row mb-4">
    <div class="col-lg-6 mb-4">
        <div class="card stat-detail-card">
            <div class="card-header-custom" style="background: linear-gradient(135deg, #1a1a1a, #2d2d2d);">
                <h5 class="mb-0"><i class="fas fa-gamepad"></i> Statistiche Giochi</h5>
            </div>
            <div class="card-body">
                <div class="row text-center mb-4">
                    <div class="col-3">
                        <div class="stat-number" style="color: var(--primary);"><?php echo $tot_giochi; ?></div>
                        <small class="text-muted">Totali</small>
                    </div>
                    <div class="col-3">
                        <div class="stat-number text-success"><?php echo $giochi_completati; ?></div>
                        <small class="text-muted">Completati</small>
                    </div>
                    <div class="col-3">
                        <div class="stat-number text-warning"><?php echo $giochi_in_corso; ?></div>
                        <small class="text-muted">In corso</small>
                    </div>
                    <div class="col-3">
                        <div class="stat-number text-secondary"><?php echo $giochi_da_giocare; ?></div>
                        <small class="text-muted">Da giocare</small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Percentuale completati</span>
                        <strong><?php $perc_giochi = $tot_giochi > 0 ? round(($giochi_completati/$tot_giochi)*100) : 0; echo $perc_giochi; ?>%</strong>
                    </div>
                    <div class="progress" style="height: 12px; border-radius: 10px;">
                        <div class="progress-bar" style="background: linear-gradient(90deg, var(--primary), var(--accent)); width: <?php echo $perc_giochi; ?>%"></div>
                    </div>
                </div>
                
                <div class="stat-row">
                    <span><i class="fas fa-star text-warning"></i> Voto medio</span>
                    <strong><?php echo $media_voto_giochi; ?>/5</strong>
                </div>
                <div class="stat-row">
                    <span><i class="fas fa-heart text-danger"></i> Preferiti</span>
                    <strong><?php echo $giochi_preferiti; ?></strong>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Piattaforme -->
    <div class="col-lg-6 mb-4">
        <div class="card stat-detail-card">
            <div class="card-header-custom" style="background: linear-gradient(135deg, #1a1a1a, #2d2d2d);">
                <h5 class="mb-0"><i class="fas fa-desktop"></i> Piattaforme & Generi</h5>
            </div>
            <div class="card-body">
                <h6 class="mb-3"><i class="fas fa-desktop text-danger"></i> Piattaforme</h6>
                <?php if(count($piattaforme) > 0): ?>
                    <div class="d-flex flex-wrap gap-2 mb-4">
                        <?php foreach($piattaforme as $p): ?>
                            <span class="badge bg-dark p-2 px-3">
                                <i class="fas fa-desktop"></i> <?php echo htmlspecialchars($p['piattaforma']); ?>
                                <span class="badge bg-danger ms-1"><?php echo $p['count']; ?></span>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">Nessuna piattaforma</p>
                <?php endif; ?>
                
                <h6 class="mb-3"><i class="fas fa-tags text-danger"></i> Generi</h6>
                <?php if(count($generi_giochi) > 0): ?>
                    <?php foreach($generi_giochi as $g): ?>
                        <?php $perc = $tot_giochi > 0 ? round(($g['count']/$tot_giochi)*100) : 0; ?>
                        <div class="genere-bar mb-2">
                            <div class="d-flex justify-content-between mb-1">
                                <small class="fw-600"><?php echo htmlspecialchars($g['genere']); ?></small>
                                <span class="badge bg-dark"><?php echo $g['count']; ?></span>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 10px;">
                                <div class="progress-bar bg-dark" style="width: <?php echo $perc; ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- STATISTICHE MULTIMEDIA & NOTE -->
<div class="row mb-4">
    <div class="col-lg-4 mb-4">
        <div class="card stat-detail-card">
            <div class="card-header-custom" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <h5 class="mb-0"><i class="fas fa-photo-video"></i> Multimedia</h5>
            </div>
            <div class="card-body text-center">
                <div class="stat-number-big"><?php echo $tot_multimedia; ?></div>
                <p class="text-muted">Contenuti Totali</p>
                
                <?php if(count($tipi_multimedia) > 0): ?>
                    <div class="d-flex flex-wrap justify-content-center gap-2 mt-3">
                        <?php foreach($tipi_multimedia as $t): ?>
                            <?php
                            $icon = 'file';
                            switch($t['tipo']) {
                                case 'video': $icon = 'video'; break;
                                case 'audio': $icon = 'music'; break;
                                case 'immagine': $icon = 'image'; break;
                                case 'podcast': $icon = 'podcast'; break;
                            }
                            ?>
                            <div class="tipo-stat-box">
                                <i class="fas fa-<?php echo $icon; ?>"></i>
                                <span class="tipo-count"><?php echo $t['count']; ?></span>
                                <small><?php echo ucfirst($t['tipo']); ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card stat-detail-card">
            <div class="card-header-custom" style="background: linear-gradient(135deg, #ffc107, #ff9800);">
                <h5 class="mb-0 text-dark"><i class="fas fa-sticky-note"></i> Note</h5>
            </div>
            <div class="card-body text-center">
                <div class="stat-number-big"><?php echo $tot_note; ?></div>
                <p class="text-muted">Note Totali</p>
                
                <div class="row mt-3">
                    <div class="col-4">
                        <div class="stat-number text-success"><?php echo $note_completate; ?></div>
                        <small class="text-muted">Completate</small>
                    </div>
                    <div class="col-4">
                        <div class="stat-number text-warning"><?php echo $tot_note - $note_completate - $note_scadute; ?></div>
                        <small class="text-muted">Attive</small>
                    </div>
                    <div class="col-4">
                        <div class="stat-number text-danger"><?php echo $note_scadute; ?></div>
                        <small class="text-muted">Scadute</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4 mb-4">
        <div class="card stat-detail-card">
            <div class="card-header-custom" style="background: linear-gradient(135deg, var(--accent), #ff6b6b);">
                <h5 class="mb-0"><i class="fas fa-bullseye"></i> Obiettivi</h5>
            </div>
            <div class="card-body text-center">
                <div class="stat-number-big"><?php echo $tot_obiettivi; ?></div>
                <p class="text-muted">Obiettivi Totali</p>
                
                <div class="mb-3 mt-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Completamento</span>
                        <strong><?php $perc_ob = $tot_obiettivi > 0 ? round(($obiettivi_completati/$tot_obiettivi)*100) : 0; echo $perc_ob; ?>%</strong>
                    </div>
                    <div class="progress" style="height: 15px; border-radius: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger" style="width: <?php echo $perc_ob; ?>%"></div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-6">
                        <div class="stat-number text-success"><?php echo $obiettivi_completati; ?></div>
                        <small class="text-muted">Raggiunti</small>
                    </div>
                    <div class="col-6">
                        <div class="stat-number text-warning"><?php echo $tot_obiettivi - $obiettivi_completati; ?></div>
                        <small class="text-muted">In corso</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ACHIEVEMENTS -->
<div class="card card-premium">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-trophy"></i> Traguardi</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="achievement-card <?php echo $libri_letti >= 5 ? 'achieved' : ''; ?>">
                    <div class="achievement-icon">
                        <i class="fas fa-book-reader"></i>
                    </div>
                    <h6>Lettore Appassionato</h6>
                    <p>Leggi 5 libri</p>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar bg-danger" style="width: <?php echo min(($libri_letti/5)*100, 100); ?>%"></div>
                    </div>
                    <small class="text-muted"><?php echo $libri_letti; ?>/5</small>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="achievement-card <?php echo $giochi_completati >= 3 ? 'achieved' : ''; ?>">
                    <div class="achievement-icon">
                        <i class="fas fa-gamepad"></i>
                    </div>
                    <h6>Gamer Esperto</h6>
                    <p>Completa 3 giochi</p>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar bg-success" style="width: <?php echo min(($giochi_completati/3)*100, 100); ?>%"></div>
                    </div>
                    <small class="text-muted"><?php echo $giochi_completati; ?>/3</small>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="achievement-card <?php echo $totale_elementi >= 20 ? 'achieved' : ''; ?>">
                    <div class="achievement-icon">
                        <i class="fas fa-backpack"></i>
                    </div>
                    <h6>Zaino Pieno</h6>
                    <p>Aggiungi 20 elementi</p>
                    <div class="progress" style="height: 8px; border-radius: 10px;">
                        <div class="progress-bar bg-warning" style="width: <?php echo min(($totale_elementi/20)*100, 100); ?>%"></div>
                    </div>
                    <small class="text-muted"><?php echo $totale_elementi; ?>/20</small>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>