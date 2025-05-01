<?php
session_start();
include '../conn.php';

// Get JSON input
$data = json_decode(file_get_contents('php://input'), true);

// Required fields
$required = ['id', 'salary', 'expenditure', 'creditScore', 'extraFund', 'principal', 'rate', 'tenure', 'startDate', 'missedEmi', 'repayment', 'savings'];
foreach ($required as $field) {
    if (!isset($data[$field]) || $data[$field] === '') {
        echo json_encode(['error' => "Missing or empty field: $field"]);
        exit;
    }
}

// Sanitize and typecast
$user_id     = (int) $data['id'];
$salary      = floatval($data['salary']);
$expenditure = floatval($data['expenditure']);
$creditScore = (int) $data['creditScore'];
$extraFund   = trim($data['extraFund']); // optional: escape string
$principal   = floatval($data['principal']);
$rate        = floatval($data['rate']);
$tenure      = (int) $data['tenure'];
$startDate   = trim($data['startDate']); // Consider validating date format
$missedEmi   = (int) $data['missedEmi'];
$repayment   = ($data['repayment'] === 'yes') ? 'yes' : 'no';
$savings     = floatval($data['savings']);

// Prepare statement
$sql = "INSERT INTO loan (
    user_id, salary, expenditure, credit_score, extra_fund,
    principal, rate, tenure, start_date, missed_emi, repayment, savings
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param(
    "ididddddsiss",
    $user_id, $salary, $expenditure, $creditScore, $extraFund,
    $principal, $rate, $tenure, $startDate, $missedEmi, $repayment, $savings
);

// Execute and respond
if ($stmt->execute()) {
    $updateSql = "UPDATE users SET newuser = 0 WHERE id = ?";
    $updateStmt = $conn->prepare($updateSql);
    if ($updateStmt) {
        $updateStmt->bind_param("i", $user_id);
        $updateStmt->execute();
        $updateStmt->close();
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
