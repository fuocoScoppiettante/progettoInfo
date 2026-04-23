<?php
require_once 'config/database.php';
if(isset($_SESSION['user_id'])) { header('Location: dashboard.php'); exit(); }

$error = '';
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if(empty($username) || empty($password)) {
        $error = "Tutti i campi sono obbligatori!";
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT id, username, password, tipologia FROM utenti WHERE username = ?");
        $stmt->execute([$username]);
        
        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['tipologia'] = $user['tipologia'];
                header('Location: dashboard.php');
                exit();
            } else { $error = "Password errata!"; }
        } else { $error = "Username non trovato!"; }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyBackpack</title>
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
            <h2>ACCEDI</h2>
            <p>Entra nel tuo zaino digitale</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" class="auth-form">
            <div class="form-floating-custom">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>
            
            <div class="form-floating-custom">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            
            <button type="submit" class="btn btn-auth">
                <i class="fas fa-sign-in-alt"></i> ACCEDI
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Non hai un account? <a href="register.php">Registrati</a></p>
            <a href="index.php" class="back-link"><i class="fas fa-arrow-left"></i> Torna alla Home</a>
        </div>
    </div>
</div>

</body>
</html>