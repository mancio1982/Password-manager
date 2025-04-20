<?php
// Filename: includes/header.php
// Header comune per tutte le pagine
require_once 'includes/functions.php';
startSession();
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="inactivity-timeout" content="<?php echo defined('INACTIVITY_TIMEOUT') ? INACTIVITY_TIMEOUT : 300; ?>">
    <title>Password Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="immagini/favicon.png" type="image/x-icon">
    <link rel="shortcut icon" href="immagini/favicon.png" type="image/x-icon">
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
  <div class="container">
      <a class="navbar-brand" href="index.php">Password Manager</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
              <?php if (isAuthenticated()): ?>
                  <li class="nav-item">
                      <a class="nav-link" href="dashboard.php">
                          <i class="bi bi-grid d-lg-none me-1"></i>Dashboard
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="add_password.php">
                          <i class="bi bi-plus-circle d-lg-none me-1"></i>Aggiungi
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="logout.php">
                          <i class="bi bi-box-arrow-right d-lg-none me-1"></i>Logout
                      </a>
                  </li>
              <?php else: ?>
                  <li class="nav-item">
                      <a class="nav-link" href="login.php">
                          <i class="bi bi-box-arrow-in-right d-lg-none me-1"></i>Login
                      </a>
                  </li>
                  <li class="nav-item">
                      <a class="nav-link" href="register.php">
                          <i class="bi bi-person-plus d-lg-none me-1"></i>Registrati
                      </a>
                  </li>
              <?php endif; ?>
          </ul>
      </div>
  </div>
</nav>

    <div class="container mt-4">
        <?php displayFlashMessage(); ?>
