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
        $autore = trim($_POST['autore']);
        $genere = trim($_POST['genere']);
        $stato = $_POST['stato'];
        $voto = !empty($_POST['voto']) ? intval($_POST['voto']) : null;
        $preferito = isset($_POST['preferito']) ? 1 : 0;
        $prezzo = !empty($_POST['prezzo']) ? floatval($_POST['prezzo']) : 0;
        
        if(empty($titolo)) {
            $error = "Il titolo è obbligatorio!";
        } else {
            try {
                if($_POST['action'] == 'add') {
                    $stmt = $db->prepare("INSERT INTO libri (user_id, titolo, autore, genere, stato, voto, preferito, prezzo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $titolo, $autore, $genere, $stato, $voto, $preferito, $prezzo]);
                    $success = "Libro aggiunto con successo!";
                } else {
                    $id = intval($_POST['id']);
                    $stmt = $db->prepare("UPDATE libri SET titolo=?, autore=?, genere=?, stato=?, voto=?, preferito=?, prezzo=? WHERE id=? AND user_id=?");
                    $stmt->execute([$titolo, $autore, $genere, $stato, $voto, $preferito, $prezzo, $id, $user_id]);
                    $success = "Libro modificato con successo!";
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
        $stmt = $db->prepare("DELETE FROM libri WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        $success = "Libro eliminato!";
    } catch(PDOException $e) {
        $error = "Errore nell'eliminazione: " . $e->getMessage();
    }
}

// Filtri e ricerca
$where = "user_id = ?";
$params = [$user_id];

if(isset($_GET['stato']) && !empty($_GET['stato'])) {
    $where .= " AND stato = ?";
    $params[] = $_GET['stato'];
}

if(isset($_GET['genere']) && !empty($_GET['genere'])) {
    $where .= " AND genere = ?";
    $params[] = $_GET['genere'];
}

if(isset($_GET['preferiti']) && $_GET['preferiti'] == '1') {
    $where .= " AND preferito = 1";
}

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $where .= " AND (titolo LIKE ? OR autore LIKE ?)";
    $search = '%' . $_GET['search'] . '%';
    $params[] = $search;
    $params[] = $search;
}

// Recupera libri
$stmt = $db->prepare("SELECT * FROM libri WHERE $where ORDER BY data_aggiunta DESC");
$stmt->execute($params);
$libri = $stmt->fetchAll();

// Recupera generi per filtro
$stmt = $db->prepare("SELECT DISTINCT genere FROM libri WHERE user_id = ? AND genere IS NOT NULL ORDER BY genere");
$stmt->execute([$user_id]);
$generi = $stmt->fetchAll();

// Se è richiesta la modifica, recupera i dati del libro
$libro_edit = null;
if(isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM libri WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $libro_edit = $stmt->fetch();
}
?>

<h2 class="mb-4"><i class="fas fa-book"></i> I Miei Libri</h2>

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
    <div class="card-header bg-primary text-white">
        <h5><i class="fas fa-plus"></i> <?php echo $libro_edit ? 'Modifica Libro' : 'Aggiungi Nuovo Libro'; ?></h5>
    </div>
    <div class="card-body">
        <form method="POST" action="libri.php">
            <input type="hidden" name="action" value="<?php echo $libro_edit ? 'edit' : 'add'; ?>">
            <?php if($libro_edit): ?>
                <input type="hidden" name="id" value="<?php echo $libro_edit['id']; ?>">
            <?php endif; ?>
            
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
                    <input type="text" class="form-control" name="genere" value="<?php echo $libro_edit ? htmlspecialchars($libro_edit['genere']) : ''; ?>" placeholder="es: Fantasy, Giallo...">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stato</label>
                    <select class="form-select" name="stato">
                        <option value="da_leggere" <?php echo ($libro_edit && $libro_edit['stato'] == 'da_leggere') ? 'selected' : ''; ?>>Da leggere</option>
                        <option value="in_lettura" <?php echo ($libro_edit && $libro_edit['stato'] == 'in_lettura') ? 'selected' : ''; ?>>In lettura</option>
                        <option value="completato" <?php echo ($libro_edit && $libro_edit['stato'] == 'completato') ? 'selected' : ''; ?>>Completato</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Voto (1-5)</label>
                    <input type="number" class="form-control" name="voto" min="1" max="5" value="<?php echo $libro_edit ? $libro_edit['voto'] : ''; ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Prezzo (€)</label>
                    <input type="number" step="0.01" class="form-control" name="prezzo" value="<?php echo $libro_edit ? $libro_edit['prezzo'] : ''; ?>">
                </div>
                
                <div class="col-md-6 mb-3">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" name="preferito" id="preferito" <?php echo ($libro_edit && $libro_edit['preferito']) ? 'checked' : ''; ?>>
                        <label class="form-check-label" for="preferito">
                            <i class="fas fa-star text-warning"></i> Aggiungi ai preferiti
                        </label>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> <?php echo $libro_edit ? 'Salva Modifiche' : 'Aggiungi Libro'; ?>
            </button>
            <?php if($libro_edit): ?>
                <a href="libri.php" class="btn btn-secondary">Annulla</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Filtri e Ricerca -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="libri.php" class="row g-3">
            <div class="col-md-3">
                <input type="text" class="form-control" name="search" placeholder="Cerca per titolo o autore..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            
            <div class="col-md-2">
                <select class="form-select" name="stato">
                    <option value="">Tutti gli stati</option>
                    <option value="da_leggere" <?php echo (isset($_GET['stato']) && $_GET['stato'] == 'da_leggere') ? 'selected' : ''; ?>>Da leggere</option>
                    <option value="in_lettura" <?php echo (isset($_GET['stato']) && $_GET['stato'] == 'in_lettura') ? 'selected' : ''; ?>>In lettura</option>
                    <option value="completato" <?php echo (isset($_GET['stato']) && $_GET['stato'] == 'completato') ? 'selected' : ''; ?>>Completato</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <select class="form-select" name="genere">
                    <option value="">Tutti i generi</option>
                    <?php foreach($generi as $g): ?>
                        <option value="<?php echo htmlspecialchars($g['genere']); ?>" <?php echo (isset($_GET['genere']) && $_GET['genere'] == $g['genere']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($g['genere']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="preferiti" value="1" id="filtro_preferiti" <?php echo (isset($_GET['preferiti']) && $_GET['preferiti'] == '1') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="filtro_preferiti">
                        Solo preferiti
                    </label>
                </div>
            </div>
            
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtra
                </button>
                <a href="libri.php" class="btn btn-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Lista Libri -->
<div class="row">
    <?php if(count($libri) > 0): ?>
        <?php foreach($libri as $libro): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge bg-<?php 
                        echo $libro['stato'] == 'completato' ? 'success' : ($libro['stato'] == 'in_lettura' ? 'warning' : 'secondary'); 
                    ?>">
                        <?php 
                        echo $libro['stato'] == 'completato' ? 'Completato' : ($libro['stato'] == 'in_lettura' ? 'In lettura' : 'Da leggere'); 
                        ?>
                    </span>
                    <?php if($libro['preferito']): ?>
                        <i class="fas fa-star text-warning"></i>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($libro['titolo']); ?></h5>
                    <?php if($libro['autore']): ?>
                        <p class="card-text text-muted"><i class="fas fa-user"></i> <?php echo htmlspecialchars($libro['autore']); ?></p>
                    <?php endif; ?>
                    
                    <?php if($libro['genere']): ?>
                        <p class="card-text"><span class="badge bg-info"><?php echo htmlspecialchars($libro['genere']); ?></span></p>
                    <?php endif; ?>
                    
                    <?php if($libro['voto']): ?>
                        <p class="card-text">
                            Voto: 
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $libro['voto'] ? 'text-warning' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if($libro['prezzo'] > 0): ?>
                        <p class="card-text"><strong>Prezzo:</strong> €<?php echo number_format($libro['prezzo'], 2); ?></p>
                    <?php endif; ?>
                    
                    <p class="card-text"><small class="text-muted">Aggiunto il <?php echo date('d/m/Y', strtotime($libro['data_aggiunta'])); ?></small></p>
                </div>
                <div class="card-footer">
                    <a href="libri.php?edit=<?php echo $libro['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Modifica
                    </a>
                    <a href="libri.php?delete=<?php echo $libro['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sei sicuro di voler eliminare questo libro?')">
                        <i class="fas fa-trash"></i> Elimina
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Nessun libro trovato. Inizia ad aggiungerne uno!
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>