<?php
include '../conn.php';

// Validate input
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

$user_id = (int) $_GET['user_id']; // Integer cast ensures safety

$sql = "SELECT u.name, l.salary, l.savings, l.extra_fund, l.credit_score
        FROM users u
        JOIN loan l ON u.id = l.user_id
        WHERE u.id = $user_id
        ORDER BY l.created_at DESC
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $userData = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $userData]);
} else {
    echo json_encode(['error' => 'No data found']);
}

$conn->close();
?>
