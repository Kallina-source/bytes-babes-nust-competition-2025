<?php
session_start();
require_once '../db_connect.php';

echo "Session username: " . ($_SESSION['username'] ?? 'NOT SET') . "<br>";
echo "Session role: " . ($_SESSION['role'] ?? 'NOT SET') . "<br>";

if (isset($_SESSION['username'])) {
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $_SESSION['username']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    echo "<pre>";
    print_r($user);
    echo "</pre>";
}
?>