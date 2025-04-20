<?php
// Filename: logout.php
// Script per il logout
require_once 'includes/functions.php';

startSession();

// Distruggi la sessione
session_unset();
session_destroy();

// Reindirizza alla pagina di login
header('Location: login.php');
exit;
?>
