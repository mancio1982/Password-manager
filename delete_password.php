<?php
// Filename: delete_password.php
// Script per eliminare una password
require_once 'includes/header.php';
require_once 'includes/db.php';

requireAuth();

$userId = getCurrentUserId();
$passwordId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Verifica che la password esista e appartenga all'utente corrente
$passwordEntry = fetchOne(
    "SELECT id, name FROM passwords WHERE id = ? AND user_id = ?",
    [$passwordId, $userId]
);

if (!$passwordEntry) {
    setFlashMessage('danger', 'Password non trovata o non autorizzata');
    header('Location: dashboard.php');
    exit;
}

// Elimina la password
$result = executeNonQuery(
    "DELETE FROM passwords WHERE id = ? AND user_id = ?",
    [$passwordId, $userId]
);

if ($result) {
    setFlashMessage('success', 'Password eliminata con successo!');
} else {
    setFlashMessage('danger', 'Si è verificato un errore durante l\'eliminazione della password');
}

header('Location: dashboard.php');
exit;
?>
