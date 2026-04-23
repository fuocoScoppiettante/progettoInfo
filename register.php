<?php
require_once 'config/database.php';
if(isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit(); }

$error = '';
$success = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    
    if(empty($username) || empty($email) || empty($password)) {
        $error = "Tutti i campi sono obbligatori!";
    } elseif($password !== $confirm) {
        $error = "Le password non corrispondono!";
    } elseif(strlen($password) < 6) {
        $error = "Password minimo 6 caratteri!";
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT id FROM utenti WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if($stmt->rowCount() > 0) {
            $error = "Username o email già esistenti!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO utenti (username, email, password, tipologia) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$username, $email, $hash]);
            $success = "Registrazione completata! Ora puoi accedere.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - MyBackpack</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="auth-body">

<div class="auth-container">
    <div class="auth-bg"></div>
    
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-logo">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h2>REGISTRATI</h2>
            <p>Crea il tuo account</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div>
            <a href="login.php" class="btn btn-auth w-100"><i class="fas fa-sign-in-alt"></i> VAI AL LOGIN</a>
        <?php else: ?>
        
        <form method="POST" class="auth-form">
            <div class="form-floating-custom">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            <div class="form-floating-custom">
                <i class="fas fa-envelope"></i>
                <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="form-floating-custom">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="form-floating-custom">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" name="confirm_password" placeholder="Conferma Password" required>
            </div>
            
            <button type="submit" class="btn btn-auth">
                <i class="fas fa-user-plus"></i> REGISTRATI
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Hai già un account? <a href="login.php">Accedi</a></p>
            <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Torna alla Home</a>
        </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>