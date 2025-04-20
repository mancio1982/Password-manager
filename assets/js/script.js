// Filename: assets/js/script.js
// JavaScript personalizzato

// Funzione per impostare timeout per i messaggi flash
document.addEventListener('DOMContentLoaded', function() {
    // Nascondi automaticamente gli alert dopo 5 secondi
    const alerts = document.querySelectorAll('.alert:not(.alert-danger)');

    alerts.forEach(function(alert) {
        setTimeout(function() {
            // Verifica che l'elemento esista ancora nel DOM
            if (alert && alert.parentNode) {
                // Aggiungi classe per animazione di fade out
                alert.classList.add('fade');

                // Rimuovi l'elemento dopo l'animazione
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 150);
            }
        }, 5000);
    });

    // Sistema di logout automatico per inattività
    setupAutoLogout();
});

// Configurazione del sistema di logout automatico
function setupAutoLogout() {
    // Solo se l'utente è loggato (controlliamo un elemento che esiste solo per utenti autenticati)
    if (!document.querySelector('.navbar-nav a[href="logout.php"]')) {
        return; // Utente non autenticato, non attivare il logout automatico
    }

    let inactivityTime = 0;
    const checkInterval = 10; // Controlla ogni 10 secondi
    let inactivityTimer;

    // Ottieni il timeout di inattività dal meta tag (in secondi)
    const inactivityTimeoutMeta = document.querySelector('meta[name="inactivity-timeout"]');
    const inactivityTimeout = inactivityTimeoutMeta ? parseInt(inactivityTimeoutMeta.getAttribute('content')) : 900; // Default: 15 minuti

    // Resetta il timer di inattività
    function resetInactivityTimer() {
        inactivityTime = 0;
    }

    // Eventi utente da monitorare
    const events = [
        'mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click', 'keydown'
    ];

    // Aggiungi event listener per tutti gli eventi
    events.forEach(function(event) {
        document.addEventListener(event, resetInactivityTimer, true);
    });

    // Timer per controllare l'inattività
    inactivityTimer = setInterval(function() {
        inactivityTime += checkInterval;

        // Se l'utente è stato inattivo per il tempo di timeout, esegui il logout
        if (inactivityTime >= inactivityTimeout) {
            clearInterval(inactivityTimer);

            // Mostra messaggio di avviso
            const warningModal = document.createElement('div');
            warningModal.className = 'modal fade show';
            warningModal.style.display = 'block';
            warningModal.setAttribute('tabindex', '-1');
            warningModal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-warning">
                            <h5 class="modal-title">Sessione Scaduta</h5>
                        </div>
                        <div class="modal-body">
                            <p>La tua sessione è scaduta per inattività.</p>
                            <p>Verrai reindirizzato alla pagina di login in 5 secondi...</p>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(warningModal);
            document.body.classList.add('modal-open');

            // Sfondo oscurato per il modal
            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            document.body.appendChild(backdrop);

            // Reindirizza alla pagina di logout dopo 5 secondi
            setTimeout(function() {
                window.location.href = 'session_timeout.php';
            }, 5000);
        }

        // Quando si raggiunge l'80% del tempo di inattività, mostra un avviso
        const warningThreshold = inactivityTimeout * 0.8;
        if (inactivityTime >= warningThreshold && inactivityTime < (warningThreshold + checkInterval)) {
            showInactivityWarning(inactivityTimeout - inactivityTime);
        }
    }, checkInterval * 1000);

    // Mostra un avviso che la sessione sta per scadere
    function showInactivityWarning(remainingTime) {
        // Rimuovi eventuali avvisi precedenti
        const existingWarning = document.getElementById('inactivity-warning');
        if (existingWarning) {
            existingWarning.remove();
        }

        const warningEl = document.createElement('div');
        warningEl.id = 'inactivity-warning';
        warningEl.className = 'alert alert-warning alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        warningEl.style.zIndex = "1060";
        warningEl.innerHTML = `
            <strong>Attenzione!</strong> La tua sessione scadrà tra circa ${Math.round(remainingTime / 60)} minuti per inattività.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="document.getElementById('inactivity-warning').remove()"></button>
            <div class="mt-2">
                <button class="btn btn-sm btn-primary" onclick="resetInactivityTimer();document.getElementById('inactivity-warning').remove()">
                    Mantieni sessione attiva
                </button>
            </div>
        `;
        document.body.appendChild(warningEl);

        // Auto rimuovi dopo 30 secondi se l'utente non interagisce
        setTimeout(function() {
            if (document.getElementById('inactivity-warning')) {
                document.getElementById('inactivity-warning').remove();
            }
        }, 30000);
    }

    // Rendi disponibile la funzione resetInactivityTimer globalmente
    window.resetInactivityTimer = resetInactivityTimer;
}
