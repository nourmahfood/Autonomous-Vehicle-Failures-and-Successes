<?php
try {
    // Corrected PDO connection with the proper port
    $conn = new PDO("mysql:host=127.0.0.1;port=3307;dbname=mystore", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected successfully!<br>";
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>

