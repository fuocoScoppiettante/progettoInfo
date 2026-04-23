<?php 
include 'includes/header.php';
checkLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];
$tipologia = getTipologia();
$success = '';
$error = '';

// CRUD
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $titolo = trim($_POST['titolo']);
    $contenuto = trim($_POST['contenuto']);
    $colore = $_POST['colore'];
    $priorita = $_POST['priorita'];
    $data_scadenza = !empty($_POST['data_scadenza']) ? $_POST['data_scadenza'] : null;
    
    if(empty($contenuto)) { $error = "Il contenuto è obbligatorio!"; }
    else {
        try {
            if($_POST['action'] == 'add') {
                $stmt = $db->prepare("INSERT INTO note (user_id, titolo, contenuto, colore, priorita, data_scadenza) VALUES (?,?,?,?,?,?)");
                $stmt->execute([$user_id, $titolo, $contenuto, $colore, $priorita, $data_scadenza]);
                $success = "Nota aggiunta!";
            } else {
                $id = intval($_POST['id']);
                $stmt = $db->prepare("UPDATE note SET titolo=?, contenuto=?, colore=?, priorita=?, data_scadenza=? WHERE id=? AND user_id=?");
                $stmt->execute([$titolo, $contenuto, $colore, $priorita, $data_scadenza, $id, $user_id]);
                $success = "Nota modificata!";
            }
        } catch(PDOException $e) { $error = "Errore: " . $e->getMessage(); }
    }
}

if(isset($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM note WHERE id = ? AND user_id = ?");
    $stmt->execute([intval($_GET['delete']), $user_id]);
    $success = "Nota eliminata!";
}

if(isset($_GET['toggle'])) {
    $id = intval($_GET['toggle']);
    $stmt = $db->prepare("SELECT completata FROM note WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $n = $stmt->fetch();
    if($n) {
        $stmt = $db->prepare("UPDATE note SET completata = ? WHERE id = ? AND user_id = ?");
        $stmt->execute([$n['completata'] ? 0 : 1, $id, $user_id]);
    }
}

// Recupera note
$stmt = $db->prepare("SELECT * FROM note WHERE user_id = ? ORDER BY completata ASC, 
    CASE priorita WHEN 'urgente' THEN 1 WHEN 'alta' THEN 2 WHEN 'media' THEN 3 WHEN 'bassa' THEN 4 END,
    data_scadenza ASC");
$stmt->execute([$user_id]);
$note = $stmt->fetchAll();

// Note in scadenza (per popup)
$stmt = $db->prepare("SELECT * FROM note WHERE user_id = ? AND data_scadenza IS NOT NULL AND data_scadenza BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 2 DAY) AND completata = 0");
$stmt->execute([$user_id]);
$note_scadenza = $stmt->fetchAll();

$nota_edit = null;
if(isset($_GET['edit'])) {
    $stmt = $db->prepare("SELECT * FROM note WHERE id = ? AND user_id = ?");
    $stmt->execute([intval($_GET['edit']), $user_id]);
    $nota_edit = $stmt->fetch();
}
?>

<!-- POPUP SCADENZA -->
<?php if(count($note_scadenza) > 0): ?>
<div class="modal fade" id="scadenzaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content scadenza-modal">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> ⚠️ Scadenze Vicine!</h5>
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

<!-- Header -->
<div class="page-header mb-4">
    <h2 class="display-5 fw-bold">
        <i class="fas fa-sticky-note text-gradient"></i> Le Mie Note
    </h2>
    <p class="text-muted">Gestisci appunti e promemoria con scadenze</p>
</div>

<?php if($success): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i> <?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- LISTA NOTE - SOPRA -->
<div class="row mb-5">
    <?php if(count($note) > 0): ?>
        <?php foreach($note as $nota): ?>
            <?php
            $scaduta = false;
            $quasi_scaduta = false;
            if($nota['data_scadenza'] && !$nota['completata']) {
                $ora = new DateTime();
                $scad = new DateTime($nota['data_scadenza']);
                $diff = $ora->diff($scad);
                $scaduta = $scad < $ora;
                $quasi_scaduta = !$scaduta && $diff->days <= 2;
            }
            ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card note-card-premium note-<?php echo $nota['colore']; ?> <?php echo $nota['completata'] ? 'note-completata' : ''; ?> <?php echo $scaduta ? 'note-scaduta' : ''; ?> <?php echo $quasi_scaduta ? 'note-quasi-scaduta' : ''; ?>">
                    
                    <!-- Header Nota -->
                    <div class="note-header">
                        <span class="badge badge-priorita-<?php echo $nota['priorita']; ?>">
                            <?php echo ucfirst($nota['priorita']); ?>
                        </span>
                        <a href="note.php?toggle=<?php echo $nota['id']; ?>" class="note-check <?php echo $nota['completata'] ? 'checked' : ''; ?>">
                            <i class="fas fa-<?php echo $nota['completata'] ? 'check-circle' : 'circle'; ?>"></i>
                        </a>
                    </div>
                    
                    <!-- Body Nota -->
                    <div class="card-body">
                        <?php if($nota['titolo']): ?>
                            <h5 class="note-title <?php echo $nota['completata'] ? 'text-decoration-line-through' : ''; ?>">
                                <?php echo htmlspecialchars($nota['titolo']); ?>
                            </h5>
                        <?php endif; ?>
                        <p class="note-content"><?php echo nl2br(htmlspecialchars($nota['contenuto'])); ?></p>
                        
                        <!-- Scadenza -->
                        <?php if($nota['data_scadenza']): ?>
                            <div class="note-scadenza <?php echo $scaduta ? 'scaduta' : ($quasi_scaduta ? 'quasi' : 'ok'); ?>">
                                <i class="fas fa-<?php echo $scaduta ? 'exclamation-triangle' : ($quasi_scaduta ? 'bell' : 'clock'); ?>"></i>
                                <?php if($scaduta): ?>
                                    SCADUTA
                                <?php elseif($quasi_scaduta): ?>
                                    Scade tra <?php echo $diff->days; ?>g <?php echo $diff->h; ?>h
                                <?php else: ?>
                                    <?php echo date('d/m/Y H:i', strtotime($nota['data_scadenza'])); ?>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Footer Nota -->
                    <div class="note-footer">
                        <small class="text-muted"><?php echo date('d/m/Y', strtotime($nota['data_creazione'])); ?></small>
                        <div class="note-actions">
                            <a href="note.php?edit=<?php echo $nota['id']; ?>#form-nota" class="btn btn-sm btn-dark"><i class="fas fa-edit"></i></a>
                            <a href="note.php?delete=<?php echo $nota['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminare?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-sticky-note fa-5x mb-3"></i>
                <h3>Nessuna nota</h3>
                <p class="text-muted">Crea la tua prima nota!</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- FORM - SOTTO -->
<div class="card card-premium" id="form-nota">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> <?php echo $nota_edit ? 'Modifica Nota' : 'Nuova Nota'; ?></h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="<?php echo $nota_edit ? 'edit' : 'add'; ?>">
            <?php if($nota_edit): ?><input type="hidden" name="id" value="<?php echo $nota_edit['id']; ?>"><?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Titolo</label>
                    <input type="text" class="form-control" name="titolo" value="<?php echo $nota_edit ? htmlspecialchars($nota_edit['titolo']) : ''; ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label"><i class="fas fa-calendar-times"></i> Scadenza</label>
                    <input type="datetime-local" class="form-control" name="data_scadenza" 
                           value="<?php echo $nota_edit && $nota_edit['data_scadenza'] ? date('Y-m-d\TH:i', strtotime($nota_edit['data_scadenza'])) : ''; ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Contenuto *</label>
                <textarea class="form-control" name="contenuto" rows="3" required><?php echo $nota_edit ? htmlspecialchars($nota_edit['contenuto']) : ''; ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Colore</label>
                    <select class="form-select" name="colore">
                        <option value="yellow" <?php echo ($nota_edit && $nota_edit['colore']=='yellow') ? 'selected' : ''; ?>>🟡 Giallo</option>
                        <option value="green" <?php echo ($nota_edit && $nota_edit['colore']=='green') ? 'selected' : ''; ?>>🟢 Verde</option>
                        <option value="blue" <?php echo ($nota_edit && $nota_edit['colore']=='blue') ? 'selected' : ''; ?>>🔵 Blu</option>
                        <option value="pink" <?php echo ($nota_edit && $nota_edit['colore']=='pink') ? 'selected' : ''; ?>>🩷 Rosa</option>
                        <option value="orange" <?php echo ($nota_edit && $nota_edit['colore']=='orange') ? 'selected' : ''; ?>>🟠 Arancione</option>
                        <option value="purple" <?php echo ($nota_edit && $nota_edit['colore']=='purple') ? 'selected' : ''; ?>>🟣 Viola</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Priorità</label>
                    <select class="form-select" name="priorita">
                        <option value="bassa" <?php echo ($nota_edit && $nota_edit['priorita']=='bassa') ? 'selected' : ''; ?>>🟢 Bassa</option>
                        <option value="media" <?php echo ($nota_edit && $nota_edit['priorita']=='media') ? 'selected' : ''; ?>>🟡 Media</option>
                        <option value="alta" <?php echo ($nota_edit && $nota_edit['priorita']=='alta') ? 'selected' : ''; ?>>🟠 Alta</option>
                        <option value="urgente" <?php echo ($nota_edit && $nota_edit['priorita']=='urgente') ? 'selected' : ''; ?>>🔴 Urgente</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $nota_edit ? 'Salva' : 'Aggiungi'; ?></button>
            <?php if($nota_edit): ?><a href="note.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annulla</a><?php endif; ?>
        </form>
    </div>
</div>

<?php include 'includes/footer.php'; ?>