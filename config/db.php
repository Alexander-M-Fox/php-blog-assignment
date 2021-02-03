<?php
$username = "REDACTED";
$password = "REDACTED";
$host = "REDACTED";
$port = 000000; // REDACTED
$database = $username;

$connection = mysqli_init();
if (!$connection) {
    echo "<p>Initalising MySQLi failed</p>";
} else {
    mysqli_ssl_set($connection, NULL, NULL, NULL, 'REDACTED', NULL);

    mysqli_real_connect($connection, $host, $username, $password, $database, $port, NULL, MYSQLI_CLIENT_SSL_DONT_VERIFY_SERVER_CERT);
    if (mysqli_connect_errno()) {
        echo "<p>Failed to connect to MySQL. " .
            "Error (" . mysqli_connect_errno() . "): " . mysqli_connect_error() . "</p>";
    }
}
