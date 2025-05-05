<?php
include '../conn.php';

if (!isset($_GET['user_id']) || empty($_GET['user_id'])) {
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

$user_id = (int) $_GET['user_id'];

$sql = "SELECT principal, rate, tenure, missed_emi, repayment,start_date
        FROM loan
        WHERE user_id = ?
        ORDER BY created_at DESC
        LIMIT 1";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $loanData = $result->fetch_assoc();
    $principal = floatval($loanData['principal']);
    $rate = floatval($loanData['rate']);
    $tenureYears = intval($loanData['tenure']);
    $tenureMonths = $tenureYears * 12;

    // Monthly interest rate
    $monthlyRate = $rate / (12 * 100);

    // EMI calculation
    $emi = ($principal * $monthlyRate * pow(1 + $monthlyRate, $tenureMonths)) / (pow(1 + $monthlyRate, $tenureMonths) - 1);

    // Number of months passed
$startDate = new DateTime($loanData['start_date']);
$now = new DateTime();
$monthsElapsed = ($now->format('Y') - $startDate->format('Y')) * 12 + ($now->format('m') - $startDate->format('m'));

// Check if today is within the last two days of the current month
if ($now->format('d') > (intval($now->format('t')) - 2)) {
    $monthsElapsed++; // Include this month
} else {
    $monthsElapsed = max(0, $monthsElapsed); // Ensure it's not negative
}
$monthsElapsed = min($monthsElapsed, $tenureMonths); // Avoid overrun

// Total paid so far
$totalPaid = $emi * $monthsElapsed;

// Principal paid till now (approximate with flat EMI method)
$principalPaid = ($principal / $tenureMonths) * $monthsElapsed;

// Interest paid = total paid − principal paid
$interestPaid = $totalPaid - $principalPaid;

$loanData['emi'] = round($emi, 2);
$loanData['months_elapsed'] = $monthsElapsed;
$loanData['total_paid'] = round($totalPaid, 2);
$loanData['principal_paid'] = round($principalPaid, 2);
$loanData['interest_paid'] = round($interestPaid, 2);

// End date
$startDate = new DateTime($loanData['start_date']);
$startDate->modify('+' . intval($loanData['tenure']) . ' years');
$loanData['end_date'] = $startDate->format('Y-m-d');

// Remaining tenure
$remainingMonths = $tenureMonths - $monthsElapsed;

// Outstanding balance (present value of remaining EMIs)
if ($remainingMonths > 0) {
    $outstandingBalance = $emi * ((1 - pow(1 + $monthlyRate, -$remainingMonths)) / $monthlyRate);
} else {
    $outstandingBalance = 0;
}

$loanData['outstanding_balance'] = round($outstandingBalance, 2);

echo json_encode(['success' => true, 'data' => $loanData]);

} else {
    echo json_encode(['error' => 'No loan data found']);
}

$stmt->close();
$conn->close();
?>
