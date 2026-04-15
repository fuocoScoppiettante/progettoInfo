<?php 
include 'includes/header.php';
checkLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];

$success = '';
$error = '';

// Gestione CRUD (simile a libri.php)
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if($_POST['action'] == 'add' || $_POST['action'] == 'edit') {
        $titolo = trim($_POST['titolo']);
        $tipo = $_POST['tipo'];
        $link = trim($_POST['link']);
        $categoria = trim($_POST['categoria']);
        $preferito = isset($_POST['preferito']) ? 1 : 0;
        
        if(empty($titolo)) {
            $error = "Il titolo è obbligatorio!";
        } else {
            try {
                if($_POST['action'] == 'add') {
                    $stmt = $db->prepare("INSERT INTO multimedia (user_id, titolo, tipo, link, categoria, preferito) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $titolo, $tipo, $link, $categoria, $preferito]);
                    $success = "Contenuto multimediale aggiunto!";
                } else {
                    $id = intval($_POST['id']);
                    $stmt = $db->prepare("UPDATE multimedia SET titolo=?, tipo=?, link=?, categoria=?, preferito=? WHERE id=? AND user_id=?");
                    $stmt->execute([$titolo, $tipo, $link, $categoria, $preferito, $id, $user_id]);
                    $success = "Contenuto modificato!";
                }
            } catch(PDOException $e) {
                $error = "Errore: " . $e->getMessage();
            }
        }
    }
}

if(isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    try {
        $stmt = $db->prepare("DELETE FROM multimedia WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        $success = "Contenuto eliminato!";
    } catch(PDOException $e) {
        $error = "Errore: " . $e->getMessage();
    }
}

// Recupera contenuti
$stmt = $db->prepare("SELECT * FROM multimedia WHERE user_id = ? ORDER BY data_aggiunta DESC");
$stmt->execute([$user_id]);
$contenuti = $stmt->fetchAll();

$contenuto_edit = null;
if(isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM multimedia WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $contenuto_edit = $stmt->fetch();
}
?>

<h2 class="mb-4"><i class="fas fa-photo-video"></i> Contenuti Multimediali</h2>

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

<!-- Form -->
<div class="card mb-4">
    <div class="card-header bg-success text-white">
        <h5><i class="fas fa-plus"></i> <?php echo $contenuto_edit ? 'Modifica' : 'Aggiungi'; ?> Contenuto</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="multimedia.php">
            <input type="hidden" name="action" value="<?php echo $contenuto_edit ? 'edit' : 'add'; ?>">
            <?php if($contenuto_edit): ?>
                <input type="hidden" name="id" value="<?php echo $contenuto_edit['id']; ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Titolo *</label>
                    <input type="text" class="form-control" name="titolo" value="<?php echo $contenuto_edit ? htmlspecialchars($contenuto_edit['titolo']) : ''; ?>" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" name="tipo">
                        <option value="video" <?php echo ($contenuto_edit && $contenuto_edit['tipo'] == 'video') ? 'selected' : ''; ?>>Video</option>
                        <option value="audio" <?php echo ($contenuto_edit && $contenuto_edit['tipo'] == 'audio') ? 'selected' : ''; ?>>Audio</option>
                        <option value="immagine" <?php echo ($contenuto_edit && $contenuto_edit['tipo'] == 'immagine') ? 'selected' : ''; ?>>Immagine</option>
                        <option value="altro" <?php echo ($contenuto_edit && $contenuto_edit['tipo'] == 'altro') ? 'selected' : ''; ?>>Altro</option>
                    </select>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Link/URL</label>
                <input type="url" class="form-control" name="link" value="<?php echo $contenuto_edit ? htmlspecialchars($contenuto_edit['link']) : ''; ?>" placeholder="https://...">
            </div>
            
            <div class="mb-3">
                <label class="form-label">Categoria</label>
                <input type="text" class="form-control" name="categoria" value="<?php echo $contenuto_edit ? htmlspecialchars($contenuto_edit['categoria']) : ''; ?>" placeholder="es: Tutorial, Musica, Foto...">
            </div>
            
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" name="preferito" id="preferito" <?php echo ($contenuto_edit && $contenuto_edit['preferito']) ? 'checked' : ''; ?>>
                <label class="form-check-label" for="preferito">
                    <i class="fas fa-star text-warning"></i> Preferito
                </label>
            </div>
            
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> <?php echo $contenuto_edit ? 'Salva' : 'Aggiungi'; ?>
            </button>
            <?php if($contenuto_edit): ?>
                <a href="multimedia.php" class="btn btn-secondary">Annulla</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Lista Contenuti -->
<div class="row">
    <?php if(count($contenuti) > 0): ?>
        <?php foreach($contenuti as $contenuto): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge bg-<?php 
                        echo $contenuto['tipo'] == 'video' ? 'danger' : ($contenuto['tipo'] == 'audio' ? 'info' : 'secondary'); 
                    ?>">
                        <i class="fas fa-<?php 
                            echo $contenuto['tipo'] == 'video' ? 'video' : ($contenuto['tipo'] == 'audio' ? 'music' : 'file'); 
                        ?>"></i> 
                        <?php echo ucfirst($contenuto['tipo']); ?>
                    </span>
                    <?php if($contenuto['preferito']): ?>
                        <i class="fas fa-star text-warning"></i>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($contenuto['titolo']); ?></h5>
                    
                    <?php if($contenuto['categoria']): ?>
                        <p class="card-text"><span class="badge bg-info"><?php echo htmlspecialchars($contenuto['categoria']); ?></span></p>
                    <?php endif; ?>
                    
                    <?php if($contenuto['link']): ?>
                        <p class="card-text">
                            <a href="<?php echo htmlspecialchars($contenuto['link']); ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i> Apri Link
                            </a>
                        </p>
                    <?php endif; ?>
                    
                    <p class="card-text"><small class="text-muted"><?php echo date('d/m/Y', strtotime($contenuto['data_aggiunta'])); ?></small></p>
                </div>
                <div class="card-footer">
                    <a href="multimedia.php?edit=<?php echo $contenuto['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="multimedia.php?delete=<?php echo $contenuto['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminare?')">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                Nessun contenuto multimediale presente.
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>