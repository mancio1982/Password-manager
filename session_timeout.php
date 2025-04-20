<?php
// Filename: session_timeout.php
// Script per gestire il logout automatico per inattività
require_once 'includes/functions.php';

startSession();

// Distruggi la sessione
session_unset();
session_destroy();

// Imposta un messaggio flash
setFlashMessage('warning', 'La tua sessione è scaduta per inattività. Effettua nuovamente il login.');

// Reindirizza alla pagina di login
header('Location: login.php');
exit;
?>
