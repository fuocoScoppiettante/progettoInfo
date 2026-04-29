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
        $titolo = trim($_POST['titolo']);
        $tipo = $_POST['tipo'];
        $link = trim($_POST['link']);
        $categoria = trim($_POST['categoria']);
        $preferito = isset($_POST['preferito']) ? 1 : 0;
        
        if(empty($titolo)) { $error = "Il titolo è obbligatorio!"; }
        else {
            try {
                if($_POST['action'] == 'add') {
                    $stmt = $db->prepare("INSERT INTO multimedia (user_id, titolo, tipo, link, categoria, preferito) VALUES (?,?,?,?,?,?)");
                    $stmt->execute([$user_id, $titolo, $tipo, $link, $categoria, $preferito]);
                    $success = "Contenuto aggiunto!";
                } elseif($_POST['action'] == 'edit') {
                    $id = intval($_POST['id']);
                    $stmt = $db->prepare("UPDATE multimedia SET titolo=?, tipo=?, link=?, categoria=?, preferito=? WHERE id=? AND user_id=?");
                    $stmt->execute([$titolo, $tipo, $link, $categoria, $preferito, $id, $user_id]);
                    $success = "Contenuto modificato!";
                }
            } catch(PDOException $e) { $error = "Errore: " . $e->getMessage(); }
        }
    }
    
    if(isset($_GET['delete'])) {
        $stmt = $db->prepare("DELETE FROM multimedia WHERE id = ? AND user_id = ?");
        $stmt->execute([intval($_GET['delete']), $user_id]);
        $success = "Contenuto eliminato!";
    }
}

// Filtri
$where = "user_id = ?";
$params = [$user_id];

if(isset($_GET['tipo']) && !empty($_GET['tipo'])) {
    $where .= " AND tipo = ?";
    $params[] = $_GET['tipo'];
}
if(isset($_GET['categoria']) && !empty($_GET['categoria'])) {
    $where .= " AND categoria = ?";
    $params[] = $_GET['categoria'];
}
if(isset($_GET['search']) && !empty($_GET['search'])) {
    $where .= " AND titolo LIKE ?";
    $params[] = '%'.$_GET['search'].'%';
}

$stmt = $db->prepare("SELECT * FROM multimedia WHERE $where ORDER BY data_aggiunta DESC");
$stmt->execute($params);
$contenuti = $stmt->fetchAll();

// Categorie disponibili
$stmt = $db->prepare("SELECT DISTINCT categoria FROM multimedia WHERE user_id = ? AND categoria IS NOT NULL ORDER BY categoria");
$stmt->execute([$user_id]);
$categorie = $stmt->fetchAll();

// Raggruppa per categoria (stile Netflix)
$contenuti_per_cat = [];
foreach($contenuti as $c) {
    $cat = $c['categoria'] ?: 'Altro';
    $contenuti_per_cat[$cat][] = $c;
}

// Edit
$contenuto_edit = null;
if(isset($_GET['edit']) && $tipologia == 'admin') {
    $stmt = $db->prepare("SELECT * FROM multimedia WHERE id = ? AND user_id = ?");
    $stmt->execute([intval($_GET['edit']), $user_id]);
    $contenuto_edit = $stmt->fetch();
}
?>

<!-- Header -->
<div class="page-header-netflix mb-4">
    <h1 class="netflix-title"><i class="fas fa-photo-video"></i> Multimedia</h1>
    
    <div class="netflix-filters">
        <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
            <input type="text" class="form-control form-control-dark" name="search" 
                   placeholder="🔍 Cerca..." 
                   value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            
            <select class="form-select form-select-dark" name="tipo">
                <option value="">Tutti i tipi</option>
                <option value="video" <?php echo (isset($_GET['tipo']) && $_GET['tipo']=='video') ? 'selected' : ''; ?>>Video</option>
                <option value="audio" <?php echo (isset($_GET['tipo']) && $_GET['tipo']=='audio') ? 'selected' : ''; ?>>Audio</option>
                <option value="immagine" <?php echo (isset($_GET['tipo']) && $_GET['tipo']=='immagine') ? 'selected' : ''; ?>>Immagine</option>
                <option value="podcast" <?php echo (isset($_GET['tipo']) && $_GET['tipo']=='podcast') ? 'selected' : ''; ?>>Podcast</option>
                <option value="altro" <?php echo (isset($_GET['tipo']) && $_GET['tipo']=='altro') ? 'selected' : ''; ?>>Altro</option>
            </select>
            
            <select class="form-select form-select-dark" name="categoria">
                <option value="">Tutte le categorie</option>
                <?php foreach($categorie as $cat): ?>
                    <option value="<?php echo htmlspecialchars($cat['categoria']); ?>" 
                            <?php echo (isset($_GET['categoria']) && $_GET['categoria']==$cat['categoria']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['categoria']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="btn btn-danger"><i class="fas fa-filter"></i></button>
            <a href="multimedia.php" class="btn btn-outline-light"><i class="fas fa-redo"></i></a>
        </form>
    </div>
</div>

<?php if($success): ?>
    <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle"></i> <?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-danger alert-dismissible fade show"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>

<!-- LISTA - Stile Netflix per categoria -->
<?php if(count($contenuti_per_cat) > 0): ?>
    <?php foreach($contenuti_per_cat as $cat => $items): ?>
        <div class="netflix-row mb-4">
            <h3 class="netflix-row-title">
                <i class="fas fa-folder"></i> <?php echo htmlspecialchars($cat); ?>
                <span class="badge bg-danger ms-2"><?php echo count($items); ?></span>
            </h3>
            <div class="netflix-slider">
                <?php foreach($items as $item): ?>
                    <?php
                    $icon = 'file'; $color = '#6c757d';
                    switch($item['tipo']) {
                        case 'video': $icon = 'video'; $color = '#dc143c'; break;
                        case 'audio': $icon = 'music'; $color = '#28a745'; break;
                        case 'immagine': $icon = 'image'; $color = '#17a2b8'; break;
                        case 'podcast': $icon = 'podcast'; $color = '#ff6b6b'; break;
                    }
                    ?>
                    <div class="netflix-card">
                        <div class="netflix-card-img">
                            <div class="netflix-card-placeholder" style="background: linear-gradient(135deg, <?php echo $color; ?>, #1a1a1a);">
                                <i class="fas fa-<?php echo $icon; ?> fa-3x"></i>
                            </div>
                            
                            <div class="netflix-card-overlay">
                                <h5><?php echo htmlspecialchars($item['titolo']); ?></h5>
                                <p class="mb-1">
                                    <span class="badge" style="background: <?php echo $color; ?>">
                                        <i class="fas fa-<?php echo $icon; ?>"></i> <?php echo ucfirst($item['tipo']); ?>
                                    </span>
                                </p>
                                
                                <?php if($item['preferito']): ?>
                                    <span class="preferito-badge"><i class="fas fa-heart"></i></span>
                                <?php endif; ?>
                                
                                <?php if($item['link']): ?>
                                    <a href="<?php echo htmlspecialchars($item['link']); ?>" target="_blank" class="btn btn-sm btn-outline-light mt-2">
                                        <i class="fas fa-external-link-alt"></i> Apri
                                    </a>
                                <?php endif; ?>
                                
                                <?php if($tipologia == 'admin'): ?>
                                <div class="netflix-card-actions mt-2">
                                    <a href="multimedia.php?edit=<?php echo $item['id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="multimedia.php?delete=<?php echo $item['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Eliminare?')"><i class="fas fa-trash"></i></a>
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
        <i class="fas fa-photo-video fa-5x mb-3"></i>
        <h3>Nessun contenuto trovato</h3>
        <p class="text-muted">La collezione multimedia è vuota</p>
    </div>
<?php endif; ?>

<!-- FORM ADMIN -->
<?php if($tipologia == 'admin'): ?>
<div class="card card-premium mt-5" id="form-multimedia">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-plus-circle"></i> <?php echo $contenuto_edit ? 'Modifica' : 'Aggiungi'; ?> Contenuto</h5>
    </div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="<?php echo $contenuto_edit ? 'edit' : 'add'; ?>">
            <?php if($contenuto_edit): ?><input type="hidden" name="id" value="<?php echo $contenuto_edit['id']; ?>"><?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Titolo *</label>
                    <input type="text" class="form-control" name="titolo" value="<?php echo $contenuto_edit ? htmlspecialchars($contenuto_edit['titolo']) : ''; ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tipo</label>
                    <select class="form-select" name="tipo">
                        <option value="video" <?php echo ($contenuto_edit && $contenuto_edit['tipo']=='video') ? 'selected' : ''; ?>>🎬 Video</option>
                        <option value="audio" <?php echo ($contenuto_edit && $contenuto_edit['tipo']=='audio') ? 'selected' : ''; ?>>🎵 Audio</option>
                        <option value="immagine" <?php echo ($contenuto_edit && $contenuto_edit['tipo']=='immagine') ? 'selected' : ''; ?>>🖼️ Immagine</option>
                        <option value="podcast" <?php echo ($contenuto_edit && $contenuto_edit['tipo']=='podcast') ? 'selected' : ''; ?>>🎙️ Podcast</option>
                        <option value="altro" <?php echo ($contenuto_edit && $contenuto_edit['tipo']=='altro') ? 'selected' : ''; ?>>📌 Altro</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Link/URL</label>
                    <input type="url" class="form-control" name="link" value="<?php echo $contenuto_edit ? htmlspecialchars($contenuto_edit['link']) : ''; ?>" placeholder="https://...">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Categoria</label>
                    <input type="text" class="form-control" name="categoria" value="<?php echo $contenuto_edit ? htmlspecialchars($contenuto_edit['categoria']) : ''; ?>" placeholder="Programmazione, Musica...">
                </div>
                <div class="col-md-2 mb-3 d-flex align-items-end">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="preferito" <?php echo ($contenuto_edit && $contenuto_edit['preferito']) ? 'checked' : ''; ?>>
                        <label class="form-check-label"><i class="fas fa-heart text-danger"></i></label>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> <?php echo $contenuto_edit ? 'Salva' : 'Aggiungi'; ?></button>
            <?php if($contenuto_edit): ?><a href="multimedia.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annulla</a><?php endif; ?>
        </form>
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>