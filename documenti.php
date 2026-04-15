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
        $tipo = trim($_POST['tipo']);
        $descrizione = trim($_POST['descrizione']);
        $file_path = trim($_POST['file_path']);
        $importante = isset($_POST['importante']) ? 1 : 0;
        
        if(empty($titolo)) {
            $error = "Il titolo è obbligatorio!";
        } else {
            try {
                if($_POST['action'] == 'add') {
                    $stmt = $db->prepare("INSERT INTO documenti (user_id, titolo, tipo, descrizione, file_path, importante) VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $titolo, $tipo, $descrizione, $file_path, $importante]);
                    $success = "Documento aggiunto con successo!";
                } else {
                    $id = intval($_POST['id']);
                    $stmt = $db->prepare("UPDATE documenti SET titolo=?, tipo=?, descrizione=?, file_path=?, importante=? WHERE id=? AND user_id=?");
                    $stmt->execute([$titolo, $tipo, $descrizione, $file_path, $importante, $id, $user_id]);
                    $success = "Documento modificato con successo!";
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
        $stmt = $db->prepare("DELETE FROM documenti WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        $success = "Documento eliminato!";
    } catch(PDOException $e) {
        $error = "Errore nell'eliminazione: " . $e->getMessage();
    }
}

// Filtri
$where = "user_id = ?";
$params = [$user_id];

if(isset($_GET['tipo']) && !empty($_GET['tipo'])) {
    $where .= " AND tipo = ?";
    $params[] = $_GET['tipo'];
}

if(isset($_GET['importanti']) && $_GET['importanti'] == '1') {
    $where .= " AND importante = 1";
}

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $where .= " AND (titolo LIKE ? OR descrizione LIKE ?)";
    $search = '%' . $_GET['search'] . '%';
    $params[] = $search;
    $params[] = $search;
}

// Recupera documenti
$stmt = $db->prepare("SELECT * FROM documenti WHERE $where ORDER BY importante DESC, data_aggiunta DESC");
$stmt->execute($params);
$documenti = $stmt->fetchAll();

// Recupera tipi
$stmt = $db->prepare("SELECT DISTINCT tipo FROM documenti WHERE user_id = ? AND tipo IS NOT NULL ORDER BY tipo");
$stmt->execute([$user_id]);
$tipi = $stmt->fetchAll();

// Modifica
$documento_edit = null;
if(isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM documenti WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $documento_edit = $stmt->fetch();
}
?>

<div class="page-header mb-4">
    <h2 class="display-5 fw-bold">
        <i class="fas fa-folder-open text-gradient"></i> I Miei Documenti
    </h2>
    <p class="text-muted">Organizza i tuoi documenti importanti</p>
</div>

<?php if($success): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Form Aggiungi/Modifica -->
<div class="card mb-4 card-premium">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-plus-circle"></i> <?php echo $documento_edit ? 'Modifica Documento' : 'Aggiungi Nuovo Documento'; ?>
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="documenti.php">
            <input type="hidden" name="action" value="<?php echo $documento_edit ? 'edit' : 'add'; ?>">
            <?php if($documento_edit): ?>
                <input type="hidden" name="id" value="<?php echo $documento_edit['id']; ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">
                        <i class="fas fa-heading"></i> Titolo Documento *
                    </label>
                    <input type="text" class="form-control" name="titolo" 
                           value="<?php echo $documento_edit ? htmlspecialchars($documento_edit['titolo']) : ''; ?>" 
                           placeholder="Es: Appunti Matematica" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        <i class="fas fa-tag"></i> Tipo
                    </label>
                    <input type="text" class="form-control" name="tipo" 
                           value="<?php echo $documento_edit ? htmlspecialchars($documento_edit['tipo']) : ''; ?>" 
                           placeholder="Es: PDF, Word, Appunti">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-align-left"></i> Descrizione
                </label>
                <textarea class="form-control" name="descrizione" rows="3" 
                          placeholder="Descrizione del documento..."><?php echo $documento_edit ? htmlspecialchars($documento_edit['descrizione']) : ''; ?></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-link"></i> Link/Percorso File
                </label>
                <input type="text" class="form-control" name="file_path" 
                       value="<?php echo $documento_edit ? htmlspecialchars($documento_edit['file_path']) : ''; ?>" 
                       placeholder="https:// o percorso locale">
            </div>
            
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="importante" id="importante" 
                           <?php echo ($documento_edit && $documento_edit['importante']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="importante">
                        <i class="fas fa-exclamation-triangle text-warning"></i> Segna come importante
                    </label>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $documento_edit ? 'Salva Modifiche' : 'Aggiungi Documento'; ?>
                </button>
                <?php if($documento_edit): ?>
                    <a href="documenti.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annulla
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Filtri -->
<div class="card mb-4 card-filters">
    <div class="card-body">
        <form method="GET" action="documenti.php" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label"><i class="fas fa-search"></i> Ricerca</label>
                <input type="text" class="form-control" name="search" 
                       placeholder="Cerca per titolo o descrizione..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            
            <div class="col-md-3">
                <label class="form-label"><i class="fas fa-filter"></i> Tipo</label>
                <select class="form-select" name="tipo">
                    <option value="">Tutti i tipi</option>
                    <?php foreach($tipi as $t): ?>
                        <option value="<?php echo htmlspecialchars($t['tipo']); ?>" 
                                <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == $t['tipo']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($t['tipo']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="importanti" value="1" id="filtro_importanti" 
                           <?php echo (isset($_GET['importanti']) && $_GET['importanti'] == '1') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="filtro_importanti">
                        <i class="fas fa-star text-warning"></i> Importanti
                    </label>
                </div>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filtra
                </button>
                <a href="documenti.php" class="btn btn-secondary w-100 mt-2">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista Documenti -->
<div class="row">
    <?php if(count($documenti) > 0): ?>
        <?php foreach($documenti as $doc): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card document-card h-100 <?php echo $doc['importante'] ? 'document-important' : ''; ?>">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <?php if($doc['tipo']): ?>
                        <span class="badge bg-dark">
                            <i class="fas fa-file"></i> <?php echo htmlspecialchars($doc['tipo']); ?>
                        </span>
                    <?php else: ?>
                        <span class="badge bg-secondary">
                            <i class="fas fa-file"></i> Documento
                        </span>
                    <?php endif; ?>
                    
                    <?php if($doc['importante']): ?>
                        <i class="fas fa-exclamation-circle text-warning pulse-icon" title="Importante"></i>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">
                        <i class="fas fa-file-alt text-danger"></i>
                        <?php echo htmlspecialchars($doc['titolo']); ?>
                    </h5>
                    
                    <?php if($doc['descrizione']): ?>
                        <p class="card-text text-muted">
                            <?php echo nl2br(htmlspecialchars(substr($doc['descrizione'], 0, 100))); ?>
                            <?php if(strlen($doc['descrizione']) > 100) echo '...'; ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if($doc['file_path']): ?>
                        <div class="mb-2">
                            <a href="<?php echo htmlspecialchars($doc['file_path']); ?>" 
                               target="_blank" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i> Apri Documento
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <p class="card-text mt-2">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt"></i> 
                            <?php echo date('d/m/Y H:i', strtotime($doc['data_aggiunta'])); ?>
                        </small>
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="documenti.php?edit=<?php echo $doc['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Modifica
                    </a>
                    <a href="documenti.php?delete=<?php echo $doc['id']; ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Eliminare questo documento?')">
                        <i class="fas fa-trash"></i> Elimina
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-folder-open fa-5x mb-3"></i>
                <h3>Nessun documento trovato</h3>
                <p class="text-muted">Inizia ad organizzare i tuoi documenti!</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>