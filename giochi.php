<?php 
include 'includes/header.php';
checkLogin();

$db = getDB();
$user_id = $_SESSION['user_id'];
$tipologia = getTipologia();
$success = '';
$error = '';

// CRUD solo ADMIN
if($tipologia == 'admin') {
    if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        $nome = trim($_POST['nome']);
        $piattaforma = trim($_POST['piattaforma']);
        $genere = trim($_POST['genere']);
        $stato = $_POST['stato'];
        $voto = !empty($_POST['voto']) ? intval($_POST['voto']) : null;
        $preferito = isset($_POST['preferito']) ? 1 : 0;
        $immagine = trim($_POST['immagine']);
        
        if(empty($nome)) { $error = "Il nome è obbligatorio!"; }
        else {
            try {
                if($_POST['action'] == 'add') {
                    $stmt = $db->prepare("INSERT INTO giochi (user_id, nome, piattaforma, genere, stato, voto, preferito, immagine) VALUES (?,?,?,?,?,?,?,?)");
                    $stmt->execute([$user_id, $nome, $piattaforma, $genere, $stato, $voto, $preferito, $immagine]);
                    $success = "Gioco aggiunto!";
                } elseif($_POST['action'] == 'edit') {
                    $id = intval($_POST['id']);
                    $stmt = $db->prepare("UPDATE giochi SET nome=?,piattaforma=?,genere=?,stato=?,voto=?,preferito=?,immagine=? WHERE id=? AND user_id=?");
                    $stmt->execute([$nome, $piattaforma, $genere, $stato, $voto, $preferito, $immagine, $id, $user_id]);
                    $success = "Gioco modificato!";
                }
            } catch(PDOException $e) { $error = "Errore: " . $e->getMessage(); }
        }
    }
    
    if(isset($_GET['delete'])) {
        $stmt = $db->prepare("DELETE FROM giochi WHERE id = ? AND user_id = ?");
        $stmt->execute([intval($_GET['delete']), $user_id]);
        $success = "Gioco eliminato!";
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
if(isset($_GET['piattaforma']) && !empty($_GET['piattaforma'])) {
    $where .= " AND piattaforma = ?";
    $params[] = $_GET['piattaforma'];
}
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $where .= " AND nome LIKE ?";
    $params[] = '%'.$_GET['search'].'%';
}

$stmt = $db->prepare("SELECT * FROM giochi WHERE $where ORDER BY data_aggiunta DESC");
$stmt->execute($params);
$giochi = $stmt->fetchAll();

// Filtri disponibili
$stmt = $db->prepare("SELECT DISTINCT genere FROM giochi WHERE user_id = ? AND genere IS NOT NULL ORDER BY genere");
$stmt->execute([$user_id]);
$generi = $stmt->fetchAll();

$stmt = $db->prepare("SELECT DISTINCT piattaforma FROM giochi WHERE user_id = ? AND piattaforma IS NOT NULL ORDER BY piattaforma");
$stmt->execute([$user_id]);
$piattaforme = $stmt->fetchAll();

// Raggruppa per genere (Netflix)
$giochi_per_genere = [];
foreach($giochi as $g) {
    $gen = $g['genere'] ?: 'Altro';
    $giochi_per_genere[$gen][] = $g;
}

// Edit
$gioco_edit = null;
if(isset($_GET['edit']) && $tipologia == 'admin') {
    $stmt = $db->prepare("SELECT * FROM giochi WHERE id = ? AND user_id = ?");
    $stmt->execute([intval($_GET['edit']), $user_id]);
    $gioco_edit = $stmt->fetch();
}
?>

<!-- Header -->
<div class="page-header-netflix mb-4">
    <h1 class="netflix-title"><i class="fas fa-gamepad"></i> I Miei Giochi</h1>
    
    <div class="netflix-filters">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" class="form-control form-control-dark" name="search" 
                   placeholder="🔍 Cerca gioco..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            
            <select class="form-select form-select-dark" name="genere">
                <option value="">Tutti i generi</option>
                <?php foreach($generi as $g): ?>
                    <option value="<?php echo htmlspecialchars($g['genere']); ?>" <?php echo (isset($_GET['genere']) && $_GET['genere']==$g['genere']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($g['genere']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <select class="form-select form-select-dark" name="piattaforma">
                <option value="">Tutte le piattaforme</option>
                <?php foreach($piattaforme as $p): ?>
                    <option value="<?php echo htmlspecialchars($p['piattaforma']); ?>" <?php echo (isset($_GET['piattaforma']) && $_GET['piattaforma']==$p['piattaforma']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($p['piattaforma']); ?></option>
                <?php endforeach; ?>
            </select>
            
            <select class="form-select form-select-dark" name="stato">
                <option value="">Tutti</option>
                <option value="da_giocare" <?php echo (isset($_GET['stato']) && $_GET['stato']=='da_giocare') ? 'selected' : ''; ?>>Da giocare</option>
                <option value="in_corso" <?php echo (isset($_GET['stato']) && $_GET['stato']=='in_corso') ? 'selected' : ''; ?>>In corso</option>
                <option value="completato" <?php echo (isset($_GET['stato']) && $_GET['stato']=='completato') ? 'selected' : ''; ?>>Completato</option>
            </select>
            
            <button type="submit" class="btn btn-danger"><i class="fas fa-filter"></i></button>
            <a href="giochi.php" class="btn btn-outline-light"><i class="fas fa-redo"></i></a>
        </form>
    </div>
</div>

<?php if($success): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i> <?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- LISTA Netflix -->
<?php if(count($giochi_per_genere) > 0): ?>
    <?php foreach($giochi_per_genere as $genere => $lista_giochi): ?>
        <div class="netflix-row mb-4">
            <h3 class="netflix-row-title">
                <i class="fas fa-dice-d20"></i> <?php echo htmlspecialchars($genere); ?>
                <span class="badge bg-danger ms-2"><?php echo count($lista_giochi); ?></span>
            </h3>
            <div class="netflix-slider">
                <?php foreach($lista_giochi as $gioco): ?>
                    <div class="netflix-card">
                        <div class="netflix-card-img">
                            <?php if($gioco['immagine']): ?>
                                <img src="<?php echo htmlspecialchars($gioco['immagine']); ?>" alt="<?php echo htmlspecialchars($gioco['nome']); ?>">
                            <?php else: ?>
                                <div class="netflix-card-placeholder">
                                    <i class="fas fa-gamepad fa-3x"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="netflix-card-overlay">
                                <h5><?php echo htmlspecialchars($gioco['nome']); ?></h5>
                                
                                <div class="netflix-card-meta">
                                    <span class="badge badge-stato-<?php echo $gioco['stato']; ?>">
                                        <?php 
                                        echo $gioco['stato']=='completato' ? '🏆 Completato' : 
                                             ($gioco['stato']=='in_corso' ? '🎮 In corso' : '⏳ Da giocare'); 
                                        ?>
                                    </span>
                                    
                                    <?php if($gioco['piattaforma']): ?>
                                        <span class="badge bg-dark"><?php echo htmlspecialchars($gioco['piattaforma']); ?></span>
                                    <?php endif; ?>
                                    
                                    <?php if($gioco['voto']): ?>
                                        <div class="netflix-stars">
                                            <?php for($i=1;$i<=5;$i++): ?>
                                                <i class="fas fa-star <?php echo $i<=$gioco['voto'] ? 'text-warning' : 'text-muted'; ?>"></i>
                                            <?php endfor; ?>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if($gioco['preferito']): ?>
                                        <span class="preferito-badge"><i class="fas fa-heart"></i></span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if($tipologia == 'admin'): ?>
                                <div class="netflix-card-actions mt-2">
                                    <a href="giochi.php?edit=<?php echo $gioco['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="giochi.php?delete=<?php echo $gioco['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminare?')"><i class="fas fa-trash"></i></a>
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
        <i class="fas fa-gamepad fa-5x mb-3"></i>
        <h3>Nessun gioco trovato</h3>
        <p class="text-muted">La collezione è vuota</p>
    </div>
<?php endif; ?>

<!-- FORM ADMIN -->
<?php if($tipologia == 'admin'): ?>
<div class="card card-premium mt-5" id="form-gioco">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> <?php echo $gioco_edit ? 'Modifica Gioco' : 'Aggiungi Gioco'; ?></h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="<?php echo $gioco_edit ? 'edit' : 'add'; ?>">
            <?php if($gioco_edit): ?><input type="hidden" name="id" value="<?php echo $gioco_edit['id']; ?>"><?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nome Gioco *</label>
                    <input type="text" class="form-control" name="nome" value="<?php echo $gioco_edit ? htmlspecialchars($gioco_edit['nome']) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Piattaforma</label>
                    <input type="text" class="form-control" name="piattaforma" value="<?php echo $gioco_edit ? htmlspecialchars($gioco_edit['piattaforma']) : ''; ?>" placeholder="PS5, PC, Xbox, Switch...">
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Genere</label>
                    <input type="text" class="form-control" name="genere" value="<?php echo $gioco_edit ? htmlspecialchars($gioco_edit['genere']) : ''; ?>" placeholder="Action, RPG, FPS...">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stato</label>
                    <select class="form-select" name="stato">
                        <option value="da_giocare" <?php echo ($gioco_edit && $gioco_edit['stato']=='da_giocare') ? 'selected' : ''; ?>>Da giocare</option>
                        <option value="in_corso" <?php echo ($gioco_edit && $gioco_edit['stato']=='in_corso') ? 'selected' : ''; ?>>In corso</option>
                        <option value="completato" <?php echo ($gioco_edit && $gioco_edit['stato']=='completato') ? 'selected' : ''; ?>>Completato</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Voto (1-5)</label>
                    <input type="number" class="form-control" name="voto" min="1" max="5" value="<?php echo $gioco_edit ? $gioco_edit['voto'] : ''; ?>">
                </div>
            </div>
            <div class="row">
                <div class="col-md-8 mb-3">
                    <label class="form-label">URL Immagine</label>
                    <input type="url" class="form-control" name="immagine" value="<?php echo $gioco_edit ? htmlspecialchars($gioco_edit['immagine']) : ''; ?>" placeholder="https://...">
                </div>
                <div class="col-md-4 mb-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="preferito" <?php echo ($gioco_edit && $gioco_edit['preferito']) ? 'checked' : ''; ?>>
                        <label class="form-check-label"><i class="fas fa-heart text-danger"></i> Preferito</label>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $gioco_edit ? 'Salva' : 'Aggiungi'; ?></button>
            <?php if($gioco_edit): ?><a href="giochi.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annulla</a><?php endif; ?>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>