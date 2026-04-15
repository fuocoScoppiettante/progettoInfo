<?php 
include 'includes/header.php';
checkLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];

// Statistiche Libri
$stmt = $db->prepare("SELECT COUNT(*) as totale FROM libri WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_libri = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM libri WHERE user_id = ? AND stato = 'completato'");
$stmt->execute([$user_id]);
$libri_letti = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM libri WHERE user_id = ? AND preferito = 1");
$stmt->execute([$user_id]);
$libri_preferiti = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT SUM(prezzo) as totale FROM libri WHERE user_id = ?");
$stmt->execute([$user_id]);
$spesa_totale = $stmt->fetch()['totale'] ?? 0;

$stmt = $db->prepare("SELECT genere, COUNT(*) as count FROM libri WHERE user_id = ? AND genere IS NOT NULL GROUP BY genere ORDER BY count DESC LIMIT 5");
$stmt->execute([$user_id]);
$generi_top = $stmt->fetchAll();

// Statistiche Altri Contenuti
$stmt = $db->prepare("SELECT COUNT(*) as totale FROM multimedia WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_multimedia = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM giochi WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_giochi = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM giochi WHERE user_id = ? AND stato = 'completato'");
$stmt->execute([$user_id]);
$giochi_completati = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM documenti WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_documenti = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM note WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_note = $stmt->fetch()['totale'];
?>

<h2 class="mb-4"><i class="fas fa-chart-bar"></i> Statistiche</h2>

<!-- Statistiche Generali -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-database"></i> Riepilogo Generale</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-2">
                        <h3 class="text-primary"><?php echo $totale_libri; ?></h3>
                        <p>Libri Totali</p>
                    </div>
                    <div class="col-md-2">
                        <h3 class="text-success"><?php echo $totale_multimedia; ?></h3>
                        <p>Multimedia</p>
                    </div>
                    <div class="col-md-2">
                        <h3 class="text-danger"><?php echo $totale_giochi; ?></h3>
                        <p>Giochi</p>
                    </div>
                    <div class="col-md-2">
                        <h3 class="text-warning"><?php echo $totale_documenti; ?></h3>
                        <p>Documenti</p>
                    </div>
                    <div class="col-md-2">
                        <h3 class="text-info"><?php echo $totale_note; ?></h3>
                        <p>Note</p>
                    </div>
                    <div class="col-md-2">
                        <h3 class="text-dark"><?php echo $totale_libri + $totale_multimedia + $totale_giochi + $totale_documenti + $totale_note; ?></h3>
                        <p>Totale Elementi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Statistiche Libri -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5><i class="fas fa-book"></i> Statistiche Libri</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>Libri totali:</strong></td>
                        <td><?php echo $totale_libri; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Libri letti:</strong></td>
                        <td><?php echo $libri_letti; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Libri preferiti:</strong></td>
                        <td><i class="fas fa-star text-warning"></i> <?php echo $libri_preferiti; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Spesa totale:</strong></td>
                        <td><strong>€<?php echo number_format($spesa_totale, 2); ?></strong></td>
                    </tr>
                    <tr>
                        <td><strong>Percentuale letti:</strong></td>
                        <td>
                            <?php 
                            $percentuale = $totale_libri > 0 ? round(($libri_letti / $totale_libri) * 100) : 0;
                            echo $percentuale . '%';
                            ?>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-success" style="width: <?php echo $percentuale; ?>%"></div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5><i class="fas fa-chart-pie"></i> Top 5 Generi</h5>
            </div>
            <div class="card-body">
                <?php if(count($generi_top) > 0): ?>
                    <table class="table">
                        <?php foreach($generi_top as $genere): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($genere['genere']); ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo $genere['count']; ?></span>
                                <div class="progress mt-1" style="height: 5px;">
                                    <div class="progress-bar" style="width: <?php echo ($genere['count'] / $totale_libri) * 100; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </table>
                <?php else: ?>
                    <p class="text-muted">Nessun genere disponibile</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Statistiche Giochi -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-danger text-white">
                <h5><i class="fas fa-gamepad"></i> Statistiche Giochi</h5>
            </div>
            <div class="card-body">
                <table class="table">
                    <tr>
                        <td><strong>Giochi totali:</strong></td>
                        <td><?php echo $totale_giochi; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Giochi completati:</strong></td>
                        <td><?php echo $giochi_completati; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Percentuale completati:</strong></td>
                        <td>
                            <?php 
                            $percentuale_giochi = $totale_giochi > 0 ? round(($giochi_completati / $totale_giochi) * 100) : 0;
                            echo $percentuale_giochi . '%';
                            ?>
                            <div class="progress mt-2">
                                <div class="progress-bar bg-danger" style="width: <?php echo $percentuale_giochi; ?>%"></div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5><i class="fas fa-trophy"></i> Obiettivi</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p><strong>10 Libri letti</strong></p>
                    <div class="progress">
                        <div class="progress-bar bg-success" style="width: <?php echo min(($libri_letti / 10) * 100, 100); ?>%">
                            <?php echo $libri_letti; ?>/10
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p><strong>5 Giochi completati</strong></p>
                    <div class="progress">
                        <div class="progress-bar bg-danger" style="width: <?php echo min(($giochi_completati / 5) * 100, 100); ?>%">
                            <?php echo $giochi_completati; ?>/5
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p><strong>20 Note create</strong></p>
                    <div class="progress">
                        <div class="progress-bar bg-info" style="width: <?php echo min(($totale_note / 20) * 100, 100); ?>%">
                            <?php echo $totale_note; ?>/20
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>