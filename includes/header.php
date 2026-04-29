<?php
require_once 'config/database.php';

function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}

function getTipologia() {
    if (!isset($_SESSION['user_id'])) return 'user';
    
    $db = getDB();
    $stmt = $db->prepare("SELECT tipologia FROM utenti WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $row ? $row['tipologia'] : 'user';
}

$tipologia = getTipologia();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBackpack - Zaino Digitale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php if(isset($_SESSION['user_id'])): ?>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        
        <!-- Logo al centro - SOLO TESTO, senza scudo -->
        <a class="navbar-brand mx-auto order-lg-1" href="dashboard.php">
            <span class="logo-main">MY</span><span class="logo-accent">BACKPACK</span>
        </a>
        
        <button class="navbar-toggler order-lg-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse order-lg-0" id="navbarNav">
            
            <!-- Links Sinistra: Dashboard, Libri, Multimedia, Giochi, Note -->
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-home"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'libri.php' ? 'active' : ''; ?>" href="libri.php">
                        <i class="fas fa-book"></i> Libri
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'multimedia.php' ? 'active' : ''; ?>" href="multimedia.php">
                        <i class="fas fa-photo-video"></i> Multimedia
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'giochi.php' ? 'active' : ''; ?>" href="giochi.php">
                        <i class="fas fa-gamepad"></i> Giochi
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'note.php' ? 'active' : ''; ?>" href="note.php">
                        <i class="fas fa-sticky-note"></i> Note
                    </a>
                </li>
            </ul>
            
            <!-- Links Destra: Statistiche, Utente, Logout -->
            <ul class="navbar-nav ms-auto order-lg-2">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'statistiche.php' ? 'active' : ''; ?>" href="statistiche.php">
                        <i class="fas fa-chart-bar"></i> Statistiche
                    </a>
                </li>
                <li class="nav-item d-flex align-items-center">
                    <span class="navbar-text user-badge">
                        <i class="fas fa-user-circle"></i> 
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                        <span class="badge bg-<?php echo $tipologia == 'admin' ? 'danger' : 'secondary'; ?> ms-1">
                            <?php echo strtoupper($tipologia); ?>
                        </span>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="btn btn-logout" href="logout.php" title="Esci">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<?php endif; ?>

<div class="<?php echo isset($_SESSION['user_id']) ? 'container mt-4' : ''; ?>">