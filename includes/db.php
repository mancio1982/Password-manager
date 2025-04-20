<?php
// Filename: includes/db.php
// Connessione al database

require_once 'config.php';

function getDbConnection() {
    static $conn = null;

    if ($conn === null) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

            if ($conn->connect_error) {
                throw new Exception("Connessione al database fallita: " . $conn->connect_error);
            }

            $conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            die("Errore: " . $e->getMessage());
        }
    }

    return $conn;
}

// Funzione per eseguire query con prepared statements
function executeQuery($sql, $params = [], $types = "") {
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Errore nella preparazione della query: " . $conn->error);
    }

    if (!empty($params)) {
        // Se i tipi non sono specificati, li generiamo in base ai parametri
        if (empty($types)) {
            $types = "";
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= "i"; // integer
                } elseif (is_float($param)) {
                    $types .= "d"; // double
                } elseif (is_string($param)) {
                    $types .= "s"; // string
                } else {
                    $types .= "b"; // blob
                }
            }
        }

        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    return $result;
}

// Funzione per ottenere un singolo record
function fetchOne($sql, $params = [], $types = "") {
    $result = executeQuery($sql, $params, $types);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }

    return null;
}

// Funzione per ottenere tutti i record
function fetchAll($sql, $params = [], $types = "") {
    $result = executeQuery($sql, $params, $types);
    $rows = [];

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    }

    return $rows;
}

// Funzione per inserire dati e ottenere l'ID inserito
function insert($sql, $params = [], $types = "") {
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Errore nella preparazione della query: " . $conn->error);
    }

    if (!empty($params)) {
        if (empty($types)) {
            $types = "";
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= "i";
                } elseif (is_float($param)) {
                    $types .= "d";
                } elseif (is_string($param)) {
                    $types .= "s";
                } else {
                    $types .= "b";
                }
            }
        }

        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $insertId = $conn->insert_id;
    $stmt->close();

    return $insertId;
}

// Funzione per aggiornare o eliminare dati
function executeNonQuery($sql, $params = [], $types = "") {
    $conn = getDbConnection();
    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Errore nella preparazione della query: " . $conn->error);
    }

    if (!empty($params)) {
        if (empty($types)) {
            $types = "";
            foreach ($params as $param) {
                if (is_int($param)) {
                    $types .= "i";
                } elseif (is_float($param)) {
                    $types .= "d";
                } elseif (is_string($param)) {
                    $types .= "s";
                } else {
                    $types .= "b";
                }
            }
        }

        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $affectedRows = $stmt->affected_rows;
    $stmt->close();

    return $affectedRows;
}

// Crea le tabelle del database se non esistono
function setupDatabase() {
    $conn = getDbConnection();

    // Tabella users
    $conn->query("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // Tabella passwords
    $conn->query("
        CREATE TABLE IF NOT EXISTS passwords (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(100) NOT NULL,
            username VARCHAR(100) NOT NULL,
            password TEXT NOT NULL,
            iv VARCHAR(64) NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    if ($conn->error) {
        die("Errore nell'impostazione del database: " . $conn->error);
    }
}

// Esegui setup database
setupDatabase();
?>
