<?php
require_once 'config/database.php';

// Controlla se l'utente è loggato (per pagine protette)
function checkLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyBackpack - Zaino Digitale</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <i class="fas fa-backpack"></i> MyBackpack
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <?php if(isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="libri.php"><i class="fas fa-book"></i> Libri</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="multimedia.php"><i class="fas fa-photo-video"></i> Multimedia</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="giochi.php"><i class="fas fa-gamepad"></i> Giochi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="documenti.php"><i class="fas fa-file-alt"></i> Documenti</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="note.php"><i class="fas fa-sticky-note"></i> Note</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="statistiche.php"><i class="fas fa-chart-bar"></i> Statistiche</a>
                </li>
                <?php endif; ?>
            </ul>
            <ul class="navbar-nav">
                <?php if(isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <span class="navbar-text text-white me-3">
                        <i class="fas fa-user"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </span>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-light" href="logout.php">Logout</a>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Registrati</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">