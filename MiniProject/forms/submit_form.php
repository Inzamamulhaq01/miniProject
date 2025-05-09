<?php
session_start();
include '../conn.php';

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Sanitize and typecast inputs
$user_id     = (int) $data['id'];
$salary      = floatval($data['salary']);
$creditScore = (int) $data['creditScore'];
$extraFund   = mysqli_real_escape_string($conn, trim($data['extraFund']));
$principal   = floatval($data['principal']);
$rate        = floatval($data['rate']);
$tenure      = (int) $data['tenure'];
$startDate   = mysqli_real_escape_string($conn, trim($data['startDate']));
$bankName    = mysqli_real_escape_string($conn, trim($data['bankName']));
$repayment   = ($data['repayment'] === 'yes') ? 'yes' : 'no';
$savings     = floatval($data['savings']);

// Insert query
$sql = "INSERT INTO loan (
    user_id, salary, credit_score, extra_fund,
    principal, rate, tenure, start_date, bank_name, repayment, savings
) VALUES (
    $user_id, $salary, $creditScore, '$extraFund',
    $principal, $rate, $tenure, '$startDate', '$bankName', '$repayment', $savings
)";

if ($conn->query($sql) === TRUE) {
    // Update the 'users' table to mark newuser = 0
    $updateSql = "UPDATE users SET newuser = 0 WHERE id = $user_id";
    $conn->query($updateSql);

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Insert failed: ' . $conn->error]);
}

$conn->close();
?>
