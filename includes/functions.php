<?php
// Filename: includes/functions.php
// Funzioni di utilità per l'applicazione

require_once 'config.php';

// Avvia la sessione se non è già attiva
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
    }
}

// Controlla se l'utente è autenticato
function isAuthenticated() {
    startSession();
    return isset($_SESSION['user_id']);
}

// Reindirizza se l'utente non è autenticato
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: login.php');
        exit;
    }
}

// Reindirizza se l'utente è già autenticato
function redirectIfAuthenticated() {
    if (isAuthenticated()) {
        header('Location: dashboard.php');
        exit;
    }
}

// Sanitizza input utente
function sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Genera un vettore di inizializzazione random per la crittografia
function generateIV() {
    return bin2hex(openssl_random_pseudo_bytes(openssl_cipher_iv_length(CIPHER_METHOD) / 2));
}

// Cripta il testo
function encrypt($plainText) {
    $iv = generateIV();
    $iv_bin = hex2bin($iv);

    $cipherText = openssl_encrypt(
        $plainText,
        CIPHER_METHOD,
        ENCRYPTION_KEY,
        0,
        $iv_bin
    );

    return ['ciphertext' => $cipherText, 'iv' => $iv];
}

// Decripta il testo
function decrypt($cipherText, $iv) {
    $iv_bin = hex2bin($iv);

    $plainText = openssl_decrypt(
        $cipherText,
        CIPHER_METHOD,
        ENCRYPTION_KEY,
        0,
        $iv_bin
    );

    return $plainText;
}

// Ottiene l'ID utente corrente
function getCurrentUserId() {
    startSession();
    return $_SESSION['user_id'] ?? null;
}

// Ottiene i dati dell'utente corrente
function getCurrentUser() {
    $userId = getCurrentUserId();

    if (!$userId) {
        return null;
    }

    require_once 'db.php';

    return fetchOne(
        "SELECT id, username, email, created_at FROM users WHERE id = ?",
        [$userId]
    );
}

// Funzione per flashare messaggi tra le pagine
function setFlashMessage($type, $message) {
    startSession();
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Funzione per ottenere il messaggio flash
function getFlashMessage() {
    startSession();

    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }

    return null;
}

// Funzione per visualizzare il messaggio flash
function displayFlashMessage() {
    $flashMessage = getFlashMessage();

    if ($flashMessage) {
        $type = $flashMessage['type'];
        $message = $flashMessage['message'];

        echo "<div class=\"alert alert-{$type} alert-dismissible fade show\" role=\"alert\">
            {$message}
            <button type=\"button\" class=\"btn-close\" data-bs-dismiss=\"alert\" aria-label=\"Close\"></button>
        </div>";
    }
}
?>
