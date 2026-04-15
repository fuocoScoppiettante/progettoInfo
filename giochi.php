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
        $nome = trim($_POST['nome']);
        $piattaforma = trim($_POST['piattaforma']);
        $genere = trim($_POST['genere']);
        $stato = $_POST['stato'];
        $voto = !empty($_POST['voto']) ? intval($_POST['voto']) : null;
        $preferito = isset($_POST['preferito']) ? 1 : 0;
        
        if(empty($nome)) {
            $error = "Il nome del gioco è obbligatorio!";
        } else {
            try {
                if($_POST['action'] == 'add') {
                    $stmt = $db->prepare("INSERT INTO giochi (user_id, nome, piattaforma, genere, stato, voto, preferito) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$user_id, $nome, $piattaforma, $genere, $stato, $voto, $preferito]);
                    $success = "Gioco aggiunto con successo!";
                } else {
                    $id = intval($_POST['id']);
                    $stmt = $db->prepare("UPDATE giochi SET nome=?, piattaforma=?, genere=?, stato=?, voto=?, preferito=? WHERE id=? AND user_id=?");
                    $stmt->execute([$nome, $piattaforma, $genere, $stato, $voto, $preferito, $id, $user_id]);
                    $success = "Gioco modificato con successo!";
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
        $stmt = $db->prepare("DELETE FROM giochi WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $user_id]);
        $success = "Gioco eliminato!";
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

if(isset($_GET['piattaforma']) && !empty($_GET['piattaforma'])) {
    $where .= " AND piattaforma = ?";
    $params[] = $_GET['piattaforma'];
}

if(isset($_GET['preferiti']) && $_GET['preferiti'] == '1') {
    $where .= " AND preferito = 1";
}

if(isset($_GET['search']) && !empty($_GET['search'])) {
    $where .= " AND nome LIKE ?";
    $search = '%' . $_GET['search'] . '%';
    $params[] = $search;
}

// Recupera giochi
$stmt = $db->prepare("SELECT * FROM giochi WHERE $where ORDER BY data_aggiunta DESC");
$stmt->execute($params);
$giochi = $stmt->fetchAll();

// Recupera piattaforme per filtro
$stmt = $db->prepare("SELECT DISTINCT piattaforma FROM giochi WHERE user_id = ? AND piattaforma IS NOT NULL ORDER BY piattaforma");
$stmt->execute([$user_id]);
$piattaforme = $stmt->fetchAll();

// Se è richiesta la modifica
$gioco_edit = null;
if(isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $stmt = $db->prepare("SELECT * FROM giochi WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $user_id]);
    $gioco_edit = $stmt->fetch();
}
?>

<div class="page-header mb-4">
    <h2 class="display-5 fw-bold">
        <i class="fas fa-gamepad text-gradient"></i> I Miei Giochi
    </h2>
    <p class="text-muted">Gestisci la tua collezione di videogiochi</p>
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
            <i class="fas fa-plus-circle"></i> <?php echo $gioco_edit ? 'Modifica Gioco' : 'Aggiungi Nuovo Gioco'; ?>
        </h5>
    </div>
    <div class="card-body">
        <form method="POST" action="giochi.php">
            <input type="hidden" name="action" value="<?php echo $gioco_edit ? 'edit' : 'add'; ?>">
            <?php if($gioco_edit): ?>
                <input type="hidden" name="id" value="<?php echo $gioco_edit['id']; ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-tag"></i> Nome Gioco *
                    </label>
                    <input type="text" class="form-control" name="nome" 
                           value="<?php echo $gioco_edit ? htmlspecialchars($gioco_edit['nome']) : ''; ?>" 
                           placeholder="Es: The Last of Us" required>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        <i class="fas fa-desktop"></i> Piattaforma
                    </label>
                    <input type="text" class="form-control" name="piattaforma" 
                           value="<?php echo $gioco_edit ? htmlspecialchars($gioco_edit['piattaforma']) : ''; ?>" 
                           placeholder="Es: PlayStation 5, Xbox, PC">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        <i class="fas fa-dice-d20"></i> Genere
                    </label>
                    <input type="text" class="form-control" name="genere" 
                           value="<?php echo $gioco_edit ? htmlspecialchars($gioco_edit['genere']) : ''; ?>" 
                           placeholder="Es: Action, RPG, FPS">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        <i class="fas fa-tasks"></i> Stato
                    </label>
                    <select class="form-select" name="stato">
                        <option value="da_giocare" <?php echo ($gioco_edit && $gioco_edit['stato'] == 'da_giocare') ? 'selected' : ''; ?>>Da giocare</option>
                        <option value="in_corso" <?php echo ($gioco_edit && $gioco_edit['stato'] == 'in_corso') ? 'selected' : ''; ?>>In corso</option>
                        <option value="completato" <?php echo ($gioco_edit && $gioco_edit['stato'] == 'completato') ? 'selected' : ''; ?>>Completato</option>
                    </select>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">
                        <i class="fas fa-star"></i> Voto (1-5)
                    </label>
                    <input type="number" class="form-control" name="voto" min="1" max="5" 
                           value="<?php echo $gioco_edit ? $gioco_edit['voto'] : ''; ?>" 
                           placeholder="Valutazione">
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="preferito" id="preferito" 
                           <?php echo ($gioco_edit && $gioco_edit['preferito']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="preferito">
                        <i class="fas fa-heart text-danger"></i> Aggiungi ai preferiti
                    </label>
                </div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?php echo $gioco_edit ? 'Salva Modifiche' : 'Aggiungi Gioco'; ?>
                </button>
                <?php if($gioco_edit): ?>
                    <a href="giochi.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annulla
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Filtri e Ricerca -->
<div class="card mb-4 card-filters">
    <div class="card-body">
        <form method="GET" action="giochi.php" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label"><i class="fas fa-search"></i> Ricerca</label>
                <input type="text" class="form-control" name="search" 
                       placeholder="Cerca per nome..." 
                       value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label"><i class="fas fa-filter"></i> Stato</label>
                <select class="form-select" name="stato">
                    <option value="">Tutti</option>
                    <option value="da_giocare" <?php echo (isset($_GET['stato']) && $_GET['stato'] == 'da_giocare') ? 'selected' : ''; ?>>Da giocare</option>
                    <option value="in_corso" <?php echo (isset($_GET['stato']) && $_GET['stato'] == 'in_corso') ? 'selected' : ''; ?>>In corso</option>
                    <option value="completato" <?php echo (isset($_GET['stato']) && $_GET['stato'] == 'completato') ? 'selected' : ''; ?>>Completato</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label"><i class="fas fa-desktop"></i> Piattaforma</label>
                <select class="form-select" name="piattaforma">
                    <option value="">Tutte</option>
                    <?php foreach($piattaforme as $p): ?>
                        <option value="<?php echo htmlspecialchars($p['piattaforma']); ?>" 
                                <?php echo (isset($_GET['piattaforma']) && $_GET['piattaforma'] == $p['piattaforma']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($p['piattaforma']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="preferiti" value="1" id="filtro_preferiti" 
                           <?php echo (isset($_GET['preferiti']) && $_GET['preferiti'] == '1') ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="filtro_preferiti">
                        <i class="fas fa-heart text-danger"></i> Preferiti
                    </label>
                </div>
            </div>
            
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i> Filtra
                </button>
                <a href="giochi.php" class="btn btn-secondary w-100 mt-2">
                    <i class="fas fa-redo"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Statistiche Rapide -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-card-danger">
            <div class="stat-icon">
                <i class="fas fa-gamepad"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo count($giochi); ?></h3>
                <p>Totale Giochi</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card-success">
            <div class="stat-icon">
                <i class="fas fa-trophy"></i>
            </div>
            <div class="stat-content">
                <?php
                $completati = array_filter($giochi, function($g) { return $g['stato'] == 'completato'; });
                ?>
                <h3><?php echo count($completati); ?></h3>
                <p>Completati</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card stat-card-warning">
            <div class="stat-icon">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stat-content">
                <?php
                $in_corso = array_filter($giochi, function($g) { return $g['stato'] == 'in_corso'; });
                ?>
                <h3><?php echo count($in_corso); ?></h3>
                <p>In Corso</p>
            </div>
        </div>
    </div>
</div>

<!-- Lista Giochi -->
<div class="row">
    <?php if(count($giochi) > 0): ?>
        <?php foreach($giochi as $gioco): ?>
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card game-card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="badge badge-<?php 
                        echo $gioco['stato'] == 'completato' ? 'success' : ($gioco['stato'] == 'in_corso' ? 'warning' : 'secondary'); 
                    ?>">
                        <i class="fas fa-<?php 
                            echo $gioco['stato'] == 'completato' ? 'trophy' : ($gioco['stato'] == 'in_corso' ? 'play' : 'clock'); 
                        ?>"></i>
                        <?php 
                        echo $gioco['stato'] == 'completato' ? 'Completato' : ($gioco['stato'] == 'in_corso' ? 'In corso' : 'Da giocare'); 
                        ?>
                    </span>
                    <?php if($gioco['preferito']): ?>
                        <i class="fas fa-heart text-danger pulse-icon"></i>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h5 class="card-title fw-bold">
                        <i class="fas fa-gamepad text-danger"></i>
                        <?php echo htmlspecialchars($gioco['nome']); ?>
                    </h5>
                    
                    <?php if($gioco['piattaforma']): ?>
                        <p class="card-text mb-2">
                            <span class="badge bg-dark">
                                <i class="fas fa-desktop"></i> <?php echo htmlspecialchars($gioco['piattaforma']); ?>
                            </span>
                        </p>
                    <?php endif; ?>
                    
                    <?php if($gioco['genere']): ?>
                        <p class="card-text mb-2">
                            <span class="badge bg-info">
                                <i class="fas fa-dice-d20"></i> <?php echo htmlspecialchars($gioco['genere']); ?>
                            </span>
                        </p>
                    <?php endif; ?>
                    
                    <?php if($gioco['voto']): ?>
                        <div class="rating mb-2">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <i class="fas fa-star <?php echo $i <= $gioco['voto'] ? 'text-warning star-filled' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                        </div>
                    <?php endif; ?>
                    
                    <p class="card-text">
                        <small class="text-muted">
                            <i class="fas fa-calendar-alt"></i> 
                            Aggiunto il <?php echo date('d/m/Y', strtotime($gioco['data_aggiunta'])); ?>
                        </small>
                    </p>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="giochi.php?edit=<?php echo $gioco['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> Modifica
                    </a>
                    <a href="giochi.php?delete=<?php echo $gioco['id']; ?>" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm('Sei sicuro di voler eliminare questo gioco?')">
                        <i class="fas fa-trash"></i> Elimina
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-12">
            <div class="empty-state">
                <i class="fas fa-gamepad fa-5x mb-3"></i>
                <h3>Nessun gioco trovato</h3>
                <p class="text-muted">Inizia ad aggiungere i tuoi giochi preferiti!</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>