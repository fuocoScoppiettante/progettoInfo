<?php 
include 'includes/header.php';
checkLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];

// Statistiche rapide
$stmt = $db->prepare("SELECT COUNT(*) as totale FROM libri WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_libri = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM multimedia WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_multimedia = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM giochi WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_giochi = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM documenti WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_documenti = $stmt->fetch()['totale'];

$stmt = $db->prepare("SELECT COUNT(*) as totale FROM note WHERE user_id = ?");
$stmt->execute([$user_id]);
$totale_note = $stmt->fetch()['totale'];
?>

<h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Dashboard</h2>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-book"></i> Libri</h5>
                <h2><?php echo $totale_libri; ?></h2>
                <a href="libri.php" class="btn btn-light btn-sm">Visualizza</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-photo-video"></i> Multimedia</h5>
                <h2><?php echo $totale_multimedia; ?></h2>
                <a href="multimedia.php" class="btn btn-light btn-sm">Visualizza</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-gamepad"></i> Giochi</h5>
                <h2><?php echo $totale_giochi; ?></h2>
                <a href="giochi.php" class="btn btn-light btn-sm">Visualizza</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-file-alt"></i> Documenti</h5>
                <h2><?php echo $totale_documenti; ?></h2>
                <a href="documenti.php" class="btn btn-dark btn-sm">Visualizza</a>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 mb-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="card-title"><i class="fas fa-sticky-note"></i> Note Rapide</h5>
                <h2><?php echo $totale_note; ?></h2>
                <a href="note.php" class="btn btn-light btn-sm">Visualizza</a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-clock"></i> Attività Recenti</h5>
            </div>
            <div class="card-body">
                <?php
                // Ultimi 5 libri aggiunti
                $stmt = $db->prepare("SELECT titolo, data_aggiunta FROM libri WHERE user_id = ? ORDER BY data_aggiunta DESC LIMIT 5");
                $stmt->execute([$user_id]);
                $ultimi_libri = $stmt->fetchAll();
                
                if(count($ultimi_libri) > 0):
                ?>
                <h6>Ultimi libri aggiunti:</h6>
                <ul class="list-group">
                    <?php foreach($ultimi_libri as $libro): ?>
                    <li class="list-group-item">
                        <i class="fas fa-book text-primary"></i> 
                        <?php echo htmlspecialchars($libro['titolo']); ?>
                        <small class="text-muted float-end">
                            <?php echo date('d/m/Y H:i', strtotime($libro['data_aggiunta'])); ?>
                        </small>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <p class="text-muted">Nessuna attività recente. Inizia ad aggiungere contenuti!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>