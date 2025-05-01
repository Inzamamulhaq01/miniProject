<?php
header("Content-Type: application/json; charset=UTF-8");

include '../conn.php';


$data = json_decode(file_get_contents("php://input"));

if (!isset($data->name) || !isset($data->email) || !isset($data->password)) {
    echo json_encode(["error" => "Please provide name, email, and password"]);
    exit();
}

$name = $data->name;
$email = $data->email;
$password = $data->password;

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if the email already exists
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(["error" => "Email is already registered."]);
    exit();
}

// Insert user into the database
$sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $name, $email, $hashed_password);

if ($stmt->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["error" => "Error: " . $stmt->error]);
}

// Close connection
$stmt->close();
$conn->close();
?>
