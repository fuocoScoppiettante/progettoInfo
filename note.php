<?php 
include 'includes/header.php';
checkLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];

$success = '';
$error = '';

// Gestione AGGIUNGI/MODIFICA
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if($_POST['action'] == 'add' || $_POST['action'] == 'edit') {
        $titolo = trim($_POST['titolo']);
        $contenuto = trim($_POST['contenuto']);
        $colore = $_POST['colore'];
        
        if(empty($contenuto)) {
            $error = "Il contenuto è obbligatorio!";
        } else {
            try {
                if($_POST['action'] == 'add') {
                    $stmt = $db->prepare("INSERT INTO note (user_id, titolo, contenuto, colore) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$user_id, $titolo, $contenuto, $colore]);
                    $success = "Nota aggiunta!";
                } else {
                    $id = intval($_POST['id']);
                    $stmt = $db->prepare("UPDATE note SET titolo=?, contenuto=?, colore=? WHERE id=? AND user_id=?");
                    $stmt->execute([$titolo, $contenuto, $colore, $id, $user_id]);
                    $success = "Nota modificata!";
                }
            } catch(PDOException $e) {
                $error = "Errore: " . $e->getMessage();
            }
        }
    }
}

// Gestione ELIMINA
if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $db->prepare("DELETE FROM note WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        $success = "Nota eliminata!";
    } catch(PDOException $e) {
        $error = "Errore: " . $e->getMessage();
    }
}

// Recupera note
$stmt = $db->prepare("SELECT * FROM note WHERE user_id = ? ORDER BY data_creazione DESC");
$stmt->execute([$user_id]);
$note = $stmt->fetchAll();

// Se è richiesta la modifica
$nota_edit = null;
if(isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM note WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $nota_edit = $stmt->fetch();
}
?>

<h2 class="mb-4"><i class="fas fa-sticky-note"></i> Note Rapide</h2>

<?php if($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Form Aggiungi/Modifica -->
<div class="card mb-4">
    <div class="card-header bg-info text-white">
        <h5><i class="fas fa-plus"></i> <?php echo $nota_edit ? 'Modifica Nota' : 'Nuova Nota'; ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" action="note.php">
            <input type="hidden" name="action" value="<?php echo $nota_edit ? 'edit' : 'add'; ?>">
            <?php if($nota_edit): ?>
                <input type="hidden" name="id" value="<?php echo $nota_edit['id']; ?>">
            <?php endif; ?>
            
            <div class="mb-3">
                <label class="form-label">Titolo (opzionale)</label>
                <input type="text" class="form-control" name="titolo" value="<?php echo $nota_edit ? htmlspecialchars($nota_edit['titolo']) : ''; ?>">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Contenuto *</label>
                <textarea class="form-control" name="contenuto" rows="4" required><?php echo $nota_edit ? htmlspecialchars($nota_edit['contenuto']) : ''; ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Colore</label>
                <select class="form-select" name="colore">
                    <option value="yellow" <?php echo ($nota_edit && $nota_edit['colore'] == 'yellow') ? 'selected' : ''; ?>>Giallo</option>
                    <option value="green" <?php echo ($nota_edit && $nota_edit['colore'] == 'green') ? 'selected' : ''; ?>>Verde</option>
                    <option value="blue" <?php echo ($nota_edit && $nota_edit['colore'] == 'blue') ? 'selected' : ''; ?>>Blu</option>
                    <option value="pink" <?php echo ($nota_edit && $nota_edit['colore'] == 'pink') ? 'selected' : ''; ?>>Rosa</option>
                    <option value="orange" <?php echo ($nota_edit && $nota_edit['colore'] == 'orange') ? 'selected' : ''; ?>>Arancione</option>
                </select>
            </div>
            
            <button type="submit" class="btn btn-info text-white">
                <i class="fas fa-save"></i> <?php echo $nota_edit ? 'Salva' : 'Aggiungi'; ?>
            </button>
            <?php if($nota_edit): ?>
                <a href="note.php" class="btn btn-secondary">Annulla</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Lista Note -->
<div class="row">
    <?php if(count($note) > 0): ?>
        <?php foreach($note as $nota): ?>
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100 note-card note-<?php echo $nota['colore']; ?>">
                <div class="card-body">
                    <?php if($nota['titolo']): ?>
                        <h5 class="card-title"><?php echo htmlspecialchars($nota['titolo']); ?></h5>
                    <?php endif; ?>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($nota['contenuto'])); ?></p>
                    <p class="card-text"><small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($nota['data_creazione'])); ?></small></p>
                </div>
                <div class="card-footer bg-transparent border-0">
                    <a href="note.php?edit=<?php echo $nota['id']; ?>" class="btn btn-sm btn-dark">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="note.php?delete=<?php echo $nota['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminare questa nota?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Nessuna nota presente. Inizia a prendere appunti!
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>