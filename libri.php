<?php 
include 'includes/header.php';
checkLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];
$tipologia = getTipologia();
$success = '';
$error = '';

// CRUD solo per ADMIN
if($tipologia == 'admin') {
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        $titolo = trim($_POST['titolo']);
        $autore = trim($_POST['autore']);
        $genere = trim($_POST['genere']);
        $stato = $_POST['stato'];
        $voto = !empty($_POST['voto']) ? intval($_POST['voto']) : null;
        $preferito = isset($_POST['preferito']) ? 1 : 0;
        $immagine = trim($_POST['immagine']);
        
        if(empty($titolo)) { $error = "Il titolo è obbligatorio!"; }
        else {
            try {
                if($_POST['action'] == 'add') {
                    $stmt = $db->prepare("INSERT INTO libri (user_id, titolo, autore, genere, stato, voto, preferito, immagine) VALUES (?,?,?,?,?,?,?,?)");
                    $stmt->execute([$user_id, $titolo, $autore, $genere, $stato, $voto, $preferito, $immagine]);
                    $success = "Libro aggiunto!";
                } elseif($_POST['action'] == 'edit') {
                    $id = intval($_POST['id']);
                    $stmt = $db->prepare("UPDATE libri SET titolo=?,autore=?,genere=?,stato=?,voto=?,preferito=?,immagine=? WHERE id=? AND user_id=?");
                    $stmt->execute([$titolo, $autore, $genere, $stato, $voto, $preferito, $immagine, $id, $user_id]);
                    $success = "Libro modificato!";
                }
            } catch(PDOException $e) { $error = "Errore: " . $e->getMessage(); }
        }
    }
    
    if(isset($_GET['delete'])) {
        $stmt = $db->prepare("DELETE FROM libri WHERE id = ? AND user_id = ?");
        $stmt->execute([intval($_GET['delete']), $user_id]);
        $success = "Libro eliminato!";
    }
}

// Filtri
$where = "user_id = ?";
$params = [$user_id];

if(isset($_GET['genere']) && !empty($_GET['genere'])) {
    $where .= " AND genere = ?";
    $params[] = $_GET['genere'];
}
if(isset($_GET['stato']) && !empty($_GET['stato'])) {
    $where .= " AND stato = ?";
    $params[] = $_GET['stato'];
}
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $where .= " AND (titolo LIKE ? OR autore LIKE ?)";
    $s = '%'.$_GET['search'].'%';
    $params[] = $s; $params[] = $s;
}

$stmt = $db->prepare("SELECT * FROM libri WHERE $where ORDER BY data_aggiunta DESC");
$stmt->execute($params);
$libri = $stmt->fetchAll();

// Generi disponibili
$stmt = $db->prepare("SELECT DISTINCT genere FROM libri WHERE user_id = ? AND genere IS NOT NULL ORDER BY genere");
$stmt->execute([$user_id]);
$generi = $stmt->fetchAll();

// Raggruppa per genere per la vista Netflix
$libri_per_genere = [];
foreach($libri as $l) {
    $g = $l['genere'] ?: 'Altro';
    $libri_per_genere[$g][] = $l;
}

// Edit
$libro_edit = null;
if(isset($_GET['edit']) && $tipologia == 'admin') {
    $stmt = $db->prepare("SELECT * FROM libri WHERE id = ? AND user_id = ?");
    $stmt->execute([intval($_GET['edit']), $user_id]);
    $libro_edit = $stmt->fetch();
}
?>

<!-- Header Pagina -->
<div class="page-header-netflix mb-4">
    <h1 class="netflix-title"><i class="fas fa-book"></i> La Mia Libreria</h1>
    
    <!-- Filtri -->
    <div class="netflix-filters">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" class="form-control form-control-dark" name="search" 
                   placeholder="🔍 Cerca titolo o autore..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            
            <select class="form-select form-select-dark" name="genere">
                <option value="">Tutti i generi</option>
                <?php foreach($generi as $g): ?>
                    <option value="<?php echo htmlspecialchars($g['genere']); ?>" 
                            <?php echo (isset($_GET['genere']) && $_GET['genere'] == $g['genere']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($g['genere']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <select class="form-select form-select-dark" name="stato">
                <option value="">Tutti gli stati</option>
                <option value="da_leggere" <?php echo (isset($_GET['stato']) && $_GET['stato']=='da_leggere') ? 'selected' : ''; ?>>Da leggere</option>
                <option value="in_lettura" <?php echo (isset($_GET['stato']) && $_GET['stato']=='in_lettura') ? 'selected' : ''; ?>>In lettura</option>
                <option value="completato" <?php echo (isset($_GET['stato']) && $_GET['stato']=='completato') ? 'selected' : ''; ?>>Completato</option>
            </select>
            
            <button type="submit" class="btn btn-danger"><i class="fas fa-filter"></i></button>
            <a href="libri.php" class="btn btn-outline-light"><i class="fas fa-redo"></i></a>
        </form>
    </div>
</div>

<?php if($success): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i> <?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- LISTA NETFLIX - Righe per Genere -->
<?php if(count($libri_per_genere) > 0): ?>
    <?php foreach($libri_per_genere as $genere => $libri_genere): ?>
        <div class="netflix-row mb-4">
            <h3 class="netflix-row-title">
                <i class="fas fa-bookmark"></i> <?php echo htmlspecialchars($genere); ?>
                <span class="badge bg-danger ms-2"><?php echo count($libri_genere); ?></span>
            </h3>
            <div class="netflix-slider">
                <?php foreach($libri_genere as $libro): ?>
                    <div class="netflix-card">
                        <div class="netflix-card-img">
                            <?php if($libro['immagine']): ?>
                                <img src="<?php echo htmlspecialchars($libro['immagine']); ?>" alt="<?php echo htmlspecialchars($libro['titolo']); ?>">
                            <?php else: ?>
                                <div class="netflix-card-placeholder">
                                    <i class="fas fa-book fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Overlay Info -->
                            <div class="netflix-card-overlay">
                                <h5><?php echo htmlspecialchars($libro['titolo']); ?></h5>
                                <p class="mb-1"><?php echo htmlspecialchars($libro['autore']); ?></p>
                                
                                <div class="netflix-card-meta">
                                    <span class="badge badge-stato-<?php echo $libro['stato']; ?>">
                                        <?php 
                                        echo $libro['stato'] == 'completato' ? '✅ Completato' : 
                                             ($libro['stato'] == 'in_lettura' ? '📖 In lettura' : '📚 Da leggere'); 
                                        ?>
                                    </span>
                                    
                                    <?php if($libro['voto']): ?>
                                        <div class="netflix-stars">
                                            <?php for($i=1;$i<=5;$i++): ?>
                                                <i class="fas fa-star <?php echo $i<=$libro['voto'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($libro['preferito']): ?>
                                        <span class="preferito-badge"><i class="fas fa-heart"></i></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($tipologia == 'admin'): ?>
                                <div class="netflix-card-actions mt-2">
                                    <a href="libri.php?edit=<?php echo $libro['id']; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="libri.php?delete=<?php echo $libro['id']; ?>" class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Eliminare questo libro?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-book-open fa-5x mb-3"></i>
        <h3>Nessun libro trovato</h3>
        <p class="text-muted">La libreria è vuota</p>
    </div>
<?php endif; ?>

<!-- FORM ADMIN - Sotto la lista -->
<?php if($tipologia == 'admin'): ?>
<div class="card card-premium mt-5" id="form-libro">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> <?php echo $libro_edit ? 'Modifica Libro' : 'Aggiungi Libro'; ?></h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="<?php echo $libro_edit ? 'edit' : 'add'; ?>">
            <?php if($libro_edit): ?><input type="hidden" name="id" value="<?php echo $libro_edit['id']; ?>"><?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Titolo *</label>
                    <input type="text" class="form-control" name="titolo" value="<?php echo $libro_edit ? htmlspecialchars($libro_edit['titolo']) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Autore</label>
                    <input type="text" class="form-control" name="autore" value="<?php echo $libro_edit ? htmlspecialchars($libro_edit['autore']) : ''; ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Genere</label>
                    <input type="text" class="form-control" name="genere" value="<?php echo $libro_edit ? htmlspecialchars($libro_edit['genere']) : ''; ?>" placeholder="Fantasy, Giallo...">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stato</label>
                    <select class="form-select" name="stato">
                        <option value="da_leggere" <?php echo ($libro_edit && $libro_edit['stato']=='da_leggere') ? 'selected' : ''; ?>>Da leggere</option>
                        <option value="in_lettura" <?php echo ($libro_edit && $libro_edit['stato']=='in_lettura') ? 'selected' : ''; ?>>In lettura</option>
                        <option value="completato" <?php echo ($libro_edit && $libro_edit['stato']=='completato') ? 'selected' : ''; ?>>Completato</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Voto (1-5)</label>
                    <input type="number" class="form-control" name="voto" min="1" max="5" value="<?php echo $libro_edit ? $libro_edit['voto'] : ''; ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">URL Immagine Copertina</label>
                    <input type="url" class="form-control" name="immagine" value="<?php echo $libro_edit ? htmlspecialchars($libro_edit['immagine']) : ''; ?>" placeholder="https://...">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="preferito" id="preferito" <?php echo ($libro_edit && $libro_edit['preferito']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="preferito"><i class="fas fa-heart text-danger"></i> Preferito</label>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $libro_edit ? 'Salva' : 'Aggiungi'; ?></button>
            <?php if($libro_edit): ?><a href="libri.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annulla</a><?php endif; ?>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>