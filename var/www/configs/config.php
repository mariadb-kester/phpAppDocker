<?php
/* Database credentials. Assuming you are running MySQL server with default setting (user 'root' with no password) */
define('DB_SERVER', 'rwsplit.mariadb-kester.svc.cluster.local');
define('DB_USERNAME', 'MARIADB_USER');
define('DB_PASSWORD', 'mariadb');
define('DB_NAME', 'employees');


/* Attempt to connect to MySQL database */
try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";port=3307;dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("ERROR: Could not connect. " . $e->getMessage());
}
