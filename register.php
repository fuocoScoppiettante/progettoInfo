<?php 
include 'includes/header.php';

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validazione
    if(empty($username) || empty($email) || empty($password)) {
        $error = "Tutti i campi sono obbligatori!";
    } elseif($password !== $confirm_password) {
        $error = "Le password non corrispondono!";
    } elseif(strlen($password) < 6) {
        $error = "La password deve essere di almeno 6 caratteri!";
    } else {
        try {
            $db = getDB();
            
            // Verifica se username o email esistono già
            $stmt = $db->prepare("SELECT id FROM utenti WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if($stmt->rowCount() > 0) {
                $error = "Username o email già esistenti!";
            } else {
                // Inserisci nuovo utente
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("INSERT INTO utenti (username, email, password) VALUES (?, ?, ?)");
                $stmt->execute([$username, $email, $hashed_password]);
                
                $success = "Registrazione completata! Ora puoi effettuare il login.";
            }
        } catch(PDOException $e) {
            $error = "Errore durante la registrazione: " . $e->getMessage();
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4><i class="fas fa-user-plus"></i> Registrazione</h4>
            </div>
            <div class="card-body">
                <?php if($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <?php if($success): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                    <a href="login.php" class="btn btn-primary">Vai al Login</a>
                <?php else: ?>
                
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Conferma Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">Registrati</button>
                </form>
                
                <div class="mt-3 text-center">
                    <p>Hai già un account? <a href="login.php">Accedi qui</a></p>
                </div>
                
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>