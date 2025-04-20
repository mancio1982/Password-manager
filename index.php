<?php
// Filename: index.php
// Pagina principale
require_once 'includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="text-center mb-4">Password Manager</h1>
                <p class="lead text-center">Gestisci le tue password in modo sicuro</p>

                <div class="text-center mt-5">
                    <?php if (isAuthenticated()): ?>
                        <a href="dashboard.php" class="btn btn-primary btn-lg">Vai alla Dashboard</a>
                    <?php else: ?>
                        <div class="d-flex justify-content-center gap-3">
                            <a href="login.php" class="btn btn-primary btn-lg">Login</a>
                            <a href="register.php" class="btn btn-outline-primary btn-lg">Registrati</a>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="row mt-5">
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock fs-1 text-primary"></i>
                        </div>
                        <h3 class="text-center">Sicuro</h3>
                        <p class="text-center">Le tue password sono criptate con algoritmi avanzati e accessibili solo a te.</p>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-cloud-check fs-1 text-primary"></i>
                        </div>
                        <h3 class="text-center">Accessibile</h3>
                        <p class="text-center">Accedi alle tue password da qualsiasi dispositivo in qualsiasi momento.</p>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <i class="bi bi-person-lock fs-1 text-primary"></i>
                        </div>
                        <h3 class="text-center">Privato</h3>
                        <p class="text-center">I tuoi dati restano privati. Non condividiamo le tue informazioni con nessuno.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
