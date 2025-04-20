<?php
// Filename: dashboard.php
// Dashboard utente
require_once 'includes/header.php';
require_once 'includes/db.php';

requireAuth();

$userId = getCurrentUserId();
$passwordEntries = fetchAll(
    "SELECT id, name, username, password, iv, notes, created_at, updated_at
     FROM passwords
     WHERE user_id = ?
     ORDER BY name ASC",
    [$userId]
);
?>

<div class="row mb-4">
    <div class="col-md-8">
        <h2>Le tue password</h2>
    </div>
    <div class="col-md-4 text-end">
        <a href="add_password.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Aggiungi Password
        </a>
    </div>
    <div class="row mb-3 mt-3">
      <div class="col-md-6 mx-auto">
        <div class="input-group">
            <input type="text" id="searchInput" class="form-control" placeholder="Cerca password...">
            <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                <i class="bi bi-x-circle"></i>
            </button>
        </div>
      </div>
    </div>
</div>

<?php if (empty($passwordEntries)): ?>
    <div class="alert alert-info">
        Non hai ancora salvato nessuna password. Clicca su "Aggiungi Password" per iniziare.
    </div>
<?php else: ?>
    <!-- Visualizzazione tabella per desktop -->
    <div class="card shadow-sm d-none d-md-block">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Note</th>
                            <th>Ultima modifica</th>
                            <th>Azioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($passwordEntries as $entry): ?>
                            <tr>
                                <td><?php echo sanitize($entry['name']); ?></td>
                                <td>
                                  <div class="password-container">
                                      <input type="text" class="form-control username-field" value="<?php echo sanitize($entry['username']); ?>" readonly>
                                      <button type="button" class="btn btn-sm btn-outline-secondary copy-username" title="Copia username">
                                      <i class="bi bi-clipboard"></i>
                                      </button>
                                  </div>
                                </td>
                                <td>
                                    <div class="password-container">
                                        <input type="password" class="form-control password-field" value="<?php echo decrypt($entry['password'], $entry['iv']); ?>" readonly>
                                        <button type="button" class="btn btn-sm btn-outline-secondary toggle-password" title="Mostra/Nascondi">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary copy-password" title="Copia negli appunti">
                                            <i class="bi bi-clipboard"></i>
                                        </button>
                                    </div>
                                </td>
                                <td><?php echo sanitize($entry['notes']); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($entry['updated_at'])); ?></td>
                                <td>
                                    <a href="edit_password.php?id=<?php echo $entry['id']; ?>" class="btn btn-sm btn-outline-primary" title="Modifica">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $entry['id']; ?>)" class="btn btn-sm btn-outline-danger" title="Elimina">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Layout a carte per mobile -->
    <div class="d-md-none">
        <?php foreach ($passwordEntries as $entry): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0"><?php echo sanitize($entry['name']); ?></h5>
                    <div>
                        <a href="edit_password.php?id=<?php echo $entry['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Modifica">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <a href="javascript:void(0);" onclick="confirmDelete(<?php echo $entry['id']; ?>)" class="btn btn-sm btn-outline-danger" title="Elimina">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <label class="fw-bold mb-1 small text-muted">Username:</label>
                        <div class="password-container">
                            <input type="text" class="form-control form-control-sm username-field" value="<?php echo sanitize($entry['username']); ?>" readonly>
                            <button type="button" class="btn btn-sm btn-outline-secondary copy-username" title="Copia username">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="fw-bold mb-1 small text-muted">Password:</label>
                        <div class="password-container">
                            <input type="password" class="form-control form-control-sm password-field" value="<?php echo decrypt($entry['password'], $entry['iv']); ?>" readonly>
                            <button type="button" class="btn btn-sm btn-outline-secondary toggle-password" title="Mostra/Nascondi">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary copy-password" title="Copia negli appunti">
                                <i class="bi bi-clipboard"></i>
                            </button>
                        </div>
                    </div>
                    <?php if (!empty($entry['notes'])): ?>
                    <div>
                        <label class="fw-bold mb-1 small text-muted">Note:</label>
                        <p class="mb-0 small"><?php echo sanitize($entry['notes']); ?></p>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-muted small">
                    Ultima modifica: <?php echo date('d/m/Y H:i', strtotime($entry['updated_at'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

<?php endif; ?>

<script>
    // Conferma eliminazione
    function confirmDelete(id) {
        if (confirm('Sei sicuro di voler eliminare questa password?')) {
            window.location.href = 'delete_password.php?id=' + id;
        }
    }

    // Mostra/nascondi password
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const passwordField = this.parentElement.querySelector('.password-field');
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
    });

    // Copia password negli appunti
    document.querySelectorAll('.copy-password').forEach(button => {
        button.addEventListener('click', function() {
            const passwordField = this.parentElement.querySelector('.password-field');

            // Cambia temporaneamente il tipo per poter copiare il valore
            const originalType = passwordField.type;
            passwordField.type = 'text';

            // Seleziona e copia
            passwordField.select();
            document.execCommand('copy');

            // Ripristina il tipo originale
            passwordField.type = originalType;

            // Feedback all'utente
            const originalTitle = this.getAttribute('title');
            this.setAttribute('title', 'Copiato!');

            // Ripristina il titolo originale dopo 2 secondi
            setTimeout(() => {
                this.setAttribute('title', originalTitle);
            }, 2000);
        });
    });

    // Copia username negli appunti
document.querySelectorAll('.copy-username').forEach(button => {
    button.addEventListener('click', function() {
        const usernameField = this.parentElement.querySelector('.username-field');

        // Seleziona e copia
        usernameField.select();
        document.execCommand('copy');

        // Deseleziona il campo
        usernameField.blur();

        // Feedback all'utente
        const originalTitle = this.getAttribute('title');
        this.setAttribute('title', 'Copiato!');

        // Ripristina il titolo originale dopo 2 secondi
        setTimeout(() => {
            this.setAttribute('title', originalTitle);
        }, 2000);
    });
});

// Funzionalità di ricerca
document.getElementById('searchInput').addEventListener('keyup', function() {
    const searchText = this.value.toLowerCase();

    // Per la visualizzazione desktop (tabella)
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchText) ? '' : 'none';
    });

    // Per la visualizzazione mobile (carte)
    const cards = document.querySelectorAll('.d-md-none .card');
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        card.style.display = text.includes(searchText) ? '' : 'none';
    });
});

document.getElementById('clearSearch').addEventListener('click', function() {
    document.getElementById('searchInput').value = '';

    // Reset della visualizzazione per la tabella
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach(row => {
        row.style.display = '';
    });

    // Reset della visualizzazione per le carte
    const cards = document.querySelectorAll('.d-md-none .card');
    cards.forEach(card => {
        card.style.display = '';
    });
});

</script>

<?php require_once 'includes/footer.php'; ?>
