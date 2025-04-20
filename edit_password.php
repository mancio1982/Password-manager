<?php
// Filename: edit_password.php
// Pagina per modificare una password esistente
require_once 'includes/header.php';
require_once 'includes/db.php';

requireAuth();

$userId = getCurrentUserId();
$passwordId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verifica che la password esista e appartenga all'utente corrente
$passwordEntry = fetchOne(
    "SELECT * FROM passwords WHERE id = ? AND user_id = ?",
    [$passwordId, $userId]
);

if (!$passwordEntry) {
    setFlashMessage('danger', 'Password non trovata o non autorizzata');
    header('Location: dashboard.php');
    exit;
}

// Decripta la password
$decryptedPassword = decrypt($passwordEntry['password'], $passwordEntry['iv']);

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Valida e sanitizza i dati
    $name = sanitize($_POST['name'] ?? '');
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $notes = sanitize($_POST['notes'] ?? '');

    // Validazione
    if (empty($name)) {
        $errors[] = "Il nome è obbligatorio";
    }

    if (empty($username)) {
        $errors[] = "Lo username è obbligatorio";
    }

    if (empty($password)) {
        $errors[] = "La password è obbligatoria";
    }

    // Se non ci sono errori, aggiorna la password
    if (empty($errors)) {
        // Cripta la nuova password solo se è cambiata
        if ($password !== $decryptedPassword) {
            $encrypted = encrypt($password);
            $ciphertext = $encrypted['ciphertext'];
            $iv = $encrypted['iv'];
        } else {
            // Mantieni la password esistente
            $ciphertext = $passwordEntry['password'];
            $iv = $passwordEntry['iv'];
        }

        $result = executeNonQuery(
            "UPDATE passwords SET name = ?, username = ?, password = ?, iv = ?, notes = ? WHERE id = ? AND user_id = ?",
            [$name, $username, $ciphertext, $iv, $notes, $passwordId, $userId]
        );

        if ($result) {
            setFlashMessage('success', 'Password aggiornata con successo!');
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = "Si è verificato un errore durante l'aggiornamento della password";
        }
    }
}
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="mb-4">Modifica Password</h2>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="POST" action="edit_password.php?id=<?php echo $passwordId; ?>">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome (sito o servizio)</label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?php echo isset($name) ? $name : sanitize($passwordEntry['name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="username" class="form-label">Username o Email</label>
                        <input type="text" class="form-control" id="username" name="username"
                               value="<?php echo isset($username) ? $username : sanitize($passwordEntry['username']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password"
                                   value="<?php echo isset($password) ? $password : $decryptedPassword; ?>" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-outline-secondary" type="button" id="generate-password">
                                <i class="bi bi-magic"></i> Genera
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">Note (opzionale)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"><?php echo isset($notes) ? $notes : sanitize($passwordEntry['notes']); ?></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Salva modifiche</button>
                        <a href="dashboard.php" class="btn btn-outline-secondary">Annulla</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle per mostrare/nascondere la password
    document.getElementById('toggle-password').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const icon = this.querySelector('i');

        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    // Generatore di password casuali
    document.getElementById('generate-password').addEventListener('click', function() {
        const length = 16;
        const charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-+=";
        let password = "";

        for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * charset.length);
            password += charset[randomIndex];
        }

        document.getElementById('password').value = password;
        document.getElementById('password').type = 'text';

        const toggleIcon = document.querySelector('#toggle-password i');
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    });
</script>

<?php require_once 'includes/footer.php'; ?>
