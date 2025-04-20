<?php
// Filename: includes/config.php
// Configurazione dell'applicazione e costanti

// Impostazioni del database
define('DB_HOST', 'sql211.infinityfree.com');
define('DB_USER', 'if0_38342629'); // Da modificare con i dati di Infinity Free
define('DB_PASS', 'DK90KzD4RpB'); // Da modificare con i dati di Infinity Free
define('DB_NAME', 'if0_38342629_passmanager');     // Da modificare con i dati di Infinity Free

// Tempo di inattività in secondi prima del logout automatico (default: 5 minuti)
define('INACTIVITY_TIMEOUT', 300);

// Chiave di crittografia per le password salvate
// IMPORTANTE: Cambia questa chiave con una stringa casuale di 32 caratteri
// e conservala in un luogo sicuro. Se cambi questa chiave, tutte le password
// salvate in precedenza non saranno più decrittografabili.
define('ENCRYPTION_KEY', 'gZ5t3NwRqA1YxVdP8uLhKjBM7sCeFZ2o');

// Metodo e IV per la crittografia
define('CIPHER_METHOD', 'aes-256-cbc');
define('SESSION_NAME', 'password_manager_session');

// Impostazioni di sicurezza per le sessioni
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
    ini_set('session.cookie_secure', 1);
}
ini_set('session.cookie_samesite', 'Strict');
?>
