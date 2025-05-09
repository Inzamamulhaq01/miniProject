<?php
header("Content-Type: application/json; charset=UTF-8");

include '../conn.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->name) || !isset($data->email) || !isset($data->password)) {
    echo json_encode(["error" => "Please provide name, email, and password"]);
    exit();
}

// Escape and sanitize inputs
$name = mysqli_real_escape_string($conn, $data->name);
$email = mysqli_real_escape_string($conn, $data->email);
$password = $data->password;

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if the email already exists
$checkQuery = "SELECT id FROM users WHERE email = '$email'";
$result = $conn->query($checkQuery);

if ($result && $result->num_rows > 0) {
    echo json_encode(["error" => "Email is already registered."]);
    exit();
}

// Insert user into the database
$insertQuery = "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$hashed_password')";
if ($conn->query($insertQuery)) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Error: " . $conn->error]);
}

$conn->close();
?>
