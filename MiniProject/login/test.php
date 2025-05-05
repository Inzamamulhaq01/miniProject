<?php
header("Content-Type: application/json; charset=UTF-8");
include '../conn.php';

// Start the session to use session variables
session_start();

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->email) || !isset($data->password)) {
    echo json_encode(["error" => "Please provide email and password"]);
    exit();
}

$email = $data->email;
$password = $data->password;

// Check if the email exists
$sql = "SELECT id, name, password FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Verify the password
    if (password_verify($password, $row['password'])) {
        // Correct the session variable assignment
        $_SESSION['id'] = $row['id'];  // Store user ID in session
        echo json_encode([
            "success" => "Login successful",
            'user_id' => $_SESSION['id'],
            "name" => $row['name']
        ]);
    } else {
        echo json_encode(["error" => "Invalid credentials"]);
    }
} else {
    echo json_encode(["error" => "User not found"]);
}

// Close connection
$stmt->close();
$conn->close();
?>
