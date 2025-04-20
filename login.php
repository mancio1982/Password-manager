<?php
// Filename: login.php
// Pagina di login
require_once 'includes/header.php';
require_once 'includes/db.php';

redirectIfAuthenticated();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validazione
    if (empty($username)) {
        $errors[] = "Inserisci il tuo nome utente o email";
    }

    if (empty($password)) {
        $errors[] = "Inserisci la tua password";
    }

    // Se non ci sono errori, tenta il login
    if (empty($errors)) {
        // Controlla se l'utente esiste
        $user = fetchOne(
            "SELECT id, username, password FROM users WHERE username = ? OR email = ?",
            [$username, $username]
        );

        if ($user && password_verify($password, $user['password'])) {
            // Login riuscito, imposta la sessione
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];

            setFlashMessage('success', 'Login effettuato con successo!');
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = "Nome utente/email o password non validi";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="text-center mb-4">Login</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome utente o email</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($username) ? $username : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Accedi</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p>Non hai un account? <a href="register.php">Registrati</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
