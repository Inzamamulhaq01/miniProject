<?php
include '../conn.php';

// Validate input
if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

$user_id = (int) $_GET['user_id'];

// SQL query to fetch latest loan data
$sql = "SELECT u.name, l.salary, l.savings, l.extra_fund, l.credit_score
        FROM users u
        JOIN loan l ON u.id = l.user_id
        WHERE u.id = ?
        ORDER BY l.created_at DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $userData]);
} else {
    echo json_encode(['error' => 'No data found']);
}

$stmt->close();
$conn->close();
?>
