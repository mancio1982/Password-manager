<?php
// Filename: register.php
// Pagina di registrazione
require_once 'includes/header.php';
require_once 'includes/db.php';

redirectIfAuthenticated();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valida i dati
    $username = sanitize($_POST['username'] ?? '');
    $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Validazione
    if (empty($username)) {
        $errors[] = "Il nome utente è obbligatorio";
    } elseif (strlen($username) < 3 || strlen($username) > 50) {
        $errors[] = "Il nome utente deve essere compreso tra 3 e 50 caratteri";
    }

    if (empty($email)) {
        $errors[] = "L'email è obbligatoria";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Inserisci un indirizzo email valido";
    }

    if (empty($password)) {
        $errors[] = "La password è obbligatoria";
    } elseif (strlen($password) < 8) {
        $errors[] = "La password deve contenere almeno 8 caratteri";
    }

    if ($password !== $confirmPassword) {
        $errors[] = "Le password non corrispondono";
    }

    // Verifica che username ed email non siano già utilizzati
    if (empty($errors)) {
        $existingUser = fetchOne(
            "SELECT id FROM users WHERE username = ? OR email = ?",
            [$username, $email]
        );

        if ($existingUser) {
            $errors[] = "Username o email già in uso";
        }
    }

    // Se non ci sono errori, registra l'utente
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $userId = insert(
            "INSERT INTO users (username, email, password) VALUES (?, ?, ?)",
            [$username, $email, $hashedPassword]
        );

        if ($userId) {
            // Imposta la sessione e reindirizza alla dashboard
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;

            setFlashMessage('success', 'Registrazione completata con successo!');
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = "Si è verificato un errore durante la registrazione";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="text-center mb-4">Registrazione</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="register.php">
                    <div class="mb-3">
                        <label for="username" class="form-label">Nome utente</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?php echo isset($username) ? $username : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                        <div class="form-text">La password deve contenere almeno 8 caratteri.</div>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Conferma password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Registrati</button>
                    </div>
                </form>

                <div class="text-center mt-3">
                    <p>Hai già un account? <a href="login.php">Accedi</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
