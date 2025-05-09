<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// $localhost="localhost";
// $username = "root";
// $dbpassword = "";
// $database = "loan_optimizer";
$localhost="sql205.infinityfree.com";
$username = "if0_38925237";
$dbpassword = "MiniProject123";
$database = "if0_38925237_loan_optimizer";

if (!isset($conn)) {
    try {
        // Establish the connection
        $conn = new mysqli($localhost, $username, $dbpassword, $database);

        // Check for connection errors
        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }

        // Optionally, uncomment the line below to confirm connection success
        // echo "Connected successfully!";
    } catch (Exception $e) {
        // Display a user-friendly error message and exit
        echo "<h1>500 Server Unreachable</h1>";
        exit;
    }
}

?>