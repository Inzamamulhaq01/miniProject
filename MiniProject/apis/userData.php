<?php
session_start();
include '../conn.php';

// Get the user_id from the session or request (optional: you can validate session for security)
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : (isset($_GET['user_id']) ? (int) $_GET['user_id'] : null);

if (!$user_id) {
    echo json_encode(['error' => 'User ID is required']);
    exit;
}

// Prepare the SQL query to retrieve loan data
$sql = "SELECT user_id, salary, credit_score, extra_fund, principal, rate, tenure, start_date, bank_name, repayment, savings FROM loan WHERE user_id = ?";

// Prepare statement
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);

// Execute and fetch result
if ($stmt->execute()) {
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        // Return loan data as JSON
        $loanData = $result->fetch_assoc();
        echo json_encode(['success' => true, 'data' => $loanData]);
    } else {
        echo json_encode(['error' => 'No loan data found for this user']);
    }
} else {
    echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
